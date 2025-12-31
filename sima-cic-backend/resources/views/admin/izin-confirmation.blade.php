<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Otorisasi - SIMA CIC</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .swal2-popup { border-radius: 2.5rem !important; padding: 0 0 1.5rem 0 !important; overflow: hidden !important; }
    </style>
</head>
<body class="bg-[#2d4a3e] flex items-center justify-center min-h-screen p-6">
    <div class="text-center text-white">
        <h2 class="text-xl font-light tracking-[0.4em] uppercase mb-8 border-b border-emerald-500/30 pb-4">
            Otorisasi Izin
        </h2>
        <div class="w-10 h-10 border-4 border-emerald-400 border-t-transparent rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] uppercase tracking-[0.3em] opacity-60">Menyiapkan Panel Konfirmasi...</p>
    </div>

    <script>
        window.onload = function() {
            Swal.fire({
                title: '',
                html: `
                    <div class="h-1.5 w-full bg-{{ $status == 'disetujui' ? 'emerald-500' : 'rose-500' }} mb-8"></div>
                    
                    <div class="flex flex-col items-center mb-6">
                        <img src="{{ asset('logo.png') }}" alt="Logo CIC" class="w-16 h-auto mb-2" />
                        <p class="text-[8px] font-black text-[#2d4a3e] uppercase tracking-[0.4em]">Ciwangun Indah Camp</p>
                    </div>

                    <div class="px-8 text-center">
                        <h2 class="text-xl font-bold text-slate-800 tracking-tight leading-tight uppercase">
                            Konfirmasi {{ ucfirst($status) }}
                        </h2>
                        <p class="text-[9px] font-black uppercase tracking-[0.2em] text-{{ $status == 'disetujui' ? 'emerald-600' : 'rose-600' }} mt-2 mb-6">
                            Otoritas Administrasi
                        </p>

                        <div class="text-left text-sm space-y-3 border-t border-slate-100 pt-6 mt-2">
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">Karyawan</span>
                                <span class="text-slate-700 font-bold text-sm">{{ $izin->user->name }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">NIP</span>
                                <span class="text-slate-600 font-semibold">{{ $izin->user->nip ?? '-' }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-slate-50 pb-4">
                                <span class="text-slate-400 font-bold uppercase text-[9px] tracking-wider">WhatsApp</span>
                                <span class="text-slate-600 font-semibold">{{ $izin->user->nomor_hp ?? '-' }}</span>
                            </div>
                            <div class="pt-4 bg-slate-50 p-4 rounded-2xl border border-slate-100">
                                <p class="text-center italic text-slate-500 text-xs leading-relaxed">
                                    "{{ $izin->keterangan }}"
                                </p>
                            </div>
                        </div>
                    </div>
                `,
                icon: undefined, // Kita hilangkan icon default karena sudah pakai header custom
                showCancelButton: true,
                confirmButtonColor: '{{ $status == "disetujui" ? "#2d4a3e" : "#f43f5e" }}',
                cancelButtonColor: '#94a3b8',
                confirmButtonText: 'Ya, Eksekusi Sekarang',
                cancelButtonText: 'Batalkan',
                reverseButtons: true,
                background: '#ffffff',
                customClass: {
                    confirmButton: 'rounded-2xl px-8 py-4 font-bold uppercase text-[10px] tracking-widest shadow-xl active:scale-95 transition-all mb-2',
                    cancelButton: 'rounded-2xl px-8 py-4 font-bold uppercase text-[10px] tracking-widest active:scale-95 transition-all mb-2'
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('izin.process.final') }}?id={{ $izin->id }}&status={{ $status }}";
                } else {
                    if (window.history.length > 1) {
                        window.history.back();
                    } else {
                        window.close();
                    }
                }
            })
        };
    </script>
</body>
</html>