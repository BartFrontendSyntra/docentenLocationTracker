<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
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
        // collection because of many-to-many relationship
        'course_types' => CourseTypeResource::collection($this->whenLoaded('types')),
        'teachers' => TeacherResource::collection($this->whenLoaded('teachers')),
    ];
    }
}
