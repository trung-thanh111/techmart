<?php

namespace App\Http\Resources\Faq;

use App\Http\Resources\Post\PostCatalogueResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FaqCatalogueResource extends JsonResource
{
    protected bool $withFaqs;
    public function __construct($resource, $withFaqs = true)
    {
        parent::__construct($resource);
        $this->withFaqs = $withFaqs;
    }
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
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'isHot' => (bool) $this->is_hot,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            'faqs' => $this->when(
                $this->withFaqs,
                fn() => FaqResource::collection($this->faqs)
            ),

            'faqCatalogues' => $this->whenLoaded('faq_catalogues', function () {
                return PostCatalogueResource::collection($this->faq_catalogues);
            }),
            'creator' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
