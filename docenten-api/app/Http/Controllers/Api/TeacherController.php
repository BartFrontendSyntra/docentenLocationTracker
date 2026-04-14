<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Address;
use App\Models\Course;
use App\Models\Certificate;
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
            'contact.telephone'      => ['nullable', 'string', 'max:50'],
            'contact.cellphone'      => ['nullable', 'string', 'max:50'],

            'address.street'       => ['nullable', 'string', 'max:255'],
            'address.house_number' => ['nullable', 'string', 'max:50'],
            'address.city'         => ['nullable', 'string', 'max:255'],
            'address.postal_code'  => ['nullable', 'string', 'max:20'],

            // Validating arrays for Many-to-Many relationships
            'course_ids'   => ['nullable', 'array'],
            'course_ids.*' => [Rule::exists('courses', 'id')],

            'certificate_ids'   => ['nullable', 'array'],
            'certificate_ids.*' => [Rule::exists('certificates', 'id')],
        ]);

        $cityId = null;

        if (!empty($validated['address']['city']) || !empty($validated['address']['postal_code'])) {

        $city = City::firstOrCreate([
            'name'        => $validated['address']['city'] ?? null,
            'postal_code' => $validated['address']['postal_code'] ?? null
        ]);

        $cityId = $city->id;
        }

        $address = Address::create([
        'street'       => $validated['address']['street'] ?? null,
        'house_number' => $validated['address']['house_number'] ?? null,
        'city_id'      => $cityId,
        'postal_code'  => $validated['address']['postal_code'] ?? null,
        ]);

        $teacher = Teacher::create([
        'first_name'     => $validated['first_name'],
        'last_name'      => $validated['last_name'],
        'email'          => $validated['email'],
        'company_number' => $validated['company_number'],
        'telephone'      => $validated['contact']['telephone'] ?? null,
        'cellphone'      => $validated['contact']['cellphone'] ?? null,
        'address_id'     => $address->id,
        ]);

        if (!empty($validated['course_ids'])) {
            if(is_numeric($validated['course_ids'][0])) {
                $courseIds = $validated['course_ids'];
            } else {
                $courseIds = Course::whereIn('name', $validated['course_ids'])->pluck('id');
            }
            $teacher->courses()->sync($courseIds);
        }
        if (!empty($validated['certificate_ids'])) {
            if(is_numeric($validated['certificate_ids'][0])) {
                $certIds = $validated['certificate_ids'];
            } else {
                $certIds = Certificate::whereIn('title', $validated['certificate_ids'])->pluck('id');
            }
            $teacher->certificates()->sync($certIds);
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
