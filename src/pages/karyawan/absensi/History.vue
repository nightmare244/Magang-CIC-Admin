<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4">
          <button 
            @click="$router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>
          <div>
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 tracking-wide">Aktivitas kerja</p>
            <h1 class="text-xl font-bold tracking-tight text-white">Riwayat Absensi</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30">
      
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] border border-slate-100 dark:border-white/5 shadow-sm overflow-hidden animate-fade-in-up">
        
        <div class="p-6 border-b border-slate-50 dark:border-white/5">
          <h3 class="text-[12px] font-bold text-slate-800 dark:text-slate-400 mb-4 tracking-wide">Filter status</h3>
          <div class="flex gap-2 overflow-x-auto pb-1 no-scrollbar">
            <button 
              v-for="s in ['Semua', 'Hadir', 'Izin', 'Alpa']" 
              :key="s"
              @click="applyFilter(s)"
              :class="statusFilter === s 
                ? 'bg-[#1e332a] text-white shadow-md' 
                : 'bg-slate-50 dark:bg-white/5 text-slate-400 border-transparent'"
              class="px-5 py-2.5 rounded-xl text-[11px] font-bold tracking-tight whitespace-nowrap transition-all border active:scale-95"
            >
              {{ s }}
            </button>
          </div>
        </div>

        <div class="p-4 min-h-[400px]">
          <div class="flex items-center justify-between px-2 mb-4">
            <h3 class="text-[12px] font-bold text-slate-800 dark:text-slate-400 tracking-wide">Daftar arsip</h3>
            <span v-if="history.total" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-100/50">
              {{ history.total }} Berkas
            </span>
          </div>

          <div v-if="loading" class="space-y-4">
            <div v-for="i in 3" :key="i" class="h-24 bg-slate-50 dark:bg-white/5 rounded-3xl animate-pulse"></div>
          </div>

          <div v-else-if="apiError" class="py-20 text-center">
            <AlertCircle class="w-10 h-10 text-rose-500 mx-auto mb-3" />
            <p class="text-[11px] text-slate-400 font-medium mb-4">{{ apiError }}</p>
            <button @click="loadHistory" class="px-8 py-3 bg-[#1e332a] text-white rounded-xl text-[11px] font-bold tracking-wide">Coba lagi</button>
          </div>

          <div v-else-if="!history.data || history.data.length === 0" class="py-20 text-center">
            <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-4">
              <MapPin class="w-8 h-8 text-slate-300" />
            </div>
            <p class="text-[11px] font-medium text-slate-400 tracking-wide">Belum ada data riwayat</p>
          </div>

          <div v-else class="space-y-3">
            <div 
              v-for="(item, index) in history.data" 
              :key="item.id" 
              @click="$router.push({ name: 'karyawan.absensi.detail', params: { id: item.id } })"
              class="flex items-center justify-between p-3 rounded-[1.5rem] border border-slate-50 dark:border-white/5 active:bg-slate-50 dark:active:bg-white/5 transition-all group"
            >
              <div class="flex items-center gap-3">
                <div class="w-11 h-11 bg-slate-50 dark:bg-white/5 rounded-xl flex flex-col items-center justify-center border border-slate-100 dark:border-white/10">
                  <span class="text-[7px] font-bold text-slate-400 leading-none mb-1">{{ getDayName(item.tanggal) }}</span>
                  <span class="text-sm font-bold text-[#1e332a] dark:text-emerald-500 leading-none">{{ getDayNum(item.tanggal) }}</span>
                </div>
                
                <div>
                  <h4 class="text-[12px] font-bold text-slate-800 dark:text-white leading-tight mb-1">
                    {{ item.status_masuk || item.status || 'Hadir' }}
                  </h4>
                  <div class="flex items-center gap-2">
                    <p class="text-[10px] text-slate-400 font-medium flex items-center gap-1 tracking-tight">
                      <Clock class="w-3 h-3 opacity-50" /> {{ item.jam_masuk || item.checkin || '--:--' }}
                    </p>
                    <div class="w-1 h-1 rounded-full bg-slate-200 dark:bg-slate-800"></div>
                    <p class="text-[10px] text-slate-400 font-medium tracking-tight">{{ getMonthName(item.tanggal) }}</p>
                  </div>
                </div>
              </div>

              <div class="flex items-center gap-2">
                <span 
                  :class="getStatusClass(item.status_hari)" 
                  class="px-2.5 py-1.5 rounded-lg text-[9px] font-bold tracking-wide"
                >
                  {{ formatStatus(item.status_hari || 'Alpa') }}
                </span>
                <ChevronRight class="w-4 h-4 text-slate-300" />
              </div>
            </div>
          </div>
        </div>

        <div v-if="history.last_page > 1" class="flex justify-between items-center p-6 bg-slate-50/50 dark:bg-white/5 border-t border-slate-50 dark:border-white/5">
          <button @click="prevPage" :disabled="page === 1" class="p-3 bg-white dark:bg-[#080908] rounded-xl border border-slate-100 dark:border-white/5 disabled:opacity-30 active:scale-90 transition-all">
            <ChevronLeft class="w-5 h-5 text-emerald-500" />
          </button>
          <span class="text-[11px] font-bold text-slate-400 tracking-widest uppercase">
            {{ page }} <span class="mx-1 text-slate-200">/</span> {{ history.last_page }}
          </span>
          <button @click="nextPage" :disabled="page === history.last_page" class="p-3 bg-white dark:bg-[#080908] rounded-xl border border-slate-100 dark:border-white/5 disabled:opacity-30 active:scale-90 transition-all">
            <ChevronRight class="w-5 h-5 text-emerald-500" />
          </button>
        </div>
      </div>

      <footer class="pt-10 pb-6 text-center">
        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest">Ciwangun indah camp</p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { 
  ChevronLeft, ChevronRight, AlertCircle, Clock, MapPin 
} from "lucide-vue-next";

const loading = ref(true);
const apiError = ref(null);
const history = ref({ data: [], current_page: 1, last_page: 1, total: 0 });
const page = ref(1);
const statusFilter = ref('Semua');

const loadHistory = async () => {
  loading.value = true;
  apiError.value = null;
  try {
    const res = await api.get(`/karyawan/absensi/history`, {
      params: {
        page: page.value,
        status: statusFilter.value !== 'Semua' ? statusFilter.value.toLowerCase() : undefined
      }
    }); 
    history.value = res.data.data; 
  } catch (err) {
    apiError.value = "Koneksi terputus, silakan coba lagi";
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
    window.scrollTo({ top: 0, behavior: 'smooth' });
  }
};

const applyFilter = (status) => {
  statusFilter.value = status;
  page.value = 1;
  loadHistory();
};

const nextPage = () => {
  if (page.value < history.value.last_page) {
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

const getMonthName = (dateStr) => {
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { month: 'long' });
};

const formatStatus = (text) => {
  if (!text) return '';
  return text.charAt(0).toUpperCase() + text.slice(1).toLowerCase();
};

const getStatusClass = (status) => {
  if (!status) return 'bg-rose-500/10 text-rose-500';
  const s = status.toUpperCase();
  if (s === 'HADIR') return 'bg-emerald-500/10 text-emerald-600';
  if (s === 'ALPA') return 'bg-rose-500/10 text-rose-500';
  if (s.includes('IZIN') || s.includes('SAKIT')) return 'bg-sky-500/10 text-sky-600';
  return 'bg-amber-500/10 text-amber-600';
};

onMounted(loadHistory);
</script>

<style scoped lang="postcss">
.no-scrollbar::-webkit-scrollbar { display: none; }
.no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

* { -webkit-tap-highlight-color: transparent; }
</style>