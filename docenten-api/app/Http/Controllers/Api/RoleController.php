<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Http\Resources\RoleResource;
use Illuminate\Support\Facades\Gate;

class RoleController extends Controller
{
    /**
     * Display a listing of roles
     */
    public function index()
    {
        Gate::authorize('viewAny', Role::class);
        return RoleResource::collection(Role::all());

    }






}
