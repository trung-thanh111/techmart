<?php

namespace App\Http\Resources\Post;

use App\Http\Resources\Comment\PostCommentResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Post\PostCatalogueResource;
use App\Http\Resources\Post\TagResource;

class PostResource extends JsonResource
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
                'description' => $this->description,
                'slug' => $this->slug,
                'image' => [
                    'path' => $this->image ?? null,
                    'url' => asset("/storage/{$this->image}") ?? null,
                ],
                'isHot' => (bool) $this->is_hot,
                'publish' => (int) $this->publish,
                'likeCount' => (int) $this->like_count,
                'creator' => $this->whenLoaded('users', function () {
                    return $this->users ? [
                        'id' => $this->users->id,
                        'avatar' => asset("/storage/{$this->users->image}") ?? null,
                        'name' => $this->users->name,
                    ] : null;
                }),
                'createdAtHuman' => $this->created_at?->diffForHumans(),
                // 'order' => (int) $this->order,
                // 'viewCount' => (int) $this->view_count,
                // 'postCatalogueId' => (int) $this->post_catalogue_id,
                // 'tagId' => (int) $this->tag_id,
                // 'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
                // 'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
                // 'publishedAt' => $this->published_at ? \Carbon\Carbon::parse($this->published_at)->format('Y-m-d H:i:s') : null,
                // 'publishedAtHuman' => $this->published_at ? \Carbon\Carbon::parse($this->published_at)->diffForHumans() : null,
                // 'updatedAtHuman' => $this->updated_at?->diffForHumans(),
            ];
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'content' => $this->content,
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'tableOfContent' => (bool) $this->table_of_content,
            'isHot' => (bool) $this->is_hot,
            'order' => (int) $this->order,
            'publish' => (int) $this->publish,
            'viewCount' => (int) $this->view_count,
            'likeCount' => (int) $this->like_count,
            'postCatalogueId' => (int) $this->post_catalogue_id,
            'tagId' => (int) $this->tag_id,
            'creator' => $this->whenLoaded('users', function () {
                return $this->users ? [
                    'id' => $this->users->id,
                    'avatar' => asset("/storage/{$this->users->image}") ?? null,
                    'name' => $this->users->name,
                ] : null;
            }),
            'postCatalogues' => PostCatalogueResource::collection($this->whenLoaded('post_catalogues')),
            'tags' => TagResource::collection($this->whenLoaded('post_tags')),
            'relateds' => $this->whenLoaded('relateds', function () {
                return PostResource::collection($this->relateds);
            }),
            'metaTitle' => $this->meta_title,
            'metaKeyword' => $this->meta_keyword,
            'metaDescription' => $this->meta_description,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            'publishedAt' => $this->published_at ? \Carbon\Carbon::parse($this->published_at)->format('Y-m-d H:i:s') : null,
            'publishedAtHuman' => $this->published_at ? \Carbon\Carbon::parse($this->published_at)->diffForHumans() : null,
            'createdAtHuman' => $this->created_at?->diffForHumans(),
            'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
