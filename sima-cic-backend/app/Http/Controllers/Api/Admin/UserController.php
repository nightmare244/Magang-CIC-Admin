<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

// Library Tambahan untuk Excel
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KaryawanExport;
use App\Imports\KaryawanImport;
use App\Exports\KaryawanTemplateExport;

class UserController extends Controller
{
    /**
     * Helper untuk generate nama file foto: NIP_Nama_Timestamp.ext
     */
    protected function generateFotoName(Request $request)
    {
        $file = $request->file('foto');
        $extension = $file->getClientOriginalExtension();
        $slug = Str::slug($request->nip . ' ' . $request->nama); 
        $fileName = $slug . '-' . time() . '.' . $extension;
        
        return $fileName;
    }

    /**
     * Tampilkan semua karyawan
     */
    public function index()
    {
        $users = User::with('departemen')
            ->where('role', 'karyawan')
            ->orderBy('created_at', 'desc')
            ->get();
        
        return response()->json([
            'success' => true, 
            'data' => $users
        ]);
    }

    /**
     * Tampilkan detail satu karyawan
     */
    public function show($id)
    {
        $user = User::with('departemen')->findOrFail($id);
        
        return response()->json([
            'success' => true, 
            'data' => $user->toArray()
        ]);
    }

    /**
     * Create Karyawan Baru Manual
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'            => 'required|string|max:255',
            'email'           => 'required|email|unique:users,email',
            'nip'             => 'required|string|unique:users,nip',
            'password'        => 'required|min:6', 
            'departemen_id'   => 'nullable|exists:departemens,id',
            'tempat_lahir'    => 'required|string|max:255',
            'tanggal_lahir'   => 'required|date',
            'role'            => 'required|in:karyawan,admin',
        ]);
        
        $user = User::create([
            'name'            => $request->nama,
            'email'           => $request->email,
            'nip'             => $request->nip,
            'password'        => Hash::make($request->password),
            'tempat_lahir'    => $request->tempat_lahir,
            'tanggal_lahir'   => $request->tanggal_lahir,
            'departemen_id'   => $request->departemen_id,
            'role'            => $request->role, 
            'is_active'       => true,
            'foto_profil'     => null, 
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Karyawan berhasil dibuat.',
            'data'    => $user->load('departemen')->toArray()
        ], 201);
    }

    /**
     * Update Karyawan (Mendukung Foto, Profil & Reset Password)
     */
    public function update(Request $request, $id)
    {
        $karyawan = User::findOrFail($id); 

        $request->validate([
            'nama'            => 'required|string|max:255',
            'email'           => ['required','email', Rule::unique('users')->ignore($karyawan->id)],
            'nip'             => ['required', Rule::unique('users')->ignore($karyawan->id)],
            'jenis_kelamin'   => 'nullable|in:L,P',
            'foto'            => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password'        => 'nullable|min:6',
        ]);

        $dataToUpdate = $request->only([
            'email', 'nip', 'tempat_lahir', 'tanggal_lahir', 'nomor_hp',
            'alamat', 'departemen_id', 'jenis_kelamin', 'is_active', 'role'
        ]);

        $dataToUpdate['name'] = $request->nama; 

        // Logika Reset Password (Hashing Secure)
        if ($request->filled('password')) {
            $dataToUpdate['password'] = Hash::make($request->password);
        }
        
        // Logika upload foto
        if ($request->hasFile('foto')) {
            if ($karyawan->foto_profil) {
                Storage::disk('public')->delete($karyawan->foto_profil);
            }
            $fileName = $this->generateFotoName($request);
            $path = $request->file('foto')->storeAs('foto_karyawan', $fileName, 'public');
            $dataToUpdate['foto_profil'] = $path;
        }

        $karyawan->update($dataToUpdate);
        
        return response()->json([
            'success' => true,
            'message' => 'Data karyawan berhasil diperbarui.',
            'data'    => $karyawan->fresh(['departemen'])->toArray()
        ]);
    }

    /**
     * EKSPOR DATA KARYAWAN (LENGKAP TANPA FOTO)
     */
    public function export()
    {
        try {
            $fileName = 'Data_Karyawan_' . date('Y-m-d_His') . '.xlsx';
            return Excel::download(new KaryawanExport, $fileName);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal ekspor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * IMPOR DATA KARYAWAN (SIMPEL 5 KOLOM)
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:2048'
        ]);

        try {
            Excel::import(new KaryawanImport, $request->file('file'));
            return response()->json([
                'success' => true,
                'message' => 'Batch impor data berhasil dilakukan.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Gagal impor: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Hapus Karyawan
     */
    public function destroy($id)
    {
        $karyawan = User::findOrFail($id); 

        if ($karyawan->foto_profil && Storage::disk('public')->exists($karyawan->foto_profil)) {
            Storage::disk('public')->delete($karyawan->foto_profil);
        }

        $karyawan->delete();

        return response()->json([
            'success' => true, 
            'message' => 'Karyawan berhasil dihapus'
        ]);
    }

public function downloadTemplate()
{
    try {
        // Gunakan \App\Exports\KaryawanTemplateExport jika tidak mau pakai 'use' di atas
        return Excel::download(new KaryawanTemplateExport, 'Template_Impor_Karyawan.xlsx');
    } catch (\Exception $e) {
        // Log error untuk debug di storage/logs/laravel.log
        \Log::error('Template Export Error: ' . $e->getMessage());
        return response()->json([
            'status' => 'error',
            'message' => 'Gagal membuat template Excel: ' . $e->getMessage()
        ], 500);
    }
}
}