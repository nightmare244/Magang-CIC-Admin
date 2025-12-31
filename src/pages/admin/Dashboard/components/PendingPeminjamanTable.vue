<template>
  <div class="space-y-4">
    <div v-if="rows.length === 0" class="flex flex-col items-center justify-center py-10 opacity-40">
      <PackageOpen class="w-10 h-10 mb-2 text-slate-400" />
      <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Logistik Aman</p>
    </div>

    <div class="space-y-3">
      <div v-for="r in rows" :key="r.id" 
        class="group relative bg-white dark:bg-[#161a16] border border-gray-100 dark:border-gray-800 p-4 rounded-2xl transition-all hover:border-emerald-500/30">
        
        <div class="flex flex-col gap-4">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="p-2.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl">
                <Package class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
              </div>
              
              <div>
                <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight leading-tight">
                  {{ r.inventaris?.nama_barang || 'Aset HQ' }}
                </h4>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[10px] font-bold text-slate-500 uppercase">{{ r.user?.name }}</span>
                  <span class="text-[9px] text-slate-400 font-mono tracking-tighter">[{{ r.user?.nip }}]</span>
                </div>
              </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
               <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 text-[9px] font-bold rounded-lg border border-amber-100 dark:border-amber-900/30 uppercase tracking-wider">
                Pending
              </span>
              <div class="bg-slate-50 dark:bg-slate-800 px-2 py-0.5 rounded-md border border-slate-100 dark:border-slate-700">
                <span class="text-[9px] font-bold text-slate-600 dark:text-slate-400">Qty: {{ r.jumlah || 1 }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between pt-3 border-t border-gray-50 dark:border-gray-800/50">
            <div class="flex items-center gap-3">
              <span class="text-[10px] text-slate-400 flex items-center gap-1.5 italic">
                <Timer class="w-3.5 h-3.5" />
                Sewa: {{ formatDate(r.tanggal_pinjam) }} — {{ formatDate(r.tanggal_kembali) }}
              </span>
            </div>
            
            <div class="flex items-center gap-1.5">
              <div class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></div>
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter">Dalam Antrian</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Package, PackageOpen, Timer } from 'lucide-vue-next';

const props = defineProps({
  rows: { type: Array, default: () => [] }
});

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short' });
};
</script>