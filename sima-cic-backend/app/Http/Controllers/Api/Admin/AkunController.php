<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Akun;
use App\Services\AktivitasLogger;
use Illuminate\Http\Request;

class AkunController extends Controller
{
    /**
     * Display a listing of chart of accounts.
     */
    public function index(Request $request)
    {
        $query = Akun::query();

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('status')) {
            $isActive = $request->status === 'active' || $request->status === '1';
            $query->where('is_active', $isActive);
        }

        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_akun', 'like', "%{$search}%")
                  ->orWhere('kode_akun', 'like', "%{$search}%");
            });
        }

        $akuns = $query->orderBy('kode_akun', 'asc')->get();

        // Compute summary counts
        $totalAkun = Akun::count();
        $totalAset = Akun::where('kategori', 'aset')->count();
        $totalPendapatan = Akun::where('kategori', 'pendapatan')->count();
        $totalBeban = Akun::where('kategori', 'beban')->count();

        return response()->json([
            'success' => true,
            'data'    => $akuns,
            'summary' => [
                'total'      => $totalAkun,
                'aset'       => $totalAset,
                'pendapatan' => $totalPendapatan,
                'beban'      => $totalBeban,
            ]
        ]);
    }

    /**
     * Store a newly created account.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_akun'    => 'required|string|max:50|unique:akuns,kode_akun',
            'nama_akun'    => 'required|string|max:255',
            'kategori'     => 'required|string|in:aset,kewajiban,ekuitas,pendapatan,beban',
            'saldo_normal' => 'required|string|in:debit,kredit',
            'saldo_awal'   => 'nullable|numeric|min:0',
            'is_active'    => 'nullable|boolean',
            'keterangan'   => 'nullable|string',
        ]);

        $akun = Akun::create([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'kategori'     => $request->kategori,
            'saldo_normal' => $request->saldo_normal,
            'saldo_awal'   => $request->saldo_awal ?? 0,
            'is_active'    => $request->boolean('is_active', true),
            'keterangan'   => $request->keterangan,
        ]);

        AktivitasLogger::created('keuangan', 'Menambahkan akun CoA baru', $akun->kode_akun . ' - ' . $akun->nama_akun, $akun);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil ditambahkan',
            'data'    => $akun
        ], 201);
    }

    /**
     * Display the specified account.
     */
    public function show($id)
    {
        $akun = Akun::withCount(['pemasukans', 'pengeluarans'])->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => $akun
        ]);
    }

    /**
     * Update the specified account.
     */
    public function update(Request $request, $id)
    {
        $akun = Akun::findOrFail($id);

        $request->validate([
            'kode_akun'    => 'required|string|max:50|unique:akuns,kode_akun,' . $id,
            'nama_akun'    => 'required|string|max:255',
            'kategori'     => 'required|string|in:aset,kewajiban,ekuitas,pendapatan,beban',
            'saldo_normal' => 'required|string|in:debit,kredit',
            'saldo_awal'   => 'nullable|numeric|min:0',
            'is_active'    => 'nullable|boolean',
            'keterangan'   => 'nullable|string',
        ]);

        $akun->update([
            'kode_akun'    => $request->kode_akun,
            'nama_akun'    => $request->nama_akun,
            'kategori'     => $request->kategori,
            'saldo_normal' => $request->saldo_normal,
            'saldo_awal'   => $request->input('saldo_awal', $akun->saldo_awal),
            'is_active'    => $request->boolean('is_active', true),
            'keterangan'   => $request->keterangan,
        ]);

        AktivitasLogger::updated('keuangan', 'Mengubah akun CoA', $akun->kode_akun . ' - ' . $akun->nama_akun, $akun);

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil diperbarui',
            'data'    => $akun
        ]);
    }

    /**
     * Remove the specified account.
     */
    public function destroy($id)
    {
        $akun = Akun::withCount(['pemasukans', 'pengeluarans'])->findOrFail($id);

        if ($akun->pemasukans_count > 0 || $akun->pengeluarans_count > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Akun tidak dapat dihapus karena masih digunakan pada data transaksi pemasukan/pengeluaran.'
            ], 422);
        }

        AktivitasLogger::deleted('keuangan', 'Menghapus akun CoA', $akun->kode_akun . ' - ' . $akun->nama_akun, $akun);

        $akun->delete();

        return response()->json([
            'success' => true,
            'message' => 'Akun berhasil dihapus'
        ]);
    }
}
