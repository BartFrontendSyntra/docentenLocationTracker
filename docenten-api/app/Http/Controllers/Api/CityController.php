<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Http\Resources\CityResource;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Gate;

class CityController extends Controller
{
    /**
     * Display a listing of cities
     */
    public function index()
    {
        Gate::authorize('viewAny', City::class);

        return CityResource::collection(City::all());

    }

    /**
     * Store a newly created city in storage.
     */
    public function store(Request $request)
    {
        Gate::authorize('create', City::class);

        $validated = $request->validate([
            'postal_code' => 'required|string|size:4|unique:cities,postal_code',
            'name' => 'required|string|max:255',
        ]);

        return new CityResource(City::create($validated));
    }

    /**
     * Display the specified city.
     */
    public function show(City $city) {
        Gate::authorize('view', $city);
        // Optional: Load addresses if you want to see all addresses in this city
        // $city->load('addresses');
        return new CityResource($city);
    }

    /**
     * Update the specified city in storage.
     */
    public function update(Request $request, City $city) {
        Gate::authorize('update', $city);
        $validated = $request->validate([
            'postal_code' => [
            'sometimes',
            'string',
            'size:4',
            Rule::unique('cities', 'postal_code')->ignore($city->id),
            ],
            'name' => 'sometimes|string|max:255',
        ]);

        $city->update($validated);
        return new CityResource($city);
    }

    /**
     * Remove the specified city from storage.
     */
    public function destroy(City $city) {
        Gate::authorize('delete', $city);
        $city->delete();
        return response()->noContent();
    }
}
