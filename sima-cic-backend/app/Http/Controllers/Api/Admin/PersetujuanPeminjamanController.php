<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeminjamanInventaris;
use App\Models\Inventaris;
use App\Models\User;
use App\Services\AktivitasLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Carbon\Carbon;

class PersetujuanPeminjamanController extends Controller
{
    /**
     * Helper untuk memformat data yang dikembalikan (memastikan relasi dimuat).
     */
    protected function formatResponse(PeminjamanInventaris $peminjaman)
    {
        // Pastikan semua relasi yang digunakan di frontend dimuat
        $peminjaman->load(['user.departemen', 'inventaris', 'approver']);
        
        return [
            'id' => $peminjaman->id,
            'status' => $peminjaman->status,
            'tanggal_mulai' => $peminjaman->tanggal_mulai?->format('Y-m-d'),
            'tanggal_selesai' => $peminjaman->tanggal_selesai?->format('Y-m-d'),
            'tanggal_pengembalian' => $peminjaman->tanggal_pengembalian ? $peminjaman->tanggal_pengembalian->format('Y-m-d H:i:s') : null,
            'quantity' => $peminjaman->quantity, // Jumlah yang dipinjam
            'keterangan' => $peminjaman->keterangan,
            'alasan_penolakan' => $peminjaman->alasan_penolakan,
            'inventaris' => [
                'id' => $peminjaman->inventaris->id,
                'nama_barang' => $peminjaman->inventaris->nama_barang,
                'kode_barang' => $peminjaman->inventaris->kode_barang,
                'stok_saat_ini' => $peminjaman->inventaris->quantity, // Stok Global saat ini
            ],
            'user' => [
                'name' => $peminjaman->user->name,
                'nip' => $peminjaman->user->nip,
                'departemen' => $peminjaman->user->departemen->nama_departemen ?? 'N/A',
            ],
            'approver' => $peminjaman->approver->name ?? null,
        ];
    }
    
    /**
     * Menampilkan daftar semua pengajuan peminjaman untuk Admin.
     */
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        
        $query = PeminjamanInventaris::with(['user:id,name,nip', 'inventaris:id,nama_barang,kode_barang,quantity'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc');

        // Tambahkan fitur search (pemohon/barang)
        if ($request->search) {
             $search = $request->search;
             $query->whereHas('user', function ($q) use ($search) {
                 $q->where('name', 'like', "%{$search}%")->orWhere('nip', 'like', "%{$search}%");
             })
             ->orWhereHas('inventaris', function ($q) use ($search) {
                 $q->where('nama_barang', 'like', "%{$search}%")->orWhere('kode_barang', 'like', "%{$search}%");
             });
        }

        $peminjamans = $query->paginate(15);
        
        // Memformat data untuk list tampilan
        $formattedData = $peminjamans->through(fn ($p) => [
             'id' => $p->id,
             'status' => $p->status,
             'tanggal_mulai' => $p->tanggal_mulai?->format('Y-m-d'),
             'quantity' => $p->quantity,
             'inventaris_name' => $p->inventaris->nama_barang,
             'user_name' => $p->user->name,
             // Tambahkan field yang diperlukan oleh list view
        ]);


        return response()->json($formattedData);
    }

    /**
     * Menampilkan detail peminjaman.
     */
    public function show($id)
    {
        $peminjaman = PeminjamanInventaris::with(['user.departemen', 'inventaris', 'approver'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => $this->formatResponse($peminjaman)
        ]);
    }

    /**
     * [TINDAKAN ADMIN] Menyetujui pengajuan peminjaman.
     */
    public function approve(Request $request, $id)
    {
        $peminjaman = PeminjamanInventaris::findOrFail($id);

        if ($peminjaman->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Pengajuan sudah diproses.'
            ], 400);
        }

        $inventaris = $peminjaman->inventaris;
        
        // 1. Cek Ketersediaan Stok
        if ($inventaris->quantity < $peminjaman->quantity) {
            // Mengembalikan 400 Bad Request jika stok tidak cukup
            return response()->json([
                'success' => false,
                'message' => 'Stok tidak mencukupi untuk peminjaman ini. Tersedia: ' . $inventaris->quantity
            ], 400);
        }

        return DB::transaction(function () use ($peminjaman, $inventaris, $request) {
            
            // 2. Kurangi Stok Inventaris Utama
            $inventaris->decrement('quantity', $peminjaman->quantity);

            // 3. Update Status Peminjaman
            $peminjaman->update([
                'status' => 'disetujui',
                'approved_by_user_id' => $request->user()->id,
                'alasan_penolakan' => null,
            ]);

            // 4. Update Status Ketersediaan Barang jika stok habis
            if ($inventaris->quantity === 0) {
                $inventaris->update(['status_ketersediaan' => 'dipinjam']);
            }
            // Catatan: Jika stok sisa > 0, status_ketersediaan tetap 'tersedia'

            AktivitasLogger::approved('peminjaman', 'Menyetujui peminjaman', ($peminjaman->inventaris->nama_barang ?? 'Barang') . ' x' . $peminjaman->quantity . ' oleh ' . ($peminjaman->user->name ?? '-'), $peminjaman);

            return response()->json([
                'success' => true,
                'message' => 'Pengajuan berhasil disetujui. Stok dikurangi.',
                'data' => $this->formatResponse($peminjaman)
            ]);
        });
    }

    /**
     * [TINDAKAN ADMIN] Menolak pengajuan peminjaman.
     */
    public function reject(Request $request, $id)
    {
        $request->validate([
            'alasan_penolakan' => 'required|string|max:255',
        ]);
        
        $peminjaman = PeminjamanInventaris::findOrFail($id);

        if ($peminjaman->status !== 'pending') {
            return response()->json(['success' => false, 'message' => 'Pengajuan sudah diproses.'], 400);
        }

        $peminjaman->update([
            'status' => 'ditolak',
            'alasan_penolakan' => $request->alasan_penolakan,
            'approved_by_user_id' => $request->user()->id,
        ]);

        AktivitasLogger::rejected('peminjaman', 'Menolak peminjaman', ($peminjaman->inventaris->nama_barang ?? 'Barang') . ' oleh ' . ($peminjaman->user->name ?? '-') . '. Alasan: ' . $request->alasan_penolakan, $peminjaman);

        return response()->json([
            'success' => true,
            'message' => 'Pengajuan berhasil ditolak.',
            'data' => $this->formatResponse($peminjaman)
        ]);
    }

    /**
     * [TINDAKAN ADMIN] Menandai barang sudah dikembalikan dan mengembalikan stok.
     */
    public function returnItem($id)
    {
        $peminjaman = PeminjamanInventaris::findOrFail($id);

        if ($peminjaman->status !== 'disetujui') {
             return response()->json([
                'success' => false,
                'message' => 'Barang belum disetujui atau sudah dikembalikan.'
            ], 400);
        }

        return DB::transaction(function () use ($peminjaman) {
            
            $inventaris = $peminjaman->inventaris;
            
            // 1. Kembalikan Stok Inventaris Utama
            $inventaris->increment('quantity', $peminjaman->quantity);

            // 2. Update Status Peminjaman
            $peminjaman->update([
                'status' => 'selesai',
                'tanggal_pengembalian' => Carbon::now(),
            ]);

            // 3. Update Status Ketersediaan Barang jika stok kembali > 0
            if ($inventaris->quantity > 0 && $inventaris->status_ketersediaan !== 'tersedia') {
                $inventaris->update(['status_ketersediaan' => 'tersedia']);
            }

            AktivitasLogger::log('return', 'peminjaman', 'Pengembalian barang', ($peminjaman->inventaris->nama_barang ?? 'Barang') . ' x' . $peminjaman->quantity . ' dikembalikan oleh ' . ($peminjaman->user->name ?? '-'), $peminjaman);

            return response()->json([
                'success' => true,
                'message' => 'Barang berhasil dikembalikan. Stok diperbarui.',
                'data' => $this->formatResponse($peminjaman)
            ]);
        });
    }
}