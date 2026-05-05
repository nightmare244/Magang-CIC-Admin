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
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">portal presensi</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Detail kehadiran</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-bold text-slate-400 capitalize tracking-widest">sinkronisasi...</p>
      </div>

      <div v-else-if="data" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 space-y-5 animate-fade-in-up">
        
        <div :class="statusTheme(data.status_hari).card" class="p-5 rounded-[2rem] flex items-center justify-between border transition-all duration-500">
          <div class="flex items-center gap-4">
            <div :class="statusTheme(data.status_hari).iconBg" class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-white">
              <CalendarCheck class="w-6 h-6" />
            </div>
            <div class="flex flex-col">
              <span class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight capitalize">{{ data.status_hari }}</span>
              <span class="text-[10px] text-slate-400 font-medium capitalize">status kehadiran</span>
            </div>
          </div>
          <span :class="statusTheme(data.status_hari).badge" class="px-4 py-2 rounded-xl text-[10px] font-bold capitalize shadow-sm">
            {{ data.status_hari }}
          </span>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
            <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Hari & Tanggal</p>
            <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200">{{ formatDate(data.tanggal) }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
            <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Keterangan</p>
            <p :class="statusMasukClass(data.status_masuk)" class="text-[12px] font-bold capitalize">{{ data.status_masuk || 'Tepat Waktu' }}</p>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5 flex flex-col items-center">
            <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Jam Masuk</p>
            <p class="text-xl font-bold text-emerald-500 tracking-wide">{{ data.jam_masuk || '--:--' }}</p>
          </div>
          <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5 flex flex-col items-center">
            <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Jam Pulang</p>
            <p class="text-xl font-bold text-slate-700 dark:text-slate-200 tracking-wide">{{ data.jam_pulang || '--:--' }}</p>
          </div>
        </div>

        <div class="bg-slate-50 dark:bg-white/5 rounded-[2.5rem] p-2 border border-slate-100 dark:border-white/5 overflow-hidden">
          <div class="flex items-center gap-3 px-4 py-3">
            <MapPin class="w-4 h-4 text-emerald-500 opacity-60" />
            <p class="text-[10px] font-bold text-slate-400 capitalize">Titik Lokasi Presensi</p>
          </div>
          <div class="px-2 pb-2">
            <div id="map-karyawan" class="h-48 w-full rounded-[2rem] bg-slate-200 dark:bg-slate-800 border border-white/10 shadow-inner"></div>
          </div>
          <div class="p-4 pt-1">
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium italic leading-relaxed">
              "{{ data.lokasi_masuk || 'lokasi tidak terekam' }}"
            </p>
          </div>
        </div>

        <button @click="$router.back()" class="w-full flex items-center justify-center gap-3 py-5 bg-[#1e332a] text-white rounded-[2rem] font-bold text-[11px] uppercase tracking-[0.2em] shadow-lg active:scale-95 transition-all">
          <ArrowLeft class="w-4 h-4" /> kembali ke riwayat
        </button>

      </div>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
import api from '@/services/api';
import { useRoute } from "vue-router";
import { 
  ChevronLeft, MapPin, ArrowLeft, 
  CalendarCheck, Clock, AlertCircle
} from "lucide-vue-next";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const route = useRoute();
const loading = ref(true);
const data = ref(null);
let map = null;

const statusTheme = (status) => {
  const s = status?.toLowerCase() || '';
  if (s.includes('hadir')) return { 
    card: 'bg-emerald-50/50 dark:bg-emerald-500/5 border-emerald-100/50 dark:border-emerald-500/10',
    iconBg: 'bg-emerald-500 text-white shadow-lg shadow-emerald-200/50',
    badge: 'bg-emerald-500 text-white'
  };
  if (s.includes('alpa')) return { 
    card: 'bg-rose-50/50 dark:bg-rose-500/5 border-rose-100/50 dark:border-rose-500/10',
    iconBg: 'bg-rose-500 text-white shadow-lg shadow-rose-200/50',
    badge: 'bg-rose-500 text-white'
  };
  return { 
    card: 'bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10',
    iconBg: 'bg-amber-500 text-white shadow-lg shadow-amber-200/50',
    badge: 'bg-amber-500 text-white'
  };
};

function statusMasukClass(status) {
    if (!status) return 'text-emerald-500';
    return status.toLowerCase() === 'terlambat' ? 'text-rose-500' : 'text-emerald-500';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'long' }).format(date);
}

const initMap = () => {
  if (!data.value?.lokasi_masuk) return;
  nextTick(() => {
    setTimeout(() => {
      const coords = data.value.lokasi_masuk.split(',').map(c => parseFloat(c.trim()));
      if (map) map.remove();
      map = L.map('map-karyawan', { zoomControl: false, attributionControl: false }).setView(coords, 16);
      L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);
      L.circleMarker(coords, { radius: 8, color: '#fff', weight: 3, fillColor: '#10b981', fillOpacity: 1 }).addTo(map);
    }, 400);
  });
};

const loadDetail = async () => {
    loading.value = true;
    try {
        const res = await api.get(`/karyawan/absensi/${route.params.id}`);
        data.value = res.data.data;
        initMap();
    } catch (err) {
        console.error(err);
    } finally {
        setTimeout(() => { loading.value = false; }, 600);
    }
};

onMounted(loadDetail);
</script>

<style scoped lang="postcss">
:deep(.leaflet-control-attribution) { display: none; }
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}
</style>