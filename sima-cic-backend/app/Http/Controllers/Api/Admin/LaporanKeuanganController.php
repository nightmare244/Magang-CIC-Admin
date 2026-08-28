<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;

class LaporanKeuanganController extends Controller
{
    /**
     * Get Jurnal Kas (Buku Kas Masuk & Keluar) with Running Balance.
     */
    public function jurnalKas(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_akhir');

        if (!$startDate || !$endDate) {
            $currentMonth = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
            $startDate = $currentMonth->copy()->startOfMonth()->toDateString();
            $endDate = $currentMonth->copy()->endOfMonth()->toDateString();
        } else {
            $currentMonth = Carbon::parse($startDate)->startOfMonth();
        }

        // 1. Calculate Initial Cash Balance before $startDate
        $kasAkun = Akun::where('kategori', 'aset')->where('kode_akun', '1-10001')->first();
        $saldoAwalMaster = $kasAkun ? (float) $kasAkun->saldo_awal : 0;

        $pemasukanSebelumnya = (float) Pemasukan::where('tanggal_pemasukan', '<', $startDate)->sum('nominal');
        $pengeluaranSebelumnya = (float) Pengeluaran::where('tanggal_pengeluaran', '<', $startDate)->sum('nominal');
        $saldoAwalPeriode = $saldoAwalMaster + $pemasukanSebelumnya - $pengeluaranSebelumnya;

        // 2. Fetch Pemasukan in period
        $pemasukans = Pemasukan::with('akun')
            ->whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->get()
            ->map(function ($p) {
                return [
                    'id'          => 'in_' . $p->id,
                    'tanggal'     => Carbon::parse($p->tanggal_pemasukan)->format('Y-m-d'),
                    'no_bukti'    => 'BKM-' . Carbon::parse($p->tanggal_pemasukan)->format('ym') . '-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                    'uraian'      => $p->nama_pemasukan . ($p->keterangan ? ' (' . $p->keterangan . ')' : ''),
                    'akun_lawan'  => $p->akun ? $p->akun->kode_akun . ' - ' . $p->akun->nama_akun : 'Pendapatan',
                    'tipe'        => 'masuk',
                    'debit'       => (float) $p->nominal,
                    'kredit'      => 0.0,
                    'created_at'  => $p->created_at,
                ];
            });

        // 3. Fetch Pengeluaran in period
        $pengeluarans = Pengeluaran::with('akun')
            ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->get()
            ->map(function ($p) {
                return [
                    'id'          => 'out_' . $p->id,
                    'tanggal'     => Carbon::parse($p->tanggal_pengeluaran)->format('Y-m-d'),
                    'no_bukti'    => 'BKK-' . Carbon::parse($p->tanggal_pengeluaran)->format('ym') . '-' . str_pad($p->id, 4, '0', STR_PAD_LEFT),
                    'uraian'      => $p->nama_pengeluaran . ($p->keterangan ? ' (' . $p->keterangan . ')' : ''),
                    'akun_lawan'  => $p->akun ? $p->akun->kode_akun . ' - ' . $p->akun->nama_akun : 'Beban Operasional',
                    'tipe'        => 'keluar',
                    'debit'       => 0.0,
                    'kredit'      => (float) $p->nominal,
                    'created_at'  => $p->created_at,
                ];
            });

        // 4. Combine and Sort Chronologically (oldest to newest for running balance)
        $allEntries = $pemasukans->concat($pengeluarans)->sortBy(function ($item) {
            return $item['tanggal'] . '_' . ($item['created_at'] ?? '');
        })->values();

        $runningBalance = $saldoAwalPeriode;
        $totalDebit = 0;
        $totalKredit = 0;

        $jurnalEntries = $allEntries->map(function ($entry) use (&$runningBalance, &$totalDebit, &$totalKredit) {
            $totalDebit += $entry['debit'];
            $totalKredit += $entry['kredit'];
            $runningBalance += ($entry['debit'] - $entry['kredit']);

            $entry['saldo_berjalan'] = $runningBalance;
            return $entry;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'periode'            => $currentMonth->translatedFormat('F Y'),
                'tanggal_mulai'      => $startDate,
                'tanggal_akhir'      => $endDate,
                'saldo_awal_periode' => (float) $saldoAwalPeriode,
                'total_debit'        => (float) $totalDebit,
                'total_kredit'       => (float) $totalKredit,
                'saldo_akhir_periode'=> (float) $runningBalance,
                'entries'            => $jurnalEntries,
            ]
        ]);
    }

    /**
     * Get Laporan Arus Kas (Cash Flow Statement).
     */
    public function arusKas(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $startDate = $currentMonth->copy()->startOfMonth()->toDateString();
        $endDate = $currentMonth->copy()->endOfMonth()->toDateString();

        // 1. Saldo Kas Awal
        $kasAkun = Akun::where('kategori', 'aset')->where('kode_akun', '1-10001')->first();
        $saldoAwalMaster = $kasAkun ? (float) $kasAkun->saldo_awal : 0;
        $pemasukanSebelumnya = (float) Pemasukan::where('tanggal_pemasukan', '<', $startDate)->sum('nominal');
        $pengeluaranSebelumnya = (float) Pengeluaran::where('tanggal_pengeluaran', '<', $startDate)->sum('nominal');
        $saldoAwalKas = $saldoAwalMaster + $pemasukanSebelumnya - $pengeluaranSebelumnya;

        // 2. Arus Kas Masuk (Penerimaan Operasional)
        $pemasukanByAkun = Pemasukan::with('akun')
            ->whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->akun ? $item->akun->nama_akun : 'Pendapatan Lainnya';
            })
            ->map(function ($group, $namaAkun) {
                return [
                    'nama_pos' => $namaAkun,
                    'kode'     => $group->first()->akun?->kode_akun ?? '4-XXXXX',
                    'nominal'  => (float) $group->sum('nominal'),
                ];
            })->values();

        $totalKasMasuk = (float) $pemasukanByAkun->sum('nominal');

        // 3. Arus Kas Keluar (Pengeluaran Operasional)
        $pengeluaranByAkun = Pengeluaran::with('akun')
            ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->get()
            ->groupBy(function ($item) {
                return $item->akun ? $item->akun->nama_akun : 'Beban Lainnya';
            })
            ->map(function ($group, $namaAkun) {
                return [
                    'nama_pos' => $namaAkun,
                    'kode'     => $group->first()->akun?->kode_akun ?? '5-XXXXX',
                    'nominal'  => (float) $group->sum('nominal'),
                ];
            })->values();

        $totalKasKeluar = (float) $pengeluaranByAkun->sum('nominal');
        $arusKasBersih = $totalKasMasuk - $totalKasKeluar;
        $saldoAkhirKas = $saldoAwalKas + $arusKasBersih;

        return response()->json([
            'success' => true,
            'data' => [
                'periode'           => $currentMonth->translatedFormat('F Y'),
                'bulan_key'         => $bulan,
                'saldo_awal_kas'    => $saldoAwalKas,
                'arus_kas_masuk'    => [
                    'rincian' => $pemasukanByAkun,
                    'total'   => $totalKasMasuk,
                ],
                'arus_kas_keluar'   => [
                    'rincian' => $pengeluaranByAkun,
                    'total'   => $totalKasKeluar,
                ],
                'arus_kas_bersih'   => $arusKasBersih,
                'saldo_akhir_kas'   => $saldoAkhirKas,
            ]
        ]);
    }

    /**
     * Get Laporan Laba Rugi (Income Statement).
     */
    public function labaRugi(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
        $startDate = $currentMonth->copy()->startOfMonth()->toDateString();
        $endDate = $currentMonth->copy()->endOfMonth()->toDateString();

        // 1. Pendapatan Operasional
        $pendapatanAccounts = Akun::where('kategori', 'pendapatan')->get();
        $pendapatanItems = [];
        $totalPendapatan = 0;

        foreach ($pendapatanAccounts as $akun) {
            $sum = (float) Pemasukan::where('akun_id', $akun->id)
                ->whereBetween('tanggal_pemasukan', [$startDate, $endDate])
                ->sum('nominal');

            $pendapatanItems[] = [
                'akun_id'   => $akun->id,
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'nominal'   => $sum,
            ];
            $totalPendapatan += $sum;
        }

        // In case there are records with null akun_id
        $unassignedPemasukan = (float) Pemasukan::whereNull('akun_id')
            ->whereBetween('tanggal_pemasukan', [$startDate, $endDate])
            ->sum('nominal');

        if ($unassignedPemasukan > 0) {
            $pendapatanItems[] = [
                'akun_id'   => null,
                'kode_akun' => '4-99999',
                'nama_akun' => 'Pendapatan Lainnya (Belum Dipetakan)',
                'nominal'   => $unassignedPemasukan,
            ];
            $totalPendapatan += $unassignedPemasukan;
        }

        // Calculate % for each pendapatan item
        foreach ($pendapatanItems as &$item) {
            $item['persentase'] = $totalPendapatan > 0 ? round(($item['nominal'] / $totalPendapatan) * 100, 1) : 0;
        }

        // 2. Beban Operasional
        $bebanAccounts = Akun::where('kategori', 'beban')->get();
        $bebanItems = [];
        $totalBeban = 0;

        foreach ($bebanAccounts as $akun) {
            $sum = (float) Pengeluaran::where('akun_id', $akun->id)
                ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
                ->sum('nominal');

            $bebanItems[] = [
                'akun_id'   => $akun->id,
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'nominal'   => $sum,
            ];
            $totalBeban += $sum;
        }

        $unassignedPengeluaran = (float) Pengeluaran::whereNull('akun_id')
            ->whereBetween('tanggal_pengeluaran', [$startDate, $endDate])
            ->sum('nominal');

        if ($unassignedPengeluaran > 0) {
            $bebanItems[] = [
                'akun_id'   => null,
                'kode_akun' => '5-99999',
                'nama_akun' => 'Beban Lainnya (Belum Dipetakan)',
                'nominal'   => $unassignedPengeluaran,
            ];
            $totalBeban += $unassignedPengeluaran;
        }

        // Calculate % for each beban item
        foreach ($bebanItems as &$item) {
            $item['persentase'] = $totalBeban > 0 ? round(($item['nominal'] / $totalBeban) * 100, 1) : 0;
        }

        // 3. Laba / (Rugi) Bersih
        $labaBersih = $totalPendapatan - $totalBeban;
        $rasioLabaBersih = $totalPendapatan > 0 ? round(($labaBersih / $totalPendapatan) * 100, 1) : 0;

        return response()->json([
            'success' => true,
            'data' => [
                'periode'           => $currentMonth->translatedFormat('F Y'),
                'bulan_key'         => $bulan,
                'pendapatan'        => [
                    'items' => $pendapatanItems,
                    'total' => $totalPendapatan,
                ],
                'beban'             => [
                    'items' => $bebanItems,
                    'total' => $totalBeban,
                ],
                'laba_kotor'        => $totalPendapatan, // HPP belum ada
                'laba_bersih'       => $labaBersih,
                'rasio_laba_bersih' => $rasioLabaBersih,
                'status'            => $labaBersih >= 0 ? 'Surplus (Laba)' : 'Defisit (Rugi)',
            ]
        ]);
    }

    /**
     * Get Laporan Neraca (Balance Sheet).
     */
    public function neraca(Request $request)
    {
        $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
        $currentMonth = Carbon::createFromFormat('Y-m', $bulan)->endOfMonth();
        $asOfDate = $currentMonth->toDateString();

        // 1. ASET (AKTIVA)
        // Kas Utama & Bank
        $kasAkun = Akun::where('kategori', 'aset')->where('kode_akun', '1-10001')->first();
        $bankAkun = Akun::where('kategori', 'aset')->where('kode_akun', '1-10002')->first();

        $saldoAwalKas = $kasAkun ? (float) $kasAkun->saldo_awal : 0;
        $saldoAwalBank = $bankAkun ? (float) $bankAkun->saldo_awal : 0;

        // Total Cumulative Pemasukan & Pengeluaran up to $asOfDate
        $totalPemasukanKumulatif = (float) Pemasukan::where('tanggal_pemasukan', '<=', $asOfDate)->sum('nominal');
        $totalPengeluaranKumulatif = (float) Pengeluaran::where('tanggal_pengeluaran', '<=', $asOfDate)->sum('nominal');

        $saldoKasSaatIni = $saldoAwalKas + $totalPemasukanKumulatif - $totalPengeluaranKumulatif;

        $asetItems = [
            [
                'kode_akun' => '1-10001',
                'nama_akun' => 'Kas Utama & Operasional',
                'nominal'   => $saldoKasSaatIni,
            ],
            [
                'kode_akun' => '1-10002',
                'nama_akun' => 'Rekening Bank Operasional',
                'nominal'   => $saldoAwalBank,
            ],
        ];

        $totalAset = $saldoKasSaatIni + $saldoAwalBank;

        // 2. KEWAJIBAN (PASIVA)
        $kewajibanItems = [
            [
                'kode_akun' => '2-10001',
                'nama_akun' => 'Hutang Usaha & Operasional Lancar',
                'nominal'   => 0.0,
            ]
        ];
        $totalKewajiban = 0.0;

        // 3. EKUITAS / MODAL
        $modalAkun = Akun::where('kategori', 'ekuitas')->where('kode_akun', '3-10001')->first();
        $modalAwal = $modalAkun ? (float) $modalAkun->saldo_awal : ($saldoAwalKas + $saldoAwalBank);

        // Laba / (Rugi) Ditahan / Berjalan Kumulatif
        $labaBerjalanKumulatif = $totalPemasukanKumulatif - $totalPengeluaranKumulatif;

        $ekuitasItems = [
            [
                'kode_akun' => '3-10001',
                'nama_akun' => 'Modal Awal Disetor / Ekuitas Yayasan',
                'nominal'   => $modalAwal,
            ],
            [
                'kode_akun' => '3-20001',
                'nama_akun' => 'Laba / (Rugi) Kumulatif Periode Berjalan',
                'nominal'   => $labaBerjalanKumulatif,
            ],
        ];

        $totalEkuitas = $modalAwal + $labaBerjalanKumulatif;
        $totalPasiva = $totalKewajiban + $totalEkuitas;

        $selisih = abs($totalAset - $totalPasiva);
        $isBalanced = $selisih < 0.01;

        return response()->json([
            'success' => true,
            'data' => [
                'periode'           => $currentMonth->translatedFormat('F Y'),
                'per_tanggal'       => $asOfDate,
                'aset'              => [
                    'items' => $asetItems,
                    'total' => $totalAset,
                ],
                'kewajiban'         => [
                    'items' => $kewajibanItems,
                    'total' => $totalKewajiban,
                ],
                'ekuitas'           => [
                    'items' => $ekuitasItems,
                    'total' => $totalEkuitas,
                ],
                'total_kewajiban_ekuitas' => $totalPasiva,
                'is_balanced'       => $isBalanced,
                'selisih'           => $selisih,
            ]
        ]);
    }
}
