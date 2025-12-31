<?php

namespace App\Http\Controllers\Api\Karyawan;

use App\Http\Controllers\Controller;
use App\Models\Izin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Mail\NotifikasiIzinKaryawan;
use Illuminate\Support\Facades\Log;

class PengajuanIzinController extends Controller
{
    /**
     * Menampilkan riwayat pengajuan izin dengan fitur FILTER STATUS.
     */
    public function index(Request $request) // Tambahkan Request $request di sini
    {
        $user = Auth::user();

        // KUNCI PERBAIKAN: Gunakan query() agar bisa difilter secara dinamis
        $query = Izin::where('user_id', $user->id);

        /**
         * LOGIKA FILTER:
         * Menangkap parameter 'status' dari Vue (params: { status: ... }).
         */
        $query->when($request->status, function ($q, $status) {
            return $q->where('status', $status);
        });

        // Urutkan berdasarkan yang terbaru dan gunakan pagination
        $izins = $query->orderBy('created_at', 'desc')
                       ->paginate(10); // Gunakan paginate agar sesuai dengan komponen Pagination Vue Anda

        return response()->json($izins);
    }

    /**
     * Menyimpan pengajuan izin baru dan kirim email.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'tipe_izin' => 'required|in:sakit,izin,cuti',
            'tanggal_mulai' => 'required|date|after_or_equal:today',
            'tanggal_selesai' => 'required|date|after_or_equal:tanggal_mulai',
            'keterangan' => 'required|string|max:1000',
            'file_pendukung' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120', 
        ]);

        $user = Auth::user();
        
        // Logika cek tumpang tindih tanggal
        $isOverlapping = Izin::where('user_id', $user->id)
            ->where(function ($query) use ($validatedData) {
                $query->whereBetween('tanggal_mulai', [$validatedData['tanggal_mulai'], $validatedData['tanggal_selesai']])
                      ->orWhereBetween('tanggal_selesai', [$validatedData['tanggal_mulai'], $validatedData['tanggal_selesai']]);
            })
            ->where('status', '!=', 'ditolak')
            ->exists();

        if ($isOverlapping) {
            return response()->json([
                'message' => 'Anda sudah memiliki pengajuan izin pada rentang tanggal tersebut.'
            ], 422);
        }

        // Handle upload file
        $path = null;
        if ($request->hasFile('file_pendukung')) {
            $file = $request->file('file_pendukung');
            $extension = $file->getClientOriginalExtension();
            $fileName = 'izin_' . time() . '_' . $user->id . '.' . $extension;
            $path = $file->storeAs('file-izin', $fileName, 'public');
        }

        // Simpan ke Database
        $izin = Izin::create([
            'user_id' => $user->id,
            'tipe_izin' => $validatedData['tipe_izin'],
            'tanggal_mulai' => $validatedData['tanggal_mulai'],
            'tanggal_selesai' => $validatedData['tanggal_selesai'],
            'keterangan' => $validatedData['keterangan'],
            'file_pendukung' => $path,
            'status' => 'pending',
        ]);

        // --- PROSES KIRIM EMAIL ---
        try {
            $emailPerusahaan = 'contact.dendi020504@gmail.com'; 
            Mail::to($emailPerusahaan)->send(new NotifikasiIzinKaryawan($izin));
        } catch (\Exception $e) {
            Log::error('Gagal kirim email izin: ' . $e->getMessage());
        }

        return response()->json($izin, 201);
    }

    public function show($id)
    {
        $izin = Izin::with('user')->find($id);

        if (!$izin) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data tidak ditemukan.'
            ], 404);
        }

        if ($izin->user_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Akses ditolak.'
            ], 403);
        }

        return response()->json($izin);
    }
}