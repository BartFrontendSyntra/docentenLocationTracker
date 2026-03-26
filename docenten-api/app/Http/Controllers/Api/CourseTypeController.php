<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CourseType;
use App\Http\Resources\CourseTypeResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class CourseTypeController extends Controller
{

    public function index()
    {
        Gate::authorize('viewAny', CourseType::class);
        return CourseTypeResource::collection(CourseType::all());
    }

    public function store(Request $request)
    {
        Gate::authorize('create', CourseType::class);

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
        Gate::authorize('view', $courseType);
        $courseType->load('courses');

        return new CourseTypeResource($courseType);
    }

    public function update(Request $request, CourseType $courseType)
    {
        Gate::authorize('update', $courseType);
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
        Gate::authorize('delete', $courseType);

        $courseType->delete();

        return response()->noContent();
    }
}
