<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;
use App\Services\AktivitasLogger;

class AuthController extends Controller
{
    /**
     * Handle user login.
     */
    public function login(Request $request)
    {
        $request->validate([
            'nip' => 'required|string',
            'password' => 'required|string',
        ]);

        // Cari user berdasarkan NIP
        $user = User::where('nip', $request->nip)->first();

        // 1. Cek jika user ada DAN password benar
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nip' => ['NIP atau password salah.'],
            ]);
        }

        /**
         * 2. REVISI LOGIKA STATUS
         * Mengganti is_active menjadi pengecekan status_kerja.
         * Di sini kita asumsikan jika status_kerja ada isinya (Permanent, Kontrak, dll), maka dia aktif.
         * Jika Anda ingin memblokir status tertentu (misal: 'Non-Aktif'), tambahkan di sini.
         */
        if (!$user->status_kerja) {
            throw ValidationException::withMessages([
                'nip' => ['Akun Anda belum memiliki status kerja aktif.'],
            ]);
        }
        
        // Hapus token lama jika ada
        $user->tokens()->delete();

        // Buat token baru
        $token = $user->createToken('auth-token-'.$user->nip)->plainTextToken;

        // Load relasi departemen
        $user->load('departemen');

        // Log aktivitas login
        AktivitasLogger::log('login', 'auth', 'Login ke sistem', 'Login sebagai ' . $user->role . ' (' . $user->name . ')', $user);

        // Return data ke frontend
        return response()->json([
            'message' => 'Login berhasil',
            'user' => $user,
            'token' => $token,
            'role' => $user->role, // 'admin' atau 'karyawan'
        ]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        $user = $request->user();
        $user->load('departemen');

        return response()->json($user);
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Log aktivitas logout sebelum token dihapus
        AktivitasLogger::log('logout', 'auth', 'Logout dari sistem', 'Logout oleh ' . $request->user()->name);

        // Hapus token yang sedang digunakan
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}