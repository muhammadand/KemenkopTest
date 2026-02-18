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
    public function index(Request $request)
    {
        // =========================
        // 1. Pagination
        // =========================
        $pageSize = $request->input('page_size', 10);
        if ($pageSize < 1 || $pageSize > 100) {
            $pageSize = 10;
        }

        $page = $request->input('page', 1);
        if ($page < 1) {
            $page = 1;
        }

        // =========================
        // 2. Query Province
        // =========================
        $query = Province::query();

        // =========================
        // 3. Search
        // =========================
        $search = $request->input('search', '');
        $trimSearch = trim($search);

        if ($trimSearch !== '') {
            $query->where('name', 'ILIKE', '%' . $trimSearch . '%');
        }
        $provinces = $query
            ->orderBy('id', 'asc')
            ->paginate($pageSize, ['*'], 'page', $page);
        if ($provinces->isEmpty()) {
            return response()->json([
                'message' => 'Tidak ada data provinsi yang ditemukan.',
                'data' => $this->encryptKDMPData([]),
                'pagination' => [
                    'current_page' => $provinces->currentPage(),
                    'last_page' => $provinces->lastPage(),
                    'page_size' => $provinces->perPage(),
                    'total' => $provinces->total(),
                ],
            ], 200);
        }
        return response()->json([
            'message' => 'Daftar provinsi berhasil diambil.',
            'data' => $this->encryptKDMPData($provinces->items()),
            'pagination' => [
                'current_page' => $provinces->currentPage(),
                'last_page' => $provinces->lastPage(),
                'page_size' => $provinces->perPage(),
                'total' => $provinces->total(),
            ],
        ], 200);
    }


    /**
     * Show satu provinsi
     */
    public function show($id)
    {
        $province = Province::find($id);
        if (!$province) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $this->encryptKDMPData($province)
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

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil dibuat',
            'data' => $this->encryptKDMPData($province)
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

        return response()->json([
            'success' => true,
            'message' => 'Provinsi berhasil diperbarui',
            'data' => $this->encryptKDMPData($province)
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
