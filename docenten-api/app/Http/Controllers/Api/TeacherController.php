<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Http\Resources\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class TeacherController extends Controller
{

    public function index()
    {

        Gate::authorize('viewAny', Teacher::class);

        $teachers = Teacher::with(['address.city', 'courses', 'certificates'])->paginate(25);

        // debuggin
    foreach ($teachers as $teacher) {
        try {
            // Force PHP to test encoding THIS specific teacher,
            // and throw an error if it fails
            json_encode($teacher->toArray(), JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            // Write the culprit directly to the Laravel log file!
            logger()->error('🚨 BROKEN TEACHER FOUND 🚨', [
                'ID' => $teacher->id,
                'Error' => $e->getMessage(),
                'Data' => $teacher->toArray() // Optional, but might be messy
            ]);

            // Gracefully fail the request with a readable message
            abort(500, "Check your laravel.log file! Broken ID: " . $teacher->id);
        }
    }


        return TeacherResource::collection($teachers);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', Teacher::class);

        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name'  => ['required', 'string', 'max:255'],
            'email'      => [
                'required',
                'email',
                Rule::unique('teachers', 'email')
            ],
            'company_number' => ['nullable', 'string', 'max:255'],
            'telephone'      => ['nullable', 'string', 'max:50'],
            'cellphone'      => ['nullable', 'string', 'max:50'],
            'address_id'     => ['required', Rule::exists('addresses', 'id')],

            // Validating arrays for Many-to-Many relationships
            'course_ids'   => ['nullable', 'array'],
            'course_ids.*' => [Rule::exists('courses', 'id')],

            'certificate_ids'   => ['nullable', 'array'],
            'certificate_ids.*' => [Rule::exists('certificates', 'id')],
        ]);

        $teacher = Teacher::create($validated);

        if ($request->has('course_ids')) {
            $teacher->courses()->attach($request->course_ids);
        }
        if ($request->has('certificate_ids')) {
            $teacher->certificates()->attach($request->certificate_ids);
        }

        $teacher->load(['address.city', 'courses', 'certificates']);

        return new TeacherResource($teacher);
    }

    public function show(Teacher $teacher)
    {
        Gate::authorize('view', $teacher);

        $teacher->load(['address.city', 'courses', 'certificates']);

        return new TeacherResource($teacher);
    }


    public function update(Request $request, Teacher $teacher)
    {
        Gate::authorize('update', $teacher);

        $validated = $request->validate([
            'first_name' => ['sometimes', 'required', 'string', 'max:255'],
            'last_name'  => ['sometimes', 'required', 'string', 'max:255'],
            'email'      => [
                'sometimes',
                'required',
                'email',
                Rule::unique('teachers', 'email')->ignore($teacher->id),
            ],
            'company_number' => ['nullable', 'string', 'max:255'],
            'telephone'      => ['nullable', 'string', 'max:50'],
            'cellphone'      => ['nullable', 'string', 'max:50'],
            'address_id'     => ['sometimes', 'required', Rule::exists('addresses', 'id')],

            'course_ids'   => ['nullable', 'array'],
            'course_ids.*' => [Rule::exists('courses', 'id')],

            'certificate_ids'   => ['nullable', 'array'],
            'certificate_ids.*' => [Rule::exists('certificates', 'id')],
        ]);

        $teacher->update($validated);

        if ($request->has('course_ids')) {
            $teacher->courses()->sync($request->course_ids);
        }
        if ($request->has('certificate_ids')) {
            $teacher->certificates()->sync($request->certificate_ids);
        }

        $teacher->load(['address.city', 'courses', 'certificates']);

        return new TeacherResource($teacher);
    }

    public function destroy(Teacher $teacher)
    {
        Gate::authorize('delete', $teacher);

        $teacher->delete();

        return response()->noContent();
    }
}
