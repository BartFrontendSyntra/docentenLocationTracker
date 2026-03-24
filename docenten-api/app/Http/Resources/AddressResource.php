<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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

        // Conditionally load the CityResource
        'city' => new CityResource($this->whenLoaded('city')),
        ];
    }
}
