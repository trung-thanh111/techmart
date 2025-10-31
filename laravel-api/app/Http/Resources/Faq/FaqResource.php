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
        if ($request->boolean('simple', false)) {
            return [
                'id' => $this->id,
                'question' => $this->question,
                'slug' => $this->slug,
                'answer' => $this->answer,
                'order' => (int) $this->order,
                'isHot' => (bool) $this->is_hot,
                'faqCatalogueId' => (int) $this->faq_catalogue_id,
                'publish' => (int) $this->publish,
            ];
        }
        return [
            'id' => $this->id,
            'question' => $this->question,
            'slug' => $this->slug,
            'answer' => $this->answer,
            'order' => (int) $this->order,
            'isHot' => (bool) $this->is_hot,
            'faqCatalogueId' => (int) $this->faq_catalogue_id,
            'publish' => (int) $this->publish,
            'faqCatalogues' => new FaqCatalogueResource($this->whenLoaded('faq_catalogues'), false),
        ];
    }
}
