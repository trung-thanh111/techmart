<?php
namespace App\Http\Resources\AgencySystem;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AgencyWorkingHourResource extends JsonResource
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
            'dayOfWeek' => $this->day_of_week ?? null,
            'openTime' => $this->open_time ?? null,
            'closeTime' => $this->close_time ?? null,
            'publish' => $this->publish ?? null,
            'createdAt' => $this->created_at ?? null,
            'updatedAt' => $this->updated_at ?? null,
        ];
    }
}
