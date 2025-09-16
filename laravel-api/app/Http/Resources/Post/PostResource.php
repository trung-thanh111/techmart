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
            'tableOfContent' => (bool) $this->table_of_content,
            'isHot' => (bool) $this->is_hot,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            'viewCount' => (int) $this->view_count,
            'postCatalogueId' => (int) $this->post_catalogue_id,
            'tagId' => (int) $this->tag_id,
            'creator' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            "postCatalogues" => new PostCatalogueResource($this->whenLoaded('post_catalogues')),
            "tags" => new TagResource($this->whenLoaded('tags')),
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
            'publishedAt' => $this->published_at,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            'createdAtHuman' => $this->created_at?->diffForHumans(),
            'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
