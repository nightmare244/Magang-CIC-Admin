<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Departemen;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // Pastikan Rule di-import

class DepartemenController extends Controller
{
    /**
     * LIST SEMUA DEPARTEMEN
     */
    public function index()
    {
        $departemens = Departemen::orderBy('nama_departemen', 'asc')->get();

        return response()->json([
            'success' => true,
            // Mengirim data tanpa pagination karena biasanya list departemen tidak terlalu panjang
            'data' => $departemens 
        ]);
    }

    /**
     * BUAT DEPARTEMEN BARU
     */
    public function store(Request $request)
    {
        $request->validate([
            // Menggunakan nama tabel 'departemens' (plural) untuk konsistensi
            'nama_departemen' => 'required|string|max:255|unique:departemens,nama_departemen', 
            'deskripsi'       => 'nullable|string',
        ]);

        $departemen = Departemen::create([
            'nama_departemen' => $request->nama_departemen,
            'deskripsi'       => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil dibuat',
            'data'    => $departemen
        ], 201); // Menggunakan status 201 Created
    }

    /**
     * DETAIL DEPARTEMEN
     */
    public function show($id)
    {
        // Eager load relasi 'users' untuk mengetahui karyawan di departemen ini
        $departemen = Departemen::with('users')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $departemen
        ]);
    }

    /**
     * UPDATE DEPARTEMEN
     */
    public function update(Request $request, $id)
    {
        $departemen = Departemen::findOrFail($id);

        $request->validate([
            // Menggunakan Rule::unique untuk mengabaikan ID saat ini
            'nama_departemen' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departemens', 'nama_departemen')->ignore($departemen->id),
            ],
            'deskripsi'       => 'nullable|string',
        ]);

        $departemen->update([
            'nama_departemen' => $request->nama_departemen,
            'deskripsi'       => $request->deskripsi,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil diperbarui',
            'data'    => $departemen
        ]);
    }

    /**
     * DELETE DEPARTEMEN
     */
    public function destroy($id)
    {
        $departemen = Departemen::findOrFail($id);

        // Cegah delete jika masih ada karyawan di departemen ini
        if ($departemen->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak dapat menghapus. Masih ada karyawan yang terdaftar di departemen ini.'
            ], 400); // Menggunakan status 400 Bad Request untuk kesalahan logika bisnis
        }

        $departemen->delete();

        return response()->json([
            'success' => true,
            'message' => 'Departemen berhasil dihapus'
        ]);
    }
}