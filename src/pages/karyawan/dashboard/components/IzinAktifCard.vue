<template>
  <div v-if="izin" 
    @click="goToDetail"
    class="bg-white dark:bg-[#121512] rounded-[2rem] p-5 shadow-lg border border-sky-50 dark:border-sky-900/20 relative overflow-hidden transition-all active:scale-[0.98] cursor-pointer group"
  >
    <div class="absolute -right-4 -top-4 opacity-[0.05] group-hover:opacity-10 transition-opacity">
      <CalendarRange class="w-24 h-24 text-sky-600" />
    </div>

    <div class="relative z-10">
      <div class="flex items-center justify-between mb-4 px-1">
        <h2 class="text-xs font-bold text-sky-700 dark:text-sky-400">Status Izin Aktif</h2>
        <div class="px-2.5 py-0.5 bg-sky-50 dark:bg-sky-900/30 rounded-full border border-sky-100 dark:border-sky-800/30">
          <span class="text-[9px] font-bold text-sky-600 dark:text-sky-300">Terverifikasi</span>
        </div>
      </div>

      <div class="flex items-center gap-4">
        <div class="w-12 h-12 bg-sky-500 rounded-2xl flex items-center justify-center text-white shadow-md shadow-sky-500/20 flex-shrink-0 group-hover:scale-110 transition-transform duration-500">
          <Plane v-if="izin.tipe_izin?.toLowerCase().includes('cuti')" class="w-5 h-5" />
          <Stethoscope v-else-if="izin.tipe_izin?.toLowerCase().includes('sakit')" class="w-5 h-5" />
          <FileText v-else class="w-5 h-5" />
        </div>

        <div class="flex-1 overflow-hidden">
          <h3 class="text-base font-bold text-slate-800 dark:text-white capitalize leading-tight">
            {{ izin.tipe_izin || 'Izin Umum' }}
          </h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-1 italic truncate font-medium">
            "{{ izin.keterangan || 'Tanpa keterangan' }}"
          </p>
        </div>
      </div>

      <div class="mt-5 p-3.5 bg-sky-50/50 dark:bg-white/5 rounded-2xl border border-sky-100 dark:border-white/5 flex items-center justify-between">
        <div class="flex items-center gap-3">
          <CalendarDays class="w-4 h-4 text-sky-500 opacity-70" />
          <div>
            <p class="text-[9px] font-medium text-slate-400">Berlaku sampai</p>
            <p class="text-[11px] font-bold text-slate-700 dark:text-slate-200 uppercase">
              {{ formatDate(izin.tanggal_selesai) }}
            </p>
          </div>
        </div>
        
        <router-link 
          :to="`/karyawan/izin/${izin.id}`" 
          class="text-sky-600 dark:text-sky-400 p-2 hover:bg-sky-100 dark:hover:bg-white/10 rounded-xl transition-colors"
          @click.stop
        >
          <ArrowUpRight class="w-4 h-4" />
        </router-link>
      </div>
    </div>
  </div>

  <div v-else class="bg-white dark:bg-[#121512] rounded-[2rem] p-8 text-center shadow-md border border-dashed border-slate-200 dark:border-white/10 transition-all">
    <div class="w-14 h-14 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center mx-auto mb-4">
      <Trees class="w-7 h-7 text-slate-200" />
    </div>
    <p class="text-sm font-bold text-slate-700 dark:text-slate-300">Semua Terjadwal</p>
    <p class="text-[11px] text-slate-400 mt-1.5 italic font-medium leading-relaxed px-4">
      Tidak ada agenda izin aktif saat ini.
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

/**
 * Navigasi ke Detail:
 * Mengarahkan user ke rute detail izin spesifik
 */
const goToDetail = () => {
  if (props.izin?.id) {
    router.push(`/karyawan/izin/${props.izin.id}`);
  }
};

/**
 * Pembersihan Format Tanggal:
 * Menghilangkan waktu (00:00:00) agar tampilan lebih bersih
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
</script>