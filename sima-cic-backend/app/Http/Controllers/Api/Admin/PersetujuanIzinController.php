<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PersetujuanIzinController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status', 'pending');
        $izins = Izin::with(['user:id,name,nip,foto_profil'])
            ->where('status', $status)
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return response()->json($izins);
    }

public function show($id)
{
    try {
        // Ambil data izin dengan relasi user secara simpel
        // Jika 'departemen' menyebabkan error, hapus dulu bagian ['user.departemen']
        $izin = Izin::with(['user.departemen'])->find($id);

        if (!$izin) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak ditemukan'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data'    => $izin
        ]);
    } catch (\Exception $e) {
        // Ini akan memunculkan pesan error asli di tab Network browser
        return response()->json([
            'success' => false,
            'message' => 'Terjadi kesalahan: ' . $e->getMessage()
        ], 500);
    }
}

    public function update(Request $request, $id)
    {
        $izin = Izin::findOrFail($id);

        $request->validate([
            'status' => 'required|in:disetujui,ditolak',
            'alasan_penolakan' => 'nullable|string',
        ]);

        DB::beginTransaction();
        try {
            $izin->status = $request->status;
            
            // Cek dulu apakah kolom 'alasan_penolakan' ada di tabel Anda agar tidak Error 500
            if (\Schema::hasColumn('izins', 'alasan_penolakan')) {
                $izin->alasan_penolakan = $request->alasan_penolakan;
            }
            
            $izin->save();

            if ($izin->status === 'disetujui') {
                $tanggalMulai = Carbon::parse($izin->tanggal_mulai);
                $tanggalSelesai = Carbon::parse($izin->tanggal_selesai);

                for ($date = $tanggalMulai; $date->lte($tanggalSelesai); $date->addDay()) {
                    Absensi::updateOrCreate(
                        ['user_id' => $izin->user_id, 'tanggal' => $date->toDateString()],
                        [
                            'status_hari' => $izin->tipe_izin,
                            'status_masuk' => null,
                            'jam_masuk' => null,
                            'jam_pulang' => null
                        ]
                    );
                }
            }

            DB::commit();
            return response()->json(['success' => true, 'data' => $izin]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}