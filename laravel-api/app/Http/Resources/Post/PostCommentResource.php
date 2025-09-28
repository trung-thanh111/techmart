<?php

namespace App\Http\Resources\Post;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostCommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'parent_id'     => $this->parent_id,
            'content'       => $this->content,
            'like_count'    => (int) $this->like_count,
            'comment_count' => (int) $this->comment_count,
            'author'        => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
                'avatar' => [
                    'path' => $this->image ?? null,
                    'url' => asset("/storage/{$this->image}") ?? null,
                ],
            ]),
            'replies' => PostCommentResource::collection($this->whenLoaded('replies')),
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'createdAtHuman' => $this->created_at?->diffForHumans(),
            // 'likes'   => PostCommentLikeResource::collection($this->whenLoaded('likes')),
        ];
    }
}
