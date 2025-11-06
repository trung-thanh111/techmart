<?php

namespace App\Services\Impl\Cart;

use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Cart\CartItemRepository;
use App\Http\Resources\Cart\CartItemResource;
use App\Services\Interfaces\Cart\CartItemServiceInterface;
use Illuminate\Support\Facades\Cache;

class CartItemService extends BaseService implements CartItemServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = [];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'cart_items';
    protected $cacheTags = ['cart_items:collection'];

    protected $useUpload = false;

    public function __construct(
        CartItemRepository $repository
    ) {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * nhiệm vụ bắt data từ client post lên (dạng thô, chuỗi, file, ....)
     *
     */
    protected function prepareModelData(Request $request): self
    {

        return $this->initializeBasicModelData($request);
    }

    protected function initializeBasicModelData(Request $request): self
    {
        $fillable = $this->repository->getFillable();
        $this->modelData = $request->only($fillable);
        return $this;
    }

    protected function getResource(Model $resource)
    {
        return new CartItemResource($resource);
    }

    /**
     * Clear cache của Cart sau khi CartItem được save
     * Vì Cart có load cart_items relation, nên khi CartItem thay đổi thì Cart cache cũng cần được clear
     */
    protected function afterSave(Request $request): self
    {
        // Clear cache của CartItem trước
        parent::afterSave($request);

        // Clear cache của Cart nếu có cart_id
        if ($this->model && $this->model->cart_id) {
            $this->clearCartCache($this->model->cart_id);
        }

        return $this;
    }

    /**
     * Lấy cart_id trước khi delete để clear cache sau
     */
    protected function beforeDelete(int $id = 0): self
    {
        // Gọi parent để check model exists và lưu model vào $this->model
        parent::beforeDelete($id);

        // Lấy cart_id từ model TRƯỚC KHI xóa
        $cartId = $this->model ? $this->model->cart_id : null;

        // Lưu cart_id vào model để dùng sau (nếu model có thuộc tính tạm)
        if ($this->model && $cartId) {
            $this->model->setAttribute('_cart_id', $cartId);
        }

        return $this;
    }

    /**
     * Clear cache của Cart sau khi CartItem được delete
     */
    protected function afterDelete(int $id = 0): self
    {
        // Lấy cart_id đã lưu trước đó
        $cartId = $this->model && $this->model->hasAttribute('_cart_id')
            ? $this->model->getAttribute('_cart_id')
            : ($this->model ? $this->model->cart_id : null);

        // Clear cache của CartItem trước
        parent::afterDelete($id);

        // Clear cache của Cart
        if ($cartId) {
            $this->clearCartCache($cartId);
        }

        return $this;
    }

    /**
     * Lấy cart_id trước khi bulk delete để clear cache sau
     */
    public function beforeBulkDelete(Request $request): self
    {
        // Lấy cart_id từ các CartItem bị xóa TRƯỚC KHI xóa
        $cartIds = [];
        if (isset($request->ids) && is_array($request->ids)) {
            $cartItems = $this->repository->findWhereIn($request->ids, 'id');
            foreach ($cartItems as $item) {
                if ($item->cart_id) {
                    $cartIds[] = $item->cart_id;
                }
            }
        }
        // Lưu cart_ids vào request để dùng sau
        $request->merge(['_cart_ids' => array_unique($cartIds)]);

        // Gọi parent để check models exists
        return parent::beforeBulkDelete($request);
    }

    /**
     * Clear cache của Cart sau khi CartItem được bulk delete
     */
    public function afterBulkDelete(Request $request): self
    {
        // Clear cache của CartItem trước
        parent::afterBulkDelete($request);

        // Lấy cart_ids đã lưu trước đó
        $cartIds = $request->input('_cart_ids', []);

        // Clear cache của Cart (unique để tránh clear nhiều lần)
        foreach ($cartIds as $cartId) {
            $this->clearCartCache($cartId);
        }

        return $this;
    }

    /**
     * Clear cache của Cart sau khi CartItem được bulk save
     */
    protected function afterBulkSave(Request $request, string $action = ''): self
    {
        // Lấy cart_id từ các CartItem được save
        $cartIds = [];
        if (isset($request->data) && is_array($request->data)) {
            foreach ($request->data as $item) {
                if (isset($item['cart_id'])) {
                    $cartIds[] = $item['cart_id'];
                }
            }
        }

        // Clear cache của CartItem trước
        parent::afterBulkSave($request, $action);

        // Clear cache của Cart (unique để tránh clear nhiều lần)
        $cartIds = array_unique($cartIds);
        foreach ($cartIds as $cartId) {
            $this->clearCartCache($cartId);
        }

        return $this;
    }

    /**
     * Clear cache của Cart sau khi handle relation
     */
    protected function afterHandleRelation(Request $request): self
    {
        // Clear cache của CartItem trước
        parent::afterHandleRelation($request);

        // Clear cache của Cart nếu có cart_id
        if ($this->model && $this->model->cart_id) {
            $this->clearCartCache($this->model->cart_id);
        }

        return $this;
    }

    /**
     * Helper method để clear cache của Cart
     */
    protected function clearCartCache(int $cartId): void
    {
        // Clear single cache của Cart
        $cartCacheKey = "carts:single:{$cartId}";
        Cache::forget($cartCacheKey);

        // Clear collection cache của Cart bằng tags
        Cache::tags(['carts:collection'])->flush();
    }
}
