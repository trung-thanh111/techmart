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
        if ($request->boolean('simple', false)) {
            return [
                'id' => $this->id,
                'name' => $this->name,
                'description' => $this->description,
                'slug' => $this->slug,
                'image' => [
                    'path' => $this->image ?? null,
                    'url' => $this->formatImageUrl($this->image),
                ],
                'isHot' => (bool) $this->is_hot,
                'publish' => (int) $this->publish,
                'likeCount' => (int) $this->like_count,
                'creator' => $this->whenLoaded('users', function () {
                    return $this->users ? [
                        'id' => $this->users->id,
                        'avatar' => $this->formatImageUrl($this->users->image),
                        'name' => $this->users->name,
                    ] : null;
                }),
                'postCatalogues' => PostCatalogueResource::collection($this->whenLoaded('post_catalogues')),
                'createdAtHuman' => $this->created_at?->diffForHumans(),
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
                'url' => $this->formatImageUrl($this->image),
            ],
            'album' => is_array($this->album) ? array_map(function ($img) {
                return [
                    'path' => $img,
                    'url' => $this->formatImageUrl($img),
                ];
            }, $this->album) : [],
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
                    'avatar' => $this->formatImageUrl($this->users->image),
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
