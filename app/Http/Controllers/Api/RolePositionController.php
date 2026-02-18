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
        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($positions)
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
        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($position)
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
        return response()->json([
            'success' => true,
            'message' => 'Role position berhasil dibuat',
            'data' => $this->encryptKDMPData($position)
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

        return response()->json([
            'success' => true,
            'message' => 'Role position berhasil diupdate',
            'data' => $this->encryptKDMPData($position)
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
