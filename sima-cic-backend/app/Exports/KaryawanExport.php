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
    protected $request;

    // Terima request filter dari Controller
    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $query = User::with('departemen')->where('role', 'karyawan');

        // Filter berdasarkan Departemen jika ada
        if ($this->request->has('departemen_id') && $this->request->departemen_id != '') {
            $query->where('departemen_id', $this->request->departemen_id);
        }

        // Filter berdasarkan Status Kerja jika ada
        if ($this->request->has('status_kerja') && $this->request->status_kerja != '') {
            $query->where('status_kerja', $this->request->status_kerja);
        }

        return $query->latest()->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Lengkap',
            'Email',
            'NIP',
            'Departemen',
            'Status Kerja', // Tambahan agar jelas di laporan
            'Tempat Lahir',
            'Tanggal Lahir',
            'Jenis Kelamin',
            'Nomor HP',
            'Alamat'
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
            $user->status_kerja, // Menampilkan status kerja (Kontrak/Permanent dll)
            $user->tempat_lahir,
            $user->tanggal_lahir ? (\Carbon\Carbon::parse($user->tanggal_lahir)->format('Y-m-d')) : '-',
            $user->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan',
            $user->nomor_hp ?? '-',
            $user->alamat ?? '-',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true, 
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '2D4A3E'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $lastRow = $this->rowNumber + 1;
                $range = 'A1:K' . $lastRow;

                $event->sheet->getStyle($range)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]);

                $centeredColumns = ['A', 'D', 'F', 'H', 'I'];
                foreach ($centeredColumns as $col) {
                    $event->sheet->getStyle($col . '2:' . $col . $lastRow)
                        ->getAlignment()
                        ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                }

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            },
        ];
    }
}