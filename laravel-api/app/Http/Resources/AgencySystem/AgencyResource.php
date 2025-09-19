<?php
namespace App\Http\Resources\AgencySystem;

use App\Http\Resources\User\UserResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyResource extends JsonResource
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
            'name' => $this->name ?? null,
            'slug' => $this->slug ?? null,
            'description' => $this->description ?? null,
            'logo' => $this->logo ?? null,
            'website' => $this->website ?? null,
            'email' => $this->email ?? null,
            'phone' => $this->phone ?? null,
            'address' => $this->address ?? null,
            'iframeMap' => $this->iframe_map ?? null,
            'agencyWorkingHour' => AgencyWorkingHourResource::collection($this->agency_working_hours),
            'publish' => (int) $this->publish,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
            // 'creator' => $this->whenLoaded('users', [
            //     'id' => $this->users->id,
            //     'name' => $this->users->name,
            // ]),
        ];
    }
}
