<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class KaryawanTemplateExport implements FromArray, WithHeadings, WithStyles, ShouldAutoSize
{
    /**
     * Data Contoh
     */
    public function array(): array
    {
        return [
            [
                'Ahmad Subagja', 
                'ahmad.subagja@email.com', 
                'Bandung', 
                '1998-05-20', 
                'L'
            ]
        ];
    }

    /**
     * Header Kolom
     */
    public function headings(): array
    {
        return [
            'nama',
            'email',
            'tempat_lahir',
            'tanggal_lahir',
            'jenis_kelamin'
        ];
    }

    /**
     * Styling Premium
     */
    public function styles(Worksheet $sheet)
    {
        // Style Header (Baris 1)
        $sheet->getStyle('1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '2D4A3E'], // Hijau CIC
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
            ],
        ]);

        // Style Baris Contoh (Baris 2)
        $sheet->getStyle('A2:E2')->applyFromArray([
            'font' => ['italic' => true, 'color' => ['rgb' => '64748B']],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'E2E8F0'],
                ],
            ],
        ]);

        return [];
    }
}