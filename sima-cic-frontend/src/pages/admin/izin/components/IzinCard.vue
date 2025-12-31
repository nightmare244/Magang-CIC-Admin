<template>
  <div class="card-eco group relative overflow-hidden">
    <div :class="statusAccentClass" class="absolute top-0 left-0 w-1.5 h-full opacity-60 group-hover:opacity-100 transition-all duration-500"></div>

    <div class="flex flex-col sm:flex-row justify-between gap-6 p-6">
      <div class="flex items-center space-x-5">
        <div class="relative flex-shrink-0">
          <div 
            class="w-14 h-14 rounded-2xl flex items-center justify-center font-bold text-xl shadow-inner transition-transform group-hover:scale-110 duration-500"
            :class="avatarBgClass"
          >
            {{ getInitials(izin.user?.name) }}
          </div>
          <div class="absolute -bottom-1 -right-1 bg-white dark:bg-[#0a0c0a] p-1.5 rounded-xl shadow-lg border border-slate-100 dark:border-slate-800">
            <component 
              :is="izin.tipe_izin?.toLowerCase() === 'sakit' ? Activity : CalendarDays" 
              class="w-3.5 h-3.5" 
              :class="izin.tipe_izin?.toLowerCase() === 'sakit' ? 'text-rose-500' : 'text-blue-500'" 
            />
          </div>
        </div>

        <div class="min-w-0">
          <h3 class="font-bold text-slate-800 dark:text-white truncate leading-tight group-hover:text-[#2d4a3e] dark:group-hover:text-emerald-500 transition-colors text-lg">
            {{ izin.user?.name }}
          </h3>
          <div class="flex items-center mt-1.5 space-x-3">
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-md">
              NIP: {{ izin.user?.nip || '---' }}
            </span>
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest flex items-center">
              <span class="w-1.5 h-1.5 rounded-full bg-slate-300 mr-2"></span>
              {{ izin.tipe_izin }}
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-center sm:items-start pt-1">
        <span class="badge-status-eco" :class="statusClass">
          <div class="w-1.5 h-1.5 rounded-full mr-2.5 animate-pulse" :class="statusDotClass"></div>
          {{ formatStatus(izin.status) }}
        </span>
      </div>
    </div>

    <div class="mx-6 mb-6 grid grid-cols-2 sm:grid-cols-3 gap-6 py-5 border-y border-slate-50 dark:border-white/5 font-poppins">
      <div class="space-y-1">
        <p class="kpi-label-small">Mulai Izin</p>
        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ formatDate(izin.tanggal_mulai) }}</p>
      </div>
      <div class="space-y-1">
        <p class="kpi-label-small">Selesai Izin</p>
        <p class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ formatDate(izin.tanggal_selesai) }}</p>
      </div>
      <div class="space-y-1">
        <p class="kpi-label-small">Total Durasi</p>
        <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">
          {{ calculateDuration(izin.tanggal_mulai, izin.tanggal_selesai) }} Hari
        </p>
      </div>
    </div>

    <div class="px-6 pb-6 flex flex-wrap gap-3">
      <button 
        @click="$emit('detail', izin.id)"
        class="flex-1 min-w-[120px] py-3.5 px-4 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-slate-600 dark:text-slate-300 rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-center gap-2 active:scale-95"
      >
        <Eye class="w-4 h-4" /> Lihat Detail
      </button>

      <template v-if="izin.status?.toLowerCase() === 'pending'">
        <button 
          @click="$emit('updateStatus', { id: izin.id, status: 'disetujui' })"
          class="flex-1 min-w-[120px] py-3.5 px-4 bg-emerald-500 hover:bg-emerald-600 text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-lg shadow-emerald-500/20 flex items-center justify-center gap-2 active:scale-95"
        >
          <CheckCircle2 class="w-4 h-4" /> Setujui
        </button>
        <button 
          @click="$emit('updateStatus', { id: izin.id, status: 'ditolak' })"
          class="flex-1 min-w-[120px] py-3.5 px-4 bg-rose-500 hover:bg-rose-600 text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest transition-all shadow-lg shadow-rose-500/20 flex items-center justify-center gap-2 active:scale-95"
        >
          <XCircle class="w-4 h-4" /> Tolak
        </button>
      </template>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { 
  Activity, 
  CalendarDays, 
  Eye, 
  CheckCircle2, 
  XCircle 
} from 'lucide-vue-next';

const props = defineProps({
  izin: { type: Object, required: true },
});

// Event detail mengirimkan ID izin untuk navigasi
defineEmits(["detail", "updateStatus"]);

const formatDate = (dateString) => {
  if (!dateString) return '---';
  const date = new Date(dateString);
  return new Intl.DateTimeFormat('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  }).format(date);
};

const calculateDuration = (start, end) => {
  if (!start || !end) return 0;
  const d1 = new Date(start);
  const d2 = new Date(end);
  const diffTime = Math.abs(d2 - d1);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
};

const getInitials = (name) => {
  if (!name) return '??';
  const parts = name.split(' ');
  return parts.length > 1 
    ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() 
    : parts[0][0].toUpperCase();
};

const formatStatus = (status) => {
  if (!status) return "MENUNGGU";
  const s = status.toLowerCase();
  if (s === 'pending') return 'MENUNGGU VERIFIKASI';
  if (s === 'approved' || s === 'disetujui') return 'DISETUJUI';
  if (s === 'rejected' || s === 'ditolak') return 'DITOLAK';
  return status.toUpperCase();
};

// Logika Tema Warna
const avatarBgClass = computed(() => {
  const colors = [
    'bg-sky-50 text-sky-600 dark:bg-sky-500/10 dark:text-sky-400',
    'bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400',
    'bg-amber-50 text-amber-600 dark:bg-amber-500/10 dark:text-amber-400'
  ];
  return colors[(props.izin.user?.id || 0) % colors.length];
});

const statusClass = computed(() => {
  const s = props.izin.status?.toLowerCase();
  if (s === "pending") return "bg-amber-100 text-amber-700 border-amber-200 dark:bg-amber-900/30 dark:text-amber-400";
  if (s === "approved" || s === "disetujui") return "bg-emerald-100 text-emerald-700 border-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-400";
  if (s === "rejected" || s === "ditolak") return "bg-rose-100 text-rose-700 border-rose-200 dark:bg-rose-900/30 dark:text-rose-400";
  return "bg-slate-50 text-slate-600 border-slate-100";
});

const statusDotClass = computed(() => {
  const s = props.izin.status?.toLowerCase();
  if (s === "pending") return "bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]";
  if (s === "approved" || s === "disetujui") return "bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]";
  if (s === "rejected" || s === "ditolak") return "bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.6)]";
  return "bg-slate-400";
});

const statusAccentClass = computed(() => {
  const s = props.izin.status?.toLowerCase();
  if (s === "pending") return "bg-amber-500";
  if (s === "approved" || s === "disetujui") return "bg-emerald-500";
  if (s === "rejected" || s === "ditolak") return "bg-rose-500";
  return "bg-slate-400";
});
</script>

<style scoped lang="postcss">
.card-eco {
  @apply relative bg-white dark:bg-[#121512] rounded-[2.2rem] border border-gray-100 
         dark:border-white/5 shadow-sm transition-all duration-500 
         hover:shadow-xl font-poppins;
}
.badge-status-eco {
  @apply inline-flex items-center px-4 py-1.5 rounded-xl text-[10px] font-bold tracking-widest border shadow-sm;
}
.kpi-label-small {
  @apply text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em];
}
</style>