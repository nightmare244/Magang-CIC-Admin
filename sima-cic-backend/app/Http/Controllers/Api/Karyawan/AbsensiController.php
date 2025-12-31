<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class AbsensiController extends Controller
{
    /**
     * Proses Absensi Masuk atau Pulang
     */
    public function checkInOrOut(Request $request)
    {
        // Menggunakan Database Transaction untuk keamanan data
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'qr_code'   => 'required|string', // Hasil scan QR permanen atau ketik manual
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // 1. Ambil Parameter dari Tabel Settings
            $currentManualCode = Setting::getByKey('static_qr_code', ''); 
            $staticQrID        = "CIC-OFFICE-PRIMARY"; 
            
            $officeLat         = (float) Setting::getByKey('company_latitude', -6.680611);
            $officeLng         = (float) Setting::getByKey('company_longitude', 107.517056);
            $radius            = (int) Setting::getByKey('company_radius_meters', 100);

            // 2. Normalisasi Input (Hapus spasi dan paksa ke huruf besar)
            $userInput = strtoupper(trim($validated['qr_code']));

            // 3. Validasi Kode (Bisa menggunakan QR Permanen ATAU Kode Manual Admin)
            $isScanQR      = ($userInput === strtoupper($staticQrID));
            $isManualInput = ($userInput === strtoupper(trim($currentManualCode)));

            if (!$isScanQR && !$isManualInput) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kode atau QR tidak valid! Silakan gunakan QR resmi atau kode terbaru.'
                ], 422);
            }

            // 4. Validasi Jarak (Geofencing)
            $distance = $this->calculateDistance($officeLat, $officeLng, $validated['latitude'], $validated['longitude']);
            
            if ($distance > $radius) {
                return response()->json([
                    'success' => false,
                    'message' => 'Gagal! Anda berada di luar radius kantor (' . round($distance) . ' meter).'
                ], 422);
            }

            // 5. Inisialisasi Waktu & Aturan Jam Masuk (TERMASUK TOLERANSI)
            $today = Carbon::today()->toDateString();
            $now = Carbon::now();
            
            $jamMasukStr = Setting::getByKey('jam_masuk_kantor', '08:00:00');
            // Ambil menit toleransi dari setting (default 0 jika tidak ada)
            $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0);

            $jamMasukLimit = Carbon::parse($jamMasukStr); 
            // Batas maksimal untuk dianggap hadir (lewat dari ini status_hari jadi ALPA)
            $batasAlpaLimit = $jamMasukLimit->copy()->addMinutes($menitToleransi);

            // 6. Cek data absensi hari ini
            $absensi = Absensi::where('user_id', Auth::id())
                              ->where('tanggal', $today)
                              ->first();

            $action = 'in';

            if (!$absensi) {
                // --- PROSES ABSEN MASUK ---
                $absensi = new Absensi();
                $absensi->user_id = Auth::id();
                $absensi->tanggal = $today;
                $absensi->jam_masuk = $now->toTimeString();
                $absensi->lokasi_masuk = $validated['latitude'] . ',' . $validated['longitude'];
                
                // LOGIKA EVALUASI TOLERANSI & ALPA
                if ($now->greaterThan($batasAlpaLimit)) {
                    // JIKA MELEBIHI BATAS TOLERANSI
                    $absensi->status_hari = 'ALPA';
                    $absensi->status_masuk = 'terlambat';
                    $message = 'Absensi dicatat, namun Anda melewati batas toleransi (' . $menitToleransi . ' menit). Status hari ini: ALPA.';
                } else {
                    // JIKA MASIH DALAM RENTANG TOLERANSI
                    $absensi->status_hari = 'HADIR';
                    // Tentukan apakah "tepat waktu" atau "terlambat" (meski masih dianggap HADIR)
                    $absensi->status_masuk = $now->greaterThan($jamMasukLimit) ? 'terlambat' : 'tepat_waktu';
                    $message = 'Berhasil melakukan absensi masuk!';
                }
                
                $action = 'in';
            } elseif (!$absensi->jam_pulang) {
                // --- PROSES ABSEN PULANG ---
                $absensi->jam_pulang = $now->toTimeString();
                $absensi->lokasi_pulang = $validated['latitude'] . ',' . $validated['longitude'];
                
                $message = 'Berhasil melakukan absensi pulang!';
                $action = 'out';
            } else {
                // Jika sudah ada jam masuk dan jam pulang
                return response()->json([
                    'success' => false,
                    'message' => 'Anda sudah menyelesaikan absensi (Masuk & Pulang) untuk hari ini.'
                ], 422);
            }

            $absensi->save();
            DB::commit(); 

            return response()->json([
                'success' => true,
                'message' => $message,
                'action'  => $action, 
                'data' => [
                    'jam_masuk'    => $absensi->jam_masuk,
                    'jam_pulang'   => $absensi->jam_pulang,
                    'status_hari'  => $absensi->status_hari,
                    'status_masuk' => strtoupper(str_replace('_', ' ', $absensi->status_masuk))
                ]
            ]);

        } catch (\Exception $e) {
            DB::rollBack(); 
            Log::error('Absensi Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Perhitungan jarak Haversine (Meter)
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat/2) * sin($dLat/2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon/2) * sin($dLon/2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        return $earthRadius * $c;
    }

    /**
     * Riwayat Absensi User
     */
    public function history(Request $request)
    {
        try {
            $absensi = Absensi::where('user_id', Auth::id())
                ->orderBy('tanggal', 'desc')
                ->paginate(10);

            $formattedData = $absensi->getCollection()->map(function ($item) {
                return [
                    'id'       => $item->id,
                    'tanggal'  => $item->tanggal,
                    'checkin'  => $item->jam_masuk,
                    'checkout' => $item->jam_pulang,
                    'status'   => $item->status_hari,
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'data' => $formattedData,
                    'meta' => [
                        'current_page' => $absensi->currentPage(),
                        'last_page'    => $absensi->lastPage(),
                        'total'        => $absensi->total(),
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal memuat riwayat.'], 500);
        }
    }

    /**
     * Detail Absensi berdasarkan ID
     */
    public function show($id)
    {
        try {
            $absensi = Absensi::where('id', $id)->where('user_id', Auth::id())->first();

            if (!$absensi) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            return response()->json([
                'success' => true,
                'data' => [
                    'id'            => $absensi->id,
                    'tanggal'       => $absensi->tanggal,
                    'jam_masuk'     => $absensi->jam_masuk,
                    'jam_pulang'    => $absensi->jam_pulang,
                    'status_hari'   => $absensi->status_hari, 
                    'status_masuk'  => $absensi->status_masuk, 
                    'lokasi_masuk'  => $absensi->lokasi_masuk,
                    'lokasi_pulang' => $absensi->lokasi_pulang,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }
}