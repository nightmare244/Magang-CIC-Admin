<template>
  <div class="group bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 transition-all duration-300 hover:shadow-md hover:border-emerald-100 dark:hover:border-emerald-900/20 cursor-pointer relative overflow-hidden">
    
    <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
      <Package class="w-24 h-24 text-emerald-600" />
    </div>

    <div class="relative z-10 flex items-center gap-4">
      <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 dark:border-white/5 shadow-inner">
        <img 
          :src="imageUrl(peminjaman.inventaris?.foto_barang)" 
          class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110"
          @error="(e) => (e.target.src = '/img/default-inventaris.png')"
        />
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start mb-1">
          <div class="min-w-0 flex-1 pr-2">
            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-[0.2em] mb-0.5 truncate">
              {{ peminjaman.inventaris?.kode_barang || 'sku-none' }}
            </p>
            <h3 class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight truncate capitalize">
              {{ peminjaman.inventaris?.nama_barang || 'barang hilang' }}
            </h3>
          </div>
          
          <span :class="statusClass(peminjaman.status)" class="text-[8px] font-black uppercase tracking-widest px-3 py-1.5 rounded-xl border transition-colors">
            {{ peminjaman.status }}
          </span>
        </div>

        <div class="flex items-center gap-3 mt-2.5">
          <div class="flex items-center gap-1.5">
            <Layers class="w-3 h-3 text-emerald-500/60" />
            <span class="text-[10px] font-bold tracking-tight text-slate-600 dark:text-slate-400 capitalize">
              {{ peminjaman.quantity }} unit
            </span>
          </div>
          
          <div class="w-1 h-1 bg-slate-200 dark:bg-white/10 rounded-full"></div>

          <div class="flex items-center gap-1.5">
            <Calendar class="w-3 h-3 text-slate-400/60" />
            <span class="text-[10px] font-medium text-slate-400 capitalize">
              {{ formatDate(peminjaman.tanggal_mulai) }}
            </span>
          </div>
        </div>
      </div>

      <div class="text-slate-200 dark:text-white/10 group-hover:text-emerald-500 transition-colors pl-1">
        <ChevronRight class="w-5 h-5" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { Package, Calendar, Layers, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  peminjaman: {
    type: Object,
    required: true
  }
});

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const imageUrl = (path) => {
  if (!path) return '/img/default-inventaris.png';
  const cleanPath = path.replace(/^\/?storage\//i, '').replace(/^\/?public\//i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};

const statusClass = (status) => {
  const s = status?.toLowerCase();
  switch (s) {
    case 'disetujui': 
      return 'bg-emerald-500/10 text-emerald-500 border-emerald-500/20';
    case 'ditolak': 
      return 'bg-rose-500/10 text-rose-500 border-rose-500/20';
    case 'pending': 
      return 'bg-amber-500/10 text-amber-500 border-amber-500/20';
    case 'selesai': 
      return 'bg-blue-500/10 text-blue-500 border-blue-500/20';
    default: 
      return 'bg-slate-500/10 text-slate-500 border-slate-500/20';
  }
};
</script>

<style scoped>
.capitalize {
  text-transform: capitalize;
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>