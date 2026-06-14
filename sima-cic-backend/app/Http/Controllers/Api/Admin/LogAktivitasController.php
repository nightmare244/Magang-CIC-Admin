<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;

class LogAktivitasController extends Controller
{
    /**
     * Menampilkan daftar log aktivitas dengan filter dan pagination.
     */
    public function index(Request $request)
    {
        $query = LogAktivitas::query()->orderBy('created_at', 'desc');

        // Filter berdasarkan modul
        if ($request->filled('modul')) {
            $query->where('modul', $request->modul);
        }

        // Filter berdasarkan aksi
        if ($request->filled('aksi')) {
            $query->where('aksi', $request->aksi);
        }

        // Filter berdasarkan user
        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        // Filter berdasarkan role
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        // Filter berdasarkan rentang tanggal
        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }
        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        // Search (judul / detail / user_name)
        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('judul', 'like', "%{$s}%")
                  ->orWhere('detail', 'like', "%{$s}%")
                  ->orWhere('user_name', 'like', "%{$s}%");
            });
        }

        $perPage = $request->input('per_page', 20);
        $logs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data'    => $logs->items(),
            'meta'    => [
                'current_page' => $logs->currentPage(),
                'last_page'    => $logs->lastPage(),
                'per_page'     => $logs->perPage(),
                'total'        => $logs->total(),
            ]
        ]);
    }

    /**
     * Statistik ringkasan log aktivitas (untuk dashboard halaman log).
     */
    public function stats()
    {
        $today = now()->startOfDay();
        $weekAgo = now()->subDays(7)->startOfDay();

        return response()->json([
            'success' => true,
            'data'    => [
                'total_hari_ini'   => LogAktivitas::where('created_at', '>=', $today)->count(),
                'total_minggu_ini' => LogAktivitas::where('created_at', '>=', $weekAgo)->count(),
                'total_keseluruhan' => LogAktivitas::count(),
                'modul_terbanyak'  => LogAktivitas::where('created_at', '>=', $weekAgo)
                    ->selectRaw('modul, count(*) as total')
                    ->groupBy('modul')
                    ->orderByDesc('total')
                    ->first(),
                'user_teraktif'    => LogAktivitas::where('created_at', '>=', $weekAgo)
                    ->selectRaw('user_name, user_id, count(*) as total')
                    ->groupBy('user_name', 'user_id')
                    ->orderByDesc('total')
                    ->first(),
            ]
        ]);
    }
}
