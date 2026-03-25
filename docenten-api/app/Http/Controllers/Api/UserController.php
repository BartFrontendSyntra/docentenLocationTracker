<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash; // <-- Crucial for encrypting passwords!

class UserController extends Controller
{

    public function index()
    {
        $users = User::with('role')->paginate(15);

        return UserResource::collection($users);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('users', 'name')],
            'email'    => ['required', 'email', 'max:255', Rule::unique('users', 'email')],
            'password' => ['required', 'string', 'min:8'], // here we can enforce more rules on passwords if we want
            'role_id'  => ['required', Rule::exists('roles', 'id')],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        $user->load('role');

        return new UserResource($user);
    }

    public function show(User $user)
    {
        $user->load('role');

        return new UserResource($user);
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'required', 'string', 'max:255', Rule::unique('users', 'name')->ignore($user->id)],
            'email'    => ['sometimes', 'required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:8'],
            'role_id'  => ['sometimes', 'required', Rule::exists('roles', 'id')],
        ]);

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
        $user->delete();

        return response()->noContent();
    }
}
