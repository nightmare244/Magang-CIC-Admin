<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class ProfilController extends Controller
{
    protected function formatUserResponse($user)
    {
        $tanggalLahirFormatted = $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : null;

        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'nip' => $user->nip,
            'tempat_lahir' => $user->tempat_lahir,
            'tanggal_lahir' => $tanggalLahirFormatted,
            'jenis_kelamin' => $user->jenis_kelamin,
            'nomor_hp' => $user->nomor_hp,
            'alamat' => $user->alamat,
            'departemen_id' => $user->departemen_id,
            'is_active' => (bool)$user->is_active,
            'role' => $user->role,
            'foto_profil' => $user->foto_profil,
            'departemen' => $user->departemen, 
        ];
    }

    /**
     * Tampilkan data profil karyawan yang sedang login.
     */
    public function show(Request $request)
    {
        $user = $request->user();
        $user->load('departemen');

        return response()->json([
            'success' => true,
            'data' => $this->formatUserResponse($user)
        ]);
    }

    /**
     * Update data profil karyawan yang sedang login (Self-Service).
     * Karyawan melengkapi data opsional.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $request->validate([
            // Field yang boleh diubah karyawan:
            'jenis_kelamin'   => 'nullable|in:L,P',
            'nomor_hp'        => 'nullable|string|max:15',
            'alamat'          => 'nullable|string',
            'tempat_lahir'    => 'required|string|max:255', // Wajib diisi saat melengkapi data
            'tanggal_lahir'   => 'required|date', // Wajib diisi saat melengkapi data
        ]);

        $dataToUpdate = $request->only([
            'jenis_kelamin', 
            'nomor_hp', 
            'alamat',
            'tempat_lahir',
            'tanggal_lahir',
        ]);
        
        $user->update($dataToUpdate);
        $user->load('departemen');

        return response()->json([
            'success' => true,
            'message' => 'Profil berhasil diperbarui.',
            'data' => $this->formatUserResponse($user)
        ]);
    }

    /**
     * Upload Foto Profil (Self-Service).
     */
    public function uploadPhoto(Request $request)
    {
        $user = $request->user();
        
        $request->validate([
            'foto' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($user->foto_profil && Storage::disk('public')->exists($user->foto_profil)) {
            Storage::disk('public')->delete($user->foto_profil);
        }

        $file = $request->file('foto');
        $extension = $file->getClientOriginalExtension();
        $slug = Str::slug($user->nip . ' ' . $user->name); 
        $fileName = $slug . '-' . time() . '.' . $extension;

        $path = $file->storeAs('foto_karyawan', $fileName, 'public');
        
        $user->foto_profil = $path;
        $user->save();
        $user->load('departemen');

        return response()->json([
            'success' => true,
            'message' => 'Foto profil berhasil diunggah.',
            'data' => $this->formatUserResponse($user)
        ]);
    }

    /**
     * Ganti Password Karyawan (Self-Service).
     */
    public function changePassword(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => 'required|min:6|confirmed',
        ]);

        $user->password = Hash::make($request->password);
        $user->save();

        return response()->json([
            'success' => true,
            'message' => 'Password berhasil diubah.'
        ]);
    }
}