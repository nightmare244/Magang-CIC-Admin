<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $title }}</title>
    <style>
        /* Konfigurasi Halaman */
        @page { 
            margin: 1cm; 
            size: a4 portrait;
        }
        
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 11pt; 
            color: #000; 
            line-height: 1.2; 
            margin: 0;
            padding: 0;
        }

        /* Kop Surat Formal */
        .table-kop {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #000;
        }
        .kop-logo {
            width: 100px; /* Diperlebar untuk menampung logo besar */
            padding-bottom: 10px;
            vertical-align: middle;
        }
        /* PERUBAHAN: Logo diperbesar ke 90px */
        .kop-logo img {
            width: 90px;
            height: auto;
        }
        .kop-instansi {
            text-align: center;
            vertical-align: middle;
            padding-right: 100px; /* Offset seimbang dengan lebar logo baru */
        }
        .instansi-nama {
            font-size: 15pt;
            font-weight: bold;
            margin: 0;
            text-transform: uppercase;
        }
        .instansi-alamat {
            font-size: 9pt;
            margin: 3px 0;
        }

        /* Garis Ganda Kop */
        .kop-line-thin {
            border-top: 0.5px solid #000;
            margin-top: 2px;
            margin-bottom: 15px;
        }

        .judul-laporan {
            text-align: center;
            font-size: 12pt;
            font-weight: bold;
            text-transform: uppercase;
            text-decoration: underline;
            margin-bottom: 15px;
        }

        /* Info Filter */
        .table-info {
            width: 100%;
            margin-bottom: 10px;
            font-size: 10pt;
        }
        .label-cell { width: 110px; font-weight: bold; }

        /* TABEL FLEKSIBEL (Anti-Potong) */
        .table-data { 
            width: 100%; 
            border-collapse: collapse; 
            table-layout: auto; /* Kolom menyesuaikan isi secara otomatis */
        }
        .table-data th { 
            border: 1px solid #000;
            padding: 8px 5px; 
            text-align: center; 
            text-transform: uppercase;
            font-size: 10pt;
            background-color: #f2f2f2;
            white-space: nowrap; 
        }
        .table-data td { 
            border: 1px solid #000;
            padding: 8px 5px; 
            font-size: 11pt; 
            vertical-align: middle;
            white-space: nowrap; /* Menjamin data tetap dalam satu baris */
        }

        /* Penyesuaian Perataan */
        .text-center { text-align: center; }
        .capitalize { text-transform: capitalize; }

        .footer { 
            position: fixed; 
            bottom: 0; 
            width: 100%; 
            text-align: center; 
            font-size: 9pt; 
            color: #666;
            border-top: 0.5px solid #ccc;
            padding-top: 5px;
        }
    </style>
</head>
<body>

    <table class="table-kop">
        <tr>
            <td class="kop-logo">
                {{-- Logo diperbesar --}}
                <img src="{{ public_path('logo.png') }}" alt="Logo">
            </td>
            <td class="kop-instansi">
                <div class="instansi-name">Ciwangun Indah Camp (CIC)</div>
                <div class="instansi-alamat">
                    Jl. Ciwangun Indah Camp, Parongpong, Bandung Barat, Jawa Barat<br>
                    Email: info@ciwangunindahcamp.com | Website: www.ciwangunindahcamp.com
                </div>
            </td>
        </tr>
    </table>
    <div class="kop-line-thin"></div>

    <div class="judul-laporan">Laporan Kehadiran Karyawan</div>

    <table class="table-info">
        <tr>
            <td class="label-cell">Tanggal Laporan</td>
            <td>: {{ $date }}</td>
            <td style="text-align: right; font-weight: bold;">Waktu Cetak : {{ now()->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td class="label-cell">Departemen</td>
            <td class="capitalize">: {{ $filter_dept }}</td>
            <td style="text-align: right;">Filter Status : {{ strtoupper($filter_status) }}</td>
        </tr>
    </table>

    <table class="main-table table-data">
        <thead>
            <tr>
                <th>No</th>
                <th>Karyawan</th>
                <th>NIP</th>
                <th>Departemen</th>
                <th>Jam</th>
                <th>Status</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($data as $index => $row)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td class="capitalize">{{ strtolower($row->name) }}</td>
                <td class="text-center">{{ $row->nip }}</td>
                <td class="capitalize">{{ strtolower($row->department_name) }}</td>
                <td class="text-center">
                    @if(strtoupper($row->status_hari) == 'HADIR')
                        {{ $row->jam_masuk ?? '--:--' }} - {{ $row->jam_pulang ?? '--:--' }}
                    @else
                        -
                    @endif
                </td>
                <td class="text-center" style="font-weight: bold;">{{ strtoupper($row->status_hari) }}</td>
                <td class="text-center">
                    @if(strtoupper($row->status_hari) == 'HADIR')
                        {{ strtoupper($row->status_masuk ?? 'TEPAT WAKTU') }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="7" class="text-center" style="padding: 20px;">Data tidak ditemukan.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Halaman 1 dari 1 - Sistem Absensi Digital Ciwangun Indah Camp
    </div>

</body>
</html>