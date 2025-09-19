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
        // trả về các trường cần lấy ra của user login thành công
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
            'address' => $this->address,
            'userCatalogueId' => (int) $this->user_catalogue_id,
            'image' => [
                'path' => $this->image ?? null,
                'url' => asset("/storage/{$this->image}") ?? null,
            ],
            'addressOrder' => AddressOrderResource::collection($this->address_orders),
            'userCatalogue' => UserCatalogueResource::collection($this->user_catalogues),
            'publish' => (int) $this->publish,
            'createdAt' => $this->created_at?->format('Y-m-d H:i:s'),
            'updatedAt' => $this->updated_at?->format('Y-m-d H:i:s'),
            // 'deletedAt' => $this->deleted_at?->format('Y-m-d H:i:s'),
            // 'createdAtHuman' => $this->created_at?->diffForHumans(),
            // 'updatedAtHuman' => $this->updated_at?->diffForHumans(),
        ];
    }
}
