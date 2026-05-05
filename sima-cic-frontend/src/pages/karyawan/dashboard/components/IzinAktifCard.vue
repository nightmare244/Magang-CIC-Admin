<template>
  <div v-if="izin" 
    @click="goToDetail"
    class="bg-white dark:bg-[#111311] rounded-[1.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer group"
  >
    <div class="absolute -right-4 -top-4 opacity-[0.03] group-hover:opacity-[0.06] transition-opacity duration-700 pointer-events-none">
      <CalendarRange class="w-24 h-24 text-emerald-600" />
    </div>

    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-2">
          <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full"></div>
          <h2 class="text-xs font-semibold text-slate-600 dark:text-emerald-400">Agenda aktif</h2>
        </div>
        <div class="px-2 py-0.5 bg-emerald-50 dark:bg-emerald-500/10 rounded-lg">
          <span class="text-[10px] font-medium text-emerald-600 dark:text-emerald-400">Disetujui</span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-600 flex-shrink-0 transition-transform duration-500">
          <Plane v-if="izin.tipe_izin?.toLowerCase().includes('cuti')" class="w-6 h-6" />
          <Stethoscope v-else-if="izin.tipe_izin?.toLowerCase().includes('sakit')" class="w-6 h-6" />
          <FileText v-else class="w-6 h-6" />
        </div>

        <div class="flex-1 overflow-hidden">
          <h3 class="text-base font-bold text-slate-800 dark:text-white capitalize leading-tight">
            {{ izin.tipe_izin || 'Izin umum' }}
          </h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 truncate font-normal">
            {{ izin.keterangan || 'Tanpa keterangan tambahan' }}
          </p>
        </div>
      </div>

      <div class="mt-5 pt-4 border-t border-slate-50 dark:border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-2.5">
          <CalendarDays class="w-4 h-4 text-slate-400" />
          <div class="flex flex-col">
            <span class="text-[10px] text-slate-400">Berakhir pada</span>
            <span class="text-xs font-semibold text-slate-700 dark:text-slate-200">
              {{ formatDate(izin.tanggal_selesai) }}
            </span>
          </div>
        </div>
        
        <div class="text-emerald-600 dark:text-emerald-400">
          <ArrowUpRight class="w-5 h-5" />
        </div>
      </div>
    </div>
  </div>

  <div v-else class="bg-white dark:bg-[#111311] rounded-[1.5rem] p-6 text-center border border-slate-100 dark:border-white/5 shadow-sm">
    <div class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-3">
      <Trees class="w-6 h-6 text-slate-300 dark:text-slate-600" />
    </div>
    <h3 class="text-sm font-bold text-slate-800 dark:text-slate-200">Semua terkendali</h3>
    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1 px-4">
      Kamu tidak memiliki agenda izin atau cuti aktif hari ini.
    </p>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { 
  CalendarRange, Plane, Stethoscope, 
  FileText, CalendarDays, ArrowUpRight, Trees 
} from 'lucide-vue-next';

const props = defineProps({
  izin: { 
    type: [Object, null], 
    default: null 
  }
});

const router = useRouter();

const goToDetail = () => {
  if (props.izin?.id) {
    router.push(`/karyawan/izin/${props.izin.id}`);
  }
};

const formatDate = (dateString) => {
  if (!dateString) return '-';
  const date = new Date(dateString);
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  });
};
</script>