<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
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
            'content' => $this->content,
            'image' => $this->image,
            'icon' => $this->icon,
            'album' => (is_array($this->album) && count($this->album)) ? array_map(function ($item) {
                return [
                    'path' => $item['path'],
                    'url' => asset("storage/{$item['path']}")
                ];
            }, $this->album) : [],
            'tableOfContent' => $this->table_of_content,
            'order' => $this->order,
            'publish' => $this->publish,
            'userId' => $this->user_id,
            'creator' => $this->whenLoaded('user', [
                'id' => $this->user->id,
                'name' => $this->user->name,
            ]),
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
            'scheme' => $this->scheme,
            'iframe' => $this->iframe,
            'publishedAt' => $this->published_at,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'createdAtHuman' => $this->created_at?->diffForHumans(),
            'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
