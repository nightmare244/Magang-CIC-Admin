<template>
  <div class="p-4 md:p-8 max-w-7xl mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen font-poppins text-slate-800 dark:text-slate-200">
    
    <header class="flex justify-between items-center border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-4">
        <button @click="$router.back()" class="p-3 bg-white dark:bg-[#121512] rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 hover:scale-110 transition-all">
          <ArrowLeft class="w-5 h-5" />
        </button>
        <div>
          <h1 class="text-2xl font-bold text-[#2d4a3e] dark:text-emerald-500">Detail Geofencing</h1>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">{{ formatDate(detailData?.tanggal) }}</p>
        </div>
      </div>
    </header>

    <div v-if="loading" class="py-40 text-center uppercase tracking-[0.5em] text-slate-400 animate-pulse text-xs">
      Sinkronisasi Data Satelit...
    </div>

    <div v-else-if="detailData" class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      
      <div class="lg:col-span-4 space-y-6">
        <div class="card-complex p-6 bg-white dark:bg-[#121512] rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl">
          <div class="flex flex-col items-center text-center mb-6">
            <div class="w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-[2rem] flex items-center justify-center overflow-hidden border-4 border-white dark:border-[#1a1d19] shadow-lg mb-4">
              <img v-if="detailData.foto_profil" 
                   :src="getProfilePhoto(detailData.foto_profil)" 
                   class="w-full h-full object-cover" 
                   alt="Profile" />
              <UserIcon v-else class="w-10 h-10 text-[#2d4a3e] dark:text-emerald-500" />
            </div>
            
            <div>
              <h2 class="font-bold text-lg leading-tight">{{ detailData.name }}</h2>
              <div class="flex flex-col gap-1 mt-2">
                <span class="text-[10px] font-black bg-emerald-100 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 px-3 py-1 rounded-full uppercase self-center">
                   {{ detailData.department_name || 'Umum' }}
                </span>
                <p class="text-xs font-mono text-slate-400 tracking-widest font-bold mt-1">NIP: {{ detailData.nip }}</p>
              </div>
            </div>
          </div>

          <div class="space-y-4 border-t border-gray-50 dark:border-gray-800 pt-6">
            <div class="space-y-1">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <Mail class="w-3 h-3" /> Email Address
              </span>
              <p class="text-xs font-bold truncate">{{ detailData.email || '-' }}</p>
            </div>
            <div class="space-y-1">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                <Phone class="w-3 h-3" /> WhatsApp / Phone
              </span>
              <p class="text-xs font-bold">{{ detailData.nomor_hp || '-' }}</p>
            </div>
          </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
          <div class="card-complex p-5 text-center bg-white dark:bg-[#121512] rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-xl">
            <LogIn class="w-4 h-4 mx-auto mb-2 text-emerald-500" />
            <p class="text-[9px] font-bold text-slate-400 uppercase">Masuk</p>
            <p class="text-lg font-black font-mono mt-1">{{ detailData.jam_masuk || '--:--' }}</p>
          </div>
          <div class="card-complex p-5 text-center bg-white dark:bg-[#121512] rounded-[2rem] border border-gray-100 dark:border-gray-800 shadow-xl">
            <LogOut class="w-4 h-4 mx-auto mb-2 text-rose-400" />
            <p class="text-[9px] font-bold text-slate-400 uppercase">Pulang</p>
            <p class="text-lg font-black font-mono mt-1">{{ detailData.jam_pulang || '--:--' }}</p>
          </div>
        </div>
      </div>

      <div class="lg:col-span-8 space-y-6">
        <div class="card-complex h-full flex flex-col overflow-hidden min-h-[550px] bg-white dark:bg-[#121512] rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl relative">
          <div class="p-6 border-b border-gray-50 dark:border-gray-800 flex justify-between items-center bg-white dark:bg-[#121512] z-[1000]">
            <div class="flex items-center gap-3">
              <div class="p-2 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl">
                <MapPin class="w-5 h-5 text-emerald-600" />
              </div>
              <h3 class="text-xs font-black uppercase tracking-widest">Peta Pelacakan Geospasial</h3>
            </div>
            <div class="flex gap-4">
               <div class="flex items-center gap-2">
                 <div class="w-2.5 h-2.5 bg-emerald-500 rounded-full animate-pulse"></div>
                 <span class="text-[9px] font-bold uppercase">Masuk</span>
               </div>
               <div class="flex items-center gap-2">
                 <div class="w-2.5 h-2.5 bg-rose-500 rounded-full animate-pulse"></div>
                 <span class="text-[9px] font-bold uppercase">Pulang</span>
               </div>
            </div>
          </div>
          
          <div id="map-compare" style="height: 450px; width: 100%;" class="z-10 bg-slate-100 dark:bg-slate-900 shadow-inner"></div>

          <div class="p-6 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800">
             <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="relative pl-6 border-l-2 border-emerald-500">
                   <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Koordinat Masuk</p>
                   <p class="text-xs font-mono font-bold">{{ detailData.lokasi_masuk || 'Data GPS Tidak Ditemukan' }}</p>
                </div>
                <div class="relative pl-6 border-l-2 border-rose-500">
                   <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Koordinat Pulang</p>
                   <p class="text-xs font-mono font-bold">{{ detailData.lokasi_pulang || 'Belum Checkout' }}</p>
                </div>
             </div>
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import { 
  ArrowLeft, LogIn, LogOut, MapPin, 
  User as UserIcon, Mail, Phone 
} from 'lucide-vue-next';

const route = useRoute();
const loading = ref(true);
const detailData = ref(null);
let map = null;

const baseUrl = import.meta.env.VITE_API_BASE_URL || "http://127.0.0.1:8000";

const formatDate = (d) => d ? new Intl.DateTimeFormat('id-ID', { dateStyle: 'full' }).format(new Date(d)) : '-';
const badgeClass = (s) => s?.toUpperCase() === 'HADIR' ? 'bg-emerald-500 text-white' : 'bg-rose-500 text-white';

const getProfilePhoto = (path) => {
  if (!path) return null;
  if (path.startsWith('http')) return path;
  const cleanPath = path.replace(/^\/storage\//i, '');
  return `${baseUrl}/storage/${cleanPath}`;
};

const initCompareMap = () => {
  const hasIn = detailData.value.lokasi_masuk;
  const hasOut = detailData.value.lokasi_pulang;

  if (!hasIn && !hasOut) {
    console.warn("Koordinat GPS tidak tersedia");
    return;
  }

  nextTick(() => {
    // Ambil koordinat utama
    const primaryStr = hasIn || hasOut;
    const primaryCoords = primaryStr.split(',').map(c => parseFloat(c.trim()));
    
    // Cegah inisialisasi ganda
    if (map) {
      map.remove();
    }

    // Inisialisasi Peta
    map = L.map('map-compare', { 
        zoomControl: true,
        attributionControl: false 
    }).setView(primaryCoords, 16);
    
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    const markers = [];

    // Marker Masuk
    if (hasIn) {
      const inCoords = hasIn.split(',').map(c => parseFloat(c.trim()));
      L.circleMarker(inCoords, {
        radius: 12, color: 'white', weight: 3, fillColor: '#10b981', fillOpacity: 1
      }).addTo(map).bindPopup(`<b>Check-in:</b> ${detailData.value.jam_masuk}`);
      markers.push(inCoords);
    }

    // Marker Pulang
    if (hasOut) {
      const outCoords = hasOut.split(',').map(c => parseFloat(c.trim()));
      L.circleMarker(outCoords, {
        radius: 12, color: 'white', weight: 3, fillColor: '#f43f5e', fillOpacity: 1
      }).addTo(map).bindPopup(`<b>Check-out:</b> ${detailData.value.jam_pulang}`);
      markers.push(outCoords);
    }

    // Auto-fit jika ada 2 marker
    if (markers.length > 1) {
      map.fitBounds(L.latLngBounds(markers).pad(0.5));
    }

    // PENTING: Paksa render ulang ukuran peta
    setTimeout(() => {
        map.invalidateSize();
    }, 400);
  });
};

const fetchDetail = async () => {
  loading.value = true;
  try {
    const res = await api.get(`/admin/absensi/detail/${route.params.id}`);
    if (res.data.success) {
      detailData.value = res.data.data;
      initCompareMap();
    }
  } catch (err) {
    console.error("Gagal memuat detail:", err);
  } finally {
    loading.value = false;
  }
};

onMounted(fetchDetail);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }
.card-complex {
  @apply bg-white dark:bg-[#121512] rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-xl;
}
.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Memastikan peta terlihat */
#map-compare {
  background-color: #f1f5f9;
  border-radius: 1rem;
}

:deep(.leaflet-container) {
  font-family: 'Poppins', sans-serif !important;
  z-index: 1;
}
</style>