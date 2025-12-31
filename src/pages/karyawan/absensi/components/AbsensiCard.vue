<template>
  <div class="card-cic-item group animate-fade-in-up">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      
      <div class="flex items-center gap-4">
        <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl flex flex-col items-center justify-center text-[#2d4a3e] dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800 shadow-sm">
          <span class="text-[9px] font-black uppercase tracking-tighter leading-none mb-1">{{ getDayName(item.tanggal) }}</span>
          <span class="text-lg font-bold leading-none">{{ getDayNum(item.tanggal) }}</span>
        </div>

        <div>
          <h2 class="font-bold text-[13px] text-slate-800 dark:text-white leading-tight uppercase tracking-wide">
            {{ formatDate(item.tanggal) }}
          </h2>
          <div class="mt-1.5">
            <span :class="['badge-cic', badgeClass(item.status_hari)]">
              {{ item.status_hari || 'Belum Presensi' }}
            </span>
          </div>
        </div>
      </div>

      <div class="flex sm:flex-col items-center sm:items-end gap-4 sm:gap-1 w-full sm:w-auto pt-3 sm:pt-0 border-t sm:border-none border-slate-100 dark:border-white/5">
        <div class="flex items-center gap-2">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Masuk:</p>
          <b :class="item.status_masuk === 'terlambat' ? 'text-rose-500' : 'text-emerald-600 dark:text-emerald-400'" class="text-xs font-black">
            {{ item.jam_masuk ?? "--:--" }}
          </b>
        </div>
        <div class="flex items-center gap-2">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Pulang:</p>
          <b class="text-xs font-black text-slate-700 dark:text-slate-300">
            {{ item.jam_pulang ?? "--:--" }}
          </b>
        </div>
      </div>
    </div>

    <div v-if="item.foto_checkin || item.foto_checkout" class="mt-5 pt-4 border-t border-slate-100 dark:border-white/5 flex gap-4">
      <div v-if="item.foto_checkin" class="flex flex-col items-center group/thumb">
        <div class="relative overflow-hidden rounded-xl border-2 border-slate-100 dark:border-white/10 shadow-sm transition-transform group-hover/thumb:scale-105">
          <img :src="imageUrl(item.foto_checkin)" class="w-16 h-20 object-cover" alt="Check-in" />
        </div>
        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 tracking-tighter">Selfie Masuk</p>
      </div>

      <div v-if="item.foto_checkout" class="flex flex-col items-center group/thumb">
        <div class="relative overflow-hidden rounded-xl border-2 border-slate-100 dark:border-white/10 shadow-sm transition-transform group-hover/thumb:scale-105">
          <img :src="imageUrl(item.foto_checkout)" class="w-16 h-20 object-cover" alt="Check-out" />
        </div>
        <p class="text-[9px] font-bold text-slate-400 uppercase mt-2 tracking-tighter">Selfie Pulang</p>
      </div>
    </div>

    <router-link
      :to="`/karyawan/absensi/${item.id}`"
      class="mt-6 btn-cic-detail group/btn"
    >
      <span>Lihat Rincian Aktivitas</span>
      <ChevronRight class="w-4 h-4 transition-transform group-hover/btn:translate-x-1" />
    </router-link>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  item: { type: Object, required: true },
});

// Computed Base URL dari ENV
const baseUrl = computed(() => {
    const url = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000";
    return url.replace(/\/$/, ""); 
});

const imageUrl = (path) => {
    if (!path) return '/default-avatar.png';
    const cleanPath = path.replace(/^\/storage\//i, '');
    return `${baseUrl.value}/storage/${cleanPath}`;
};

const formatDate = (dateString) => {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(date);
};

const getDayName = (dateStr) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'short' });
};

const getDayNum = (dateStr) => {
  const d = new Date(dateStr);
  return d.getDate();
};

function badgeClass(status) {
    const s = status ? status.toLowerCase() : '';
    if (s.includes('hadir')) return 'bg-emerald-500 text-white border-emerald-400';
    if (s.includes('terlambat')) return 'bg-amber-500 text-white border-amber-400';
    if (s.includes('izin') || s.includes('sakit')) return 'bg-sky-500 text-white border-sky-400';
    return 'bg-slate-400 text-white border-slate-300';
}
</script>

<style scoped lang="postcss">
.card-cic-item {
    @apply bg-white dark:bg-[#121512] rounded-[2.5rem] p-6 shadow-lg border border-slate-50 dark:border-white/5 
           transition-all duration-300 hover:shadow-xl active:scale-[0.98];
}

.badge-cic {
    @apply px-4 py-1 rounded-full text-[9px] font-black uppercase tracking-widest inline-block shadow-sm;
}

.btn-cic-detail {
    @apply w-full flex items-center justify-center gap-2 bg-slate-50 dark:bg-white/5 text-[#2d4a3e] dark:text-emerald-500 
           py-4 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] border border-slate-100 
           dark:border-white/5 transition-all hover:bg-[#2d4a3e] hover:text-white;
}

@keyframes fadeIn-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeIn-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>