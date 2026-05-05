<template>
  <div class="card-cic-wrapper animate-fade-in-up">
    <div class="flex justify-between items-start mb-4">
      <div class="space-y-1">
        <h2 class="text-[14px] font-extrabold text-slate-800 dark:text-white leading-none">
          {{ formatDate(item.tanggal) }}
        </h2>
        <span :class="['badge-status-cic', badgeClass(item.status_hari)]">
          {{ item.status_hari || 'Belum Absen' }}
        </span>
      </div>

      <div class="flex gap-2">
        <div class="time-box">
          <span class="time-label">Masuk</span>
          <span :class="item.status_masuk === 'terlambat' ? 'text-rose-500' : 'text-emerald-500'" class="time-value">
            {{ item.jam_masuk ?? "--:--" }}
          </span>
        </div>
        <div class="time-box">
          <span class="time-label">Pulang</span>
          <span class="time-value text-slate-700 dark:text-slate-200">
            {{ item.jam_pulang ?? "--:--" }}
          </span>
        </div>
      </div>
    </div>

    <div v-if="item.foto_checkin || item.foto_checkout" class="foto-container-cic">
      <div v-if="item.foto_checkin" class="flex flex-col items-center gap-1 flex-1">
        <img :src="imageUrl(item.foto_checkin)" class="img-thumb-cic" alt="Check-in" />
        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Selfie Masuk</p>
      </div>
      
      <div v-if="item.foto_checkin && item.foto_checkout" class="w-px h-10 bg-slate-200 dark:bg-white/10 self-center"></div>

      <div v-if="item.foto_checkout" class="flex flex-col items-center gap-1 flex-1">
        <img :src="imageUrl(item.foto_checkout)" class="img-thumb-cic" alt="Check-out" />
        <p class="text-[8px] font-bold text-slate-400 uppercase tracking-widest">Selfie Pulang</p>
      </div>
    </div>

    <div class="flex gap-3 mt-4 pt-3 border-t border-slate-50 dark:border-white/5">
      <router-link
        :to="`/karyawan/absensi/${item.id}`"
        class="flex-1 py-3 bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-slate-300 rounded-xl text-[10px] font-bold uppercase tracking-widest text-center border border-slate-100 dark:border-white/5 active:scale-95 transition-all"
      >
        Lihat Detail
      </router-link>
      
      <button
        v-if="item.jam_masuk && !item.jam_pulang"
        @click="checkOut"
        class="flex-1 py-3 bg-[#1e332a] text-white rounded-xl text-[10px] font-bold uppercase tracking-[0.2em] shadow-lg shadow-emerald-900/20 active:scale-95 transition-all"
      >
        Check-out
      </button>
    </div>
  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';

const props = defineProps({
  item: { type: Object, required: true },
});

const baseUrl = computed(() => {
    const url = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
    return url.replace(/\/$/, ""); 
});

const imageUrl = (path) => {
    if (!path) return '/default-user-avatar.png';
    const cleanPath = path.replace(/^\/storage\//i, '');
    return `${baseUrl.value}/storage/${cleanPath}`;
};

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(date);
}

function badgeClass(status) {
    const s = status ? status.toLowerCase() : '';
    if (s.includes('hadir')) return 'bg-emerald-500 text-white';
    if (s.includes('terlambat')) return 'bg-rose-500 text-white';
    if (s.includes('izin') || s.includes('sakit')) return 'bg-sky-500 text-white';
    return 'bg-slate-400 text-white';
}

const checkOut = () => {
    if (confirm("Anda akan diarahkan ke halaman utama untuk proses Check-out melalui Scanner. Lanjutkan?")) {
        window.location.href = '/karyawan/absensi';
    }
};
</script>

<style scoped lang="postcss">
.card-cic-wrapper {
  @apply bg-white dark:bg-[#111311] p-5 rounded-[2rem] shadow-sm border border-slate-100 dark:border-white/5 mb-4;
}

.badge-status-cic {
  @apply px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-widest inline-block;
}

.time-box {
  @apply flex flex-col items-center bg-slate-50 dark:bg-white/5 px-3 py-1.5 rounded-2xl border border-slate-100 dark:border-white/5;
}

.time-label {
  @apply text-[7px] font-bold text-slate-400 uppercase tracking-tighter mb-0.5;
}

.time-value {
  @apply text-[11px] font-black;
}

.foto-container-cic {
  @apply flex gap-4 p-3 bg-slate-50 dark:bg-[#0a0c0a] rounded-2xl border border-slate-100 dark:border-white/5 mt-3;
}

.img-thumb-cic {
  @apply w-12 h-12 rounded-xl object-cover shadow-sm border-2 border-white dark:border-white/10;
}

@keyframes fadeIn-up {
  from { opacity: 0; transform: translateY(15px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeIn-up 0.5s ease-out forwards; }
</style>