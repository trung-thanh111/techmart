<?php

namespace App\Http\Resources\Menu;

use App\Enums\Menu as MenuEnum;
use App\Http\Resources\Menu\MenuItemResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MenuResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'position' => $this->position,
            'publish' => (int) $this->publish,
            'menuItems' => $this->whenLoaded('menu_items', function () {
                return MenuItemResource::collection($this->menu_items->sortBy(['lft', 'order']));
            }),
        ];
    }
}
