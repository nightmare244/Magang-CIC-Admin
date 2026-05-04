<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4">
          <button 
            @click="$router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>

          <div>
            <p class="text-[10px] font-medium text-emerald-500/90 leading-none mb-1 capitalize tracking-[0.3em]">manajemen aset</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">inventaris barang</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-5 animate-fade-in-up">
      
      <router-link to="/karyawan/inventaris" class="nav-card-cic group">
        <div class="icon-box bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600">
          <Library class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-500 capitalize tracking-[0.2em] mb-1">katalog</p>
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight capitalize">
            eksplorasi barang
          </h2>
          <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">daftar aset tersedia untuk diajukan.</p>
        </div>
        <div class="text-slate-300 group-hover:text-emerald-500 transition-all duration-300">
          <ChevronRight class="w-5 h-5 group-hover:translate-x-1" />
        </div>
      </router-link>

      <router-link to="/karyawan/peminjaman/keranjang" class="nav-card-cic group">
        <div class="icon-box bg-blue-50 dark:bg-blue-500/10 text-blue-600">
          <ShoppingBag class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <p class="text-[10px] font-black text-blue-600 dark:text-blue-400 capitalize tracking-[0.2em] mb-1">keranjang</p>
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight capitalize">
            daftar pilihan
          </h2>
          <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">review barang sebelum verifikasi.</p>
        </div>
        <div class="text-slate-300 group-hover:text-blue-500 transition-all duration-300">
          <ChevronRight class="w-5 h-5 group-hover:translate-x-1" />
        </div>
      </router-link>
      
      <router-link to="/karyawan/peminjaman/riwayat" class="nav-card-cic group">
        <div class="icon-box bg-amber-50 dark:bg-amber-500/10 text-amber-600">
          <History class="w-7 h-7" />
        </div>
        <div class="flex-1 text-left">
          <p class="text-[10px] font-black text-amber-600 dark:text-amber-400 capitalize tracking-[0.2em] mb-1">arsip</p>
          <h2 class="text-base font-bold text-slate-800 dark:text-white leading-tight capitalize">
            riwayat pinjam
          </h2>
          <p class="text-[10px] text-slate-400 mt-1 font-medium tracking-wide">pantau status & pengembalian.</p>
        </div>
        <div class="text-slate-300 group-hover:text-amber-500 transition-all duration-300">
          <ChevronRight class="w-5 h-5 group-hover:translate-x-1" />
        </div>
      </router-link>

    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-[0.5em] capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { 
  PackageSearch, Library, ShoppingBag, 
  History, ChevronRight, ChevronLeft, Info 
} from 'lucide-vue-next';
</script>

<style scoped lang="postcss">
.nav-card-cic {
    @apply bg-white dark:bg-[#111311] p-5 rounded-[2.5rem] shadow-sm border border-slate-100 
           dark:border-white/5 flex items-center gap-5 transition-all duration-500 
           active:scale-[0.97] hover:shadow-md hover:border-emerald-500/20 cursor-pointer;
}

.icon-box {
    @apply w-16 h-16 rounded-[1.8rem] flex items-center justify-center 
           transition-all duration-500 group-hover:scale-110 group-hover:rotate-3;
}

.animate-fade-in-up { 
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
    opacity: 0;
}

@keyframes fadeInUp { 
    from { transform: translateY(30px); opacity: 0; } 
    to { transform: translateY(0); opacity: 1; } 
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}

* {
    -webkit-tap-highlight-color: transparent;
}
</style>