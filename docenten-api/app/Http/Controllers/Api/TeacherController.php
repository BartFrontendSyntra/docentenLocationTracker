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
    // Gate::authorize('create', Teacher::class);

    $request->validate([
        'file' => ['required', 'file', 'mimes:csv,txt', 'max:5120'],
    ]);

    $file = $request->file('file');
    $csvData = array_map('str_getcsv', file($file->getRealPath()));
    $headers = array_shift($csvData);

    $headers = array_map('trim', $headers);
    $headerCount = count($headers);

    $importedTeachers = [];

    foreach ($csvData as $row) {
        // Skip completely empty rows
        if (empty(array_filter($row))) {
            continue;
        }
        $rowCount = count($row);
        if ($rowCount < $headerCount) {
            // If the row is too short (missing trailing commas), pad it with empty strings
            $row = array_pad($row, $headerCount, '');
        } elseif ($rowCount > $headerCount) {
            // If the row has extra rogue commas at the end, slice them off
            $row = array_slice($row, 0, $headerCount);
        }

        $rowData = array_combine($headers, $row);

        $cityId = null;
        if (!empty($rowData['city']) || !empty($rowData['postal_code'])) {
            $city = City::firstOrCreate([
                'name'        => trim($rowData['city'] ?? ''),
                'postal_code' => trim($rowData['postal_code'] ?? '')
            ]);
            $cityId = $city->id;
        }

        $locationData = null;
        // Check if the CSV has lat and lng columns, and if they actually contain numbers
        if (isset($rowData['lat']) && isset($rowData['lng']) && $rowData['lat'] !== '' && $rowData['lng'] !== '') {
            $lat = (float) trim($rowData['lat']);
            $lng = (float) trim($rowData['lng']);

            // 4326 is the standard SRID for GPS coordinates
            $locationData = new Point($lat, $lng, 4326);
        }

        $addressId = null;
        if (!empty($rowData['street']) || $cityId) {
            $address = Address::firstOrCreate([
                'street'       => trim($rowData['street'] ?? ''),
                'house_number' => trim($rowData['house_number'] ?? ''),
                'city_id'      => $cityId,
            ]);
            if ($locationData) {
                $address->update(['location_data' => $locationData]);
            }
            $addressId = $address->id;
        }

        $teacher = Teacher::updateOrCreate(
            ['email' => trim($rowData['email'])],
            [
                'first_name'     => trim($rowData['first_name'] ?? 'Unknown'),
                'last_name'      => trim($rowData['last_name'] ?? 'Unknown'),
                'company_number' => trim($rowData['company_number'] ?? null),
                'telephone'      => trim($rowData['telephone'] ?? null),
                'cellphone'      => trim($rowData['cellphone'] ?? null),
                'address_id'     => $addressId,
            ]
        );

        if (!empty($rowData['courses'])) {
            $courseNames = array_map('trim', explode(',', $rowData['courses']));
            $courseIds = [];

            foreach ($courseNames as $name) {
                if (!empty($name)) {
                    $course = Course::firstOrCreate(['name' => $name]);
                    $courseIds[] = $course->id;
                }
            }
            $teacher->courses()->sync($courseIds);
        }

        if (!empty($rowData['certificates'])) {
            $certTitles = array_map('trim', explode(',', $rowData['certificates']));
            $certIds = [];

            foreach ($certTitles as $name) {
                if (!empty($name)) {
                    $cert = Certificate::firstOrCreate(['name' => $name]);
                    $certIds[] = $cert->id;
                }
            }
            $teacher->certificates()->sync($certIds);
        }

        $importedTeachers[] = $teacher;
    }

    $teachersToReturn = collect($importedTeachers)->map(function ($teacher) {
        return $teacher->load(['address.city', 'courses', 'certificates']);
    });

    return response()->json([
        'message' => count($importedTeachers) . ' teachers imported successfully.',
        'data' => TeacherResource::collection($teachersToReturn)
    ], 200);
    }
}
