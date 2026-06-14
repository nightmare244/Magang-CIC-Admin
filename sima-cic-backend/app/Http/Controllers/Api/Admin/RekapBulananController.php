<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\{User, Absensi, Izin, Inventaris, PeminjamanInventaris, Pemasukan, Pengeluaran};
use Carbon\Carbon;
use Illuminate\Http\Request;

class RekapBulananController extends Controller
{
    /**
     * Get monthly recap summary.
     * 
     * Query params:
     *   - bulan: format 'YYYY-MM' (default: current month)
     */
    public function index(Request $request)
    {
        // Determine the requested month
        $bulanInput = $request->input('bulan', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::createFromFormat('Y-m', $bulanInput)->startOfMonth();
        $prevMonth = $currentMonth->copy()->subMonth();

        $currentMonthKey = $currentMonth->format('Y-m');
        $prevMonthKey = $prevMonth->format('Y-m');

        // ============================================================
        // 1. ABSENSI
        // ============================================================
        $totalKaryawan = User::where('role', 'karyawan')->count();

        // Current month attendance
        $absensiCurrent = $this->getAbsensiStats($currentMonthKey);
        $absensiPrev    = $this->getAbsensiStats($prevMonthKey);

        // ============================================================
        // 2. IZIN / CUTI / SAKIT
        // ============================================================
        $izinCurrent = $this->getIzinStats($currentMonthKey);
        $izinPrev    = $this->getIzinStats($prevMonthKey);

        // ============================================================
        // 3. KEUANGAN
        // ============================================================
        $keuanganCurrent = $this->getKeuanganStats($currentMonthKey);
        $keuanganPrev    = $this->getKeuanganStats($prevMonthKey);

        // ============================================================
        // 4. INVENTARIS & PEMINJAMAN
        // ============================================================
        $peminjamanCurrent = $this->getPeminjamanStats($currentMonthKey);
        $peminjamanPrev    = $this->getPeminjamanStats($prevMonthKey);

        // ============================================================
        // 5. CHART: Daily attendance for the selected month
        // ============================================================
        $absensiChart = $this->getAbsensiChart($currentMonth);

        // ============================================================
        // 6. CHART: Income vs Expense per week of the month
        // ============================================================
        $keuanganChart = $this->getKeuanganChart($currentMonth);

        return response()->json([
            'success' => true,
            'data' => [
                'bulan'         => $currentMonth->translatedFormat('F Y'),
                'bulan_key'     => $currentMonthKey,
                'total_karyawan' => $totalKaryawan,

                'absensi' => [
                    'current' => $absensiCurrent,
                    'prev'    => $absensiPrev,
                    'change'  => $this->calcChange($absensiCurrent['hadir'], $absensiPrev['hadir']),
                ],

                'izin' => [
                    'current' => $izinCurrent,
                    'prev'    => $izinPrev,
                    'change'  => $this->calcChange($izinCurrent['total'], $izinPrev['total']),
                ],

                'keuangan' => [
                    'current' => $keuanganCurrent,
                    'prev'    => $keuanganPrev,
                    'change_pemasukan'   => $this->calcChange($keuanganCurrent['pemasukan'], $keuanganPrev['pemasukan']),
                    'change_pengeluaran' => $this->calcChange($keuanganCurrent['pengeluaran'], $keuanganPrev['pengeluaran']),
                    'change_keuntungan'  => $this->calcChange($keuanganCurrent['keuntungan'], $keuanganPrev['keuntungan']),
                ],

                'peminjaman' => [
                    'current' => $peminjamanCurrent,
                    'prev'    => $peminjamanPrev,
                    'change'  => $this->calcChange($peminjamanCurrent['total'], $peminjamanPrev['total']),
                ],

                'charts' => [
                    'absensi_harian' => $absensiChart,
                    'keuangan'       => $keuanganChart,
                ],
            ],
        ]);
    }

    // =================================================================
    // HELPER METHODS
    // =================================================================

    private function getAbsensiStats(string $monthKey): array
    {
        $hadir = Absensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$monthKey])
            ->whereIn('status_hari', ['hadir', 'HADIR', 'H'])
            ->count();

        $tepatWaktu = Absensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$monthKey])
            ->where('status_masuk', 'tepat_waktu')
            ->count();

        $terlambat = Absensi::whereRaw("DATE_FORMAT(tanggal, '%Y-%m') = ?", [$monthKey])
            ->where('status_masuk', 'terlambat')
            ->count();

        return [
            'hadir'       => $hadir,
            'tepat_waktu' => $tepatWaktu,
            'terlambat'   => $terlambat,
        ];
    }

    private function getIzinStats(string $monthKey): array
    {
        $baseQuery = fn($tipe) => Izin::where('tipe_izin', $tipe)
            ->whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$monthKey]);

        $sakit     = $baseQuery('sakit')->count();
        $izin      = $baseQuery('izin')->count();
        $cuti      = $baseQuery('cuti')->count();

        $pending   = Izin::whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'pending')->count();
        $disetujui = Izin::whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'disetujui')->count();
        $ditolak   = Izin::whereRaw("DATE_FORMAT(tanggal_mulai, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'ditolak')->count();

        return [
            'sakit'     => $sakit,
            'izin'      => $izin,
            'cuti'      => $cuti,
            'total'     => $sakit + $izin + $cuti,
            'pending'   => $pending,
            'disetujui' => $disetujui,
            'ditolak'   => $ditolak,
        ];
    }

    private function getKeuanganStats(string $monthKey): array
    {
        $pemasukan = (float) Pemasukan::whereRaw("DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = ?", [$monthKey])
            ->sum('nominal');

        $pengeluaran = (float) Pengeluaran::whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$monthKey])
            ->sum('nominal');

        // Top categories of expenses
        $topKategori = Pengeluaran::selectRaw("kategori, SUM(nominal) as total")
            ->whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$monthKey])
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get()
            ->map(fn($item) => [
                'kategori' => ucfirst($item->kategori),
                'total'    => (float) $item->total,
            ]);

        return [
            'pemasukan'    => $pemasukan,
            'pengeluaran'  => $pengeluaran,
            'keuntungan'   => $pemasukan - $pengeluaran,
            'top_kategori' => $topKategori,
        ];
    }

    private function getPeminjamanStats(string $monthKey): array
    {
        $total = PeminjamanInventaris::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])->count();

        $pending   = PeminjamanInventaris::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'pending')->count();
        $disetujui = PeminjamanInventaris::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'disetujui')->count();
        $selesai   = PeminjamanInventaris::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'selesai')->count();
        $ditolak   = PeminjamanInventaris::whereRaw("DATE_FORMAT(created_at, '%Y-%m') = ?", [$monthKey])
            ->where('status', 'ditolak')->count();

        return [
            'total'     => $total,
            'pending'   => $pending,
            'disetujui' => $disetujui,
            'selesai'   => $selesai,
            'ditolak'   => $ditolak,
        ];
    }

    private function getAbsensiChart(Carbon $month): array
    {
        $daysInMonth = $month->daysInMonth;
        $labels = [];
        $hadirData = [];
        $terlambatData = [];

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $date = $month->copy()->day($d);

            // Only include dates up to today
            if ($date->isFuture()) break;

            $labels[] = $date->format('d');

            $hadirData[] = Absensi::whereDate('tanggal', $date)
                ->whereIn('status_hari', ['hadir', 'HADIR', 'H'])
                ->count();

            $terlambatData[] = Absensi::whereDate('tanggal', $date)
                ->where('status_masuk', 'terlambat')
                ->count();
        }

        return [
            'labels'    => $labels,
            'hadir'     => $hadirData,
            'terlambat' => $terlambatData,
        ];
    }

    private function getKeuanganChart(Carbon $month): array
    {
        $daysInMonth = $month->daysInMonth;
        $weeks = [];
        $pemasukanData = [];
        $pengeluaranData = [];

        // Split month into ~4 weeks
        $weekStart = $month->copy()->startOfMonth();
        $weekNum = 1;

        while ($weekStart->month === $month->month) {
            $weekEnd = $weekStart->copy()->addDays(6);
            if ($weekEnd->month !== $month->month) {
                $weekEnd = $month->copy()->endOfMonth();
            }

            $weeks[] = "Minggu $weekNum";

            $pemasukanData[] = (float) Pemasukan::whereBetween('tanggal_pemasukan', [
                $weekStart->toDateString(), $weekEnd->toDateString()
            ])->sum('nominal');

            $pengeluaranData[] = (float) Pengeluaran::whereBetween('tanggal_pengeluaran', [
                $weekStart->toDateString(), $weekEnd->toDateString()
            ])->sum('nominal');

            $weekStart = $weekEnd->copy()->addDay();
            $weekNum++;
        }

        return [
            'labels'      => $weeks,
            'pemasukan'   => $pemasukanData,
            'pengeluaran' => $pengeluaranData,
        ];
    }

    /**
     * Calculate percentage change between current and previous values.
     */
    private function calcChange($current, $prev): array
    {
        if ($prev == 0) {
            $pct = $current > 0 ? 100 : 0;
        } else {
            $pct = round((($current - $prev) / abs($prev)) * 100, 1);
        }

        return [
            'value'     => $current - $prev,
            'percent'   => $pct,
            'direction' => $pct > 0 ? 'up' : ($pct < 0 ? 'down' : 'flat'),
        ];
    }
}
