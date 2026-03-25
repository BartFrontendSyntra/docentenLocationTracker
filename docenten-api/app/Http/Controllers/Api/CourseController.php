<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Course;
use App\Http\Resources\CourseResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function index() {
        Gate::authorize('viewAny', Course::class);
        // also return the types and teachers
        return CourseResource::collection(Course::with(['types', 'teachers'])->get());
    }

    public function store(Request $request) {
        Gate::authorize('create', Course::class);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'course_type_ids' => 'nullable|array',
            // validate that each course_type_id exists in the course_types table
            'course_type_ids.*' => [
                                    'integer',
                                    'distinct',
                                    Rule::exists('course_types', 'id'),
                                ],
        ]);

        $course = Course::create(['name' => $validated['name']]);

        // Attach the course types (Many-to-Many)
        if ($request->has('course_type_ids')) {
            $course->types()->attach($request->course_type_ids);
        }

        $course->load('types');
        return new CourseResource($course);
    }

    public function show(Course $course) {
        Gate::authorize('view', $course);
        $course->load(['types', 'teachers']);
        return new CourseResource($course);
    }

    public function update(Request $request, Course $course) {
        Gate::authorize('update', $course);
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'course_type_ids' => 'nullable|array',
            'course_type_ids.*' => [
                                    'integer',
                                    'distinct',
                                    Rule::exists('course_types', 'id'),
                                ],
        ]);

        $course->update($request->only('name'));

        // Sync the course types (removes old ones not in the array, adds new ones)
        if ($request->has('course_type_ids')) {
            $course->types()->sync($request->course_type_ids);
        }

        $course->load('types');
        return new CourseResource($course);
    }

    public function destroy(Course $course) {
        Gate::authorize('delete', $course);
        $course->delete();
        return response()->noContent();
    }
}
