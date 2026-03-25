<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Address;
use App\Http\Resources\AddressResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{

    public function index()
    {

        $addresses = Address::with('city')->paginate(20);

        return AddressResource::collection($addresses);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'street'       => ['required', 'string', 'max:255'],
            'house_number' => ['required', 'string', 'max:50'],
            'city_id'      => ['required', Rule::exists('cities', 'id')],

            // latitude and longitude are the gps coordinates, for now they are optional, but in the future we might want to make them required
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        // after validation, we can safely work with the data.
        $addressData = [
            'street'       => $validated['street'],
            'house_number' => $validated['house_number'],
            'city_id'      => $validated['city_id'],
        ];

        if (isset($validated['latitude']) && isset($validated['longitude'])) {
        // we need to transform the latitude and longitude into a POINT
            $addressData['location_data'] = DB::raw("ST_GeomFromText('POINT({$validated['longitude']} {$validated['latitude']})', 4326)");
        }

        $address = Address::create($addressData);
        $address->load('city');

        return new AddressResource($address);
    }

    public function show(Address $address)
    {
        $address->load(['city', 'teacher']);

        return new AddressResource($address);
    }

    public function update(Request $request, Address $address)
    {
        $validated = $request->validate([
            'street'       => ['sometimes', 'required', 'string', 'max:255'],
            'house_number' => ['sometimes', 'required', 'string', 'max:50'],
            'city_id'      => ['sometimes', 'required', Rule::exists('cities', 'id')],
            'latitude'     => ['nullable', 'numeric', 'between:-90,90'],
            'longitude'    => ['nullable', 'numeric', 'between:-180,180'],
        ]);

        $updateData = [];

        // Only add fields to the update array if they were provided in the request
        if ($request->has('street'))
            {
                $updateData['street'] = $validated['street'];
            }
        if ($request->has('house_number'))
            {
                 $updateData['house_number'] = $validated['house_number'];
            }
        if ($request->has('city_id'))
            {
                $updateData['city_id'] = $validated['city_id'];
            }
            // we need to transform the latitude and longitude
        if ($request->has('latitude') && $request->has('longitude')) {
            $updateData['location_data'] = DB::raw("ST_GeomFromText('POINT({$validated['longitude']} {$validated['latitude']})', 4326)");
        }

        // Only run the update if there's actually data to update
        if (!empty($updateData)) {
            $address->update($updateData);
        }

        $address->load('city');

        return new AddressResource($address);
    }

    public function destroy(Address $address)
    {
        $address->delete();

        return response()->noContent();
    }
}
