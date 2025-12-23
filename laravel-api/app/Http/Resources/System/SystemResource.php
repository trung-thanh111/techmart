<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SystemResource extends JsonResource
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
            'keyword' => $this->keyword ?? null,
            'content' => $this->content ?? null,
            'publish' => (int) $this->publish,
            'userId' => $this->user_id ?? null,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
