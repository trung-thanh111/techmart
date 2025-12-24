<?php

namespace App\Http\Resources\System;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PopupResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => [
                'path' => $this->image ?? null,
                'url' => $this->image ? asset("/storage/{$this->image}") : null,
            ],
            'link' => $this->link,
            'buttonText' => $this->button_text,
            'formHtml' => $this->form_html,
            'showDelay' => (int) $this->show_delay,
            'priority' => (bool) $this->priority,
            'publish' => (int) $this->publish,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
