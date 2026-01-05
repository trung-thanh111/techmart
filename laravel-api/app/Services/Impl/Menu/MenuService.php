<?php

namespace App\Services\Impl\Menu;

use App\Classes\Nestedset;
use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Menu\MenuRepository;
use App\Http\Resources\Menu\MenuResource;
use App\Models\MenuItem;
use App\Services\Interfaces\Menu\MenuServiceInterface;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class MenuService extends BaseService implements MenuServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];
    protected $with = ['menu_items'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'menus';
    protected $cacheTags = ['menus:collection'];
    protected $useUpload = false;

    public function __construct(MenuRepository $repository)
    {
        $this->repository = $repository;
    }

    protected function getRepository()
    {
        return $this->repository;
    }

    /**
     * Chuẩn bị dữ liệu từ request
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

    protected function handleRelation(Request $request): self
    {
        $relations = $this->repository->getRelationable();
        if (count($relations)) {
            foreach ($relations as $relation) {
                if ($relation === 'menu_items') {
                    continue; // Bỏ qua, xử lý riêng
                }

                if ($request->has($relation)) {
                    $this->model->{$relation}()->sync($request->{$relation});
                }
            }
        }

        $this->handleNestedMenuItems($request);

        return $this;
    }

    /**
     * Xử lý nested menu items từ request
     * Tự động flatten và tạo parent_id relationships
     */
    private function handleNestedMenuItems(Request $request): self
    {
        if (!$request->has('menu_items')) {
            return $this;
        }

        $menuItems = $request->input('menu_items');

        if ($this->model->exists) {
            $this->model->menu_items()->withTrashed()->forceDelete();
        }

        if (!is_array($menuItems) || empty($menuItems)) {
            Log::info("Menu items rỗng hoặc không hợp lệ");
            return $this;
        }

        DB::transaction(function () use ($menuItems) {
            $this->processNestedMenuItems($menuItems, null);
        });

        $this->rebuildNestedSet();

        return $this;
    }

    /**
     * Xử lý đệ quy nested menu items
     * Tạo menu items và children của chúng
     */
    private function processNestedMenuItems(
        array $items,
        ?int $parentId = null,
        int $startOrder = 0
    ): void {
        foreach ($items as $index => $itemData) {
            $createdItem = $this->createMenuItem(
                $itemData,
                $parentId,
                $startOrder + $index + 1
            );

            if (
                isset($itemData['children']) &&
                is_array($itemData['children']) &&
                count($itemData['children']) > 0
            ) {
                $this->processNestedMenuItems(
                    $itemData['children'],
                    $createdItem->id,
                    0
                );

                Log::info("📦 Đã xử lý children cho item: {$createdItem->name}");
            }
        }
    }

    /**
     * Tạo một menu item
     * Xử lý validation và type conversion
     */
    private function createMenuItem(
        array $itemData,
        ?int $parentId,
        int $order
    ): MenuItem {
        $publishRaw = $itemData['publish'] ?? 2;
        $publish = is_bool($publishRaw)
            ? ($publishRaw ? 2 : 1)
            : (int)$publishRaw;

        $lft = isset($itemData['lft']) ? (int)$itemData['lft'] : 0;
        $rgt = isset($itemData['rgt']) ? (int)$itemData['rgt'] : 0;
        $level = isset($itemData['level']) ? (int)$itemData['level'] : 0;

        $attributes = [
            'parent_id' => $parentId ?? 0,
            'name' => $itemData['name'] ?? null,
            'slug' => $itemData['slug'] ?? null,
            'order' => $itemData['order'] ?? $order,
            'publish' => $publish,
            'lft' => $lft,
            'rgt' => $rgt,
            'level' => $level,
            'created_at' => now(),
        ];

        $menuItem = $this->model->menu_items()->create($attributes);

        return $menuItem;
    }

    /**
     * Rebuild nested set tree cho menu items
     */
    private function rebuildNestedSet(): void
    {
        $nestedset = new Nestedset([
            'table' => 'menu_items',
            'menu_id' => $this->model->id
        ]);

        $nestedset->rebuild();
    }

    protected function getResource(Model $resource)
    {
        return new MenuResource($resource);
    }
}
