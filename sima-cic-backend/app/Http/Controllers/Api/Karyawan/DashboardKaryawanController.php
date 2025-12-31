<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{Absensi, Izin, User, PeminjamanInventaris};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

class DashboardKaryawanController extends Controller
{
    public function summary()
    {
        try {
            $user = User::with('departemen')->find(Auth::id());
            if (!$user) return response()->json(['success' => false], 401);

            $today = Carbon::today()->toDateString();
            $startOfMonth = Carbon::now()->startOfMonth()->toDateString();
            $endOfMonth = Carbon::now()->endOfMonth()->toDateString();

            // --- 1. ABSENSI HARI INI ---
            $absen = Absensi::where('user_id', $user->id)
                ->where('tanggal', $today)
                ->first();

            $absensiToday = [
                'jam_masuk'    => $absen ? $absen->jam_masuk : null,
                'jam_pulang'   => $absen ? $absen->jam_pulang : null,
                'status_masuk' => $absen ? $absen->status_masuk : null,
            ];

           // --- 2. KPI: KEHADIRAN & DISIPLIN (PERBAIKAN TOTAL) ---

// Pastikan format tanggal start dan end bulan ini benar
$startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
$endOfMonth = Carbon::now()->endOfMonth()->format('Y-m-d');

// Ambil data absensi bulan ini
$absensiBulanIni = Absensi::where('user_id', $user->id)
    ->whereBetween('tanggal', [$startOfMonth, $endOfMonth])
    ->get();

// DEBUG: Pastikan data ditemukan (Cek di Log Laravel jika masih kosong)
if ($absensiBulanIni->isEmpty()) {
    \Log::info("Data absen tidak ditemukan untuk User: {$user->id} antara {$startOfMonth} - {$endOfMonth}");
}

// Menghitung kehadiran menggunakan filter agar lebih akurat terhadap spasi
$totalHadir = $absensiBulanIni->filter(function($item) {
    return in_array(strtoupper(trim($item->status_hari)), ['HADIR', 'H']);
})->count();

$totalAlpa = $absensiBulanIni->filter(function($item) {
    return strtoupper(trim($item->status_hari)) === 'ALPA';
})->count();

// Logika Skor Disiplin
$skorDisiplin = 0;
if ($totalHadir > 0) {
    $totalPoin = 0;
    foreach ($absensiBulanIni as $item) {
        $statusHarian = strtoupper(trim($item->status_hari));
        // Hilangkan spasi dan underscore untuk pengecekan status masuk
        $statusMasuk = strtolower(str_replace([' ', '_'], '', $item->status_masuk));

        if ($statusHarian === 'HADIR' || $statusHarian === 'H') {
            if ($statusMasuk === 'tepatwaktu') {
                $totalPoin += 100;
            } elseif ($statusMasuk === 'terlambat') {
                $totalPoin += 60; // Memberikan poin 60 meskipun terlambat
            }
        }
    }
    // Skor dibagi target bulanan tetap (26 hari)
    $skorDisiplin = round($totalPoin / 26);
}

$skorDisiplin = max(0, min(100, (int)$skorDisiplin));

            // --- 3. LOGISTIK ---
            $barangDipinjam = PeminjamanInventaris::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereNull('tanggal_pengembalian') 
                ->sum('quantity');

            // --- 4. RIWAYAT CHART ---
            $riwayat = Absensi::where('user_id', $user->id)
                ->orderBy('tanggal', 'desc')
                ->take(7)
                ->get()
                ->reverse()
                ->values()
                ->map(fn($item) => [
                    'tanggal' => Carbon::parse($item->tanggal)->format('d M'),
                    'total'   => $item->jam_masuk ? 1 : 0,
                ]);

            return response()->json([
                'success' => true,
                'user'    => $user,
                'summary' => [
                    'absensi_today' => $absensiToday,
                    'chart_7_hari'  => $riwayat,
                    'kpi' => [
                        'skor_disiplin'   => $skorDisiplin,
                        'total_hadir'     => $totalHadir,
                        'total_alpa'      => $totalAlpa,
                        'target_hari'     => 26,
                        'barang_dipinjam' => (int) $barangDipinjam,
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }
}