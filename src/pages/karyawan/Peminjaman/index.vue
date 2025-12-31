<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-24 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-28 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px]"></div>
      <div class="absolute -left-10 -bottom-10 w-40 h-40 bg-emerald-400/10 rounded-full blur-[60px]"></div>
      
      <div class="relative z-10 flex flex-col items-center text-center">
        <div class="w-16 h-16 bg-white/10 backdrop-blur-md rounded-2xl flex items-center justify-center mb-4 border border-white/20 shadow-inner">
          <PackageSearch class="w-8 h-8 text-emerald-300" />
        </div>
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-400 mb-2">Manajemen Aset</p>
        <h1 class="text-3xl font-bold tracking-tight">Inventaris Barang</h1>
        <p class="text-[11px] opacity-60 mt-2 font-medium italic text-emerald-50 tracking-wide">
          Ciwangun Indah Camp - Administrasi Internal
        </p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-5 animate-fade-in-up">
      
      <router-link to="/karyawan/inventaris" class="nav-card group">
        <div class="icon-box bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600">
          <Library class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight group-hover:text-emerald-600 transition-colors">
            Katalog Barang
          </h2>
          <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Lihat daftar aset tersedia untuk diajukan.</p>
        </div>
        <div class="arrow-box">
          <ChevronRight class="w-5 h-5" />
        </div>
      </router-link>

      <router-link to="/karyawan/peminjaman/keranjang" class="nav-card group">
        <div class="icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600">
          <ShoppingBag class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight group-hover:text-blue-600 transition-colors">
            Daftar Pilihan
          </h2>
          <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Review barang pilihan sebelum verifikasi.</p>
        </div>
        <div class="arrow-box">
          <ChevronRight class="w-5 h-5" />
        </div>
      </router-link>
      
      <router-link to="/karyawan/peminjaman/riwayat" class="nav-card group">
        <div class="icon-box bg-amber-50 dark:bg-amber-500/10 text-amber-600">
          <History class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight group-hover:text-amber-600 transition-colors">
            Riwayat Peminjaman
          </h2>
          <p class="text-[11px] text-slate-400 mt-1 leading-relaxed">Pantau status administrasi & pengembalian.</p>
        </div>
        <div class="arrow-box">
          <ChevronRight class="w-5 h-5" />
        </div>
      </router-link>

    </div>

    <footer class="mt-16 text-center px-10 animate-fade-in" style="animation-delay: 400ms">
      <div class="inline-flex items-center gap-3 bg-white dark:bg-[#121512] px-5 py-3 rounded-full border border-slate-100 dark:border-white/5 shadow-sm">
        <div class="w-6 h-6 bg-emerald-500 rounded-full flex items-center justify-center">
          <Info class="w-3.5 h-3.5 text-white" />
        </div>
        <p class="text-[10px] font-bold text-slate-500 dark:text-emerald-300 uppercase tracking-widest">
          Patuhi Prosedur Pengembalian Aset
        </p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { 
  PackageSearch, Library, ShoppingBag, 
  History, ChevronRight, Info 
} from 'lucide-vue-next';
</script>

<style scoped lang="postcss">
.nav-card {
    @apply bg-white dark:bg-[#121512] p-5 rounded-[2.5rem] shadow-xl border border-white 
           dark:border-white/5 flex items-center gap-5 transition-all duration-500 
           active:scale-[0.96] hover:shadow-2xl hover:-translate-y-1 cursor-pointer;
}

.icon-box {
    @apply w-16 h-16 rounded-[1.8rem] flex items-center justify-center 
           transition-all duration-500 group-hover:rotate-[10deg];
}

.arrow-box {
    @apply w-10 h-10 flex items-center justify-center rounded-full bg-slate-50 
           dark:bg-white/5 text-slate-300 group-hover:text-emerald-500 
           group-hover:bg-emerald-50 transition-all duration-300;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
</style>