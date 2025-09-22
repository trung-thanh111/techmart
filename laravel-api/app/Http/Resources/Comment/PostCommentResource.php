<?php

namespace App\Http\Resources\Comment;

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
            'id' => $this->id,
            'content' => $this->content,
            'isHot' => (bool) $this->is_hot,
            'publish' => (int) $this->publish,
            'createdAt' => $this->created_at?->toDateTimeString(),
            'updatedAt' => $this->updated_at?->toDateTimeString(),
            'author' => $this->whenLoaded('users', [
                'id' => $this->users->id,
                'name' => $this->users->name,
            ]),
            'parent' => $this->whenLoaded('parent', [
                'id' => $this->parent->id,
                'content' => $this->parent->content,
            ]),
            'replies' => $this->whenLoaded('replies', function () {
                return PostCommentResource::collection($this->replies);
            }),

            // 'votes' => $this->whenLoaded('votes', function () {
            //     return $this->votes->map(function ($vote) {
            //         return [
            //             'userId' => $vote->user_id,
            //             'vote' => (int) $vote->vote,
            //         ];
            //     });
            // }),
        ];
    }
}
