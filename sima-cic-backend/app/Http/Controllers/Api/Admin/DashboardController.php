<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Absensi, Izin, Inventaris, PeminjamanInventaris};
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function summary()
    {
        try {
            $today = Carbon::today();
            
            // --- KONFIGURASI STATUS ---
            $statusPending   = 'pending';
            $statusDisetujui = 'disetujui';

            // --- 1. PENGHITUNGAN KPI ---
            $kpi = [
                // Logika Personil (Total & Rincian Gender)
                'total_karyawan' => [
                    'total'     => User::where('role', 'karyawan')->count(),
                    'laki'      => User::where('role', 'karyawan')->where('jenis_kelamin', 'L')->count(),
                    'perempuan' => User::where('role', 'karyawan')->where('jenis_kelamin', 'P')->count(),
                ],

                // Absensi & Izin
                'hadir_hari_ini'      => Absensi::whereDate('tanggal', $today)->whereNotNull('jam_masuk')->count(),
                'total_izin_pending'  => Izin::where('status', $statusPending)->count(),
                'izin_approved_month' => Izin::where('status', $statusDisetujui)
                                            ->whereMonth('updated_at', Carbon::now()->month)
                                            ->count(),

                // Logistik (Total Unit Berdasarkan Quantity & Jenis Barang)
                'total_inventaris' => [
                    'total_unit'  => (int) Inventaris::sum('quantity'),
                    'total_jenis' => Inventaris::count()
                ],
                
                'total_peminjaman_pending' => PeminjamanInventaris::where('status', $statusPending)->count(),
                'peminjaman_aktif'         => PeminjamanInventaris::where('status', $statusDisetujui)->count(),

                'total_inventaris' => [
        'total_unit'  => (int) Inventaris::sum('quantity'),
        'total_jenis' => Inventaris::count(),
        // Tambahkan ini untuk total aset dalam Rupiah
        'total_nilai' => (float) Inventaris::sum('nilai_barang') 
    ],
            ];

            // --- 2. DATA TABEL (Antrian Pending) ---
            $pendingIzin = Izin::with('user:id,name,nip')
                ->where('status', $statusPending)
                ->latest()
                ->take(5)
                ->get();

            $pendingPeminjaman = PeminjamanInventaris::with(['user:id,name,nip', 'inventaris:id,nama_barang'])
                ->where('status', $statusPending)
                ->latest()
                ->take(5)
                ->get();

            // --- 3. RESPONSE JSON ---
            return response()->json([
                'success'            => true,
                'kpi'                => $kpi,
                'pending_izin'       => $pendingIzin,
                'pending_peminjaman' => $pendingPeminjaman,
                'charts'             => [
                    'absensi_7_hari'  => $this->getAbsensiMingguan(),
                    'absensi_hari_ini' => $this->getAbsensiHariIni(),
                ]
            ], 200);

        } catch (\Exception $e) {
            Log::error("Dashboard Admin Error: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * Data statistik kehadiran 7 hari terakhir
     */
    private function getAbsensiMingguan()
    {
        $labels = [];
        $dataValues = [];

        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $count = Absensi::whereDate('tanggal', $day)
                        ->whereIn('status_hari', ['HADIR', 'H']) 
                        ->count();
            
            $labels[] = $day->format('d M');
            $dataValues[] = $count;
        }

        return [
            'labels'   => $labels,
            'datasets' => $dataValues 
        ];
    }

    /**
     * Rasio Ketepatan Waktu Hari Ini
     */
    private function getAbsensiHariIni()
    {
        $tepat = Absensi::whereDate('tanggal', Carbon::today())
                    ->whereRaw('LOWER(status_masuk) = ?', ['tepat waktu'])
                    ->count();

        $telat = Absensi::whereDate('tanggal', Carbon::today())
                    ->whereRaw('LOWER(status_masuk) = ?', ['terlambat'])
                    ->count();
        
        return [
            'labels' => ['Tepat Waktu', 'Terlambat'],
            'data'   => [$tepat, $telat]
        ];
    }
}