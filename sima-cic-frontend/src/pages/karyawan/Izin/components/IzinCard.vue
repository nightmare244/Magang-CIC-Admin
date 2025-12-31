<template>
  <div 
    @click="$emit('click')"
    class="group bg-white dark:bg-[#121512] rounded-[2.5rem] p-5 shadow-sm border border-slate-50 dark:border-white/5 transition-all duration-300 hover:shadow-md hover:border-emerald-100 dark:hover:border-emerald-900/30 cursor-pointer overflow-hidden relative"
  >
    
    <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-10 transition-opacity">
      <component :is="getIcon(izin.tipe_izin)" class="w-24 h-24 text-emerald-600" />
    </div>

    <div class="relative z-10 flex items-center gap-4">
      <div :class="statusBg(izin.status)" class="w-14 h-14 rounded-2xl flex items-center justify-center transition-transform group-hover:scale-110 duration-500">
        <component :is="getIcon(izin.tipe_izin)" class="w-6 h-6" />
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex justify-between items-start mb-1">
          <h3 class="text-base font-bold text-slate-800 dark:text-white truncate capitalize leading-tight">
            {{ izin.tipe_izin }}
          </h3>
          <span :class="statusText(izin.status)" class="text-[9px] font-black uppercase tracking-widest px-2.5 py-1 rounded-full border">
            {{ izin.status }}
          </span>
        </div>

        <div class="flex items-center gap-3 text-slate-400">
          <div class="flex items-center gap-1.5">
            <Calendar class="w-3.5 h-3.5 opacity-70" />
            <p class="text-[11px] font-medium tracking-tight">
              {{ formatDate(izin.tanggal_mulai) }}
            </p>
          </div>
          <div class="w-1 h-1 bg-slate-300 rounded-full"></div>
          <p class="text-[10px] font-bold text-slate-400/80 italic line-clamp-1">
            "{{ izin.keterangan || 'Tanpa keterangan' }}"
          </p>
        </div>
      </div>

      <div class="text-slate-300 group-hover:text-emerald-500 transition-colors">
        <ChevronRight class="w-5 h-5" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Calendar, 
  ChevronRight, 
  Stethoscope, 
  Plane, 
  FileText, 
  Clock 
} from 'lucide-vue-next';

const props = defineProps({
  izin: {
    type: Object,
    required: true
  }
});

// Mendefinisikan emit agar bisa didengarkan oleh parent (RiwayatIzin.vue)
defineEmits(['click']);

/**
 * Fungsi Format Tanggal: 
 * Mengubah "2023-12-29 00:00:00" menjadi "29 Dec 2023"
 */
const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'short',
    year: 'numeric'
  });
};

const getIcon = (tipe) => {
  const t = tipe?.toLowerCase() || '';
  if (t.includes('sakit')) return Stethoscope;
  if (t.includes('cuti')) return Plane;
  if (t.includes('penting')) return Clock;
  return FileText;
};

const statusBg = (status) => {
  switch (status?.toLowerCase()) {
    case 'disetujui': return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
    case 'ditolak': return 'bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400';
    default: return 'bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400';
  }
};

const statusText = (status) => {
  switch (status?.toLowerCase()) {
    case 'disetujui': return 'text-emerald-600 border-emerald-100 bg-emerald-50/50 dark:border-emerald-500/20';
    case 'ditolak': return 'text-rose-600 border-rose-100 bg-rose-50/50 dark:border-rose-500/20';
    default: return 'text-amber-600 border-amber-100 bg-amber-50/50 dark:border-amber-500/20';
  }
};
</script>