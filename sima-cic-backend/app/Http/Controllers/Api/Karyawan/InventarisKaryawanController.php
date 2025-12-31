<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Carbon\Carbon;

class InventarisKaryawanController extends Controller
{
    /**
     * Tampilkan daftar inventaris yang tersedia untuk karyawan.
     */
    public function index(Request $request)
    {
        $query = Inventaris::query();

        if ($request->search) {
            $query->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
        }

        // Karyawan hanya melihat yang kuantitasnya > 0
        $items = $query->where('quantity', '>', 0)
                       ->orderBy('nama_barang', 'asc')
                       ->get();
                       
        // Catatan: Model Accessors (foto_barang_url) akan otomatis di-append
        return response()->json(['success' => true, 'data' => $items]);
    }

    /**
     * Tampilkan detail barang inventaris berdasarkan KODE BARANG.
     */
    public function show($kode_barang)
    {
        // Mencari barang berdasarkan kode_barang dan meng-eager load peminjaman aktif.
        $item = Inventaris::with(['peminjamans' => function ($query) {
            // Kita hanya ambil peminjaman yang DISetujui DAN belum dikembalikan secara fisik (tanggal_pengembalian IS NULL)
            $query->where('status', 'disetujui')
                  ->whereNull('tanggal_pengembalian')
                  ->with('user'); 
        }])
        ->where('kode_barang', $kode_barang)
        ->firstOrFail();

        // Mengambil array dasar dari Model untuk response
        $response = $item->toArray();
        $response['peminjam_aktif'] = null; // Default null

        // Cek jika ada peminjaman aktif
        if ($item->status_ketersediaan === 'dipinjam' && $item->peminjamans->isNotEmpty()) {
            $peminjamanAktif = $item->peminjamans->first(); // Ambil yang paling baru disetujui
            $user = $peminjamanAktif->user;
            
            // Format data peminjam yang sedang aktif
            $response['peminjam_aktif'] = [
                'id_peminjaman' => $peminjamanAktif->id, // ID transaksi peminjaman
                'nama' => $user->name,
                'nip' => $user->nip,
                'foto_profil' => $user->foto_profil,
                'tanggal_mulai' => $peminjamanAktif->tanggal_mulai,
                'tanggal_selesai' => $peminjamanAktif->tanggal_selesai,
                'status' => $peminjamanAktif->status,
                'keterangan' => $peminjamanAktif->keterangan,
            ];
        }
        
        // Memformat riwayat (jika ada)
        $response['riwayat'] = $item->peminjamans->map(function ($p) {
            return [
                'id' => $p->id,
                'nama' => $p->user->name,
                'nip' => $p->user->nip,
                'foto_profil' => $p->user->foto_profil,
                'tanggal_mulai' => $p->tanggal_mulai,
                'tanggal_selesai' => $p->tanggal_selesai,
                'status' => $p->status,
            ];
        });
        
        // Karena karyawan tidak memerlukan riwayat yang difilter, kita hapus relasi peminjamans asli
        unset($response['peminjamans']);


        return response()->json(['success' => true, 'data' => $response]);
    }
}