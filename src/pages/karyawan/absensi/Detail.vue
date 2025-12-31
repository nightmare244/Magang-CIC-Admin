<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-20 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="absolute left-6 top-10 opacity-10">
        <ClipboardList class="w-20 h-20" />
      </div>
      
      <div class="relative z-10 text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300 mb-2">Presensi Detail</p>
        <h1 class="text-3xl font-bold tracking-tight">Rincian Kehadiran</h1>
        <p class="text-[11px] opacity-70 mt-1 font-medium italic">Ciwangun Indah Camp - Alam Menyejukkan</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20">
      <div v-if="loading" class="bg-white dark:bg-[#121512] rounded-[3.5rem] p-12 text-center shadow-xl border border-white dark:border-white/5">
        <div class="relative w-20 h-20 mx-auto mb-6">
          <div class="absolute inset-0 border-4 border-[#2d4a3e]/10 rounded-[2rem]"></div>
          <div class="absolute inset-0 border-4 border-[#2d4a3e] border-t-transparent rounded-[2rem] animate-spin"></div>
          <RefreshCw class="w-8 h-8 absolute inset-0 m-auto text-[#2d4a3e] animate-pulse" />
        </div>
        <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-slate-400">Menyusun Informasi...</p>
      </div>

      <div v-else-if="apiError" class="animate-fade-in-up p-8 bg-white dark:bg-[#121512] rounded-[3rem] shadow-xl text-center border border-rose-100">
        <AlertCircle class="w-12 h-12 text-rose-500 mx-auto mb-4" />
        <p class="text-[12px] text-rose-600 font-bold uppercase tracking-widest mb-2">Gagal Memuat Detail</p>
        <p class="text-[11px] text-slate-500 leading-relaxed mb-6">{{ apiError }}</p>
        <button @click="$router.back()" class="btn-cic-primary w-full py-4 text-[11px]">Kembali</button>
      </div>

      <div v-else-if="data" class="space-y-6 animate-fade-in-up">
        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-slate-50 dark:border-white/5">
          <div class="text-center pb-6 border-b border-slate-100 dark:border-white/5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1">Hari & Tanggal</p>
            <h2 class="text-lg font-bold text-[#2d4a3e] dark:text-emerald-400">{{ formatDate(data.tanggal) }}</h2>
            <div class="mt-4 flex justify-center">
              <span :class="badgeClass(data.status_hari)" class="px-6 py-2 rounded-full text-[10px] font-black uppercase tracking-widest border">
                {{ data.status_hari }}
              </span>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mt-8">
            <div class="p-5 rounded-[2rem] bg-slate-50 dark:bg-white/5 border border-slate-100 text-center">
              <LogIn class="w-4 h-4 text-emerald-500 mx-auto mb-2" />
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Masuk</p>
              <p :class="statusMasukClass(data.status_masuk)" class="text-base font-black mt-1">{{ data.jam_masuk || '--:--' }}</p>
            </div>
            <div class="p-5 rounded-[2rem] bg-slate-50 dark:bg-white/5 border border-slate-100 text-center">
              <LogOut class="w-4 h-4 text-rose-400 mx-auto mb-2" />
              <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Pulang</p>
              <p class="text-base font-black text-slate-800 dark:text-white mt-1">{{ data.jam_pulang || '--:--' }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-4 shadow-xl border border-slate-50 dark:border-white/5 overflow-hidden">
          <div class="flex items-center gap-3 px-4 py-3 mb-1">
            <MapPin class="w-5 h-5 text-[#2d4a3e]" />
            <h3 class="text-xs font-bold uppercase tracking-widest text-slate-700 dark:text-slate-300">Lokasi Presensi</h3>
          </div>
          
          <div id="map-karyawan" class="h-64 w-full rounded-[2.5rem] border-2 border-slate-50 z-10 bg-slate-100"></div>
          
          <div class="p-4 space-y-3">
            <div class="flex items-start gap-3">
              <div class="w-2 h-2 mt-1 rounded-full bg-emerald-500"></div>
              <div class="flex-1">
                <p class="text-[8px] font-bold text-slate-400 uppercase">Koordinat Masuk</p>
                <p class="text-[10px] font-mono break-all">{{ data.lokasi_masuk || '-' }}</p>
              </div>
            </div>
            <div v-if="data.lokasi_pulang" class="flex items-start gap-3">
              <div class="w-2 h-2 mt-1 rounded-full bg-rose-500"></div>
              <div class="flex-1">
                <p class="text-[8px] font-bold text-slate-400 uppercase">Koordinat Pulang</p>
                <p class="text-[10px] font-mono break-all">{{ data.lokasi_pulang || '-' }}</p>
              </div>
            </div>
          </div>
        </div>

        <button @click="$router.back()" class="btn-cic-secondary w-full flex items-center justify-center gap-3 py-5">
          <ArrowLeft class="w-4 h-4" /> Kembali ke Riwayat
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from "vue";
import api from '@/services/api';
import { useRoute } from "vue-router";
import { 
  ClipboardList, RefreshCw, AlertCircle, 
  MapPin, LogIn, LogOut, ArrowLeft 
} from "lucide-vue-next";
import L from "leaflet";
import "leaflet/dist/leaflet.css";

const route = useRoute();
const loading = ref(true);
const apiError = ref(null);
const data = ref(null);
let map = null;

function badgeClass(status) {
    if (!status) return 'bg-slate-400 text-white border-slate-300';
    const s = status.toLowerCase();
    if (s.includes('hadir')) return 'bg-emerald-500 text-white border-emerald-400';
    if (s.includes('izin') || s.includes('sakit')) return 'bg-amber-500 text-white border-amber-400';
    if (s.includes('alpa')) return 'bg-rose-500 text-white border-rose-400';
    return 'bg-slate-400 text-white border-slate-300';
}

function statusMasukClass(status) {
    if (!status) return 'text-slate-800 dark:text-white';
    return status.toLowerCase() === 'terlambat' ? 'text-rose-500' : 'text-emerald-500';
}

function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(date);
}

// Fungsi Inisialisasi Peta
const initMap = () => {
  const hasIn = data.value.lokasi_masuk;
  const hasOut = data.value.lokasi_pulang; // Mengambil data lokasi_pulang dari backend

  if (!hasIn && !hasOut) return;

  nextTick(() => {
    setTimeout(() => {
      // Fokus peta tetap pada lokasi masuk sebagai titik awal
      const primaryCoords = (hasIn || hasOut).split(',').map(c => parseFloat(c.trim()));
      
      if (map) map.remove();

      map = L.map('map-karyawan', { zoomControl: false }).setView(primaryCoords, 16);
      L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png').addTo(map);

      const markers = [];

      // Marker Masuk (Warna Hijau)
      if (hasIn) {
        const inCoords = hasIn.split(',').map(c => parseFloat(c.trim()));
        L.circleMarker(inCoords, {
          radius: 8, color: '#10b981', fillColor: '#10b981', fillOpacity: 0.8
        }).addTo(map).bindPopup(`<b>Masuk:</b> ${data.value.jam_masuk}`);
        markers.push(inCoords);
      }

      // Marker Pulang (Warna Merah)
      if (hasOut) {
        const outCoords = hasOut.split(',').map(c => parseFloat(c.trim()));
        L.circleMarker(outCoords, {
          radius: 8, color: '#f43f5e', fillColor: '#f43f5e', fillOpacity: 0.8
        }).addTo(map).bindPopup(`<b>Pulang:</b> ${data.value.jam_pulang}`);
        markers.push(outCoords);
      }

      // Jika ada masuk & pulang, peta akan otomatis zoom out agar keduanya terlihat
      if (markers.length > 1) {
        map.fitBounds(L.latLngBounds(markers).pad(0.5));
      }

      map.invalidateSize();
    }, 300);
  });
};

const loadDetail = async () => {
    loading.value = true;
    apiError.value = null;
    try {
        const res = await api.get(`/karyawan/absensi/${route.params.id}`);
        data.value = res.data.data;
        initMap();
    } catch (err) {
        apiError.value = err.response?.data?.message || "Data rincian presensi tidak dapat ditemukan.";
    } finally {
        loading.value = false;
    }
};

onMounted(loadDetail);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

/* Menghilangkan logo leaflet agar bersih */
:deep(.leaflet-control-attribution) { display: none; }

.btn-cic-secondary {
    @apply bg-white dark:bg-[#121512] text-[#2d4a3e] dark:text-emerald-500 rounded-[2.5rem] font-bold text-[11px] uppercase tracking-[0.2em] border border-slate-100 dark:border-white/5 shadow-md active:scale-95 transition-all;
}

@keyframes fadeIn-up {
  from { opacity: 0; transform: translateY(40px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeIn-up 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>