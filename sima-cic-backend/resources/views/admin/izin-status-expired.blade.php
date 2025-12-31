<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tindakan Selesai - SIMA CIC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Poppins', sans-serif; }</style>
</head>
<body class="bg-[#2d4a3e] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100">
        <div class="h-1.5 w-full bg-rose-500"></div>

        <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50">
            <img src="{{ asset('logo.png') }}" alt="Logo CIC" class="w-16 h-auto mb-2" />
            <p class="text-[8px] font-black text-[#2d4a3e] uppercase tracking-[0.4em]">Ciwangun Indah Camp</p>
        </div>

        <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-rose-50 rounded-2xl flex items-center justify-center mb-6 text-rose-500 border border-rose-100 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-tight uppercase">
                    Akses Berakhir
                </h2>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-500 mt-2 mb-4">Tindakan Sudah Diproses</p>
                
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    Maaf, pengajuan ini sudah diproses sebelumnya dengan status:
                    <span class="block font-bold text-rose-600 mt-2 uppercase text-base tracking-widest">
                       {{ $status }}
                    </span>
                </p>
            </div>

            <div class="mt-8">
                <button onclick="window.close()" class="w-full py-4 px-6 bg-slate-100 text-slate-500 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-slate-200 transition-all active:scale-95">
                    Tutup Halaman
                </button>
            </div>
        </div>

        <div class="px-8 py-5 bg-slate-50 border-t border-gray-100 flex flex-col items-center gap-1">
            <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">Manajemen Izin Karyawan</p>
            <div class="w-12 h-0.5 bg-slate-200 rounded-full mt-1"></div>
        </div>
    </div>
</body>
</html>