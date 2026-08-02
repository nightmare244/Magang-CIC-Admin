<?php

namespace App\Http\Controllers\Api\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

// Import Namespace untuk Excel (PENTING: Pastikan Library Maatwebsite Excel Terinstal)
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\KaryawanExport;
use App\Exports\KaryawanTemplateExport;
use App\Imports\KaryawanImport;

class UserController extends Controller
{
    /**
     * Display a listing of the employees.
     */
    public function index()
    {
        $users = User::with('departemen')->latest()->get();
        return response()->json([
            'status' => 'success',
            'data'   => $users
        ]);
    }

    /**
     * Store a newly created employee.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'nip'           => 'required|unique:users,nip',
            'departemen_id' => 'required|exists:departemens,id',
            'role'          => 'required|in:admin,karyawan',
            'status_kerja'  => 'required|in:Aktif,Permanent,Kontrak,Harian,Non-Aktif',
            'kategori'      => 'nullable|in:karyawan,thl',
            'can_absen_thl' => 'nullable|boolean',
            'password'      => 'required|min:6',
            'tanggal_lahir' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'name'          => $request->nama,
            'email'         => $request->email,
            'nip'           => $request->nip,
            'departemen_id' => $request->departemen_id,
            'role'          => $request->role,
            'status_kerja'  => $request->status_kerja,
            'kategori'      => $request->input('kategori', 'karyawan'),
            'can_absen_thl' => $request->boolean('can_absen_thl', false),
            'password'      => Hash::make($request->password),
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin ?? 'L',
        ]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Karyawan berhasil diregistrasi',
            'data'    => $user
        ], 201);
    }

    /**
     * Show detail employee.
     */
    public function show($id)
    {
        $user = User::with('departemen')->findOrFail($id);
        return response()->json([
            'status' => 'success',
            'data'   => $user
        ]);
    }

    /**
     * Update employee data.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nama'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $id,
            'nip'           => 'required|unique:users,nip,' . $id,
            'departemen_id' => 'required|exists:departemens,id',
            'status_kerja'  => 'required|in:Aktif,Permanent,Kontrak,Harian,Non-Aktif',
            'foto'          => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $data = [
            'name'          => $request->nama,
            'email'         => $request->email,
            'nip'           => $request->nip,
            'departemen_id' => $request->departemen_id,
            'role'          => $request->role,
            'status_kerja'  => $request->status_kerja,
            'kategori'      => $request->input('kategori', $user->kategori),
            'can_absen_thl' => $request->boolean('can_absen_thl', $user->can_absen_thl),
            'nomor_hp'      => $request->nomor_hp,
            'tempat_lahir'  => $request->tempat_lahir,
            'tanggal_lahir' => $request->tanggal_lahir,
            'jenis_kelamin' => $request->jenis_kelamin,
            'alamat'        => $request->alamat,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('foto')) {
            if ($user->foto_profil) {
                Storage::disk('public')->delete($user->foto_profil);
            }
            $path = $request->file('foto')->store('profil', 'public');
            $data['foto_profil'] = $path;
        }

        $user->update($data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Data personel berhasil diperbarui',
            'data'    => $user
        ]);
    }

    /**
     * Remove employee.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        if ($user->foto_profil) {
            Storage::disk('public')->delete($user->foto_profil);
        }
        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Data karyawan berhasil dihapus'
        ]);
    }

    /**
     * Download Template Excel untuk Import
     */
    public function downloadTemplate()
    {
        try {
            return Excel::download(new KaryawanTemplateExport, 'template_karyawan.xlsx');
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal membuat template: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export Data Karyawan ke Excel
     */
public function export(Request $request)
{
    try {
        // Kirim $request agar filter terbaca di class KaryawanExport
        return Excel::download(new KaryawanExport($request), 'LAPORAN_KARYAWAN_FILTERED.xlsx');
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
    }
}

    /**
     * Import Data Karyawan dari Excel
     */
    public function import(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:xlsx,xls,csv|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            // Gunakan class KaryawanImport yang sudah ada di folder Imports
            Excel::import(new KaryawanImport, $request->file('file'));

            return response()->json([
                'status' => 'success',
                'message' => 'Data personel berhasil diimpor ke database'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Terjadi kesalahan saat impor: ' . $e->getMessage()
            ], 500);
        }
    }
}