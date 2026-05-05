<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Absensi;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel; 
use Barryvdh\DomPDF\Facade\Pdf;    

class AbsensiAdminController extends Controller
{
    /**
     * ======================================================
     * LAPORAN KEHADIRAN (ADMIN) - Menampilkan data di tabel
     * ======================================================
     */
    public function getReport(Request $request)
    {
        $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
        
        $configJamMasuk = Setting::getByKey('jam_masuk_kantor', '08:00:00');
        $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0); 
        $jamMasukLimit = Carbon::parse($configJamMasuk);
        $batasAlpa = $jamMasukLimit->copy()->addMinutes($menitToleransi);

        $query = Absensi::with(['user.departemen'])->whereDate('tanggal', $date);

        if ($request->departemen_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('departemen_id', $request->departemen_id);
            });
        }

        $report = $query->get()->map(function ($absensi) use ($jamMasukLimit, $batasAlpa) {
            $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
            
            $statusHari = strtoupper($absensi->status_hari ?? 'ALPA');
            $statusMasuk = '-';

            if ($jamMasuk) {
                if ($jamMasuk->greaterThan($batasAlpa)) {
                    $statusMasuk = 'TERLAMBAT (ALPA)';
                } else {
                    $statusMasuk = $jamMasuk->greaterThan($jamMasukLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
                }
            } else {
                $statusMasuk = 'TIDAK ABSEN';
            }

            return [
                'absensi_id'      => $absensi->id,
                'name'            => $absensi->user->name ?? '-',
                'nip'             => $absensi->user->nip ?? '-',
                'department_name' => $absensi->user->departemen->nama_departemen ?? 'Umum', 
                'jam_masuk'       => $absensi->jam_masuk,
                'jam_pulang'      => $absensi->jam_pulang,
                'status_hari'     => $statusHari,
                'status_masuk'    => $statusMasuk,
            ];
        });

        if ($request->status) {
            $report = $report->filter(function ($item) use ($request) {
                if ($request->status === 'ALPA') return $item['status_hari'] === 'ALPA';
                return $item['status_masuk'] === $request->status;
            })->values();
        }

        return response()->json(['success' => true, 'data' => $report]);
    }

    /**
     * ======================================================
     * EXPORT LAPORAN (EXCEL / PDF)
     * ======================================================
     */
    public function exportLaporan(Request $request)
    {
        ini_set('memory_limit', '512M');
        set_time_limit(300);

        try {
            $date = $request->date ? Carbon::parse($request->date) : Carbon::today();
            $type = $request->type ?? 'excel';
            $statusFilter = $request->status;
            $departemenId = $request->departemen_id;

            $configJamMasuk = Setting::getByKey('jam_masuk_kantor', '08:00:00');
            $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0);
            
            // Perbaikan: Menggunakan nama $jamMasukLimit agar konsisten
            $jamMasukLimit = Carbon::parse($configJamMasuk);
            $batasAlpa = $jamMasukLimit->copy()->addMinutes($menitToleransi);

            $query = Absensi::with(['user.departemen'])->whereDate('tanggal', $date);

            if ($departemenId) {
                $query->whereHas('user', function($q) use ($departemenId) {
                    $q->where('departemen_id', $departemenId);
                });
            }

            $records = $query->get();

            $dataProcessed = $records->map(function (Absensi $absensi) use ($jamMasukLimit, $batasAlpa): array {
                $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
                $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');
                $statusMasuk = '-';

                if ($jamMasuk) {
                    if ($jamMasuk->greaterThan($batasAlpa)) {
                        $statusMasuk = 'TERLAMBAT (SISTEM ALPA)';
                        $statusHari = 'HADIR'; 
                    } else {
                        $statusMasuk = $jamMasuk->greaterThan($jamMasukLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
                        $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');
                    }
                }
                
                return [
                    'name'            => ucwords(strtolower($absensi->user->name ?? '-')),
                    'nip'             => $absensi->user->nip ?? '-',
                    'department_name' => ucwords(strtolower($absensi->user->departemen->nama_departemen ?? 'Umum')),
                    'jam'             => ($absensi->jam_masuk ?? '--:--') . ' - ' . ($absensi->jam_pulang ?? '--:--'),
                    'status_hari'     => $statusHari,
                    'keterangan'      => $statusMasuk
                ];
            });

            if ($statusFilter) {
                $dataProcessed = $dataProcessed->filter(function ($item) use ($statusFilter) {
                    if (strtoupper($statusFilter) === 'ALPA') return $item['status_hari'] === 'ALPA';
                    return $item['keterangan'] === strtoupper($statusFilter);
                })->values();
            }

            if ($dataProcessed->isEmpty()) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
            }

            if ($type === 'excel') {
                return Excel::download(new class($dataProcessed) implements 
                    \Maatwebsite\Excel\Concerns\FromCollection, 
                    \Maatwebsite\Excel\Concerns\WithHeadings, 
                    \Maatwebsite\Excel\Concerns\WithMapping, 
                    \Maatwebsite\Excel\Concerns\ShouldAutoSize,
                    \Maatwebsite\Excel\Concerns\WithStyles {
                    
                    private $data;
                    public function __construct($data) { $this->data = $data; }
                    public function collection() { return $this->data; }

                    public function headings(): array { 
                        return ["NAMA KARYAWAN", "NIP", "DEPARTEMEN", "JAM MASUK - PULANG", "STATUS", "KETERANGAN"]; 
                    }

                    public function map($row): array {
                        return [
                            $row['name'], $row['nip'], $row['department_name'], $row['jam'], $row['status_hari'], $row['keterangan'],
                        ];
                    }

                    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
                        return [
                            1 => [
                                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
                                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '2D4A3E']],
                                'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                            ],
                            'A1:F' . ($this->data->count() + 1) => [
                                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
                                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
                            ],
                        ];
                    }
                }, "Laporan_Presensi_{$date->format('Y-m-d')}.xlsx");
            }

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /**
     * ======================================================
     * DETAIL ABSENSI (ADMIN)
     * ======================================================
     */
    public function show($id)
    {
        $absensi = Absensi::with(['user.departemen'])->findOrFail($id);
        
        $configJamMasuk = Setting::getByKey('jam_masuk_kantor', '08:00:00');
        $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0);
        
        $jamMasukLimit = Carbon::parse($configJamMasuk);
        $batasAlpa = $jamMasukLimit->copy()->addMinutes($menitToleransi);

        $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
        $statusMasuk = '-';
        $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');

        if ($jamMasuk) {
            if ($jamMasuk->greaterThan($batasAlpa)) {
                $statusMasuk = 'TERLAMBAT (ALPA)';
                $statusHari = 'ALPA';
            } else {
                $statusMasuk = $jamMasuk->greaterThan($jamMasukLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
                $statusHari = 'HADIR';
            }
        }

        $data = [
            'id'               => $absensi->id,
            'tanggal'          => $absensi->tanggal,
            'name'             => $absensi->user->name ?? '-',
            'nip'              => $absensi->user->nip ?? '-',
            'email'            => $absensi->user->email ?? '-',
            'nomor_hp'         => $absensi->user->nomor_hp ?? '-',
            'foto_profil'      => $absensi->user->foto_profil,
            'department_name'  => $absensi->user->departemen->nama_departemen ?? 'Umum',
            'jam_masuk'        => $absensi->jam_masuk,
            'jam_pulang'       => $absensi->jam_pulang,
            'lokasi_masuk'     => $absensi->lokasi_masuk,
            'lokasi_pulang'    => $absensi->lokasi_pulang,
            'status_hari'      => $statusHari,
            'status_masuk'     => $statusMasuk,
        ];

        return response()->json(['success' => true, 'data' => $data]);
    }

    public function destroy($id)
    {
        $absensi = Absensi::find($id);
        if (!$absensi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }
        $absensi->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $jamMasukBaru = Carbon::parse($request->jam_masuk_kantor);
        $toleransi = (int) $request->toleransi_keterlambatan;
        $batasAlpaBaru = $jamMasukBaru->copy()->addMinutes($toleransi);

        $absensiHariIni = Absensi::whereDate('tanggal', Carbon::today())->get();

        foreach ($absensiHariIni as $absen) {
            if ($absen->jam_masuk) {
                $jmKaryawan = Carbon::parse($absen->jam_masuk);
                
                if ($jmKaryawan->greaterThan($batasAlpaBaru)) {
                    $absen->status_hari = 'ALPA';
                    $absen->status_masuk = 'terlambat_alpa';
                } else {
                    $absen->status_hari = 'HADIR';
                    $absen->status_masuk = $jmKaryawan->greaterThan($jamMasukBaru) ? 'terlambat' : 'tepat_waktu';
                }
                $absen->save(); 
            }
        }

        return response()->json([
            'success' => true, 
            'message' => 'Konfigurasi disimpan & Status absensi hari ini telah disinkronkan!'
        ]);
    }

    public function getSettings()
    {
        return response()->json([
            'success' => true,
            'data' => [
                'static_qr_code'           => Setting::getByKey('static_qr_code'),
                'jam_masuk_kantor'         => Setting::getByKey('jam_masuk_kantor'),
                'jam_pulang_kantor'        => Setting::getByKey('jam_pulang_kantor'),
                'company_latitude'         => Setting::getByKey('company_latitude'),
                'company_longitude'        => Setting::getByKey('company_longitude'),
                'company_radius_meters'    => Setting::getByKey('company_radius_meters'),
                'toleransi_keterlambatan'  => Setting::getByKey('toleransi_keterlambatan', 0),
            ]
        ]);
    }
}