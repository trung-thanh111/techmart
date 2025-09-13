<?php

namespace App\Http\Resources\Faq;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqResource extends JsonResource
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
            'question' => $this->question,
            'slug' => $this->slug,
            'answer' => $this->answer,
            'sort_order' => $this->sort_order,
            'is_featured' => $this->is_featured,
            'faq_catalogue_id' => (int) $this->faq_catalogue_id,
            'publish' => (int) $this->publish,
            'faq_catalogue' => new FaqCatalogueResource($this->whenLoaded('faq_catalogues')),
            'creator' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
