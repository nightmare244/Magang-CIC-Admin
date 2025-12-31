<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

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

        // Cek jika user ada DAN password benar DAN user aktif
        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'nip' => ['NIP atau password salah.'],
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'nip' => ['Akun Anda telah dinonaktifkan.'],
            ]);
        }
        
        // Hapus token lama jika ada (opsional, tapi bagus untuk kebersihan)
        $user->tokens()->delete();

        // Buat token baru
$token = $user->createToken('auth-token-'.$user->nip)->plainTextToken;

// Load relasi jika perlu
$user->load('departemen');

// Return role ke frontend juga
return response()->json([
    'message' => 'Login berhasil',
    'user' => $user,
    'token' => $token,
    'role' => $user->role, // misal 'admin' atau 'karyawan'
]);
    }

    /**
     * Get the authenticated user.
     */
    public function user(Request $request)
    {
        // Mengambil data user yang sedang login
        // dan me-load relasi departemen
        $user = $request->user();
        $user->load('departemen');

        return response()->json($request->user());
    }

    /**
     * Handle user logout.
     */
    public function logout(Request $request)
    {
        // Hapus token yang sedang digunakan untuk request ini
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logout berhasil']);
    }
}