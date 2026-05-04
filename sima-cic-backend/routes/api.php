<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Import Controllers
use App\Http\Controllers\Api\AuthController;
// Admin Controllers
use App\Http\Controllers\Api\Admin\DepartemenController;
use App\Http\Controllers\Api\Admin\UserController;
use App\Http\Controllers\Api\Admin\PersetujuanIzinController;
use App\Http\Controllers\Api\Admin\InventarisController;
use App\Http\Controllers\Api\Admin\PersetujuanPeminjamanController;
use App\Http\Controllers\Api\Admin\PengumumanAdminController;
use App\Http\Controllers\Api\Admin\DashboardController;
use App\Http\Controllers\Api\Admin\AbsensiAdminController;
use App\Http\Controllers\PemasukanController;
use App\Http\Controllers\PengeluaranController;
// Karyawan Controllers
use App\Http\Controllers\Api\Karyawan\AbsensiController;
use App\Http\Controllers\Api\Karyawan\PengajuanIzinController;
use App\Http\Controllers\Api\Karyawan\InventarisKaryawanController;
use App\Http\Controllers\Api\Karyawan\PengajuanPeminjamanController;
use App\Http\Controllers\Api\Karyawan\PengumumanKaryawanController;
use App\Http\Controllers\Api\Karyawan\DashboardKaryawanController;
use App\Http\Controllers\Api\Karyawan\ProfilController;

/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES
|--------------------------------------------------------------------------
*/
Route::post('/login', [AuthController::class, 'login']);

Route::get('/api-status', function () {
    return response()->json([
        'status' => 'success',
        'message' => 'SIMA CIC API is Operational',
        'version' => '1.0.6'
    ]);
});

/*
|--------------------------------------------------------------------------
| PROTECTED ROUTES (Wajib Login)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', [AuthController::class, 'user']);
    Route::post('/logout', [AuthController::class, 'logout']);

    /*
    |--------------------------------------------------------------------------
    | RUTE KARYAWAN
    |--------------------------------------------------------------------------
    */
    Route::prefix('karyawan')->group(function () {
        /** ABSENSI */
        Route::post('/absensi', [AbsensiController::class, 'checkInOrOut']);
        Route::get('/absensi/history', [AbsensiController::class, 'history']); 
        Route::get('/absensi/{id}', [AbsensiController::class, 'show']); 

        /** IZIN */
        Route::get('/izin', [PengajuanIzinController::class, 'index']);
        Route::post('/izin', [PengajuanIzinController::class, 'store']);
        Route::get('/izin/{id}', [PengajuanIzinController::class, 'show']);

        /** INVENTARIS */
        Route::get('/inventaris', [InventarisKaryawanController::class, 'index']);
        Route::get('/inventaris/{kode_barang}', [InventarisKaryawanController::class, 'show']);

        /** PEMINJAMAN */
        Route::get('/peminjaman', [PengajuanPeminjamanController::class, 'index']);
        Route::post('/peminjaman', [PengajuanPeminjamanController::class, 'store']);
        Route::get('/peminjaman/{id}', [PengajuanPeminjamanController::class, 'show']);
        Route::put('/peminjaman/{id}/cancel', [PengajuanPeminjamanController::class, 'cancel']);
        Route::put('/peminjaman/{id}/kembalikan', [PengajuanPeminjamanController::class, 'kembalikan']);

        /** PENGUMUMAN */
        Route::get('/pengumuman', [PengumumanKaryawanController::class, 'index']);
        Route::get('/pengumuman/{id}', [PengumumanKaryawanController::class, 'show']);
        Route::post('/pengumuman/{pengumuman}/baca', [PengumumanKaryawanController::class, 'tandaiDibaca']);

        /** PROFIL & DASHBOARD */
        Route::get('/dashboard-stats', [DashboardKaryawanController::class, 'summary']);
        Route::get('/profil', [ProfilController::class, 'show']);
        Route::put('/profil', [ProfilController::class, 'update']);
       Route::post('/profil/upload-photo', [ProfilController::class, 'uploadPhoto']);
        Route::post('/profil/ganti-password', [ProfilController::class, 'changePassword']);
    });

  /*
    |--------------------------------------------------------------------------
    | RUTE ADMIN
    |--------------------------------------------------------------------------
    */
    Route::middleware(['is.admin'])->prefix('admin')->group(function () {

        Route::get('/dashboard-summary', [DashboardController::class, 'summary']);

        // --- MASTER DATA KARYAWAN ---
        // PENTING: Rute khusus (Export/Import) HARUS di atas apiResource
        Route::get('/karyawan/export', [UserController::class, 'export']); 
        Route::post('/karyawan/import', [UserController::class, 'import']);
        Route::get('/karyawan/template', [UserController::class, 'downloadTemplate']);
        
        // Resource Karyawan
        Route::apiResource('karyawan', UserController::class)->except(['update']);
        
        Route::put('/karyawan/{user}', [UserController::class, 'update']);

        // Master Data Departemen
        Route::apiResource('departemens', DepartemenController::class);

        // --- ABSENSI ---
        Route::prefix('absensi')->group(function () {
            Route::get('/settings', [AbsensiAdminController::class, 'getSettings']);
            Route::put('/settings', [AbsensiAdminController::class, 'updateSettings']);
            Route::get('/laporan', [AbsensiAdminController::class, 'getReport']);
            Route::get('/laporan/export', [AbsensiAdminController::class, 'exportLaporan']);
            Route::get('/detail/{id}', [AbsensiAdminController::class, 'show']); 
            Route::delete('/{id}', [AbsensiAdminController::class, 'destroy']);
        });

        // --- PERSETUJUAN IZIN ---
        Route::apiResource('persetujuan-izin', PersetujuanIzinController::class)->only(['index', 'update', 'show']);

        // --- PERSETUJUAN PEMINJAMAN ---
        Route::get('/persetujuan-peminjaman', [PersetujuanPeminjamanController::class, 'index']);
        Route::get('/persetujuan-peminjaman/{id}', [PersetujuanPeminjamanController::class, 'show']);
        Route::put('/persetujuan-peminjaman/{peminjaman}/approve', [PersetujuanPeminjamanController::class, 'approve']);
        Route::put('/persetujuan-peminjaman/{peminjaman}/reject', [PersetujuanPeminjamanController::class, 'reject']);
        Route::put('/persetujuan-peminjaman/{peminjaman}/return', [PersetujuanPeminjamanController::class, 'returnItem']);

        // --- INVENTARIS ---
        Route::apiResource('inventaris', InventarisController::class);
        Route::put('/inventaris/{inventaris}/return', [InventarisController::class, 'returnItem']); 

        // --- PENGUMUMAN ---
        Route::get('/pengumuman/{id}/file-stream', [PengumumanAdminController::class, 'fileStream']);
        Route::delete('/pengumuman/{id}', [PengumumanAdminController::class, 'destroy']);
        Route::apiResource('pengumuman', PengumumanAdminController::class)->except(['destroy']);
    });
});