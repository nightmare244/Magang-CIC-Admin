<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CekAbsenDiri
 *
 * Memastikan hanya karyawan dengan kategori='karyawan' yang boleh
 * melakukan self-absen (metode=self, input_by = diri sendiri).
 *
 * THL (kategori='thl') diblokir 403 saat mencoba self-absen.
 *
 * Cara pakai di route:
 *   ->middleware('cek.absen.diri')
 */
class CekAbsenDiri
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        // Pastikan user sudah login
        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // THL tidak boleh self-absen
        if ($user->kategori === 'thl') {
            return response()->json([
                'success' => false,
                'message' => 'Akun THL tidak dapat melakukan absensi mandiri. Hubungi mandor/pengawas Anda.',
            ], 403);
        }

        // Role harus karyawan (bukan admin) untuk endpoint self-absen
        if ($user->role !== 'karyawan') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk karyawan.',
            ], 403);
        }

        return $next($request);
    }
}
