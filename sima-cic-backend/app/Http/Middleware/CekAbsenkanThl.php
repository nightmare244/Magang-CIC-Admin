<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

/**
 * CekAbsenkanThl
 *
 * Memastikan hanya karyawan yang punya can_absen_thl=true yang boleh
 * menginput absensi untuk user lain (target), DAN target harus
 * berkategori 'thl'.
 *
 * Cara pakai di route:
 *   ->middleware('cek.absenkan.thl')
 *
 * Request body yang diperlukan:
 *   - user_id: ID dari THL yang akan diabsen
 */
class CekAbsenkanThl
{
    public function handle(Request $request, Closure $next)
    {
        $mandor = $request->user();

        // Pastikan user sudah login
        if (!$mandor) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Cek permission: apakah mandor punya izin absenkan THL
        if (!$mandor->can_absen_thl) {
            return response()->json([
                'success' => false,
                'message' => 'Anda tidak memiliki izin untuk menginput absensi karyawan THL.',
            ], 403);
        }

        // Cek target user_id ada di request
        $targetUserId = $request->input('user_id');
        if (!$targetUserId) {
            return response()->json([
                'success' => false,
                'message' => 'Parameter user_id (target THL) wajib disertakan.',
            ], 422);
        }

        // Cek target user ada di database dan berkategori 'thl'
        $targetUser = User::find($targetUserId);
        if (!$targetUser) {
            return response()->json([
                'success' => false,
                'message' => 'User target tidak ditemukan.',
            ], 404);
        }

        if ($targetUser->kategori !== 'thl') {
            return response()->json([
                'success' => false,
                'message' => 'User target bukan THL. Hanya dapat menginput absensi untuk akun THL.',
            ], 403);
        }

        // Inject target user ke request agar controller bisa langsung pakai
        $request->merge(['target_user' => $targetUser]);

        return $next($request);
    }
}
