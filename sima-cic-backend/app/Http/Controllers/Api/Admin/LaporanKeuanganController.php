<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Services\AktivitasLogger;

class LaporanKeuanganController extends Controller
{
    /**
     * Get Jurnal Kas - Buku Kas Masuk & Keluar Kronologis dengan Saldo Berjalan.
     * Kolom: Nomor Akun | Nama Akun | Quantity | Pemasukan (Rp) | Pengeluaran (Rp) | Saldo (Rp)
     */
    public function jurnalKas(Request $request)
    {
        $mode = $request->input('mode', 'bulanan'); // bulanan | tahunan | semua

        // Master Saldo Awal Akun Kas (kategori 3 atau AUTO--241 atau 1-10001, jika belum ada default 0)
        $kasAkun = Akun::where('nomor_kategori', 3)->first()
            ?? Akun::where('nomor_akun', 'AUTO--241')->first()
            ?? Akun::where('kode_akun', '1-10001')->first();
        $saldoAwalMaster = $kasAkun ? (float) $kasAkun->saldo_awal : 0.0;

        if ($mode === 'tahunan') {
            $tahun = $request->input('tahun', Carbon::now()->format('Y'));
            $startDate = Carbon::createFromDate($tahun, 1, 1)->startOfDay()->toDateString();
            $endDate = Carbon::createFromDate($tahun, 12, 31)->endOfDay()->toDateString();
            $periodeLabel = "Tahun " . $tahun;

            $pemasukanSebelumnya = (float) Pemasukan::where('tanggal_pemasukan', '<', $startDate)->sum('nominal');
            $pengeluaranSebelumnya = (float) Pengeluaran::where('tanggal_pengeluaran', '<', $startDate)->sum('nominal');
            $saldoAwalPeriode = $saldoAwalMaster + $pemasukanSebelumnya - $pengeluaranSebelumnya;
        } elseif ($mode === 'semua') {
            $startDate = null;
            $endDate = null;
            $periodeLabel = "Semua Waktu";

            $saldoAwalPeriode = $saldoAwalMaster;
        } else {
            // Default: bulanan
            $mode = 'bulanan';
            $bulan = $request->input('bulan', Carbon::now()->format('Y-m'));
            $startDate = $request->input('tanggal_mulai');
            $endDate = $request->input('tanggal_akhir');

            if (!$startDate || !$endDate) {
                $currentMonth = Carbon::createFromFormat('Y-m', $bulan)->startOfMonth();
                $startDate = $currentMonth->copy()->startOfMonth()->toDateString();
                $endDate = $currentMonth->copy()->endOfMonth()->toDateString();
                $periodeLabel = $currentMonth->translatedFormat('F Y');
            } else {
                $currentMonth = Carbon::parse($startDate)->startOfMonth();
                $periodeLabel = $currentMonth->translatedFormat('F Y');
            }

            $pemasukanSebelumnya = (float) Pemasukan::where('tanggal_pemasukan', '<', $startDate)->sum('nominal');
            $pengeluaranSebelumnya = (float) Pengeluaran::where('tanggal_pengeluaran', '<', $startDate)->sum('nominal');
            $saldoAwalPeriode = $saldoAwalMaster + $pemasukanSebelumnya - $pengeluaranSebelumnya;
        }

        // Query Pemasukan & Pengeluaran
        $pemasukanQuery = Pemasukan::with('akun');
        $pengeluaranQuery = Pengeluaran::with('akun');

        if ($startDate && $endDate) {
            $pemasukanQuery->whereBetween('tanggal_pemasukan', [$startDate, $endDate]);
            $pengeluaranQuery->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
        }

        $pemasukans = $pemasukanQuery->get()->map(function ($item) {
            $nomorAkun = $item->akun?->nomor_akun ?? ($item->akun?->kode_akun ?? '-');
            return [
                'id'             => $item->id,
                'tipe_transaksi' => 'pemasukan',
                'tanggal'        => $item->tanggal_pemasukan ? Carbon::parse($item->tanggal_pemasukan)->format('Y-m-d') : null,
                'created_at'     => $item->created_at ? $item->created_at->toISOString() : null,
                'akun_id'        => $item->akun_id,
                'nomor_akun'     => $item->akun_id ? $nomorAkun : '-',
                'nama_akun'      => $item->akun ? $item->akun->nama_akun : ($item->nama_pemasukan ?? 'Pemasukan Kas'),
                'is_unmapped'    => empty($item->akun_id),
                'nomor_kategori' => $item->akun?->nomor_kategori,
                'nama_kategori'  => $item->akun?->nama_kategori,
                'quantity'       => (float) ($item->quantity ?? ($item->jumlah ?? 1.0)),
                'pemasukan'      => (float) $item->nominal,
                'pengeluaran'    => 0.0,
                'keterangan'     => $item->keterangan,
            ];
        });

        $pengeluarans = $pengeluaranQuery->get()->map(function ($item) {
            $nomorAkun = $item->akun?->nomor_akun ?? ($item->akun?->kode_akun ?? '-');
            return [
                'id'             => $item->id,
                'tipe_transaksi' => 'pengeluaran',
                'tanggal'        => $item->tanggal_pengeluaran ? Carbon::parse($item->tanggal_pengeluaran)->format('Y-m-d') : null,
                'created_at'     => $item->created_at ? $item->created_at->toISOString() : null,
                'akun_id'        => $item->akun_id,
                'nomor_akun'     => $item->akun_id ? $nomorAkun : '-',
                'nama_akun'      => $item->akun ? $item->akun->nama_akun : ($item->nama_pengeluaran ?? 'Pengeluaran Kas'),
                'is_unmapped'    => empty($item->akun_id),
                'nomor_kategori' => $item->akun?->nomor_kategori,
                'nama_kategori'  => $item->akun?->nama_kategori,
                'quantity'       => (float) ($item->quantity ?? 1.0),
                'pemasukan'      => 0.0,
                'pengeluaran'    => (float) $item->nominal,
                'keterangan'     => $item->keterangan,
            ];
        });

        // Gabungkan dan urutkan kronologis: tanggal ASC, id ASC
        $allEntries = $pemasukans->concat($pengeluarans)->sort(function ($a, $b) {
            $dateA = $a['tanggal'] ?? '';
            $dateB = $b['tanggal'] ?? '';
            if ($dateA !== $dateB) {
                return strcmp($dateA, $dateB);
            }
            return $a['id'] <=> $b['id'];
        })->values();

        // Hitung Saldo Berjalan (Running Balance)
        $runningBalance = $saldoAwalPeriode;
        $totalDebitPeriode = 0.0;
        $totalKreditPeriode = 0.0;

        $entries = $allEntries->map(function ($entry) use (&$runningBalance, &$totalDebitPeriode, &$totalKreditPeriode) {
            $runningBalance += $entry['pemasukan'] - $entry['pengeluaran'];
            $totalDebitPeriode += $entry['pemasukan'];
            $totalKreditPeriode += $entry['pengeluaran'];
            $entry['saldo'] = (float) $runningBalance;
            return $entry;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'mode'                => $mode,
                'periode'             => $periodeLabel,
                'tanggal_mulai'       => $startDate,
                'tanggal_akhir'       => $endDate,
                'saldo_awal_periode'  => (float) $saldoAwalPeriode,
                'total_debit'         => (float) $totalDebitPeriode,
                'total_kredit'        => (float) $totalKreditPeriode,
                'saldo_akhir_periode' => (float) $runningBalance,
                'entries'             => $entries,
            ]
        ]);
    }

    /**
     * Typeahead Pencarian Akun untuk Jurnal Kas.
     * Mencari berdasarkan nomor_akun / nama_akun / kode_akun, limit 10.
     * Akun kategori 3 (Kas & Bank) dikecualikan.
     * Exact match pada nomor_akun diurutkan paling atas.
     */
    public function searchAkun(Request $request)
    {
        $q = trim($request->input('q', ''));
        if ($q === '') {
            return response()->json(['success' => true, 'data' => []]);
        }

        $query = Akun::active()
            ->where(function ($sub) {
                $sub->where('nomor_kategori', '!=', 3)
                    ->orWhereNull('nomor_kategori');
            })
            ->where('kode_akun', '!=', '1-10001')
            ->where(function ($sub) use ($q) {
                $sub->where('nomor_akun', 'like', "%{$q}%")
                    ->orWhere('nama_akun', 'like', "%{$q}%")
                    ->orWhere('kode_akun', 'like', "%{$q}%");
            });

        // Exact match pada nomor_akun mendapat prioritas paling atas
        $escaped = addslashes($q);
        $akuns = $query->orderByRaw("CASE WHEN nomor_akun = '{$escaped}' THEN 0 ELSE 1 END")
            ->orderBy('nomor_akun', 'asc')
            ->limit(10)
            ->get();

        $results = $akuns->map(function ($a) {
            $nomor = $a->nomor_akun ?? $a->kode_akun;
            $kategori = $a->nama_kategori ?? ucfirst($a->kategori ?? 'Akun');
            return [
                'id'             => $a->id,
                'nomor_akun'     => $nomor,
                'nama_akun'      => $a->nama_akun,
                'nomor_kategori' => $a->nomor_kategori,
                'nama_kategori'  => $kategori,
                'label'          => "{$nomor} - {$a->nama_akun} - {$kategori}",
            ];
        });

        return response()->json([
            'success' => true,
            'data'    => $results,
        ]);
    }

    /**
     * Simpan transaksi baru dari baris input Jurnal Kas.
     */
    public function storeTransaksiKas(Request $request)
    {
        $validated = $request->validate([
            'jenis'      => 'required|string|in:pemasukan,pengeluaran',
            'akun_id'    => 'required|exists:akuns,id',
            'nominal'    => 'required|numeric|min:0.01',
            'quantity'   => 'nullable|numeric|min:0.01',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $akun = Akun::findOrFail($validated['akun_id']);
        if ($akun->nomor_kategori === 3) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Kas & Bank tidak boleh dipilih sebagai akun lawan transaksi.',
            ], 422);
        }

        $quantity = (float) ($validated['quantity'] ?? 1.0);
        $nominal  = (float) $validated['nominal'];
        $tanggal  = $validated['tanggal'];
        $ket      = $validated['keterangan'] ?? null;

        if ($validated['jenis'] === 'pemasukan') {
            $pemasukan = Pemasukan::create([
                'akun_id'           => $akun->id,
                'nama_pemasukan'    => $akun->nama_akun,
                'tipe'              => 'lainnya',
                'jumlah'            => max(1, (int) round($quantity)),
                'quantity'          => $quantity,
                'nominal'           => $nominal,
                'tanggal_pemasukan' => $tanggal,
                'keterangan'        => $ket,
            ]);

            AktivitasLogger::created(
                'pemasukan',
                'Menambahkan pemasukan kas',
                $akun->nama_akun . ' - Rp ' . number_format($nominal, 0, ',', '.'),
                $pemasukan
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan kas berhasil disimpan',
                'data'    => $pemasukan,
            ], 201);
        } else {
            $pengeluaran = Pengeluaran::create([
                'akun_id'             => $akun->id,
                'nama_pengeluaran'    => $akun->nama_akun,
                'kategori'            => 'operasional',
                'quantity'            => $quantity,
                'nominal'             => $nominal,
                'tanggal_pengeluaran' => $tanggal,
                'keterangan'          => $ket,
            ]);

            AktivitasLogger::created(
                'pengeluaran',
                'Menambahkan pengeluaran kas',
                $akun->nama_akun . ' - Rp ' . number_format($nominal, 0, ',', '.'),
                $pengeluaran
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran kas berhasil disimpan',
                'data'    => $pengeluaran,
            ], 201);
        }
    }

    /**
     * Update transaksi kas yang ada.
     */
    public function updateTransaksiKas(Request $request, $jenis, $id)
    {
        if (!in_array($jenis, ['pemasukan', 'pengeluaran'])) {
            return response()->json(['success' => false, 'message' => 'Jenis transaksi tidak valid'], 400);
        }

        $validated = $request->validate([
            'akun_id'    => 'required|exists:akuns,id',
            'nominal'    => 'required|numeric|min:0.01',
            'quantity'   => 'nullable|numeric|min:0.01',
            'tanggal'    => 'required|date',
            'keterangan' => 'nullable|string|max:500',
        ]);

        $akun = Akun::findOrFail($validated['akun_id']);
        if ($akun->nomor_kategori === 3) {
            return response()->json([
                'success' => false,
                'message' => 'Akun Kas & Bank tidak boleh dipilih sebagai akun lawan transaksi.',
            ], 422);
        }

        $quantity = (float) ($validated['quantity'] ?? 1.0);
        $nominal  = (float) $validated['nominal'];
        $tanggal  = $validated['tanggal'];
        $ket      = $validated['keterangan'] ?? null;

        if ($jenis === 'pemasukan') {
            $item = Pemasukan::findOrFail($id);
            $item->update([
                'akun_id'           => $akun->id,
                'nama_pemasukan'    => $akun->nama_akun,
                'jumlah'            => max(1, (int) round($quantity)),
                'quantity'          => $quantity,
                'nominal'           => $nominal,
                'tanggal_pemasukan' => $tanggal,
                'keterangan'        => $ket,
            ]);

            AktivitasLogger::updated(
                'pemasukan',
                'Memperbarui pemasukan kas',
                $akun->nama_akun . ' - Rp ' . number_format($nominal, 0, ',', '.'),
                $item
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan berhasil diperbarui',
                'data'    => $item,
            ]);
        } else {
            $item = Pengeluaran::findOrFail($id);
            $item->update([
                'akun_id'             => $akun->id,
                'nama_pengeluaran'    => $akun->nama_akun,
                'quantity'            => $quantity,
                'nominal'             => $nominal,
                'tanggal_pengeluaran' => $tanggal,
                'keterangan'          => $ket,
            ]);

            AktivitasLogger::updated(
                'pengeluaran',
                'Memperbarui pengeluaran kas',
                $akun->nama_akun . ' - Rp ' . number_format($nominal, 0, ',', '.'),
                $item
            );

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil diperbarui',
                'data'    => $item,
            ]);
        }
    }

    /**
     * Hapus transaksi kas.
     */
    public function deleteTransaksiKas(Request $request, $jenis, $id)
    {
        if (!in_array($jenis, ['pemasukan', 'pengeluaran'])) {
            return response()->json(['success' => false, 'message' => 'Jenis transaksi tidak valid'], 400);
        }

        if ($jenis === 'pemasukan') {
            $item = Pemasukan::findOrFail($id);
            $detail = ($item->akun?->nama_akun ?? $item->nama_pemasukan) . ' - Rp ' . number_format($item->nominal, 0, ',', '.');
            $item->delete();

            AktivitasLogger::deleted('pemasukan', 'Menghapus transaksi pemasukan kas', $detail, $item);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan berhasil dihapus',
            ]);
        } else {
            $item = Pengeluaran::findOrFail($id);
            $detail = ($item->akun?->nama_akun ?? $item->nama_pengeluaran) . ' - Rp ' . number_format($item->nominal, 0, ',', '.');
            $item->delete();

            AktivitasLogger::deleted('pengeluaran', 'Menghapus transaksi pengeluaran kas', $detail, $item);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil dihapus',
            ]);
        }
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
