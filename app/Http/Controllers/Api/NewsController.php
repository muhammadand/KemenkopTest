<?php

namespace App\Http\Controllers\Api;

use App\Exports\NewsExport;
use App\Http\Controllers\Controller;
use App\Models\News;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class NewsController extends Controller
{

    public function export()
    {
        return Excel::download(new NewsExport, 'news.xlsx');
    }
    /**
     * List semua News
     */
    public function index(Request $request)
    {
        $pageSize = (int) $request->input('page_size', 10);
        $pageSize = ($pageSize >= 1 && $pageSize <= 100) ? $pageSize : 10;
        $page = (int) $request->input('page', 1);
        $page = $page >= 1 ? $page : 1;
        $query = News::query();
        $search = trim($request->input('search', ''));
        if ($search !== '') {
            $query->where('title', 'ILIKE', '%' . $search . '%');
        }
        $news = $query
            ->orderBy('id', 'asc')
            ->paginate($pageSize, ['*'], 'page', $page);
        return response()->json([
            'message' => $news->isEmpty()
                ? 'Tidak ada data News yang ditemukan.'
                : 'Daftar news berhasil diambil.',
            'data' => $this->encryptKDMPData($news->items()),
            'pagination' => [
                'current_page' => $news->currentPage(),
                'last_page' => $news->lastPage(),
                'page_size' => $news->perPage(),
                'total' => $news->total(),
            ],
        ], 200);
    }
    public function getByProvinceId($id)
    {
        $getbyprovice = News::with('province')
            ->where('province_id', $id)
            ->get();
        if ($getbyprovice->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data'=>$getbyprovice
        ]);
    }
    /**
     * Show satu News
     */
    public function show($id)
    {
        $province = News::find($id);

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
     * Create News Baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'sub_title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'province_id' => 'required|string|max:255',
        ]);
        $news = News::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'News berhasil dibuat',
            'data' => $this->encryptKDMPData($news)
        ], 201);
    }

    /**
     * Update News 
     */
    public function update(Request $request, $id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'News tidak ditemukan'
            ], 404);
        }
        $validated = $request->validate([
            'title' => 'required|string|max:100',
            'sub_title' => 'required|string|max:255',
            'content' => 'required|string|max:255',
            'province_id' => 'required|string|max:255',
        ]);
        $news->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'News berhasil diperbarui',
            'data' => $this->encryptKDMPData($news)
        ]);
    }

    /**
     * Hapus News
     */
    public function destroy($id)
    {
        $news = News::find($id);
        if (!$news) {
            return response()->json([
                'success' => false,
                'message' => 'Provinsi tidak ditemukan'
            ], 404);
        }
        try {
            $news->delete();
            return response()->json([
                'success' => true,
                'message' => 'News berhasil dihapus'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus News: ' . $e->getMessage()
            ], 500);
        }
    }
}
