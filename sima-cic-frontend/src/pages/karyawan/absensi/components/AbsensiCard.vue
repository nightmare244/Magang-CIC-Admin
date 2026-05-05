<template>
  <div class="card-cic-item group animate-fade-in-up">
    <div class="flex justify-between items-start gap-4">
      <div class="flex items-center gap-4">
        <div class="calendar-badge">
          <span class="day-name">{{ getDayName(item.tanggal) }}</span>
          <span class="day-num">{{ getDayNum(item.tanggal) }}</span>
        </div>

        <div class="flex flex-col gap-1.5">
          <h2 class="text-[13px] font-extrabold text-slate-800 dark:text-white uppercase tracking-tight leading-none">
            {{ formatDate(item.tanggal) }}
          </h2>
          <div>
            <span :class="['badge-status-cic', badgeClass(item.status_hari)]">
              {{ item.status_hari || 'Belum Presensi' }}
            </span>
          </div>
        </div>
      </div>

      <div class="flex flex-col items-end gap-2">
        <div class="time-pill group/time">
          <span class="time-label">Masuk</span>
          <span :class="item.status_masuk === 'terlambat' ? 'text-rose-500' : 'text-emerald-500'" class="time-value">
            {{ item.jam_masuk ?? "--:--" }}
          </span>
        </div>
        <div class="time-pill">
          <span class="time-label">Pulang</span>
          <span class="time-value text-slate-700 dark:text-slate-300">
            {{ item.jam_pulang ?? "--:--" }}
          </span>
        </div>
      </div>
    </div>

    <div v-if="item.foto_checkin || item.foto_checkout" class="selfie-grid">
      <div v-if="item.foto_checkin" class="selfie-item">
        <div class="img-frame">
          <img :src="imageUrl(item.foto_checkin)" class="img-obj" alt="Check-in" />
          <div class="img-overlay"><LogIn class="w-3 h-3 text-white" /></div>
        </div>
        <p class="img-caption">Selfie Masuk</p>
      </div>

      <div v-if="item.foto_checkout" class="selfie-item border-l border-slate-100 dark:border-white/5 pl-4">
        <div class="img-frame">
          <img :src="imageUrl(item.foto_checkout)" class="img-obj" alt="Check-out" />
          <div class="img-overlay bg-rose-500/40"><LogOut class="w-3 h-3 text-white" /></div>
        </div>
        <p class="img-caption">Selfie Pulang</p>
      </div>
    </div>

    <router-link
      :to="`/karyawan/absensi/${item.id}`"
      class="btn-cic-action group/btn"
    >
      <span>Lihat Rincian Aktivitas</span>
      <div class="w-7 h-7 bg-white/20 dark:bg-white/10 rounded-full flex items-center justify-center transition-transform group-hover/btn:translate-x-1">
        <ChevronRight class="w-4 h-4" />
      </div>
    </router-link>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import { ChevronRight, LogIn, LogOut } from 'lucide-vue-next';

const props = defineProps({
  item: { type: Object, required: true },
});

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
    if (s.includes('hadir')) return 'bg-emerald-500 text-white';
    if (s.includes('terlambat')) return 'bg-amber-500 text-white';
    if (s.includes('izin') || s.includes('sakit')) return 'bg-sky-500 text-white';
    return 'bg-slate-400 text-white';
}
</script>

<style scoped lang="postcss">
.card-cic-item {
    @apply bg-white dark:bg-[#111311] rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-white/5 
           transition-all duration-300 mb-5 relative overflow-hidden;
}

/* Kalender Badge */
.calendar-badge {
  @apply w-14 h-14 bg-slate-50 dark:bg-white/5 rounded-2xl flex flex-col items-center justify-center 
         border border-slate-100 dark:border-white/10 shadow-inner;
}
.day-name { @apply text-[8px] font-black uppercase text-slate-400 leading-none mb-1; }
.day-num { @apply text-lg font-black text-[#2d4a3e] dark:text-emerald-400 leading-none; }

/* Status & Time */
.badge-status-cic { @apply px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest inline-block; }

.time-pill {
  @apply flex items-center gap-3 bg-slate-50 dark:bg-white/5 px-3 py-1.5 rounded-xl border border-slate-50 dark:border-white/5;
}
.time-label { @apply text-[8px] font-bold text-slate-400 uppercase; }
.time-value { @apply text-[11px] font-black; }

/* Selfie Styling */
.selfie-grid {
  @apply mt-6 pt-5 border-t border-slate-50 dark:border-white/5 flex gap-4;
}
.selfie-item { @apply flex flex-col items-center flex-1; }
.img-frame { @apply relative w-16 h-20 rounded-2xl overflow-hidden border-2 border-white dark:border-white/10 shadow-md; }
.img-obj { @apply w-full h-full object-cover transition-transform duration-500 group-hover:scale-110; }
.img-overlay { @apply absolute inset-0 bg-emerald-500/40 flex items-center justify-center opacity-60; }
.img-caption { @apply text-[8px] font-black text-slate-400 uppercase mt-2 tracking-widest; }

/* Button Action */
.btn-cic-action {
    @apply mt-6 w-full flex items-center justify-between pl-6 pr-2 bg-[#2d4a3e] dark:bg-emerald-500/10 
           text-white dark:text-emerald-400 py-2 rounded-2xl text-[10px] font-bold uppercase tracking-widest 
           transition-all active:scale-95 shadow-lg shadow-emerald-900/10 dark:shadow-none;
}

@keyframes fadeIn-up {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeIn-up 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>