<?php

namespace App\Services\Impl\Menu;

use App\Classes\Nestedset;
use App\Models\MenuItem;
use Illuminate\Http\Request;
use App\Services\Impl\BaseService;
use Illuminate\Database\Eloquent\Model;
use App\Repositories\Menu\MenuItemRepository;
use App\Http\Resources\Menu\MenuItemResource;
use App\Services\Interfaces\Menu\MenuItemServiceInterface;

class MenuItemService extends BaseService implements MenuItemServiceInterface
{

    protected $repository;

    protected $complexFilter = ['id'];

    protected $with = ['children', 'parent', 'menu'];
    protected $relationFilter = [];

    protected $cacheKeyPrefix = 'menu_items';
    protected $cacheTags = ['menu_items:collection'];

    protected $useUpload = false;
    protected $useNestedSet = true;

    public function __construct(
        MenuItemRepository $repository
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
        return new MenuItemResource($resource);
    }

    /**
     * Rebuild nested set tree for menu items
     */
    public function rebuildTree(Request $request)
    {
        $menuId = $request->input('menu_id');

        if (!$menuId) {
            return response()->json([
                'success' => false,
                'message' => 'Menu ID is required'
            ], 400);
        }

        $nestedset = new Nestedset([
            'table' => 'menu_items',
            'menu_id' => $menuId
        ]);

        $nestedset->rebuild();

        return response()->json([
            'success' => true,
            'message' => 'Menu tree rebuilt successfully'
        ]);
    }

    /**
     * Handle drag and drop for menu items
     */
    public function dragDrop(Request $request)
    {
        $data = $request->validate([
            'menu_id' => 'required|integer|exists:menus,id',
            'item_id' => 'required|integer|exists:menu_items,id',
            'parent_id' => 'nullable|integer|exists:menu_items,id',
            'position' => 'required|integer|min:0'
        ]);

        $menuItem = $this->repository->findById($data['item_id']);

        if ($menuItem->menu_id != $data['menu_id']) {
            return response()->json([
                'success' => false,
                'message' => 'Menu item does not belong to the specified menu'
            ], 400);
        }

        // Update parent_id
        $menuItem->parent_id = $data['parent_id'] ?? 0;
        $menuItem->save();

        $siblings = MenuItem::where('menu_id', $data['menu_id'])
            ->where('parent_id', $data['parent_id'] ?? 0)
            ->where('id', '!=', $data['item_id'])
            ->orderBy('order', 'asc')
            ->get();

        $order = 0;
        foreach ($siblings as $index => $sibling) {
            if ($index == $data['position']) {
                $order++;
            }
            $sibling->order = $order;
            $sibling->save();
            $order++;
        }

        $menuItem->order = $data['position'];
        $menuItem->save();

        $rebuildRequest = new Request(['menu_id' => $data['menu_id']]);
        $this->rebuildTree($rebuildRequest);

        return response()->json([
            'success' => true,
            'message' => 'Menu item moved successfully'
        ]);
    }
}
