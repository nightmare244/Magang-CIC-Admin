<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\{Absensi, Izin, User, PeminjamanInventaris, Setting};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardKaryawanController extends Controller
{
    public function summary()
    {
        try {
            $user = User::with('departemen')->find(Auth::id());
            if (!$user) return response()->json(['success' => false], 401);

            $today = Carbon::today();
            
            // --- 1. CONFIG JAM ---
            $configJamMasuk = Setting::getByKey('jam_masuk_kantor', '08:00:00');

            // --- 2. ABSENSI HARI INI ---
            $absenToday = Absensi::where('user_id', $user->id)
                ->whereDate('tanggal', $today)
                ->first();

            $absensiSummaryToday = [
                'jam_masuk'    => $absenToday ? $absenToday->jam_masuk : null,
                'jam_pulang'   => $absenToday ? $absenToday->jam_pulang : null,
                'status_masuk' => $absenToday ? strtoupper(str_replace('_', ' ', $absenToday->status_masuk)) : '-', 
                'status_hari'  => $absenToday ? strtoupper($absenToday->status_hari) : 'BELUM ABSEN',
            ];

            // --- 3. LOGIKA IZIN AKTIF ---
            $izinAktif = Izin::where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereDate('tanggal_mulai', '<=', $today)
                ->whereDate('tanggal_selesai', '>=', $today)
                ->first();

            // --- 4. LOGIKA PEMINJAMAN AKTIF (MEMANGGIL RELASI 'barang') ---
            $peminjamanAktif = PeminjamanInventaris::with('barang') 
                ->where('user_id', $user->id)
                ->where('status', 'disetujui')
                ->whereNull('tanggal_pengembalian')
                ->latest()
                ->get(); // Mengirim Array ke Vue

            // --- 5. DATA BULAN INI UNTUK KPI ---
            $absensiBulanIni = Absensi::where('user_id', $user->id)
                ->whereMonth('tanggal', Carbon::now()->month)
                ->whereYear('tanggal', Carbon::now()->year)
                ->get();

            $totalHadir = $absensiBulanIni->where('status_hari', 'HADIR')->count();
            $totalAlpa  = $absensiBulanIni->where('status_hari', 'ALPA')->count();

            // --- 6. SKOR DISIPLIN ---
            $skorDisiplin = 0;
            $dataAbsenMasuk = $absensiBulanIni->whereNotNull('jam_masuk');
            
            if ($dataAbsenMasuk->count() > 0) {
                $totalPoin = 0;
                foreach ($dataAbsenMasuk as $item) {
                    if (str_contains(strtolower($item->status_masuk ?? ''), 'tepat')) {
                        $totalPoin += 100;
                    } elseif ($item->status_hari === 'HADIR') {
                        $totalPoin += 60;
                    }
                }
                $skorDisiplin = ($totalHadir + $totalAlpa) > 0 ? round($totalPoin / ($totalHadir + $totalAlpa)) : 0;
            }

            // --- 7. RIWAYAT CHART ---
            $riwayatChart = [];
            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::today()->subDays($i);
                $dayData = Absensi::where('user_id', $user->id)->whereDate('tanggal', $date->toDateString())->first();
                $riwayatChart[] = [
                    'tanggal' => $date->format('d M'),
                    'total'   => ($dayData && $dayData->status_hari === 'HADIR') ? 1 : 0,
                ];
            }

            return response()->json([
                'success' => true,
                'user'    => $user,
                'summary' => [
                    'absensi_today'    => $absensiSummaryToday,
                    'izin_aktif'       => $izinAktif,
                    'peminjaman_aktif' => $peminjamanAktif,
                    'chart_7_hari'     => $riwayatChart,
                    'kpi' => [
                        'skor_disiplin'   => (int) $skorDisiplin,
                        'total_hadir'     => (int) $totalHadir,
                        'total_alpa'      => (int) $totalAlpa,
                        'target_hari'     => 26,
                        'barang_dipinjam' => $peminjamanAktif->count(), 
                    ]
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Dashboard Error: " . $e->getMessage());
            return response()->json([
                'success' => false, 
                'error'   => $e->getMessage()
            ], 500);
        }
    }
}