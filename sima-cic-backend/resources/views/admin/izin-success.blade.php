<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Otorisasi Berhasil - SIMA CIC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-[#2d4a3e] flex items-center justify-center min-h-screen p-4">
    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 animate-in fade-in zoom-in duration-500">
        <div class="h-1.5 w-full bg-emerald-500"></div>

        <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50">
            <img src="{{ asset('logo.png') }}" alt="Logo CIC" class="w-16 h-auto drop-shadow-sm mb-2" />
            <p class="text-[8px] font-black text-[#2d4a3e] uppercase tracking-[0.4em]">Ciwangun Indah Camp</p>
        </div>

        <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center">
                <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mb-6 text-emerald-600 border border-emerald-100 shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                    </svg>
                </div>

                <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-tight uppercase">
                    Otorisasi Berhasil
                </h2>
                <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600 mt-2 mb-4">Persetujuan Selesai</p>
                
                <p class="text-sm text-slate-500 leading-relaxed font-medium">
                    Pengajuan izin atas nama 
                    <span class="block font-bold text-[#2d4a3e] mt-2 italic text-base">
                        {{ $name }}
                    </span>
                    telah berhasil <span class="font-bold text-{{ $status == 'disetujui' ? 'emerald' : 'rose' }}-600 uppercase">{{ $status }}</span>.
                </p>
            </div>

            <div class="mt-8">
                <button onclick="window.close()" class="w-full py-4 px-6 bg-[#2d4a3e] text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-xl shadow-[#2d4a3e]/20 hover:bg-[#385b4d] transition-all active:scale-95">
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