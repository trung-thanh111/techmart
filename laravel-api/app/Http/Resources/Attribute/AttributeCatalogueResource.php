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
        if ($request->boolean('simple', false)) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'order' => (int) $this->order,
                'publish' => (int) $this->publish,
                'attributes' => $this->whenLoaded('attributes', function () {
                    return $this->attributes->map(fn($attr) => [
                        'id' => $attr->id,
                        'name' => $attr->name,
                    ]);
                }),
            ];
        }
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            // egerload tránh n+1 tối ưu time 
            'attributes' => $this->whenLoaded('attributes', function () {
                return $this->attributes->map(fn($attr) => [
                    'id' => $attr->id,
                    'name' => $attr->name,
                ]);
            }),
        ];
    }
}
