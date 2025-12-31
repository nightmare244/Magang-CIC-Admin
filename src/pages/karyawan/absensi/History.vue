<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-20 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="absolute left-6 top-10 opacity-10">
        <Trees class="w-20 h-20" />
      </div>
      
      <div class="relative z-10 text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300 mb-2">Presensi Log</p>
        <h1 class="text-3xl font-bold tracking-tight">Riwayat Absensi</h1>
        <p class="text-[11px] opacity-70 mt-1 font-medium italic">Ciwangun Indah Camp - Alam Menyejukkan</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20">
      <div v-if="loading" class="bg-white dark:bg-[#121512] rounded-[3.5rem] p-12 text-center shadow-xl border border-white dark:border-white/5">
        <div class="relative w-20 h-20 mx-auto mb-6">
          <div class="absolute inset-0 border-4 border-[#2d4a3e]/10 rounded-[2rem]"></div>
          <div class="absolute inset-0 border-4 border-[#2d4a3e] border-t-transparent rounded-[2rem] animate-spin"></div>
          <MapPin class="w-8 h-8 absolute inset-0 m-auto text-[#2d4a3e] animate-bounce" />
        </div>
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Menyiapkan Data...</p>
      </div>

      <div v-else-if="apiError" class="animate-fade-in-up p-8 bg-white dark:bg-[#121512] rounded-[3rem] shadow-xl text-center border border-rose-100">
        <div class="w-16 h-16 bg-rose-50 rounded-full flex items-center justify-center mx-auto mb-4">
          <AlertCircle class="w-8 h-8 text-rose-500" />
        </div>
        <p class="text-[12px] text-rose-600 font-bold uppercase tracking-widest mb-2">Gagal Memuat</p>
        <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed mb-6">{{ apiError }}</p>
        <button @click="loadHistory" class="btn-cic-primary w-full py-4 text-[11px]">Coba Segarkan</button>
      </div>

      <div v-else-if="history.data && history.data.length" class="space-y-5">
        <div v-for="(item, index) in history.data" :key="item.id" class="animate-fade-in-up" :style="{ animationDelay: `${index * 50}ms` }">
          <div 
            @click="$router.push({ name: 'karyawan.absensi.detail', params: { id: item.id } })"
            class="bg-white dark:bg-[#121512] rounded-[2.5rem] p-6 shadow-lg border border-slate-50 dark:border-white/5 flex items-center justify-between group active:scale-95 cursor-pointer transition-all"
          >
            <div class="flex items-center gap-5">
              <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-900/10 rounded-2xl flex flex-col items-center justify-center text-[#2d4a3e] dark:text-emerald-400 border border-emerald-100 dark:border-emerald-800">
                <span class="text-[9px] font-black uppercase tracking-tighter leading-none mb-1">{{ getDayName(item.tanggal) }}</span>
                <span class="text-lg font-bold leading-none">{{ getDayNum(item.tanggal) }}</span>
              </div>
              <div>
                <h3 class="text-[11px] font-bold text-slate-800 dark:text-white uppercase tracking-wider leading-none">
                  {{ item.status }}
                </h3>
                <p class="text-[10px] text-slate-400 font-medium mt-1.5 flex items-center gap-1">
                  <Clock class="w-3 h-3" /> {{ item.checkin || '--:--' }} WIB
                </p>
              </div>
            </div>
            
            <div class="flex flex-col items-end gap-2">
              <span :class="getStatusClass(item.status)" class="px-4 py-1.5 rounded-full text-[8px] font-bold tracking-widest uppercase shadow-sm">
                {{ item.status }}
              </span>
              <div class="flex items-center gap-1 text-[9px] font-bold text-emerald-600 dark:text-emerald-400 opacity-0 group-hover:opacity-100 transition-opacity">
                <span>DETAIL</span>
                <ChevronRight class="w-3 h-3 transition-transform group-hover:translate-x-1" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="history.meta && history.meta.last_page > 1" class="flex justify-between items-center mt-12 py-6 px-2">
          <button @click="prevPage" :disabled="page === 1" class="btn-nav-cic group">
            <ChevronLeft class="w-4 h-4 transition-transform group-active:-translate-x-2" />
          </button>

          <div class="bg-white dark:bg-[#121512] px-6 py-2 rounded-2xl shadow-sm border border-slate-100 dark:border-white/5">
            <p class="text-[11px] font-bold text-slate-600 dark:text-slate-300">
              Halaman <span class="text-[#2d4a3e] font-black">{{ page }}</span> dari {{ history.meta.last_page }}
            </p>
          </div>

          <button @click="nextPage" :disabled="page === history.meta.last_page" class="btn-nav-cic group">
            <ChevronRight class="w-4 h-4 transition-transform group-active:translate-x-2" />
          </button>
        </div>
      </div>

      <div v-else class="animate-fade-in-up bg-white dark:bg-[#121512] rounded-[3.5rem] p-12 text-center shadow-xl border border-dashed border-slate-200">
        <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
          <Trees class="w-10 h-10 text-slate-300" />
        </div>
        <p class="text-[12px] font-bold text-slate-400 uppercase tracking-widest">Belum Ada Aktivitas</p>
        <p class="text-[11px] text-slate-400 mt-2 leading-relaxed px-4">Mari awali harimu di CIC dengan semangat!</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { 
  Trees, MapPin, ChevronLeft, ChevronRight, 
  AlertCircle, Clock 
} from "lucide-vue-next";

const loading = ref(true);
const apiError = ref(null);
const history = ref({ data: [], meta: {} });
const page = ref(1);

const loadHistory = async () => {
  loading.value = true;
  apiError.value = null;
  try {
    const res = await api.get(`/karyawan/absensi/history?page=${page.value}`); 
    history.value = res.data.data; 
  } catch (err) {
    apiError.value = "Sistem gagal mengambil data. Silakan coba lagi.";
  } finally {
    loading.value = false;
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const nextPage = () => {
  if (page.value < history.value.meta.last_page) {
    page.value++;
    loadHistory();
  }
};

const prevPage = () => {
  if (page.value > 1) {
    page.value--;
    loadHistory();
  }
};

const getDayName = (dateStr) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { weekday: 'short' });
};

const getDayNum = (dateStr) => {
  const d = new Date(dateStr);
  return d.getDate();
};

const getStatusClass = (status) => {
  if (!status) return 'bg-slate-400 text-white';
  const s = status.toLowerCase();
  if (s.includes('hadir')) return 'bg-emerald-500 text-white border-emerald-400';
  if (s.includes('izin') || s.includes('sakit')) return 'bg-amber-500 text-white border-amber-400';
  return 'bg-rose-500 text-white border-rose-400';
};

onMounted(loadHistory);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.btn-cic-primary { 
    @apply bg-[#2d4a3e] text-white rounded-[2.5rem] font-bold uppercase tracking-[0.2em] shadow-lg 
           active:scale-95 transition-all duration-200 disabled:opacity-50;
}

.btn-nav-cic {
    @apply p-4 bg-white dark:bg-[#121512] text-[#2d4a3e] rounded-2xl shadow-md border border-slate-100 
           dark:border-white/5 active:scale-90 transition-all disabled:opacity-30;
}

@keyframes fadeIn-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeIn-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>