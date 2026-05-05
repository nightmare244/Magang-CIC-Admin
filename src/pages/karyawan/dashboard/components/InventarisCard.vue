<template>
  <div 
    v-if="jumlah > 0" 
    @click="handleClick"
    class="bg-white dark:bg-[#111311] rounded-[1.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer group"
  >
    <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity duration-700 pointer-events-none">
      <Package class="w-24 h-24 text-amber-600" />
    </div>

    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-1.5 h-1.5 bg-amber-500 rounded-full"></div>
          <h2 class="text-xs font-semibold text-slate-600 dark:text-amber-400">Aset perusahaan</h2>
        </div>
        
        <div class="px-2 py-0.5 bg-amber-50 dark:bg-amber-500/10 rounded-lg">
          <span class="text-[10px] font-medium text-amber-600 dark:text-amber-400">
            {{ jumlah }} Unit aktif
          </span>
        </div>
      </div>

      <div class="flex items-start gap-4">
        <div class="relative flex-shrink-0">
          <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/20 rounded-xl flex items-center justify-center text-amber-600 z-20 relative transition-transform duration-500">
            <Box v-if="jumlah === 1" class="w-6 h-6" />
            <Layers v-else class="w-6 h-6" />
          </div>
          
          <div v-if="jumlah > 1" class="absolute top-1 left-1 w-12 h-12 bg-amber-200/30 dark:bg-amber-800/10 rounded-xl z-10"></div>
        </div>

        <div class="flex-1 overflow-hidden text-left">
          <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
            Tanggung jawab aset
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 font-normal leading-relaxed">
            <template v-if="jumlah === 1">
              Kamu meminjam 1 aset. Pastikan kondisi barang tetap terjaga baik.
            </template>
            <template v-else>
              Ada {{ jumlah }} aset dalam pengawasanmu. Ketuk untuk lihat daftar.
            </template>
          </p>
        </div>
      </div>

      <div class="mt-5 pt-4 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <Info class="w-4 h-4 text-slate-400" />
          <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
            Detail inventaris
          </span>
        </div>
        
        <div class="text-amber-600 dark:text-amber-400">
          <ChevronRight class="w-5 h-5" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Package, Box, Info, ChevronRight, Layers } from 'lucide-vue-next';

const props = defineProps({
  jumlah: {
    type: Number,
    default: 0
  }
});

const emit = defineEmits(['click']);

const handleClick = () => {
  emit('click');
};
</script>

<style scoped>
/* Optimasi Mobile: Mencegah highlight biru bawaan browser */
div {
  -webkit-tap-highlight-color: transparent;
}

/* Transisi halus sesuai tema Grab */
* {
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}
</style>