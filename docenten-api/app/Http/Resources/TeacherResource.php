<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TeacherResource extends JsonResource
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
        'first_name' => $this->first_name,
        'last_name' => $this->last_name,
        'email' => $this->email,
        'company_number' => $this->company_number,
        // Group mobile and telephone information together
        'contact' => [
            'telephone' => $this->telephone,
            'cellphone' => $this->cellphone,
        ],

        // Relationships
        'address' => new AddressResource($this->whenLoaded('address')),
        'courses' => CourseResource::collection($this->whenLoaded('courses')),
        'certificates' => CertificateResource::collection($this->whenLoaded('certificates')),

        ];
    }
}
