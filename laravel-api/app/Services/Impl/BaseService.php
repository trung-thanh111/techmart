<?php

namespace App\Services\Impl;

use App\Classes\Nestedset;
use App\Enums\Common;
use App\Traits\HasCache;
use App\Traits\HasUpload;
use Illuminate\Http\Request;
use App\Traits\HasTransaction;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Cache;
use App\Services\Interfaces\BaseServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class BaseService implements BaseServiceInterface
{

    use HasTransaction, HasCache, HasUpload;

    protected $auth;
    protected $model;
    protected $repository;
    protected $modelData; //  chính là payload
    protected $result;

    protected $perpage = 20;
    protected $limit = 0;
    protected $sort = ['id', 'desc'];
    protected $with = [];
    protected $fieldSearchs = ['name'];
    protected $simpleFilter = ['publish'];
    protected $complexFilter = ['id'];
    protected $dateFilter = ['created_at', 'updated_at'];
    protected $relationFilter = [];

    protected $cacheTags = [];

    protected $baseFolder;

    protected $nested;
    protected $useNestedSet = false; // flag check xem các lớp con nào có sử dụng nestedset (true ở lớp con nếu sử dụng)
    protected $useUpload = false; // flag check xem các lớp con nào có sử dụng upload (true ở lớp con nếu sử dụng)

    public function __construct()
    {
        $this->repository = $this->getRepository();
        $this->auth = auth(Common::API);
        $this->setCacheTTL();
        $this->baseFolder = $this->generateUploadBasePath();
    }

    protected abstract function getRepository();

    protected abstract function prepareModelData(Request $request): self;

    /**
     * Dữ liệu lấy ra là dạng danh sách:
     * 1. lấy toàn bộ data
     *  ---> option: có thể lựa chọn lấy theo kiểu gì
     * 2. lấy có phân trang
     *
     * --> những cấu hình mà mặc định phải có dành cho lấy dữ liệu theo danh sách:
     *   1. nếu như là phân trang -> phải cho tùy chon bản ghi / trang -> phải can thiệp được ở mỗi module
     *   2. sort direction: sắp xếp dữ liệu
     *   3. tìm kiếm dữ liệu:
     *      gồm có 5 mục chính:
     *          a. tìm kiếm theo từ khóa: cần chỉ được được tìm kiếm keyword theo cột nào ở mỗi module
     *          b. tìm kiếm căng bản theo toán tử =
     *          c. lọc nâng cao:
     *              - là các toán tử >, <, >=, <=
     * `            - tìm bản ghi có trong tệp
     *              - tìm trong khoảng
     *              - v,v...
     *          d. lọc theo thời gian
     *              - created_at, updated_at
     *              - history, track, log
     *          e. lọc vào qun hệ:
     *              - các quan hệ có thể lòng nhau -> dùng đệ quy
     *
     * nếu mà phương thức lấy ra danh sách quá phức tạp thì viết ra một phương thức đnghia riêng -> k gọi vào paginate nữa
     */

    public function paginate(Request $request)
    {
        $cacheKey = $this->generateCollectionCacheKey($request->all());
        $lockKey = "lock:{$cacheKey}";

        $this->result = Cache::lock($lockKey, 2)->block(2, function () use ($cacheKey, $request) {
            return Cache::tags($this->cacheTags)->remember($cacheKey, $this->cacheTTL, function () use ($request) {
                $specification = $this->specifications($request);
                return $this->repository->paginate($specification);
            });
        });

        return $this->getResult();
    }

    private function specifications(Request $request): array
    {
        return [
            'type' => $request->type === 'all',
            'perpage' => $request->perpage ?? $this->perpage,
            'sort' => $request->sort ? explode(',', $request->sort) : $this->sort,
            'limit' => $request->limit ?? $this->limit,
            'with' => $request->with ?? $this->with,
            'keyword' => [
                'q' => $request->keyword,
                'fields' => $this->fieldSearchs
            ],
            'filters' => [
                'simple' => $this->biuldFilter($request, $this->simpleFilter),
                'complex' => $this->biuldFilter($request, $this->complexFilter),
                'date' => $this->biuldFilter($request, $this->dateFilter),
                'relation' => $this->biuldFilter($request, $this->relationFilter)
            ],
        ];
    }

    public function biuldFilter(Request $request, array $filters = []): array
    {
        $conditions = [];
        if (count($filters)) {
            foreach ($filters as $val) {
                // nếu mà có value gửi lên thì mới xử lý
                if ($request->has($val)) {
                    // chuẩn hóa dữ liệu: nếu là chuỗi JSON thì decode, nếu là scalar thì wrap về mảng
                    $input = $request->input($val);
                    if (is_string($input)) {
                        $decoded = json_decode($input, true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $input = $decoded;
                        }
                    }
                    // add vào $conditions
                    $conditions[$val] = $input;
                }
            }
        }
        return $conditions;
    }

    /**
     *Các bước thực hiện chuẩn chinhr phương thức save
     *1. mở trấnction
     *2. chuẩn bị data
     *3. trước khi lưu
     *4. Lưu
     *5. Sau khi lưu
     *6. commit lên csdl
     *7. trả thông tin
     */
    public function save(Request $request, ?int $id = null)
    {
        try {
            // các chaining method này được định nghĩ trong Trait hasTransaction
            return $this->beginTransaction()
                ->prepareModelData($request, $id) // phương thức bên thằng con triển khai
                ->beforeSave($request, $id) // mỗi module -> phương thức này khác nhau sẽ tự triển khai ở lớp con
                ->saveModel($id) // lưu dữ liệu
                ->afterSave($request, $id) // sau khi save lấy data từ đâu trả về chẳng hạn or cache data
                ->handleRelation($request) // xử lý quan hệ nế
                ->afterHandleRelation($request) // cache lại data sau khi handle relation
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function destroy(int $id = 0)
    {
        try {
            return $this->beginTransaction()
                ->beforeDelete($id)
                ->deleteModel($id)
                ->afterDelete($id)
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function bulkDestroy(Request $request)
    {
        try {
            return $this->beginTransaction()
                ->beforeBulkDelete($request)
                ->bulkModelDelete($request)
                ->afterBulkDelete($request)
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    // kiếm tra bản ghi có tồn tại không trước khi lưu
    protected function checkModelExists(?int $id = null): self
    {
        $cacheKey = $this->generateSingleCacheKey();
        $lockKey = "lock:{$cacheKey}";
        // nếu mà cache có data thì lấy trong cache ra còn không thì mới lấy từ database
        $this->model = Cache::lock($lockKey, 2)->block(2, function () use ($cacheKey, $id) {
            return Cache::remember($cacheKey, $this->cacheTTL, function () use ($id) {
                // nếu mà k có data thì lấy trong model ra (db) với quan hệ
                return $this->repository->findById($id, $this->with);
            });
        });

        if (!$this->model) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }
        $this->result = $this->model;
        return $this;
    }

    public function show(int $id = 0)
    {
        return $this->checkModelExists($id)->getResult();
    }

    public function showBySlug(string $slug = '')
    {
        $model = $this->findByField('slug', $slug);
        if (!$model) {
            throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        }
        return $model;
    }

    public function relation(Request $request, int $id = 0, string $action = '')
    {
        try {
            return $this->beginTransaction()
                ->beforeActionRelation($request, $id)
                ->relationAction($request, $action)
                ->afterActionRelation()
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function bulkSave(Request $request, string $action = '')
    {
        try {
            return $this->beginTransaction()
                ->beforeBulkSave($request, $action)
                ->bulkModelSave($request, $action)
                ->afterBulkSave($request, $action)
                ->bulkHandleRelation($request, $action)
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function restore(Request $request, int $id = 0)
    {
        try {
            return $this->beginTransaction()
                ->beforeRestore($request, $id)
                ->modelRestore($request, $id)
                ->afterRestore($request, $id)
                ->commit()
                ->getResult();
        } catch (\Throwable $th) {
            $this->rollback();
            throw $th;
        }
    }

    public function findByField(string $field = '', mixed $value = null)
    {
        $cacheKey = "{$this->cacheKeyPrefix}:{$field}:{$value}";
        $lockKey = "lock:{$cacheKey}";
        $this->model = Cache::lock($lockKey, 2)->block(2, function () use ($cacheKey, $field, $value) {
            return Cache::tags($this->cacheTags)->remember($cacheKey, $this->cacheTTL, function () use ($field, $value) {
                return $this->repository->findByField($field, $value, $this->with);
            });
        });

        // if (!$this->model) {
        //     throw new ModelNotFoundException(Lang::get('message.record_not_found'));
        // }
        $this->result = $this->model;
        return $this->getResult();
    }

    public function initializeNestedset(): self
    {
        // khởi tạo intance do Nestedset có nhận vào data ở contruct
        $this->nested = new Nestedset([
            'table' => $this->cacheKeyPrefix // do cachekeyPrefix trùng với tên bảng -> dùng lại k cần khởi tạo biến mới chứa tên bản
        ]);
        $this->nested->rebuild(); // rebiuld lại các node trong tree
        return $this;
    }

    protected function afterHandleRelation(Request $request): self
    {
        // Reload model với relations mới sau khi handle relation
        $this->model = $this->repository->findById($this->model->getKey(), $this->with);
        $this->result = $this->model;

        // Cache lại data sau khi đã handle relation để đảm bảo data mới được cache
        return $this->cacheSingleRecord();
    }
}
