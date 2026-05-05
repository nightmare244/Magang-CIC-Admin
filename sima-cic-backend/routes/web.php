<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use App\Models\Izin;

/*
|--------------------------------------------------------------------------
| Web Routes - IZIN EMAIL ACTIONS
|--------------------------------------------------------------------------
*/

// 1. Tampilan Konfirmasi SweetAlert (Halaman Perantara)
Route::get('/admin/izin/action-email', function (Request $request) {
    // Gunakan try-catch agar jika ID tidak ada tidak langsung error 404 kasar
    try {
        $izin = Izin::with('user')->findOrFail($request->id);
        
        if($izin->status !== 'pending') {
            return view('admin.izin-status-expired', ['status' => $izin->status]);
        }

        return view('admin.izin-confirmation', [
            'izin' => $izin,
            'status' => $request->status // 'disetujui' atau 'ditolak'
        ]);
    } catch (\Exception $e) {
        return "Data pengajuan tidak ditemukan.";
    }
})->name('izin.action.email');

// 2. Eksekusi Perubahan Database (Dipanggil dari tombol di SweetAlert)
Route::get('/admin/izin/process-final', function (Request $request) {
    try {
        $izin = Izin::with('user')->findOrFail($request->id);
        
        if($izin->status === 'pending') {
            $izin->update(['status' => $request->status]);
        }

        return view('admin.izin-success', [
            'name' => $izin->user->name,
            'status' => $request->status
        ]);
    } catch (\Exception $e) {
        return "Gagal memproses data.";
    }
})->name('izin.process.final');