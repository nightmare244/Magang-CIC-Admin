<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Models\Pemasukan;
use App\Models\Pengeluaran;
use App\Services\AktivitasLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TransaksiKeuanganController extends Controller
{
    /**
     * Display a unified listing of financial transactions (pemasukan & pengeluaran).
     */
    public function index(Request $request)
    {
        $bulan = $request->input('bulan');
        $jenis = $request->input('jenis', 'semua'); // semua, pemasukan, pengeluaran
        $akunId = $request->input('akun_id');
        $search = $request->input('q');
        $startDate = $request->input('tanggal_mulai');
        $endDate = $request->input('tanggal_akhir');

        $transactions = collect();

        // 1. Fetch Pemasukan
        if ($jenis === 'semua' || $jenis === 'pemasukan') {
            $pQuery = Pemasukan::with('akun');

            if ($bulan) {
                $pQuery->whereRaw("DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = ?", [$bulan]);
            }
            if ($startDate && $endDate) {
                $pQuery->whereBetween('tanggal_pemasukan', [$startDate, $endDate]);
            }
            if ($akunId) {
                $pQuery->where('akun_id', $akunId);
            }
            if ($search) {
                $pQuery->where(function ($q) use ($search) {
                    $q->where('nama_pemasukan', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pemasukans = $pQuery->get()->map(function ($item) {
                return [
                    'id'               => 'in_' . $item->id,
                    'raw_id'           => $item->id,
                    'jenis'            => 'pemasukan',
                    'kode_transaksi'   => 'IN-' . Carbon::parse($item->tanggal_pemasukan)->format('ym') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'nama_transaksi'   => $item->nama_pemasukan,
                    'akun_id'          => $item->akun_id,
                    'akun'             => $item->akun,
                    'tipe_kategori'    => $item->tipe,
                    'jumlah'           => $item->jumlah,
                    'nominal'          => (float) $item->nominal,
                    'tanggal'          => Carbon::parse($item->tanggal_pemasukan)->format('Y-m-d'),
                    'keterangan'       => $item->keterangan,
                    'created_at'       => $item->created_at,
                    'updated_at'       => $item->updated_at,
                ];
            });

            $transactions = $transactions->concat($pemasukans);
        }

        // 2. Fetch Pengeluaran
        if ($jenis === 'semua' || $jenis === 'pengeluaran') {
            $eQuery = Pengeluaran::with('akun');

            if ($bulan) {
                $eQuery->whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$bulan]);
            }
            if ($startDate && $endDate) {
                $eQuery->whereBetween('tanggal_pengeluaran', [$startDate, $endDate]);
            }
            if ($akunId) {
                $eQuery->where('akun_id', $akunId);
            }
            if ($search) {
                $eQuery->where(function ($q) use ($search) {
                    $q->where('nama_pengeluaran', 'like', "%{$search}%")
                      ->orWhere('keterangan', 'like', "%{$search}%");
                });
            }

            $pengeluarans = $eQuery->get()->map(function ($item) {
                return [
                    'id'               => 'out_' . $item->id,
                    'raw_id'           => $item->id,
                    'jenis'            => 'pengeluaran',
                    'kode_transaksi'   => 'OUT-' . Carbon::parse($item->tanggal_pengeluaran)->format('ym') . '-' . str_pad($item->id, 4, '0', STR_PAD_LEFT),
                    'nama_transaksi'   => $item->nama_pengeluaran,
                    'akun_id'          => $item->akun_id,
                    'akun'             => $item->akun,
                    'tipe_kategori'    => $item->kategori,
                    'jumlah'           => null,
                    'nominal'          => (float) $item->nominal,
                    'tanggal'          => Carbon::parse($item->tanggal_pengeluaran)->format('Y-m-d'),
                    'keterangan'       => $item->keterangan,
                    'created_at'       => $item->created_at,
                    'updated_at'       => $item->updated_at,
                ];
            });

            $transactions = $transactions->concat($pengeluarans);
        }

        // Sort descending by date, then by created_at
        $sortedTransactions = $transactions->sortByDesc(function ($t) {
            return $t['tanggal'] . '_' . ($t['created_at'] ?? '');
        })->values();

        // Calculate summary for active filter
        $totalPemasukan = $transactions->where('jenis', 'pemasukan')->sum('nominal');
        $totalPengeluaran = $transactions->where('jenis', 'pengeluaran')->sum('nominal');
        $saldoBersih = $totalPemasukan - $totalPengeluaran;
        $totalTiket = $transactions->where('jenis', 'pemasukan')->where('tipe_kategori', 'tiket_masuk')->sum('jumlah');

        return response()->json([
            'success' => true,
            'data'    => $sortedTransactions,
            'summary' => [
                'total_pemasukan'   => (float) $totalPemasukan,
                'total_pengeluaran' => (float) $totalPengeluaran,
                'saldo_bersih'      => (float) $saldoBersih,
                'total_transaksi'   => $transactions->count(),
                'total_tiket'       => (int) $totalTiket,
            ]
        ]);
    }

    /**
     * Store a newly created transaction (either pemasukan or pengeluaran).
     */
    public function store(Request $request)
    {
        $request->validate([
            'jenis'          => 'required|in:pemasukan,pengeluaran',
            'akun_id'        => 'required|exists:akuns,id',
            'nama_transaksi' => 'required|string|max:255',
            'nominal'        => 'required|numeric|min:0',
            'tanggal'        => 'required|date',
            'keterangan'     => 'nullable|string',
            // Optional specific fields
            'tipe_kategori'  => 'nullable|string',
            'jumlah'         => 'nullable|integer|min:1',
        ]);

        $akun = Akun::findOrFail($request->akun_id);

        if ($request->jenis === 'pemasukan') {
            $tipe = $request->tipe_kategori ?: $this->deducePemasukanTipe($akun->kode_akun, $request->nama_transaksi);
            $jumlah = $request->input('jumlah', 1);

            $record = Pemasukan::create([
                'akun_id'           => $akun->id,
                'nama_pemasukan'    => $request->nama_transaksi,
                'tipe'              => $tipe,
                'jumlah'            => $jumlah,
                'nominal'           => $request->nominal,
                'tanggal_pemasukan' => $request->tanggal,
                'keterangan'        => $request->keterangan,
            ]);

            AktivitasLogger::created('pemasukan', 'Menambahkan transaksi pemasukan baru', $record->nama_pemasukan . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan berhasil dicatat dan terhubung ke Jurnal & Laporan Keuangan.',
                'data'    => $record->load('akun')
            ], 201);
        } else {
            $kategori = $request->tipe_kategori ?: $this->deducePengeluaranKategori($akun->kode_akun, $request->nama_transaksi);

            $record = Pengeluaran::create([
                'akun_id'             => $akun->id,
                'nama_pengeluaran'    => $request->nama_transaksi,
                'kategori'            => $kategori,
                'nominal'             => $request->nominal,
                'tanggal_pengeluaran' => $request->tanggal,
                'keterangan'          => $request->keterangan,
            ]);

            AktivitasLogger::created('pengeluaran', 'Menambahkan transaksi pengeluaran baru', $record->nama_pengeluaran . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil dicatat dan terhubung ke Jurnal & Laporan Keuangan.',
                'data'    => $record->load('akun')
            ], 201);
        }
    }

    /**
     * Display the specified transaction.
     */
    public function show(Request $request, $id)
    {
        $parsed = $this->parseCompositeId($id, $request->input('jenis'));

        if ($parsed['jenis'] === 'pemasukan') {
            $record = Pemasukan::with('akun')->findOrFail($parsed['raw_id']);
            $data = [
                'id'             => 'in_' . $record->id,
                'raw_id'         => $record->id,
                'jenis'          => 'pemasukan',
                'kode_transaksi' => 'IN-' . Carbon::parse($record->tanggal_pemasukan)->format('ym') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                'nama_transaksi' => $record->nama_pemasukan,
                'akun_id'        => $record->akun_id,
                'akun'           => $record->akun,
                'tipe_kategori'  => $record->tipe,
                'jumlah'         => $record->jumlah,
                'nominal'        => (float) $record->nominal,
                'tanggal'        => Carbon::parse($record->tanggal_pemasukan)->format('Y-m-d'),
                'keterangan'     => $record->keterangan,
                'created_at'     => $record->created_at,
                'updated_at'     => $record->updated_at,
            ];
        } else {
            $record = Pengeluaran::with('akun')->findOrFail($parsed['raw_id']);
            $data = [
                'id'             => 'out_' . $record->id,
                'raw_id'         => $record->id,
                'jenis'          => 'pengeluaran',
                'kode_transaksi' => 'OUT-' . Carbon::parse($record->tanggal_pengeluaran)->format('ym') . '-' . str_pad($record->id, 4, '0', STR_PAD_LEFT),
                'nama_transaksi' => $record->nama_pengeluaran,
                'akun_id'        => $record->akun_id,
                'akun'           => $record->akun,
                'tipe_kategori'  => $record->kategori,
                'jumlah'         => null,
                'nominal'        => (float) $record->nominal,
                'tanggal'        => Carbon::parse($record->tanggal_pengeluaran)->format('Y-m-d'),
                'keterangan'     => $record->keterangan,
                'created_at'     => $record->created_at,
                'updated_at'     => $record->updated_at,
            ];
        }

        return response()->json([
            'success' => true,
            'data'    => $data
        ]);
    }

    /**
     * Update the specified transaction.
     */
    public function update(Request $request, $id)
    {
        $parsed = $this->parseCompositeId($id, $request->input('jenis'));

        $request->validate([
            'akun_id'        => 'required|exists:akuns,id',
            'nama_transaksi' => 'required|string|max:255',
            'nominal'        => 'required|numeric|min:0',
            'tanggal'        => 'required|date',
            'keterangan'     => 'nullable|string',
            'tipe_kategori'  => 'nullable|string',
            'jumlah'         => 'nullable|integer|min:1',
        ]);

        $akun = Akun::findOrFail($request->akun_id);

        if ($parsed['jenis'] === 'pemasukan') {
            $record = Pemasukan::findOrFail($parsed['raw_id']);
            $tipe = $request->tipe_kategori ?: $record->tipe;
            $jumlah = $request->input('jumlah', $record->jumlah ?: 1);

            $record->update([
                'akun_id'           => $akun->id,
                'nama_pemasukan'    => $request->nama_transaksi,
                'tipe'              => $tipe,
                'jumlah'            => $jumlah,
                'nominal'           => $request->nominal,
                'tanggal_pemasukan' => $request->tanggal,
                'keterangan'        => $request->keterangan,
            ]);

            AktivitasLogger::updated('pemasukan', 'Mengubah data transaksi pemasukan', $record->nama_pemasukan . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pemasukan berhasil diperbarui.',
                'data'    => $record->load('akun')
            ]);
        } else {
            $record = Pengeluaran::findOrFail($parsed['raw_id']);
            $kategori = $request->tipe_kategori ?: $record->kategori;

            $record->update([
                'akun_id'             => $akun->id,
                'nama_pengeluaran'    => $request->nama_transaksi,
                'kategori'            => $kategori,
                'nominal'             => $request->nominal,
                'tanggal_pengeluaran' => $request->tanggal,
                'keterangan'          => $request->keterangan,
            ]);

            AktivitasLogger::updated('pengeluaran', 'Mengubah data transaksi pengeluaran', $record->nama_pengeluaran . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);

            return response()->json([
                'success' => true,
                'message' => 'Transaksi pengeluaran berhasil diperbarui.',
                'data'    => $record->load('akun')
            ]);
        }
    }

    /**
     * Remove the specified transaction.
     */
    public function destroy(Request $request, $id)
    {
        $parsed = $this->parseCompositeId($id, $request->input('jenis'));

        if ($parsed['jenis'] === 'pemasukan') {
            $record = Pemasukan::findOrFail($parsed['raw_id']);
            AktivitasLogger::deleted('pemasukan', 'Menghapus transaksi pemasukan', $record->nama_pemasukan . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);
            $record->delete();
        } else {
            $record = Pengeluaran::findOrFail($parsed['raw_id']);
            AktivitasLogger::deleted('pengeluaran', 'Menghapus transaksi pengeluaran', $record->nama_pengeluaran . ' - Rp ' . number_format($record->nominal, 0, ',', '.'), $record);
            $record->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Transaksi berhasil dihapus dan seluruh laporan telah disinkronkan.'
        ]);
    }

    // ==========================================
    // HELPER METHODS
    // ==========================================

    private function parseCompositeId($id, $jenisHint = null): array
    {
        if (str_starts_with($id, 'in_')) {
            return ['jenis' => 'pemasukan', 'raw_id' => (int) substr($id, 3)];
        }
        if (str_starts_with($id, 'out_')) {
            return ['jenis' => 'pengeluaran', 'raw_id' => (int) substr($id, 4)];
        }

        $jenis = $jenisHint ?: 'pemasukan';
        return ['jenis' => $jenis, 'raw_id' => (int) $id];
    }

    private function deducePemasukanTipe($kodeAkun, $nama): string
    {
        return match ($kodeAkun) {
            '4-10001' => 'tiket_masuk',
            '4-10002' => 'donasi',
            '4-10003' => 'sponsor',
            default   => 'lainnya',
        };
    }

    private function deducePengeluaranKategori($kodeAkun, $nama): string
    {
        return match ($kodeAkun) {
            '5-10001' => 'gaji',
            '5-10002' => 'operasional',
            '5-10003' => 'maintenance',
            '5-10004' => 'utility',
            default   => 'lainnya',
        };
    }
}
