<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Http\Resources\CourseTypeResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourseTypeController extends Controller
{

    public function index()
    {
        return CourseTypeResource::collection(CourseType::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('course_types', 'name')
            ],
        ]);

        $courseType = CourseType::create($validated);

        return new CourseTypeResource($courseType);
    }


    public function show(CourseType $courseType)
    {
        $courseType->load('courses');

        return new CourseTypeResource($courseType);
    }

    public function update(Request $request, CourseType $courseType)
    {
        $validated = $request->validate([
            'name' => [
                'sometimes',
                'required',
                'string',
                'max:255',
                Rule::unique('course_types', 'name')->ignore($courseType->id),
            ],
        ]);

        $courseType->update($validated);

        return new CourseTypeResource($courseType);
    }

    public function destroy(CourseType $courseType)
    {
        $courseType->delete();

        return response()->noContent();
    }
}
