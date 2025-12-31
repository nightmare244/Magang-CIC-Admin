<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class KaryawanExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize, WithEvents
{
    private $rowNumber = 0;

    public function collection()
    {
        return User::with('departemen')->where('role', 'karyawan')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Email',
            'NIP',
            'Departemen',
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Nomor HP',
            'Alamat',
            'Status'
        ];
    }

    public function map($user): array
    {
        $this->rowNumber++;

        return [
            $this->rowNumber,
            $user->name,
            $user->email,
            $user->nip,
            $user->departemen->nama_departemen ?? '-',
            $user->tempat_lahir,
            $user->tanggal_lahir ? $user->tanggal_lahir->format('Y-m-d') : '-',
            $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $user->nomor_hp ?? '-',
            $user->alamat ?? '-',
            $user->is_active ? 'Aktif' : 'Non-Aktif',
        ];
    }

    /**
     * Mengatur style dasar (Header)
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style untuk Baris 1 (Header)
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D4A3E'], // Warna Hijau CIC
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Mengatur style lanjutan (Border, Alignment Baris)
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $this->rowNumber + 1; // +1 karena ada header
                $range = 'A1:K' . $lastRow;

                // 1. Terapkan Border ke seluruh sel yang ada datanya
                $event->sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                // 2. Center alignment untuk kolom tertentu (No, NIP, Tgl Lahir, Jenis Kelamin, Status)
                $centeredColumns = ['A', 'D', 'G', 'H', 'K'];
                foreach ($centeredColumns as $col) {
                    $event->sheet->getStyle($col . '2:' . $col . $lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                // 3. Mengatur tinggi baris header
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            },
        ];
    }
}