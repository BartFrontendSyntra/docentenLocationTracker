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
        'location_data' => $this->when(
            $this->location_data !== null,
            function () {
                $location = $this->location_data;

                // Try common spatial POINT representations
                if (is_object($location)) {
                    // Properties commonly used by spatial point types
                    if (isset($location->lat, $location->lng)) {
                        return [
                            'lat' => $location->lat,
                            'lng' => $location->lng,
                        ];
                    }
                    if (isset($location->latitude, $location->longitude)) {
                        return [
                            'lat' => $location->latitude,
                            'lng' => $location->longitude,
                        ];
                    }

                    // Fallbacks if the type provides array/string conversion
                    if (method_exists($location, 'toArray')) {
                        return $location->toArray();
                    }
                    if (method_exists($location, '__toString')) {
                        return (string) $location;
                    }
                }

                // If it's already scalar/array or an unknown type, return as-is
                return $location;
            }
        ),
        'city' => new CityResource($this->whenLoaded('city')),
        'teachers' => new TeacherResource($this->whenLoaded('teachers')),
        ];
    }
}
