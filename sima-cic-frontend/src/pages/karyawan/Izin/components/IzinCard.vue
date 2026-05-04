<template>
  <div 
    @click="$emit('click')"
    class="group relative bg-white dark:bg-[#111311] rounded-[2rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 transition-all duration-500 hover:shadow-xl hover:shadow-emerald-900/5 hover:-translate-y-1 cursor-pointer overflow-hidden"
  >
    
    <div class="absolute -right-6 -top-6 opacity-[0.03] dark:opacity-[0.05] group-hover:opacity-10 group-hover:scale-110 transition-all duration-700">
      <component :is="getIcon(izin.tipe_izin)" class="w-32 h-32 text-emerald-600" />
    </div>

    <div class="relative z-10 flex items-center gap-5">
      <div :class="statusBg(izin.status)" class="w-14 h-14 rounded-2xl flex items-center justify-center shadow-sm transition-all duration-500 group-hover:rotate-6 group-hover:scale-110">
        <component :is="getIcon(izin.tipe_izin)" class="w-6 h-6" />
      </div>

      <div class="flex-1 min-w-0">
        <div class="flex justify-between items-center mb-1.5">
          <h3 class="text-[15px] font-black text-slate-800 dark:text-white truncate capitalize tracking-tight">
            {{ izin.tipe_izin }}
          </h3>
          <span :class="statusBadge(izin.status)" class="text-[8px] font-black uppercase tracking-[0.15em] px-3 py-1.5 rounded-xl border transition-colors duration-500">
            {{ izin.status }}
          </span>
        </div>

        <div class="flex flex-col gap-1">
          <div class="flex items-center gap-2">
            <Calendar class="w-3.5 h-3.5 text-emerald-500/60" />
            <p class="text-[11px] font-bold text-slate-500 dark:text-slate-400">
              {{ formatDate(izin.tanggal_mulai) }}
              <span v-if="izin.tanggal_selesai !== izin.tanggal_mulai" class="mx-1 opacity-50 font-medium">sampai</span>
              <span v-if="izin.tanggal_selesai !== izin.tanggal_mulai">{{ formatDate(izin.tanggal_selesai) }}</span>
            </p>
          </div>
          
          <div class="flex items-center gap-2">
            <Hash class="w-3 h-3 text-slate-300" />
            <p class="text-[10px] font-medium text-slate-400 dark:text-slate-500 italic truncate pr-4">
              "{{ izin.keterangan || 'tidak ada keterangan' }}"
            </p>
          </div>
        </div>
      </div>

      <div class="flex items-center justify-center w-8 h-8 rounded-full bg-slate-50 dark:bg-white/5 group-hover:bg-emerald-500 group-hover:text-white transition-all duration-500">
        <ChevronRight class="w-4 h-4" />
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
  Clock,
  Hash
} from 'lucide-vue-next';

const props = defineProps({
  izin: {
    type: Object,
    required: true
  }
});

defineEmits(['click']);

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
  if (t.includes('keperluan') || t.includes('izin')) return Clock;
  return FileText;
};

const statusBg = (status) => {
  const s = status?.toLowerCase() || '';
  if (s.includes('setuju')) return 'bg-emerald-500 text-white shadow-emerald-200 dark:shadow-none';
  if (s.includes('tolak')) return 'bg-rose-500 text-white shadow-rose-200 dark:shadow-none';
  return 'bg-amber-500 text-white shadow-amber-200 dark:shadow-none';
};

const statusBadge = (status) => {
  const s = status?.toLowerCase() || '';
  if (s.includes('setuju')) return 'text-emerald-600 border-emerald-100 bg-emerald-50 dark:bg-emerald-500/10 dark:border-emerald-500/20';
  if (s.includes('tolak')) return 'text-rose-600 border-rose-100 bg-rose-50 dark:bg-rose-500/10 dark:border-rose-500/20';
  return 'text-amber-600 border-amber-100 bg-amber-50 dark:bg-amber-500/10 dark:border-amber-500/20';
};
</script>

<style scoped>
/* transisi halus untuk hover */
.group {
  backface-visibility: hidden;
  transform: translateZ(0);
  -webkit-font-smoothing: subpixel-antialiased;
}
</style>