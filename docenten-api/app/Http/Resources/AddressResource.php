<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\TeacherResource;

class AddressResource extends JsonResource
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
        'street' => $this->street,
        'house_number' => $this->house_number,
        'location_data' => $this->location_data,

        'city' => new CityResource($this->whenLoaded('city')),
        'teachers' => new TeacherResource($this->whenLoaded('teachers')),
        ];
    }
}
