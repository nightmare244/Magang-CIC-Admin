<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

/**
 * CekAbsenDiri
 *
 * Memastikan user ber-role 'karyawan' (baik kategori 'karyawan' maupun 'thl')
 * dapat melakukan absensi mandiri.
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

        // Role harus karyawan (bukan admin) untuk endpoint self-absen
        if ($user->role !== 'karyawan') {
            return response()->json([
                'success' => false,
                'message' => 'Endpoint ini hanya untuk akun karyawan/THL.',
            ], 403);
        }

        return $next($request);
    }
}
