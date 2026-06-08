<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemasukan;
use Illuminate\Http\Request;

class PemasukanController extends Controller
{
    /**
     * Display a listing of pemasukans.
     */
    public function index(Request $request)
    {
        $query = Pemasukan::query();

        if ($request->filled('bulan')) {
            $query->whereRaw("DATE_FORMAT(tanggal_pemasukan, '%Y-%m') = ?", [$request->bulan]);
        }

        if ($request->filled('tipe')) {
            $query->where('tipe', $request->tipe);
        }

        $pemasukans = $query->orderBy('tanggal_pemasukan', 'desc')->get();

        return response()->json([
            'success' => true,
            'data'    => $pemasukans
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_pemasukan'    => 'required|string|max:255',
            'tipe'              => 'required|string|in:tiket_masuk,donasi,sponsor,lainnya',
            'jumlah'            => 'required|integer|min:1',
            'nominal'           => 'required|numeric|min:0',
            'tanggal_pemasukan' => 'required|date',
            'keterangan'        => 'nullable|string',
        ]);

        $pemasukan = Pemasukan::create([
            'nama_pemasukan'    => $request->nama_pemasukan,
            'tipe'              => $request->tipe,
            'jumlah'            => $request->jumlah,
            'nominal'           => $request->nominal,
            'tanggal_pemasukan' => $request->tanggal_pemasukan,
            'keterangan'        => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemasukan berhasil ditambahkan',
            'data'    => $pemasukan
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $pemasukan
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $pemasukan = Pemasukan::findOrFail($id);

        $request->validate([
            'nama_pemasukan'    => 'required|string|max:255',
            'tipe'              => 'required|string|in:tiket_masuk,donasi,sponsor,lainnya',
            'jumlah'            => 'required|integer|min:1',
            'nominal'           => 'required|numeric|min:0',
            'tanggal_pemasukan' => 'required|date',
            'keterangan'        => 'nullable|string',
        ]);

        $pemasukan->update([
            'nama_pemasukan'    => $request->nama_pemasukan,
            'tipe'              => $request->tipe,
            'jumlah'            => $request->jumlah,
            'nominal'           => $request->nominal,
            'tanggal_pemasukan' => $request->tanggal_pemasukan,
            'keterangan'        => $request->keterangan,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Pemasukan berhasil diperbarui',
            'data'    => $pemasukan
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $pemasukan = Pemasukan::findOrFail($id);
        $pemasukan->delete();

        return response()->json([
            'success' => true,
            'message' => 'Pemasukan berhasil dihapus'
        ]);
    }
}
