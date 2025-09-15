<?php
namespace App\Services\Impl\User;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\User\AddressOrderRepository;
use App\Http\Resources\User\AddressOrderResource;
use App\Services\Interfaces\User\AddressOrderServiceInterface;

class AddressOrderService extends BaseService implements AddressOrderServiceInterface {

protected $repository;

protected $fieldSearchs = ['title', 'full_name', 'phone', 'email', 'address'];

protected $complexFilter = ['id'];
protected $simpleFilter = ['publish', 'user_id'];


protected $with = ['users'];
protected $relationFilter = [];

protected $cacheKeyPrefix = 'address_orders';
protected $cacheTags = ['address_orders:collection'];

protected $useUpload = false;

    public function __construct(
        AddressOrderRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository(){
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self {

        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource){
        return new AddressOrderResource($resource);
    }

    protected function beforeSave(Request $request, ?int $id = 0): self
    {
        return parent::beforeSave($request, $id);
    }
    // set is_default = 0 for other records
    protected function afterSave(Request $request): self
    {
        $result = parent::afterSave($request);
        $isDefault = (int)($request->input('is_default') ?? 0) === 1;
        if ($isDefault && $this->model) {
            $currentId = $this->model->id;
            $userId = $this->model->user_id ?? $request->input('user_id');
            if ($userId) {
                $table = $this->repository->getTable();
                $affectedIds = DB::table($table)
                    ->where('user_id', $userId)
                    ->where('id', '!=', $currentId)
                    ->where('is_default', 1)
                    ->pluck('id')
                    ->all();

                if (!empty($affectedIds)) {
                    DB::table($table)
                        ->whereIn('id', $affectedIds)
                        ->update(['is_default' => 0]);

                    foreach ($affectedIds as $affectedId) {
                        $this->clearCacheById($affectedId);
                    }
                }
            }
        }
        return $result;
    }

}
