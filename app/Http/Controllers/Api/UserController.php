<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserController extends Controller
{
    // GET /users
    public function index()
    {
        $users = User::with(['roles.positions'])->get();
        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($users)
        ]);
    }

    // GET /users/{id}
    public function show($id)
    {
        $users = User::with(['roles.positions'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($users)
        ]);
    }
    // POST /users
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6',
            'roles' => 'array', // optional: array of role IDs
        ]);

        $user = User::create([
            'id' => (string) Str::uuid(),
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);
    

        if (!empty($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }

        $user->load(['roles.positions']);

        return response()->json([
            'success' => true,
            'message' => 'User created',
            'data' => $this->encryptKDMPData($user)
        ], 201);
    }

    // PUT /users/{id}
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|string|max:100',
            'email' => 'sometimes|email|unique:users,email,' . $id,
            'password' => 'sometimes|min:6',
            'roles' => 'sometimes|array',
        ]);
        if (isset($validated['name'])) $user->name = $validated['name'];
        if (isset($validated['email'])) $user->email = $validated['email'];
        if (isset($validated['password'])) $user->password = Hash::make($validated['password']);

        $user->save();

        if (isset($validated['roles'])) {
            $user->roles()->sync($validated['roles']);
        }
        $user->load(['roles.positions']);
        return response()->json([
            'success' => true,
            'message' => 'User updated',
            'data' => $this->encryptKDMPData($user)
        ]);
    }

    // DELETE /users/{id}
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->roles()->detach(); // detach roles from pivot
        $user->delete();
        return response()->json([
            'success' => true,
            'message' => 'User deleted'
        ]);
    }
}
