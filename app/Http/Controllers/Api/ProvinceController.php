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
        $pageSize = (int) $request->input('page_size', 10);
        $pageSize = ($pageSize >= 1 && $pageSize <= 100) ? $pageSize : 10;

        $page = (int) $request->input('page', 1);
        $page = $page >= 1 ? $page : 1;

        $query = Province::query();

        $search = trim($request->input('search', ''));

        if ($search !== '') {
            $query->where('name', 'ILIKE', '%' . $search . '%');
        }

        $provinces = $query
            ->orderBy('province_id', 'asc')
            ->paginate($pageSize, ['*'], 'page', $page);

        return response()->json([
            'message' => $provinces->isEmpty()
                ? 'Tidak ada data provinsi yang ditemukan.'
                : 'Daftar provinsi berhasil diambil.',
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
                Rule::unique('provinces', 'code')
                    ->ignore($province->province_id, 'province_id'),
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
