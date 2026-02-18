<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Province;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProvinceController extends Controller
{
    /**
     * List semua provinsi
     */
    public function index()
    {
        $province = Province::orderBy('id')->get();
        $encryptedData = $this->encryptKDMPData($province);
    
        return response()->json([
            'success' => true,
            'data' => $encryptedData
        ]);
    }

    /**
     * Show satu provinsi
     */
    public function show($id)
    {
        $province = Province::find($id);
        $encryptedData = $this->encryptKDMPData($province);

        if (!$province) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $encryptedData
        ]);
    }

    /**
     * Create provinsi baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => 'required|string|max:50|unique:provinces,code',
        ]);

        $province = Province::create($validated);
        $encryptedData = $this->encryptKDMPData($province);

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil dibuat',
            'data' => $encryptedData
        ], 201);
    }

    /**
     * Update provinsi
     */
    public function update(Request $request, $id)
    {
        $province = Province::find($id);

        if (!$province) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'code' => [
                'required',
                'string',
                'max:50',
                Rule::unique('provinces')->ignore($province->id),
            ],
        ]);

        $province->update($validated);

        // Enkripsi data setelah update
        $encryptedData = $this->encryptKDMPData($province);

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil diperbarui',
            'data' => $encryptedData
        ]);
    }


    /**
     * Hapus provinsi (soft delete)
     */
    public function destroy($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        try {
            $province->delete();

            return response()->json([
                'success' => true,
                'message' => 'Provinsi berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus provinsi: ' . $e->getMessage()
            ], 500);
        }
    }
}
