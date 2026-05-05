<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use App\Models\PeminjamanInventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanPeminjamanController extends Controller
{
    /**
     * List peminjaman milik karyawan yang login
     */
    public function index()
    {
        $userId = Auth::id();

        $data = PeminjamanInventaris::with(['inventaris'])
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get();

        return response()->json([
            'status' => true,
            'message' => 'Data peminjaman berhasil diambil',
            'data' => $data
        ]);
    }

    /**
     * Ajukan peminjaman barang
     */
    public function store(Request $request)
    {
        $request->validate([
            'inventaris_id'   => 'required|exists:inventaris,id',
            // Memvalidasi quantity
            'quantity'        => 'required|integer|min:1', 
            'tanggal_mulai'   => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan'      => 'nullable|string',
        ]);

        $inventaris = Inventaris::findOrFail($request->inventaris_id);

        // 1. Cek apakah barang sedang tersedia (status_ketersediaan)
        if ($inventaris->status_ketersediaan !== 'tersedia') {
             return response()->json([
                'status' => false,
                'message' => 'Barang sedang tidak tersedia untuk dipinjam.'
            ], 400);
        }
        
        // 2. Cek stok (membandingkan stok yang diminta dengan stok yang ada)
        if ($inventaris->quantity < $request->quantity) {
             return response()->json([
                'status' => false,
                'message' => 'Jumlah pinjaman (' . $request->quantity . ') melebihi stok yang tersedia (' . $inventaris->quantity . ').'
            ], 400);
        }

        // 3. Buat pengajuan
        $pengajuan = PeminjamanInventaris::create([
            'user_id' => Auth::id(),
            'inventaris_id' => $request->inventaris_id,
            'quantity' => $request->quantity, // Menyimpan quantity yang diminta
            'tanggal_mulai' => $request->tanggal_mulai,
            'tanggal_selesai' => $request->tanggal_selesai,
            'keterangan' => $request->keterangan,
            'status' => 'pending',
        ]);

        // Catatan: Pengurangan stok dilakukan di Controller Persetujuan Admin setelah disetujui.

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan peminjaman berhasil dikirim',
            'data' => $pengajuan
        ], 201);
    }

    /**
     * Detail peminjaman (hanya untuk pemilik)
     */
    public function show($id)
    {
        $data = PeminjamanInventaris::with(['inventaris'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $data
        ]);
    }

    /**
     * Batalkan peminjaman (hanya jika masih pending)
     */
    public function cancel($id)
    {
        $p = PeminjamanInventaris::where('user_id', Auth::id())
            ->where('status', 'pending')
            ->findOrFail($id);
            
        // Catatan: Stok belum perlu dikembalikan karena status masih pending.

        $p->update([
            'status' => 'ditolak',
            'alasan_penolakan' => 'Dibatalkan oleh pemohon',
            'approved_by_user_id' => null
        ]);

        return response()->json([
            'status' => true,
            'message' => 'Pengajuan peminjaman berhasil dibatalkan',
            'data' => $p
        ]);
    }
    public function kembalikan($id)
    {
        // 1. Gunakan model PeminjamanInventaris (sesuai yang di-import di atas)
        // 2. Gunakan Auth::id() agar lebih konsisten dengan fungsi index/store Anda
        $peminjaman = PeminjamanInventaris::where('user_id', Auth::id())
                            ->where('id', $id)
                            ->first();

        // Cek jika data tidak ditemukan
        if (!$peminjaman) {
            return response()->json([
                'status' => false,
                'message' => 'Data peminjaman tidak ditemukan.'
            ], 404);
        }

        // Validasi: hanya bisa mengembalikan jika statusnya 'disetujui'
        if ($peminjaman->status !== 'disetujui') {
            return response()->json([
                'status' => false,
                'message' => 'Status peminjaman saat ini tidak memungkinkan untuk pengembalian.'
            ], 422);
        }

        try {
            // Mulai Database Transaction agar aman (Opsional tapi disarankan)
            \DB::beginTransaction();

            // 1. Update status peminjaman menjadi 'selesai'
            $peminjaman->update([
                'status' => 'selesai',
                'tanggal_kembali_asli' => now() 
            ]);

            // 2. KEMBALIKAN STOK KE GUDANG (Inventaris)
            // Cari barangnya
            $inventaris = Inventaris::find($peminjaman->inventaris_id);
            if ($inventaris) {
                // Tambah kembali stok sesuai jumlah yang dipinjam
                $inventaris->increment('quantity', $peminjaman->quantity);
                
                // Jika sebelumnya statusnya 'habis', ubah kembali jadi 'tersedia'
                if ($inventaris->quantity > 0) {
                    $inventaris->update(['status_ketersediaan' => 'tersedia']);
                }
            }

            \DB::commit();

            return response()->json([
                'status' => true,
                'success' => true, // Menambahkan key success agar sinkron dengan Frontend
                'message' => 'Terima kasih! Aset telah dikonfirmasi kembali dan stok telah diperbarui.'
            ]);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Terjadi kesalahan saat memproses data: ' . $e->getMessage()
            ], 500);
        }
    }
}