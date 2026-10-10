<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCatalogueResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    protected function formatImageUrl(?string $image): ?string
    {
        if (empty($image)) {
            return null;
        }
        if (str_starts_with($image, 'http://') || str_starts_with($image, 'https://')) {
            return $image;
        }
        if (str_starts_with($image, '/')) {
            return asset($image);
        }
        return asset("/storage/{$image}");
    }

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
            'description' => $this->description,
            'image' => [
                'path' => $this->image ?? null,
                'url' => $this->formatImageUrl($this->image),
            ],
            'order' => (int) $this->order,
            'isHot' => (bool) $this->is_hot,
            'publish' => (int) $this->publish,
            'creator' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            'publishedAt' => $this->published_at,
            "metaTitle" => $this->meta_title,
            "metaKeyword" => $this->meta_keyword,
            "metaDescription" => $this->meta_description,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            // 'publishedAt' => $this->published_at,
            // 'metaTitle' => $this->meta_title,
            // 'metaKeyword' => $this->meta_keyword,
            // 'metaDescription' => $this->meta_description,
            // 'scheme' => $this->scheme,
            // 'iframe' => $this->iframe,
            // 'content' => $this->content,
            // 'icon' => $this->icon,
            // 'album' => (is_array($this->album) && count($this->album)) ? array_map(function($item){
            //     return [
            //         'path' => $item['path'],
            //         'url' => asset("storage/{$item['path']}")
            //     ];
            // }, $this->album) : [],
            // 'tableOfContent' => $this->table_of_content,
            // 'postCatalogueId' => $this->post_catalogue_id,
            // 'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            // 'createdAtHuman' => $this->created_at?->diffForHumans(),
            // 'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
