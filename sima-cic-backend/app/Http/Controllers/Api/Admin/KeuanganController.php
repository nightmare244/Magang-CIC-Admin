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
}
