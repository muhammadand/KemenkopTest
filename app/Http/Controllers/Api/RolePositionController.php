<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RolePosition;
use App\Models\Role;

class RolePositionController extends Controller
{
    /**
     * List semua role positions
     */
    public function index()
    {
        $positions = RolePosition::with('role')->get();
        $encryptedData = $this->encryptKDMPData($positions);
        return response()->json([
            'success' => true,
            'data' => $encryptedData
        ]);
    }

    /**
     * Detail role position
     */
    public function show($id)
    {
        $position = RolePosition::with('role')->find($id);
        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Role position tidak ditemukan'
            ], 404);
        }
        $encryptedData = $this->encryptKDMPData($position);

        return response()->json([
            'success' => true,
            'data' => $encryptedData
        ]);
    }

    /**
     * Buat role position baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'position' => 'required|string|max:100|unique:role_positions,position',
            'role_id' => 'required|exists:roles,id',
        ]);

        $position = RolePosition::create($validated);
        $encryptedData = $this->encryptKDMPData($position);

        return response()->json([
            'success' => true,
            'message' => 'Role position berhasil dibuat',
            'data' => $encryptedData
        ], 201);
    }

    /**
     * Update role position
     */
    public function update(Request $request, $id)
    {
        $position = RolePosition::find($id);
   
        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Role position tidak ditemukan'
            ], 404);
        }


        $validated = $request->validate([
            'position' => 'required|string|max:100|unique:role_positions,position,' . $position->id,
            'role_id' => 'nullable|exists:roles,id',
        ]);

        $position->update($validated);
        $encryptedData = $this->encryptKDMPData($position);

        return response()->json([
            'success' => true,
            'message' => 'Role position berhasil diupdate',
            'data' => $encryptedData
        ]);
    }

    /**
     * Hapus role position
     */
    public function destroy($id)
    {
        $position = RolePosition::find($id);
        if (!$position) {
            return response()->json([
                'success' => false,
                'message' => 'Role position tidak ditemukan'
            ], 404);
        }
        $position->delete();
        return response()->json([
            'success' => true,
            'message' => 'Role position berhasil dihapus'
        ]);
    }
}
