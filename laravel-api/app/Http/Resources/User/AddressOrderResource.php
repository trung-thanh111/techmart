<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressOrderResource extends JsonResource
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
            'title' => $this->title ?? null,
            'slug' => $this->slug ?? null,
            'fullName' => $this->full_name ?? null,
            'phone' => $this->phone ?? null,
            'email' => $this->email ?? null,
            'address' => $this->address ?? null,
            'state' => $this->state ?? null,
            'postalCode' => $this->postal_code ?? null,
            'countryCode' => $this->country_code,
            'isDefault' => $this->is_default,
            'publish' => $this->publish,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
