<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pengeluaran;
use App\Services\AktivitasLogger;
use Illuminate\Http\Request;

class PengeluaranController extends Controller
{
    /**
     * Display a listing of pengeluarans.
     */
    public function index(Request $request)
    {
        $query = Pengeluaran::query();

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal_pengeluaran, '%Y-%m') = ?", [$request->bulan]);
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        $pengeluarans = $query->orderBy('tanggal_pengeluaran', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $pengeluarans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pengeluaran'    => 'required|string|max:255',
            'kategori'            => 'required|string|in:gaji,operasional,maintenance,utility,lainnya',
            'nominal'             => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'keterangan'          => 'nullable|string',
        ]);

        $pengeluaran = Pengeluaran::create([
            'nama_pengeluaran'    => $request->nama_pengeluaran,
            'kategori'            => $request->kategori,
            'nominal'             => $request->nominal,
            'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
            'keterangan'          => $request->keterangan,
        ]);

        AktivitasLogger::created('pengeluaran', 'Menambahkan pengeluaran baru', $pengeluaran->nama_pengeluaran . ' - Rp ' . number_format($pengeluaran->nominal, 0, ',', '.'), $pengeluaran);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil ditambahkan',
            'data'    => $pengeluaran
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $pengeluaran
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        $request->validate([
            'nama_pengeluaran'    => 'required|string|max:255',
            'kategori'            => 'required|string|in:gaji,operasional,maintenance,utility,lainnya',
            'nominal'             => 'required|numeric|min:0',
            'tanggal_pengeluaran' => 'required|date',
            'keterangan'          => 'nullable|string',
        ]);

        $pengeluaran->update([
            'nama_pengeluaran'    => $request->nama_pengeluaran,
            'kategori'            => $request->kategori,
            'nominal'             => $request->nominal,
            'tanggal_pengeluaran' => $request->tanggal_pengeluaran,
            'keterangan'          => $request->keterangan,
        ]);

        AktivitasLogger::updated('pengeluaran', 'Mengubah data pengeluaran', $pengeluaran->nama_pengeluaran . ' - Rp ' . number_format($pengeluaran->nominal, 0, ',', '.'), $pengeluaran);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diperbarui',
            'data'    => $pengeluaran
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pengeluaran = Pengeluaran::findOrFail($id);

        AktivitasLogger::deleted('pengeluaran', 'Menghapus data pengeluaran', $pengeluaran->nama_pengeluaran . ' - Rp ' . number_format($pengeluaran->nominal, 0, ',', '.'), $pengeluaran);

        $pengeluaran->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus'
        ]);
    }
}
