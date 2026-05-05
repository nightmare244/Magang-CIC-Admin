<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; margin: 0; padding: 0; background-color: #f4f7f6; }
        .container { max-width: 600px; margin: 20px auto; background-color: #ffffff; border-radius: 24px; overflow: hidden; border: 1px solid #e1e8e5; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05); }
        
        /* Header Style Match dengan Otorisasi Admin */
        .header { background-color: #2d4a3e; padding: 50px 20px; text-align: center; }
        .header h2 { color: #ffffff; margin: 0; font-size: 16px; letter-spacing: 4px; text-transform: uppercase; font-weight: 300; border-bottom: 1px solid rgba(16, 185, 129, 0.3); display: inline-block; padding-bottom: 10px; }
        
        .content { padding: 40px; }
        .greeting { color: #2d4a3e; font-size: 16px; font-weight: bold; margin-bottom: 10px; }
        .description { color: #64748b; font-size: 14px; line-height: 1.6; margin-bottom: 30px; }
        
        /* Info Box - Rapi & Terstruktur */
        .info-box { background-color: #f8fafc; border-radius: 20px; padding: 30px; margin-bottom: 30px; border: 1px solid #f1f5f9; }
        .table { width: 100%; border-collapse: collapse; }
        .table td { padding: 12px 0; font-size: 14px; color: #334139; vertical-align: top; border-bottom: 1px solid #f1f5f9; }
        .table tr:last-child td { border-bottom: none; }
        
        .label { font-weight: bold; width: 130px; color: #94a3b8; text-transform: uppercase; font-size: 10px; letter-spacing: 1px; }
        .value { color: #1e293b; font-weight: 600; }
        
        .status-badge { background-color: #e6f4ea; color: #1e7e34; padding: 4px 12px; border-radius: 10px; font-weight: bold; font-size: 11px; text-transform: uppercase; }
        
        /* Tombol Otoritas */
        .btn-group { text-align: center; margin-top: 40px; }
        .btn { display: inline-block; padding: 16px 35px; text-decoration: none; border-radius: 16px; font-weight: bold; font-size: 12px; margin: 10px 5px; text-transform: uppercase; letter-spacing: 1px; }
        .btn-approve { background-color: #10b981; color: #ffffff !important; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2); }
        .btn-reject { background-color: #f43f5e; color: #ffffff !important; box-shadow: 0 4px 12px rgba(244, 63, 94, 0.2); }
        
        /* Attachment Style */
        .attachment-section { text-align: center; margin: 20px 0; padding: 20px; background-color: #f0fdf4; border-radius: 16px; border: 1px dashed #bbf7d0; }
        .attachment-link { color: #166534; font-size: 12px; font-weight: bold; text-decoration: none; text-transform: uppercase; }

        .footer { background-color: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #f1f5f9; }
        .footer-text { font-size: 10px; color: #94a3b8; text-transform: uppercase; letter-spacing: 2px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>Otorisasi Izin</h2>
        </div>

        <div class="content">
            <p class="greeting">Halo Admin CIC,</p>
            <p class="description">Sistem mendeteksi permohonan izin baru. Mohon tinjau rincian data karyawan di bawah ini:</p>
            
            <div class="info-box">
                <table class="table">
                    <tr>
                        <td class="label">Karyawan</td>
                        <td class="value">{{ $izin->user->name }}</td>
                    </tr>
                    <tr>
                        <td class="label">NIP</td>
                        <td class="value">{{ $izin->user->nip ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">No. WhatsApp</td>
                        <td class="value">{{ $izin->user->nomor_hp ?? '-' }}</td>
                    </tr>
                    <tr>
                        <td class="label">Tipe Izin</td>
                        <td><span class="status-badge">{{ strtoupper($izin->tipe_izin) }}</span></td>
                    </tr>
                    <tr>
                        <td class="label">Periode</td>
                        <td class="value">{{ \Carbon\Carbon::parse($izin->tanggal_mulai)->format('d M Y') }} - {{ \Carbon\Carbon::parse($izin->tanggal_selesai)->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">Alasan</td>
                        <td class="value" style="font-style: italic; color: #64748b;">"{{ $izin->keterangan }}"</td>
                    </tr>
                </table>
            </div>

            @if($izin->file_pendukung)
            <div class="attachment-section">
                <a href="{{ config('app.url') . '/storage/' . $izin->file_pendukung }}" class="attachment-link">
                    📄 Lihat Dokumen Pendukung
                </a>
            </div>
            @endif

            <div class="btn-group">
                <p style="font-size: 11px; color: #94a3b8; margin-bottom: 20px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px;">Ambil Tindakan Cepat</p>
                <a href="{{ $urlSetuju }}" class="btn btn-approve">Setujui Izin</a>
                <a href="{{ $urlTolak }}" class="btn btn-reject">Tolak Izin</a>
            </div>
        </div>

        <div class="footer">
            <p class="footer-text">Ciwangun Indah Camp • Asset Management System</p>
        </div>
    </div>
</body>
</html>