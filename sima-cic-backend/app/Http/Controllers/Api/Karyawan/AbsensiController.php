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
        DB::beginTransaction();

        try {
            $validated = $request->validate([
                'qr_code'   => 'required|string',
                'latitude'  => 'required|numeric',
                'longitude' => 'required|numeric',
            ]);

            // 1. Ambil Konfigurasi dari Admin
            $currentManualCode = Setting::getByKey('static_qr_code', '');
            $staticQrID        = "CIC-OFFICE-PRIMARY";
            $officeLat         = (float) Setting::getByKey('company_latitude', -6.680611);
            $officeLng         = (float) Setting::getByKey('company_longitude', 107.517056);
            $radius            = (int) Setting::getByKey('company_radius_meters', 100);
            $jamPulangKantorStr = Setting::getByKey('jam_pulang_kantor', '17:00:00');

            // 2. Validasi Kode QR / Manual
            $userInput = strtoupper(trim($validated['qr_code']));
            $isScanQR      = ($userInput === strtoupper($staticQrID));
            $isManualInput = ($userInput === strtoupper(trim($currentManualCode)));

            if (!$isScanQR && !$isManualInput) {
                return response()->json(['success' => false, 'message' => 'Kode atau QR tidak valid!'], 422);
            }

            // 3. Validasi Geofencing (Radius)
            $distance = $this->calculateDistance($officeLat, $officeLng, $validated['latitude'], $validated['longitude']);
            if ($distance > $radius) {
                return response()->json(['success' => false, 'message' => 'Di luar radius (' . round($distance) . 'm).'], 422);
            }

            $today = Carbon::today()->toDateString();
            $now = Carbon::now('Asia/Jakarta'); // Pastikan timezone sesuai
            $absensi = Absensi::where('user_id', Auth::id())->where('tanggal', $today)->first();

            if (!$absensi) {
                // --- PROSES MASUK ---
                $jamMasukStr = Setting::getByKey('jam_masuk_kantor', '08:00:00');
                $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0);
                $jamMasukLimit = Carbon::parse($jamMasukStr);
                $batasAlpaLimit = $jamMasukLimit->copy()->addMinutes($menitToleransi);

                $absensi = new Absensi();
                $absensi->user_id = Auth::id();
                $absensi->tanggal = $today;
                $absensi->jam_masuk = $now->toTimeString();
                $absensi->lokasi_masuk = $validated['latitude'] . ',' . $validated['longitude'];

                if ($now->lessThanOrEqualTo($jamMasukLimit)) {
                    $absensi->status_hari = 'HADIR';
                    $absensi->status_masuk = 'tepat_waktu';
                    $message = "Berhasil masuk tepat waktu.";
                } elseif ($now->lessThanOrEqualTo($batasAlpaLimit)) {
                    $absensi->status_hari = 'HADIR';
                    $absensi->status_masuk = 'terlambat';
                    $message = "Berhasil masuk (Terlambat).";
                } else {
                    $absensi->status_hari = 'ALPA';
                    $absensi->status_masuk = 'terlambat_alpa';
                    $message = "Masuk dicatat ALPA (Lewat toleransi $menitToleransi mnt).";
                }
                $action = 'in';
} elseif (!$absensi->jam_pulang) {
            // --- PROSES PULANG (ANTI SHIFT MALAM ERROR) ---
            
            $getSetting = Setting::where('key', 'jam_pulang_kantor')->first();
            $jamPulangKantorStr = $getSetting ? $getSetting->value : '17:00:00';
            
            $now = Carbon::now('Asia/Jakarta');
            $jamPulangLimit = Carbon::parse($jamPulangKantorStr, 'Asia/Jakarta');

            // LOGIKA KHUSUS: Jika jam pulang di-set 00:00, kita anggap itu akhir hari (23:59:59)
            // agar tidak dianggap sebagai jam 12 malam tadi pagi.
            if ($jamPulangKantorStr === '00:00:00' || $jamPulangKantorStr === '00:00') {
                $jamPulangLimit = Carbon::today('Asia/Jakarta')->endOfDay(); 
            } else {
                $jamPulangLimit->setDate($now->year, $now->month, $now->day);
            }

            // VALIDASI
            if ($now->lt($jamPulangLimit)) {
                $diff = $now->diff($jamPulangLimit);
                
                // Hitung sisa waktu agar informatif
                $sisa = "";
                if ($diff->h > 0) $sisa .= $diff->h . " jam ";
                $sisa .= $diff->i . " menit";

                return response()->json([
                    'success' => false,
                    'message' => "BELUM WAKTUNYA PULANG! Batas absen pulang adalah pukul $jamPulangKantorStr. Tunggu $sisa lagi."
                ], 403);
            }

            // Lolos validasi
            $absensi->jam_pulang = $now->toTimeString();
            $absensi->lokasi_pulang = $validated['latitude'] . ',' . $validated['longitude'];
            $message = 'Berhasil absen pulang! Hati-hati di jalan.';
            $action = 'out';
            
        } else {
                return response()->json(['success' => false, 'message' => 'Anda sudah menyelesaikan absensi hari ini.'], 422);
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
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
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
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
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
                    'id'           => $item->id,
                    'tanggal'      => $item->tanggal,
                    'jam_masuk'    => $item->jam_masuk,
                    'jam_pulang'   => $item->jam_pulang,
                    'status_hari'  => $item->status_hari,
                    'status_masuk' => $item->status_masuk,
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