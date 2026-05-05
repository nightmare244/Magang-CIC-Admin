<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Inventaris;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class InventarisController extends Controller
{
    /**
     * Helper untuk menyimpan foto inventaris dengan penamaan unik.
     */
    protected function storeFoto(Request $request, $fileField)
    {
        $file = $request->file($fileField);
        $fileName = Str::slug($request->nama_barang) . '-' . time() . '.' . $file->getClientOriginalExtension();
        return $file->storeAs('inventaris', $fileName, 'public');
    }

    /**
     * Tampilkan daftar inventaris.
     * SINKRONISASI STOK: Mengambil relasi peminjaman untuk hitung stok keluar.
     */
    public function index(Request $request)
    {
        // PENTING: Eager load 'peminjamans' agar frontend bisa menghitung jumlah yang sedang dipinjam
        $query = Inventaris::with(['peminjamans' => function ($q) {
            $q->whereIn('status', ['disetujui', 'dipinjam']);
        }]);

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('nama_barang', 'like', '%' . $request->search . '%')
                  ->orWhere('kode_barang', 'like', '%' . $request->search . '%');
            });
        }

        $items = $query->orderBy('nama_barang', 'asc')->get();

        // Menambahkan metadata perhitungan sederhana jika diperlukan (opsional)
        return response()->json([
            'success' => true, 
            'data' => $items
        ]);
    }

    /**
     * Ambil detail inventaris.
     */
    public function show($id)
    {
        $item = Inventaris::with(['peminjamans' => function ($query) {
            $query->whereIn('status', ['disetujui', 'dipinjam'])
                  ->with('user'); 
        }])->findOrFail($id);

        $response = $item->toArray();
        $response['peminjam_aktif'] = null;

        if ($item->peminjamans->isNotEmpty()) {
            $peminjaman = $item->peminjamans->first();
            $userPeminjam = $peminjaman->user;
            
            $response['peminjam_aktif'] = [
                'name' => $userPeminjam->name,
                'nip' => $userPeminjam->nip,
                'tanggal_mulai' => $peminjaman->tanggal_mulai,
                'tanggal_selesai' => $peminjaman->tanggal_selesai,
                'keterangan' => $peminjaman->keterangan,
                'foto_profil' => $userPeminjam->foto_profil_url, 
            ];
        }

        return response()->json(['success' => true, 'data' => $response]);
    }

    /**
     * Simpan inventaris baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_satuan' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status_ketersediaan' => 'required|in:tersedia,dipinjam,tidak_tersedia',
            'foto_barang' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:10240', 
        ]);

        $path = null;
        if ($request->hasFile('foto_barang')) {
            $path = $this->storeFoto($request, 'foto_barang');
        }

        $item = Inventaris::create(array_merge($request->except('foto_barang'), [
            'foto_barang' => $path,
        ]));

        return response()->json(['success' => true, 'message' => 'Barang inventaris berhasil ditambahkan.', 'data' => $item], 201);
    }

    /**
     * Update inventaris.
     */
    public function update(Request $request, $id)
    {
        $item = Inventaris::findOrFail($id);

        $request->validate([
            'nama_barang' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'harga_satuan' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'status_ketersediaan' => 'required|in:tersedia,dipinjam,tidak_tersedia',
            'foto' => 'sometimes|nullable|image|mimes:jpg,jpeg,png,gif,webp,svg|max:10240', 
        ]);
        
        $dataToUpdate = $request->except('foto');
        $oldFoto = $item->foto_barang;

        if ($request->hasFile('foto')) {
            if ($oldFoto && Storage::disk('public')->exists($oldFoto)) {
                Storage::disk('public')->delete($oldFoto);
            }
            $dataToUpdate['foto_barang'] = $this->storeFoto($request, 'foto'); 
        }

        $item->update($dataToUpdate);

        return response()->json(['success' => true, 'message' => 'Barang inventaris berhasil diperbarui.', 'data' => $item]);
    }
    
    /**
     * Hapus inventaris.
     */
    public function destroy($id)
    {
        $item = Inventaris::findOrFail($id);
        
        if ($item->foto_barang && Storage::disk('public')->exists($item->foto_barang)) {
            Storage::disk('public')->delete($item->foto_barang);
        }
        
        $item->delete();
        return response()->json(['success' => true, 'message' => 'Barang inventaris berhasil dihapus.']);
    }
}