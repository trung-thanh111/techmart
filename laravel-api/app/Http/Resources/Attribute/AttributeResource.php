<?php

namespace App\Http\Resources\Attribute;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AttributeResource extends JsonResource
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
            'attributeCatalogueId' => (int) $this->attribute_catalogue_id,
            // 'attributeCatalogues' => AttributeCatalogueResource::collection($this->attribute_catalogues),
            // 'creator' => $this->whenLoaded('users', [
            //     'id' => $this->users->id,
            //     'name' => $this->users->name,
            // ]),
            // 'createdAtHuman' => $this->created_at?->diffForHumans(),
            // 'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
