<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'email' => $this->email,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'description' => $this->description,
            'birthday' => $this->birthday ? \Carbon\Carbon::parse($this->birthday)->format('Y-m-d') : null,
            'gender' => $this->gender,
            'phone' => $this->phone,
            'street' => $this->street ?? '',
            'city' => $this->city ?? '',
            'ward' => $this->ward ?? '',
            'userCatalogueId' => (int) $this->user_catalogue_id,
            'isAdmin' => (bool) ($this->is_admin || $this->user_catalogue?->is_admin || (int) $this->user_catalogue_id === 2),
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'addressOrder' => AddressOrderResource::collection($this->address_orders),
            'userCatalogue' => ($this->user_catalogues && $this->user_catalogues->isNotEmpty())
                ? UserCatalogueResource::collection($this->user_catalogues)
                : ($this->user_catalogue ? [new UserCatalogueResource($this->user_catalogue)] : []),
            'publish' => (int) $this->publish,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
        ];
    }
}
