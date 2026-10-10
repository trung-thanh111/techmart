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
        $rawImage = $this->image;
        $url = null;
        if (!empty($rawImage)) {
            if (str_starts_with($rawImage, 'http://') || str_starts_with($rawImage, 'https://') || str_starts_with($rawImage, '/')) {
                $url = $rawImage;
            } else {
                $cleanPath = ltrim($rawImage, '/');
                $url = str_starts_with($cleanPath, 'storage/') ? asset($cleanPath) : asset("storage/{$cleanPath}");
            }
        }

        return [
            'id' => $this->id,
            'title' => $this->title,
            'type' => $this->type,
            'slug' => $this->slug,
            'content' => $this->content,
            'image' => [
                'path' => $rawImage ?? null,
                'url' => $url,
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
