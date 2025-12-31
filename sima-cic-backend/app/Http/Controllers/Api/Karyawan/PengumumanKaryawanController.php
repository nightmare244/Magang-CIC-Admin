<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Pengumuman;
use App\Models\PengumumanRead;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PengumumanKaryawanController extends Controller
{
    /**
     * Menampilkan daftar pengumuman yang relevan untuk karyawan.
     * Mengambil pengumuman berdasarkan departemen atau untuk semua departemen.
     */
    public function index()
    {
        $user = Auth::user();
        $departemenId = $user->departemen_id; // Mengambil ID departemen dari user yang sedang login

        // 1. Ambil data pengumuman dengan relasi pembuat (user) dan log baca (reads)
        $pengumumans = Pengumuman::with(['user:id,name', 'reads' => function ($query) use ($user) {
                $query->where('user_id', $user->id); // Filter log baca hanya untuk user ini
            }])
            ->where(function ($query) use ($departemenId) {
                // Filter pengumuman: Target departemen cocok ATAU untuk semua (null)
                $query->where('target_departemen_id', $departemenId)
                      ->orWhereNull('target_departemen_id');
            })
            ->orderBy('created_at', 'desc') // Urutkan dari yang terbaru
            ->paginate(10); // Menggunakan paginate agar frontend menerima data 'links'

        // 2. Transformasi koleksi untuk menambahkan status 'telah_dibaca' secara reaktif
        $pengumumans->getCollection()->transform(function ($item) {
            // Jika relasi 'reads' tidak kosong, berarti user sudah konfirmasi paham
            $item->telah_dibaca = $item->reads->isNotEmpty();
            
            // Hapus relasi reads agar payload API lebih ringan dan bersih
            unset($item->reads); 
            return $item;
        });

        return response()->json($pengumumans);
    }

    /**
     * Menampilkan detail pengumuman tunggal.
     */
    public function show($id)
    {
        $user = Auth::user();
        
        $pengumuman = Pengumuman::with('user:id,name')
            ->where('id', $id)
            ->firstOrFail();

        // Cek status baca untuk detail
        $telahDibaca = PengumumanRead::where('user_id', $user->id)
            ->where('pengumuman_id', $id)
            ->exists();

        $pengumuman->telah_dibaca = $telahDibaca;

        return response()->json([
            'success' => true,
            'data'    => $pengumuman
        ]);
    }

    /**
     * Menandai pengumuman sebagai "telah dibaca" (Konfirmasi Paham).
     * Terhubung dengan tabel pengumuman_reads melalui Model PengumumanRead
     */
    public function tandaiDibaca(Request $request, $id)
    {
        $user = Auth::user();

        // Gunakan DB Transaction agar data konsisten
        return DB::transaction(function () use ($user, $id) {
            // Gunakan firstOrCreate untuk mencegah duplikasi data jika tombol diklik berkali-kali
            $read = PengumumanRead::firstOrCreate(
                [
                    'user_id'       => $user->id,
                    'pengumuman_id' => $id,
                ],
                [
                    'read_at'       => now() // Mencatat timestamp saat konfirmasi dilakukan
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Konfirmasi paham berhasil dicatat.',
                'data'    => $read
            ]);
        });
    }
}