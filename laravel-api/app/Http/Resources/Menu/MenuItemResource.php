<?php

namespace App\Http\Resources\Menu;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Menu\MenuResource;

class MenuItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'parentId' => (int) $this->parent_id,
            'lft' => (int) $this->lft,
            'rgt' => (int) $this->rgt,
            'level' => (int) $this->level,
            'name' => $this->name,
            'slug' => $this->slug,
            'icon' => $this->icon,
            'order' => (int) $this->order,
            'menuId' => (int) $this->menu_id,
            'userId' => (int) $this->user_id,
            'publish' => (int) $this->publish,
            'children' => $this->whenLoaded('children', function () {
                return self::collection($this->children);
            }),
            'parent' => $this->whenLoaded('parent', function () {
                return new self($this->parent);
            }),
            'menu' => $this->whenLoaded('menu', function () {
                return new MenuResource($this->menu);
            }),
        ];
    }
}
