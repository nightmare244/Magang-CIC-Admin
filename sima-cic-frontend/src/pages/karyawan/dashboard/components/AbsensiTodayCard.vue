<template>
  <div v-if="data" class="bg-white dark:bg-[#111311] rounded-[1.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 transition-all">
    
    <div class="flex items-center justify-between mb-5 px-1">
      <div class="flex items-center gap-2">
        <Clock class="w-4 h-4 text-emerald-500 opacity-60" />
        <h2 class="text-xs font-semibold text-slate-600 dark:text-slate-400">Presensi hari ini</h2>
      </div>
      <div v-if="data.jam_masuk" class="w-1.5 h-1.5 bg-emerald-500 rounded-full animate-pulse"></div>
    </div>

    <div class="grid grid-cols-2 gap-3">
      <div class="bg-slate-50 dark:bg-white/[0.03] p-4 rounded-2xl border border-slate-100 dark:border-white/5 text-center">
        <div class="flex flex-col items-center">
          <div class="w-8 h-8 bg-emerald-100 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center mb-2">
            <LogIn class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
          </div>
          <p class="text-[10px] font-medium text-slate-400 mb-1">Masuk</p>
          <p class="text-lg font-bold text-slate-800 dark:text-white leading-none">
            {{ data.jam_masuk || "--:--" }}
          </p>
        </div>
      </div>

      <div class="bg-slate-50 dark:bg-white/[0.03] p-4 rounded-2xl border border-slate-100 dark:border-white/5 text-center">
        <div class="flex flex-col items-center">
          <div class="w-8 h-8 bg-rose-100 dark:bg-rose-500/10 rounded-xl flex items-center justify-center mb-2">
            <LogOut class="w-4 h-4 text-rose-500" />
          </div>
          <p class="text-[10px] font-medium text-slate-400 mb-1">Pulang</p>
          <p class="text-lg font-bold text-slate-800 dark:text-white leading-none">
            {{ data.jam_pulang || "--:--" }}
          </p>
        </div>
      </div>
    </div>

    <div v-if="data.jam_masuk || data.status_hari !== 'BELUM ABSEN'" class="mt-5 flex flex-col items-center gap-3">
      <div :class="badgeClass" class="px-5 py-1.5 rounded-lg text-[11px] font-bold border transition-all duration-500">
        {{ formatStatus(data.status_hari) }}
      </div>

      <div v-if="data.status_masuk && data.status_masuk !== '-'" class="flex items-center gap-2">
        <div class="w-1 h-1 rounded-full" :class="isLate ? 'bg-rose-500' : 'bg-emerald-500'"></div>
        <span class="text-[10px] font-medium italic" 
              :class="isLate ? 'text-rose-500' : 'text-emerald-500'">
          {{ data.status_masuk.replace('_', ' ').toLowerCase() }}
        </span>
      </div>
    </div>

    <div v-else class="mt-5 p-4 bg-slate-50 dark:bg-white/[0.03] rounded-2xl border border-dashed border-slate-200 dark:border-white/10 text-center">
       <p class="text-[10px] text-slate-400 font-medium leading-relaxed">
         Sistem belum mencatat presensi kamu.<br>Silakan scan QR di lokasi kerja.
       </p>
    </div>
  </div>

  <div v-else class="bg-white dark:bg-[#111311] p-8 rounded-[1.5rem] text-center flex flex-col items-center border border-slate-100 dark:border-white/5">
    <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-2xl mb-3 animate-pulse"></div>
    <p class="text-xs font-medium text-slate-400 animate-pulse">Menyelaraskan data...</p>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Clock, LogIn, LogOut } from 'lucide-vue-next';

const props = defineProps({
  data: {
    type: [Object, null],
    required: true,
  },
});

// Helper untuk format teks status agar Sentence Case
const formatStatus = (text) => {
  if (!text) return '';
  return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
};

// Deteksi keterlambatan
const isLate = computed(() => {
  const s = props.data?.status_masuk?.toUpperCase() || '';
  return s.includes('TERLAMBAT') || s.includes('ALPA');
});

// Warna Badge Status
const badgeClass = computed(() => {
  const s = props.data?.status_hari?.toUpperCase() || 'ALPA';
  
  if (s === 'HADIR') {
    return 'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-100/50 dark:border-emerald-500/20';
  } 
  
  if (s === 'ALPA') {
    return 'bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border-rose-100/50 dark:border-rose-500/20';
  }
  
  if (['IZIN', 'SAKIT', 'CUTI'].includes(s)) {
    return 'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400 border-sky-100/50 dark:border-sky-500/20';
  }

  return 'bg-slate-50 text-slate-500 dark:bg-white/5 dark:text-slate-400 border-slate-200 dark:border-white/10';
});
</script>