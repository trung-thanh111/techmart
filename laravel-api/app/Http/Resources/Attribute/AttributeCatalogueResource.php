<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeCatalogueResource extends JsonResource
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
            'description' => $this->description,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            // 'creator' => $this->whenLoaded('users', [
            //     'id' => $this->users->id,
            //     'name' => $this->users->name,
            // ]),
        ];
    }
}
