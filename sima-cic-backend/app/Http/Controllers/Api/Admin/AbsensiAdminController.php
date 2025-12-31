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
        
        // Ambil Pengaturan
        $configJamMasuk = Setting::getByKey('jam_masuk_kantor', '08:00:00');
        $menitToleransi = (int) Setting::getByKey('toleransi_keterlambatan', 0); 
        
        $jamMasukLimit = Carbon::parse($configJamMasuk);
        // Batas Akhir sebelum dianggap ALPA
        $batasAlpa = $jamMasukLimit->copy()->addMinutes($menitToleransi);

        // Load relasi 'departemen' melalui user
        $query = Absensi::with(['user.departemen'])->whereDate('tanggal', $date);

        // Filter berdasarkan departemen_id jika dikirim dari frontend
        if ($request->departemen_id) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('departemen_id', $request->departemen_id);
            });
        }

        $absensiRecords = $query->get();

        $report = $absensiRecords->map(function ($absensi) use ($jamMasukLimit, $batasAlpa) {
            $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
            $statusMasuk = null;
            $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');

            if ($jamMasuk) {
                // Logika Evaluasi Toleransi
                if ($jamMasuk->greaterThan($batasAlpa)) {
                    $statusMasuk = 'TERLAMBAT (ALPA)';
                    $statusHari = 'ALPA'; // Override jika lewat batas toleransi
                } else {
                    $statusMasuk = $jamMasuk->greaterThan($jamMasukLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
                }
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

        // Filter status dari frontend (TEPAT WAKTU, TERLAMBAT, ALPA)
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
            
            $jamLimit = Carbon::parse($configJamMasuk);
            $batasAlpa = $jamLimit->copy()->addMinutes($menitToleransi);

            $query = Absensi::with(['user.departemen'])->whereDate('tanggal', $date);

            if ($departemenId) {
                $query->whereHas('user', function($q) use ($departemenId) {
                    $q->where('departemen_id', $departemenId);
                });
            }

            $records = $query->get();

            $dataProcessed = $records->map(function ($absensi) use ($jamLimit, $batasAlpa) {
                $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
                $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');
                $statusMasuk = '-';

                if ($jamMasuk) {
                    if ($jamMasuk->greaterThan($batasAlpa)) {
                        $statusMasuk = 'TERLAMBAT (ALPA)';
                        $statusHari = 'ALPA';
                    } else {
                        $statusMasuk = $jamMasuk->greaterThan($jamLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
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
                    if ($statusFilter === 'ALPA') return $item['status_hari'] === 'ALPA';
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

            // PDF Logic (Optional if you have a view)
            // $pdf = Pdf::loadView('pdf.laporan_absensi', compact('dataProcessed', 'date'));
            // return $pdf->download("Laporan_Presensi_{$date->format('Y-m-d')}.pdf");

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
        
        $jamLimit = Carbon::parse($configJamMasuk);
        $batasAlpa = $jamLimit->copy()->addMinutes($menitToleransi);

        $jamMasuk = $absensi->jam_masuk ? Carbon::parse($absensi->jam_masuk) : null;
        $statusMasuk = '-';
        $statusHari = strtoupper($absensi->status_hari ?? 'HADIR');

        if ($jamMasuk) {
            if ($jamMasuk->greaterThan($batasAlpa)) {
                $statusMasuk = 'TERLAMBAT (ALPA)';
                $statusHari = 'ALPA';
            } else {
                $statusMasuk = $jamMasuk->greaterThan($jamLimit) ? 'TERLAMBAT' : 'TEPAT WAKTU';
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

    /**
     * ======================================================
     * HAPUS ABSENSI (PERMANEN)
     * ======================================================
     */
    public function destroy($id)
    {
        $absensi = Absensi::find($id);
        if (!$absensi) {
            return response()->json(['success' => false, 'message' => 'Data tidak ditemukan.'], 404);
        }
        $absensi->delete();
        return response()->json(['success' => true, 'message' => 'Data berhasil dihapus.']);
    }

    /**
     * Update Pengaturan Kehadiran
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'static_qr_code'           => 'required|string',
            'jam_masuk_kantor'         => 'required',
            'jam_pulang_kantor'        => 'required',
            'company_latitude'         => 'required|numeric',
            'company_longitude'        => 'required|numeric',
            'company_radius_meters'    => 'required|integer',
            'toleransi_keterlambatan'  => 'required|integer|min:0', // <--- Tambah ini
        ]);

        foreach ($validated as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        Artisan::call('config:clear');
        Artisan::call('cache:clear');

        return response()->json(['success' => true, 'message' => 'Pengaturan diperbarui.']);
    }

    /**
     * Ambil Pengaturan Kehadiran
     */
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
                'toleransi_keterlambatan'  => Setting::getByKey('toleransi_keterlambatan', 0), // <--- Tambah ini
            ]
        ]);
    }
}