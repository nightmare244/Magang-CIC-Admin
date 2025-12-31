<template>
  <div v-if="data" class="bg-white dark:bg-[#121512] rounded-[2rem] p-5 shadow-lg border border-slate-50 dark:border-white/5 transition-all">
    <div class="flex items-center justify-between mb-4 px-1">
      <h2 class="text-xs font-semibold text-slate-500 dark:text-slate-400">Kehadiran Hari Ini</h2>
      <Clock class="w-4 h-4 text-[#2d4a3e] opacity-20" />
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-2xl text-center border border-slate-100 dark:border-white/5">
        <div class="flex flex-col items-center">
          <LogIn class="w-4 h-4 text-emerald-500 mb-2 opacity-80" />
          <p class="text-[10px] font-medium text-slate-400 mb-1">Jam Masuk</p>
          <p class="text-lg font-bold text-slate-800 dark:text-white leading-none">
            {{ data.jam_masuk || "--:--" }}
          </p>
        </div>
      </div>

      <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-2xl text-center border border-slate-100 dark:border-white/5">
        <div class="flex flex-col items-center">
          <LogOut class="w-4 h-4 text-rose-400 mb-2 opacity-80" />
          <p class="text-[10px] font-medium text-slate-400 mb-1">Jam Pulang</p>
          <p class="text-lg font-bold text-slate-800 dark:text-white leading-none">
            {{ data.jam_pulang || "--:--" }}
          </p>
        </div>
      </div>
    </div>

    <div
      v-if="data.jam_masuk"
      class="mt-4 p-3 rounded-xl flex items-center justify-center gap-2.5 border transition-all duration-300"
      :class="{
        'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-900/10 dark:border-emerald-800/20': data.status_masuk === 'tepat_waktu',
        'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-900/10 dark:border-amber-800/20': data.status_masuk === 'terlambat',
        'bg-slate-50 text-slate-500 border-slate-200 dark:bg-white/5 dark:border-white/10': !data.status_masuk
      }"
    >
      <div class="w-1.5 h-1.5 rounded-full animate-pulse" :class="data.status_masuk === 'tepat_waktu' ? 'bg-emerald-500' : 'bg-amber-500'"></div>
      <span class="text-xs font-semibold capitalize">
        {{ data.status_masuk ? data.status_masuk.replace('_', ' ') : 'Status Tercatat' }}
      </span>
    </div>

    <div v-else class="mt-4 p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-dashed border-slate-200 dark:border-white/10 text-center">
       <p class="text-[11px] font-medium text-slate-400 italic">Silakan scan QR untuk presensi hari ini</p>
    </div>
  </div>

  <div v-else class="bg-white dark:bg-[#121512] p-6 rounded-[2rem] shadow-md text-center animate-pulse flex flex-col items-center border border-slate-50 dark:border-white/5">
    <div class="w-8 h-8 bg-slate-100 dark:bg-white/5 rounded-full mb-3"></div>
    <p class="text-[11px] font-medium text-slate-300">Sinkronisasi data...</p>
  </div>
</template>

<script setup>
import { Clock, LogIn, LogOut } from 'lucide-vue-next';

defineProps({
  data: {
    type: [Object, null],
    required: true,
  },
});
</script>

<style scoped>
/* Font Poppins diambil dari global.css */
</style>