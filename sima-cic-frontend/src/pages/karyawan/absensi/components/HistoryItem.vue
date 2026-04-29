<template>
  <div class="card-cic-wrapper animate-fade-in">
    <div class="flex flex-col gap-4">
      
      <div class="flex justify-between items-start">
        <div>
          <h2 class="text-[14px] font-extrabold text-slate-800 dark:text-white leading-none">
            {{ formatDate(item.tanggal) }}
          </h2>
          <div class="mt-2">
            <span :class="['badge-status-cic', badgeClass(item.status_hari)]">
              {{ item.status_hari || 'Belum Absen' }}
            </span>
          </div>
        </div>

        <div class="flex gap-3">
          <div class="text-center bg-slate-50 dark:bg-white/5 px-3 py-2 rounded-2xl border border-slate-100 dark:border-white/5">
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mb-1">Masuk</p>
            <p :class="item.status_masuk === 'terlambat' ? 'text-rose-500' : 'text-emerald-500'" class="text-[12px] font-black">
              {{ item.jam_masuk ?? "--:--" }}
            </p>
          </div>
          <div class="text-center bg-slate-50 dark:bg-white/5 px-3 py-2 rounded-2xl border border-slate-100 dark:border-white/5">
            <p class="text-[8px] font-bold text-slate-400 uppercase tracking-tighter mb-1">Pulang</p>
            <p class="text-[12px] font-black text-slate-800 dark:text-white">
              {{ item.jam_pulang ?? "--:--" }}
            </p>
          </div>
        </div>
      </div>

      <div v-if="item.foto_checkin || item.foto_checkout" class="flex gap-4 p-3 bg-slate-50 dark:bg-white/5 rounded-[1.5rem] border border-slate-100 dark:border-white/5">
        <div v-if="item.foto_checkin" class="flex items-center gap-3 flex-1">
          <img :src="imageUrl(item.foto_checkin)" class="img-thumb-cic" alt="In" />
          <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Selfie<br>Masuk</p>
        </div>
        <div class="w-px bg-slate-200 dark:bg-white/10 my-1"></div>
        <div v-if="item.foto_checkout" class="flex items-center gap-3 flex-1">
          <img :src="imageUrl(item.foto_checkout)" class="img-thumb-cic" alt="Out" />
          <p class="text-[9px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Selfie<br>Pulang</p>
        </div>
      </div>

      <div class="flex gap-3 pt-2">
        <router-link
          :to="`/karyawan/absensi/${item.id}`"
          class="flex-1 text-center py-3 bg-white dark:bg-white/5 text-slate-600 dark:text-slate-300 rounded-xl text-[11px] font-bold border border-slate-200 dark:border-white/10 active:scale-95 transition-all shadow-sm"
        >
          Rincian Detail
        </router-link>
        
        <button
          v-if="item.jam_masuk && !item.jam_pulang"
          @click="checkOut"
          class="flex-1 py-3 bg-[#1e332a] text-white rounded-xl text-[11px] font-bold shadow-lg shadow-emerald-900/20 active:scale-95 transition-all uppercase tracking-widest"
        >
          Check-out
        </button>
      </div>

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
    if (s === 'hadir') return 'badge-hadir';
    if (s === 'terlambat') return 'badge-terlambat';
    if (s === 'izin' || s === 'sakit' || s === 'cuti') return 'badge-izin';
    return 'badge-default';
}

const checkOut = () => {
    if (confirm("Gunakan scanner untuk proses Check-out resmi. Lanjutkan ke halaman Absensi?")) {
        window.location.href = '/karyawan/absensi';
    }
};
</script>

<style scoped lang="postcss">
.card-cic-wrapper {
  @apply bg-white dark:bg-[#111311] p-5 rounded-[2rem] shadow-sm border border-slate-100 dark:border-white/5 mb-4;
}

.badge-status-cic {
  @apply px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest border border-transparent inline-block;
}

.badge-hadir { @apply bg-emerald-500 text-white; }
.badge-terlambat { @apply bg-rose-500 text-white; }
.badge-izin { @apply bg-sky-500 text-white; }
.badge-default { @apply bg-slate-400 text-white; }

.img-thumb-cic {
  @apply w-12 h-12 rounded-2xl object-cover shadow-sm border-2 border-white dark:border-white/10;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }
</style>