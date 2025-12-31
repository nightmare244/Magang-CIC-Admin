<?php

namespace App\Mail;

use App\Models\Izin;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Storage;

class NotifikasiIzinKaryawan extends Mailable
{
    use Queueable, SerializesModels;

    public $izin;
    public $urlSetuju;
    public $urlTolak;

    /**
     * Create a new message instance.
     */
    public function __construct(Izin $izin)
    {
        $this->izin = $izin;
        
        /** * Mengarahkan link ke route perantara untuk memunculkan Modal/SweetAlert Konfirmasi.
         * Pastikan route ini terdaftar di routes/web.php atau routes/api.php.
         */
        $this->urlSetuju = url("/admin/izin/action-email?id={$izin->id}&status=disetujui");
        $this->urlTolak = url("/admin/izin/action-email?id={$izin->id}&status=ditolak");
    }

    /**
     * Build the message.
     */
public function build()
{
    $email = $this->subject('🔔 Pengajuan Izin Baru: ' . $this->izin->user->name)
                  ->view('emails.notifikasi-izin');

    if ($this->izin->file_pendukung && Storage::disk('public')->exists($this->izin->file_pendukung)) {
        // PERBAIKAN: Gunakan PATHINFO_EXTENSION
        $extension = pathinfo($this->izin->file_pendukung, PATHINFO_EXTENSION);
        
        $email->attachFromStorageDisk('public', $this->izin->file_pendukung, 'Lampiran_Izin.' . $extension, [
            'mime' => Storage::disk('public')->mimeType($this->izin->file_pendukung),
        ]);
    }

    return $email;
}
}