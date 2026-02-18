<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * List semua role
     */
    public function index()
    {
        $roles = Role::with('positions')->get();
        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($roles),
        ]);
    }
    public function testDecrypt(Request $request)
    {
        $encrypted = $request->input('data'); // string terenkripsi
        $decrypted = $this->decryptKDMPData($encrypted, 'json'); // kembalikan sebagai array

        return response()->json([
            'success' => true,
            'data' => $decrypted
        ]);
    }
    /**
     * Detail role
     */
    public function show($id)
    {
        $role = Role::with('positions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan'
            ], 404);
        }
         $encryptedData = $this->encryptKDMPData($role);

        return response()->json([
            'success' => true,
            'data' =>  $encryptedData
        ]);
    }

    /**
     * Buat role baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100|unique:roles,name',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);
        $encryptedData = $this->encryptKDMPData($role);
        return response()->json([
            'success' => true,
            'message' => 'Role berhasil dibuat',
            'data' => $encryptedData
        ], 201);
    }

    /**
     * Update role
     */
    public function update(Request $request, $id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role tidak ditemukan'
            ], 404);
        }

        // Validasi input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles')->ignore($role->id)],
        ]);

        // Update data
        $role->update([
            'name' => $validated['name'],
            'slug' => Str::slug($validated['name']),
        ]);

        // Encrypt data sebelum dikembalikan
        $encryptedData = $this->encryptKDMPData($role);

        return response()->json([
            'success' => true,
            'message' => 'Role berhasil diupdate',
            'data' => $encryptedData
        ]);
    }

    /**
     * Hapus role
     */
    public function destroy($id)
    {
        $role = Role::find($id);
        if (!$role) {
            return response()->json([
                'success' => true, // tetap sukses, tapi datanya terencrypt
                'data' => $this->encryptKDMPData(['message' => 'Role tidak ditemukan'])
            ], 404);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData(['message' => 'Role berhasil dihapus'])
        ]);
    }
}
