<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Gate;


class UserController extends Controller
{

    public function index()
    {
        Gate::authorize('viewAny', User::class);
        $users = User::with('role')->paginate(15);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        Gate::authorize('create', User::class);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'], // here we can enforce more rules on passwords if we want
            'role'  => ['required', Rule::exists('roles', 'name')],
        ]);

        $role = Role::where('name', $validated['role'])->firstOrFail();

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create([
        'name'     => $validated['name'],
        'email'    => $validated['email'],
        'password' => Hash::make($validated['password']),
        'role_id'  => $role->id,
    ]);

        $user->load('role');

        return new UserResource($user);
    }

    public function show(User $user)
    {
        Gate::authorize('view', $user);

        $user->load('role');

        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        Gate::authorize('update', $user);

        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role'  => ['sometimes', 'required', Rule::exists('roles', 'name')],
        ]);

        // Check if the user wants to change role
        if (isset($validated['role'])) {
        $role = Role::where('name', $validated['role'])->firstOrFail();
        $validated['role_id'] = $role->id;

        // Remove the string 'role' from the array so it doesn't try to save it to the DB
        unset($validated['role']);
        }
        // Check if the user wants to change password
        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            // important! remove the password from validated date, otherwise it will set password to null!
            unset($validated['password']);
        }

        $user->update($validated);

        $user->load('role');

        return new UserResource($user);
    }

    public function destroy(User $user)
    {
        Gate::authorize('delete', $user);
        $user->delete();

        return response()->noContent();
    }
}
