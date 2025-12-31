<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class PengumumanAdminController extends Controller
{
    /**
     * Menampilkan daftar pengumuman (Data untuk index.vue)
     * Ini akan memperbaiki Error 500 saat memuat tabel
     */
    public function index()
    {
        try {
            // Memuat relasi targetDepartemen dan menghitung jumlah pembaca
            $data = Pengumuman::with(['targetDepartemen'])
                ->withCount('reads') 
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'status' => 'success',
                'data' => $data
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Gagal sinkronisasi database: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Menyimpan pengumuman baru
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nomor_surat' => 'required|string|unique:pengumumans,nomor_surat',
            'judul'       => 'required|string',
            'isi'         => 'required|string',
            'file'        => 'nullable|file|mimes:pdf|max:10240',
            'target_departemen_id' => 'nullable|exists:departemens,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
        }

        try {
            $filePath = null;
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('pengumuman', 'public');
            }

            $pengumuman = Pengumuman::create([
                'user_id'     => Auth::id(),
                'nomor_surat' => $request->nomor_surat,
                'judul'       => $request->judul,
                'isi'         => $request->isi,
                'file_path'   => $filePath,
                'target_departemen_id' => $request->target_departemen_id ?: null,
            ]);

            return response()->json(['status' => 'success', 'data' => $pengumuman], 201);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Menampilkan detail pengumuman (Data untuk Edit/View)
     */
    public function show($id)
    {
        try {
            $pengumuman = Pengumuman::with(['targetDepartemen', 'reads.user'])->findOrFail($id);
            return response()->json(['status' => 'success', 'data' => $pengumuman]);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Data tidak ditemukan'], 404);
        }
    }

    /**
     * Prosedur Likuidasi (Hapus Bersih)
     */
    public function destroy($id)
    {
        try {
            // Gunakan find alih-alih findOrFail agar lebih stabil saat data sudah hilang
            $pengumuman = Pengumuman::with('reads')->find($id);
            
            if (!$pengumuman) {
                return response()->json(['status' => 'success', 'message' => 'Data sudah terhapus']);
            }

            // Hapus log anak (FK Constraint)
            $pengumuman->reads()->delete(); 
            
            // Hapus file fisik
            if ($pengumuman->file_path) {
                Storage::disk('public')->delete($pengumuman->file_path);
            }

            $pengumuman->delete();
            return response()->json(['status' => 'success', 'message' => 'Data dilikuidasi']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
    public function update(Request $request, $id)
{
    // 1. Cari data atau lempar 404
    $pengumuman = Pengumuman::findOrFail($id);

    // 2. Validasi (Penting: nomor_surat ditambahkan ignore ID agar tidak dianggap duplikat)
    $validator = Validator::make($request->all(), [
        'nomor_surat' => 'required|string|unique:pengumumans,nomor_surat,' . $id,
        'judul'       => 'required|string',
        'isi'         => 'required|string',
        'file'        => 'nullable|file|mimes:pdf|max:10240',
        'target_departemen_id' => 'nullable|exists:departemens,id'
    ]);

    if ($validator->fails()) {
        return response()->json(['status' => 'error', 'errors' => $validator->errors()], 422);
    }

    try {
        $dataUpdate = [
            'nomor_surat' => $request->nomor_surat,
            'judul'       => $request->judul,
            'isi'         => $request->isi,
            'target_departemen_id' => ($request->target_departemen_id === 'null' || !$request->target_departemen_id) ? null : $request->target_departemen_id,
        ];

        // 3. Logika File
        if ($request->hasFile('file')) {
            // Hapus file lama jika ada di storage fisik
            if ($pengumuman->file_path && \Storage::disk('public')->exists($pengumuman->file_path)) {
                \Storage::disk('public')->delete($pengumuman->file_path);
            }
            // Simpan file baru
            $dataUpdate['file_path'] = $request->file('file')->store('pengumuman', 'public');
        }

        $pengumuman->update($dataUpdate);

        return response()->json([
            'status' => 'success', 
            'message' => 'Otoritas data berhasil diperbarui.',
            'data' => $pengumuman
        ]);

    } catch (\Exception $e) {
        \Log::error("Gagal Update Pengumuman ID $id: " . $e->getMessage());
        return response()->json([
            'status' => 'error', 
            'message' => 'Kegagalan Server: ' . $e->getMessage()
        ], 500);
    }
}
}