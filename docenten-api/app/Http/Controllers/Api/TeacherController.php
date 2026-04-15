<?php

namespace App\Http\Controllers\Api;

use MatanYadaev\EloquentSpatial\Objects\Point;

use App\Http\Controllers\Controller;
use App\Models\Teacher;
use App\Models\Address;
use App\Models\Course;
use App\Models\Certificate;
use App\Models\City;
use App\Http\Resources\TeacherResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

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
            'address.location_data.lat' => ['nullable', 'numeric'],
            'address.location_data.lng' => ['nullable', 'numeric'],

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

        $locationData = null;
        if (isset($validated['address']['location_data']['lat']) && isset($validated['address']['location_data']['lng'])) {
        $locationData = new Point($validated['address']['location_data']['lat'], $validated['address']['location_data']['lng'], 4326);
    }


        $address = Address::create([
        'street'       => $validated['address']['street'] ?? null,
        'house_number' => $validated['address']['house_number'] ?? null,
        'city_id'      => $cityId,
        'postal_code'  => $validated['address']['postal_code'] ?? null,
        'location_data' => $locationData,
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
        'first_name'           => ['required', 'string', 'max:255'],
        'last_name'            => ['required', 'string', 'max:255'],
        'email'                => ['required', 'email', Rule::unique('teachers', 'email')->ignore($teacher->id)],
        'company_number'       => ['nullable', 'string', 'max:255'],
        'contact.telephone'    => ['nullable', 'string', 'max:50'],
        'contact.cellphone'    => ['nullable', 'string', 'max:50'],
        'address.street'       => ['nullable', 'string', 'max:255'],
        'address.house_number' => ['nullable', 'string', 'max:50'],
        'address.city'         => ['nullable', 'string', 'max:255'],
        'address.postal_code'  => ['nullable', 'string', 'max:20'],
        'course_ids'           => ['nullable', 'array'],
        'certificate_ids'      => ['nullable', 'array'],
        'address.location_data.lat' => ['nullable', 'numeric'],
        'address.location_data.lng' => ['nullable', 'numeric'],
    ]);

        $cityId = null;
        if (!empty($validated['address']['city']) || !empty($validated['address']['postal_code'])) {
        $city = City::firstOrCreate([
            'name'        => $validated['address']['city'] ?? null,
            'postal_code' => $validated['address']['postal_code'] ?? null
        ]);
        $cityId = $city->id;
        }
        $locationData = null;
        if (isset($validated['address']['location_data']['lat']) && isset($validated['address']['location_data']['lng'])) {
        $locationData = new Point($validated['address']['location_data']['lat'], $validated['address']['location_data']['lng'], 4326);
        }
        if ($teacher->address) {
        $teacher->address->update([
            'street'       => $validated['address']['street'] ?? null,
            'house_number' => $validated['address']['house_number'] ?? null,
            'city_id'      => $cityId,
            'location_data' => $locationData,
        ]);
        } else {
        $address = Address::create([
            'street'       => $validated['address']['street'] ?? null,
            'house_number' => $validated['address']['house_number'] ?? null,
            'city_id'      => $cityId,
            'location_data' => $locationData,
        ]);
        $teacher->address_id = $address->id;
        }
        $teacher->update([
        'first_name'     => $validated['first_name'],
        'last_name'      => $validated['last_name'],
        'email'          => $validated['email'],
        'company_number' => $validated['company_number'],
        'telephone'      => $validated['contact']['telephone'] ?? null,
        'cellphone'      => $validated['contact']['cellphone'] ?? null,
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

    public function destroy(Teacher $teacher)
    {
        Gate::authorize('delete', $teacher);

        $teacher->delete();

        return response()->noContent();
    }

    public function import(Request $request)
    {
     Gate::authorize('create', Teacher::class);

    $request->validate([
        'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
    ]);

    $file = $request->file('file');

    $csvData = array_map('str_getcsv', file($file->getRealPath()));

    $headers = array_shift($csvData);

    $importedTeachers = [];

    foreach ($csvData as $row) {
        $rowData = array_combine($headers, $row);

        $teacher = Teacher::updateOrCreate(
            ['email' => $rowData['email']],
            [
                'first_name'     => $rowData['first_name'] ?? 'Unknown',
                'last_name'      => $rowData['last_name'] ?? 'Unknown',
                'company_number' => $rowData['company_number'] ?? null,
                'telephone'      => $rowData['telephone'] ?? null,
                'cellphone'      => $rowData['cellphone'] ?? null,
            ]
        );

        $importedTeachers[] = $teacher;
    }

    return response()->json([
        'message' => count($importedTeachers) . ' teachers imported successfully.',
        'data' => TeacherResource::collection($importedTeachers)
    ], 200);
    }
}
