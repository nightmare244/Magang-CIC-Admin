<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;

class KeuanganController extends Controller
{
    /**
     * Get financial statistics and chart data.
     */
    public function summary()
    {
        $totalPemasukan = (float) Pemasukan::sum('nominal');
        $totalPengeluaran = (float) Pengeluaran::sum('nominal');
        $totalKeuntungan = $totalPemasukan - $totalPengeluaran;

        // Generate last 6 months
        $months = [];
        $pemasukanValues = [];
        $pengeluaranValues = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthDate = Carbon::today()->subMonths($i);
            $monthKey = $monthDate->format('Y-m');
            $months[] = $monthDate->translatedFormat('M'); // e.g. Jan, Feb, Mar...

            // Sum pemasukan
            $pemasukanSum = (float) Pemasukan::whereRaw("DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = ?", [$monthKey])->sum('nominal');
            $pemasukanValues[] = $pemasukanSum;

            // Sum pengeluaran
            $pengeluaranSum = (float) Pengeluaran::whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$monthKey])->sum('nominal');
            $pengeluaranValues[] = $pengeluaranSum;
        }

        // Kategori pengeluaran for donut chart
        $categoriesList = ['operasional', 'gaji', 'maintenance', 'utility', 'lainnya'];
        $categoriesLabelMap = [
            'operasional' => 'Operasional',
            'gaji'        => 'Gaji',
            'maintenance' => 'Maintenance',
            'utility'     => 'Utility',
            'lainnya'     => 'Lainnya',
        ];

        $kategoriNominals = [];
        $kategoriLabels = [];

        foreach ($categoriesList as $cat) {
            $sum = (float) Pengeluaran::where('kategori', $cat)->sum('nominal');
            $kategoriNominals[] = $sum;
            $kategoriLabels[] = $categoriesLabelMap[$cat];
        }

        return response()->json([
            'success' => true,
            'data' => [
                'total_pemasukan' => $totalPemasukan,
                'total_pengeluaran' => $totalPengeluaran,
                'total_keuntungan' => $totalKeuntungan,
                'charts' => [
                    'months' => $months,
                    'pemasukan' => $pemasukanValues,
                    'pengeluaran' => $pengeluaranValues,
                    'kategori' => [
                        'labels' => $kategoriLabels,
                        'series' => $kategoriNominals,
                    ]
                ]
            ]
        ]);
    }

    /**
     * Laporan Laba Rugi — breakdown per tipe pemasukan vs kategori pengeluaran.
     * Param: periode_type (bulan|tahun), periode (YYYY-MM atau YYYY)
     */
    public function labaRugi(\Illuminate\Http\Request $request)
    {
        $periodeType = $request->input('periode_type', 'bulan');
        $periode     = $request->input('periode', Carbon::now()->format($periodeType === 'bulan' ? 'Y-m' : 'Y'));

        // Query builder helper
        $filterPemasukan   = Pemasukan::query();
        $filterPengeluaran = Pengeluaran::query();

        if ($periodeType === 'bulan') {
            $filterPemasukan->whereRaw("DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = ?", [$periode]);
            $filterPengeluaran->whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$periode]);
        } else {
            $filterPemasukan->whereYear('tanggal_pemasukan', $periode);
            $filterPengeluaran->whereYear('tanggal_pengeluaran', $periode);
        }

        // Breakdown pemasukan per tipe
        $tipeList = [
            'tiket_masuk'      => 'Tiket Masuk',
            'tiket_event'      => 'Tiket Event',
            'pendapatan_jasa'  => 'Pendapatan Jasa',
            'penjualan_produk' => 'Penjualan Produk',
            'donasi'           => 'Donasi',
            'sponsor'          => 'Sponsor',
            'grant'            => 'Hibah / Grant',
            'lainnya'          => 'Lainnya',
        ];

        $breakdownPemasukan = [];
        $totalPemasukan     = 0;

        foreach ($tipeList as $value => $label) {
            $nominal = (float) (clone $filterPemasukan)->where('tipe', $value)->sum('nominal');
            $jumlah  = (int)   (clone $filterPemasukan)->where('tipe', $value)->sum('jumlah');
            $breakdownPemasukan[] = [
                'tipe'    => $value,
                'label'   => $label,
                'jumlah'  => $jumlah,
                'nominal' => $nominal,
            ];
            $totalPemasukan += $nominal;
        }

        // Hitung persentase
        foreach ($breakdownPemasukan as &$item) {
            $item['persentase'] = $totalPemasukan > 0
                ? round(($item['nominal'] / $totalPemasukan) * 100, 2)
                : 0;
        }
        unset($item);

        // Breakdown pengeluaran per kategori
        $kategoriList = [
            'gaji'         => 'Gaji Karyawan',
            'operasional'  => 'Operasional',
            'maintenance'  => 'Maintenance',
            'utility'      => 'Utility',
            'lainnya'      => 'Lainnya',
        ];

        $breakdownPengeluaran = [];
        $totalPengeluaran     = 0;

        foreach ($kategoriList as $value => $label) {
            $nominal = (float) (clone $filterPengeluaran)->where('kategori', $value)->sum('nominal');
            $breakdownPengeluaran[] = [
                'kategori' => $value,
                'label'    => $label,
                'nominal'  => $nominal,
            ];
            $totalPengeluaran += $nominal;
        }

        // Hitung persentase
        foreach ($breakdownPengeluaran as &$item) {
            $item['persentase'] = $totalPengeluaran > 0
                ? round(($item['nominal'] / $totalPengeluaran) * 100, 2)
                : 0;
        }
        unset($item);

        $labaRugiBersih = $totalPemasukan - $totalPengeluaran;

        return response()->json([
            'success' => true,
            'data' => [
                'periode_type'          => $periodeType,
                'periode'               => $periode,
                'total_pemasukan'       => $totalPemasukan,
                'total_pengeluaran'     => $totalPengeluaran,
                'laba_rugi_bersih'      => $labaRugiBersih,
                'is_laba'               => $labaRugiBersih >= 0,
                'breakdown_pemasukan'   => $breakdownPemasukan,
                'breakdown_pengeluaran' => $breakdownPengeluaran,
            ]
        ]);
    }

    /**
     * Neraca — ringkasan aset (pemasukan kumulatif) vs beban (pengeluaran kumulatif).
     * Param: tahun (opsional, filter per tahun; kosong = semua data)
     */
    public function neraca(\Illuminate\Http\Request $request)
    {
        $tahun = $request->input('tahun', null);

        $queryPemasukan   = Pemasukan::query();
        $queryPengeluaran = Pengeluaran::query();

        if ($tahun) {
            $queryPemasukan->whereYear('tanggal_pemasukan', $tahun);
            $queryPengeluaran->whereYear('tanggal_pengeluaran', $tahun);
        }

        // Aktiva — breakdown sumber pendapatan per tipe
        $tipeList = [
            'tiket_masuk'      => 'Tiket Masuk',
            'tiket_event'      => 'Tiket Event',
            'pendapatan_jasa'  => 'Pendapatan Jasa',
            'penjualan_produk' => 'Penjualan Produk',
            'donasi'           => 'Donasi',
            'sponsor'          => 'Sponsor',
            'grant'            => 'Hibah / Grant',
            'lainnya'          => 'Lainnya',
        ];

        $aktiva      = [];
        $totalAktiva = 0;

        foreach ($tipeList as $value => $label) {
            $nominal = (float) (clone $queryPemasukan)->where('tipe', $value)->sum('nominal');
            if ($nominal > 0) {
                $aktiva[] = ['label' => $label, 'tipe' => $value, 'nominal' => $nominal];
                $totalAktiva += $nominal;
            }
        }

        // Pasiva — breakdown beban per kategori
        $kategoriList = [
            'gaji'        => 'Gaji Karyawan',
            'operasional' => 'Operasional',
            'maintenance' => 'Maintenance',
            'utility'     => 'Utility',
            'lainnya'     => 'Lainnya',
        ];

        $pasiva      = [];
        $totalPasiva = 0;

        foreach ($kategoriList as $value => $label) {
            $nominal = (float) (clone $queryPengeluaran)->where('kategori', $value)->sum('nominal');
            if ($nominal > 0) {
                $pasiva[] = ['label' => $label, 'kategori' => $value, 'nominal' => $nominal];
                $totalPasiva += $nominal;
            }
        }

        $saldoBersih = $totalAktiva - $totalPasiva;

        // Ambil tahun-tahun yang tersedia untuk filter dropdown
        $tahunTersedia = Pemasukan::selectRaw('YEAR(tanggal_pemasukan) as tahun')
            ->distinct()
            ->orderBy('tahun', 'desc')
            ->pluck('tahun')
            ->toArray();

        return response()->json([
            'success' => true,
            'data' => [
                'tahun'          => $tahun,
                'tahun_tersedia' => $tahunTersedia,
                'total_aktiva'   => $totalAktiva,
                'total_pasiva'   => $totalPasiva,
                'saldo_bersih'   => $saldoBersih,
                'is_surplus'     => $saldoBersih >= 0,
                'aktiva'         => $aktiva,
                'pasiva'         => $pasiva,
            ]
        ]);
    }
}
