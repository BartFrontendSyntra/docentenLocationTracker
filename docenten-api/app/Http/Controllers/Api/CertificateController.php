<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CertificateController extends Controller
{

    public function index()
    {
        return CertificateResource::collection(Certificate::all());
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:certificates,name',
        ]);

        $certificate = Certificate::create($validated);

        return new CertificateResource($certificate);
    }

    public function show(Certificate $certificate)
    {
        $certificate->load('teachers');

        return new CertificateResource($certificate);
    }

    public function update(Request $request, Certificate $certificate)
    {
           $validated = $request->validate([
            'name' => ['sometimes',
            'required',
            'string',
            'max:255',
            Rule::unique('certificates', 'name')->ignore($certificate->id),
            ]
        ]);


        $certificate->update($validated);

        return new CertificateResource($certificate);
    }

    public function destroy(Certificate $certificate)
    {
        $certificate->delete();

        return response()->noContent();
    }
}
