<?php
namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Aktivitas; // Pastikan kamu punya model untuk aktivitas
use Illuminate\Http\Request;

class AktivitasController extends Controller
{
    /**
     * Ambil aktivitas terbaru (terakhir 24 jam)
     */
    public function index()
    {
        $aktivitas = Aktivitas::where('created_at', '>=', now()->subDay()) // Ambil aktivitas terakhir 24 jam
                            ->orderBy('created_at', 'desc')
                            ->get(['title', 'detail', 'created_at as time']);
        
        return response()->json($aktivitas);
    }
}
