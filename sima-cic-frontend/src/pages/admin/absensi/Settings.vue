<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Settings class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Pengaturan Sistem
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-wider">
            Manajemen Geofencing & QR Access Point
          </p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row gap-2 items-end">
        <Transition name="slide-fade">
          <div v-if="errorMessage" class="flex items-center gap-3 px-6 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl shadow-sm">
            <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ errorMessage }}</span>
          </div>
        </Transition>
        <Transition name="slide-fade">
          <div v-if="successMessage" class="flex items-center gap-3 px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl shadow-sm">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ successMessage }}</span>
          </div>
        </Transition>
      </div>
    </header>

    <div v-if="isLoading" class="flex flex-col items-center justify-center py-40">
      <Loader2 class="w-10 h-10 animate-spin text-[#2d4a3e] mb-4" />
      <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyinkronkan data konfigurasi...</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
      
      <div class="lg:col-span-5 space-y-8">
        
        <div class="card-eco p-8 bg-white dark:bg-[#121512] text-center shadow-xl border-2 border-emerald-500/30 relative overflow-hidden">
          <div class="absolute top-4 right-4 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 text-[9px] font-black px-3 py-1 rounded-full uppercase tracking-tighter">
            HD 1080px Ready
          </div>

          <h3 class="kpi-label flex items-center gap-2 mb-6 text-left !text-emerald-600 font-bold">
            <QrCode class="w-4 h-4" /> QR Point Permanen (HQ)
          </h3>
          
          <div class="inline-block p-6 bg-white rounded-[2.5rem] border-[4px] border-slate-900 shadow-2xl mb-6 relative group" ref="qrContainer">
            <qrcode-vue 
              value="CIC-OFFICE-PRIMARY" 
              :size="250" 
              level="H" 
            />
            <div class="absolute inset-0 bg-black/60 opacity-0 group-hover:opacity-100 transition-opacity rounded-[2.3rem] flex items-center justify-center backdrop-blur-sm pointer-events-none">
              <p class="text-white text-[10px] font-bold uppercase tracking-widest text-center px-4">Klik Download untuk PNG 1080px</p>
            </div>
          </div>

          <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-2xl mb-6 border border-slate-100 dark:border-white/10 text-left">
            <ul class="text-[9px] space-y-2 text-slate-500 dark:text-slate-400 uppercase font-bold tracking-wider">
              <li class="flex items-center gap-2"><div class="w-1 h-1 bg-emerald-500 rounded-full"></div> Output: PNG High-Res (1080px)</li>
              <li class="flex items-center gap-2"><div class="w-1 h-1 bg-emerald-500 rounded-full"></div> Error Correction: Level H (High)</li>
            </ul>
          </div>

          <button type="button" @click="downloadQRHighRes" class="btn-refresh-eco w-full justify-center !bg-slate-900 hover:!bg-black shadow-2xl py-5">
            <Download class="w-4 h-4 mr-2" /> Download Cetak (1080px)
          </button>

          <div class="mt-8 pt-8 border-t border-dashed border-slate-200 dark:border-slate-800 text-left">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block mb-3">
              Kode Akses Manual
            </label>
            <div class="relative">
              <input 
                type="text" 
                v-model="form.static_qr_code" 
                class="input-field-eco font-mono font-bold text-center tracking-widest !bg-slate-50 dark:!bg-white/5 uppercase" 
                placeholder="CONTOH: CIC-OFFICE-2024"
              />
              <Keyboard class="absolute left-4 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
          </div>
        </div>

        <div class="card-eco p-8 bg-white dark:bg-[#121512] shadow-sm space-y-6">
          <h3 class="kpi-label flex items-center gap-2 mb-2 !text-emerald-600 font-bold">
            <Clock class="w-4 h-4" /> Aturan Kehadiran
          </h3>
          
          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-[9px] font-bold text-slate-400 uppercase">Jam Masuk</label>
              <input type="time" v-model="form.jam_masuk_kantor" step="1" class="input-field-eco font-bold" />
            </div>
            <div class="space-y-2">
              <label class="text-[9px] font-bold text-slate-400 uppercase">Jam Pulang</label>
              <input type="time" v-model="form.jam_pulang_kantor" step="1" class="input-field-eco font-bold" />
            </div>
          </div>

          <div class="pt-4 border-t border-slate-100 dark:border-white/5 space-y-3">
            <div class="flex justify-between items-center">
              <label class="text-[10px] font-bold text-rose-500 uppercase tracking-widest flex items-center gap-2">
                <AlertCircle class="w-3 h-3" /> Toleransi Keterlambatan
              </label>
              <span class="text-[10px] font-bold text-slate-400 uppercase">{{ form.toleransi_keterlambatan }} Menit</span>
            </div>
            <div class="relative">
              <input 
                type="number" 
                v-model="form.toleransi_keterlambatan" 
                class="input-field-eco font-bold text-rose-500 !bg-rose-50/30 dark:!bg-rose-500/5 !border-rose-100 dark:!border-rose-500/20" 
                placeholder="0"
              />
              <span class="absolute right-4 top-1/2 -translate-y-1/2 text-[9px] font-bold text-rose-400 uppercase">Menit</span>
            </div>
            <p class="text-[9px] text-slate-400 italic leading-relaxed">
              *Jika karyawan absen melewati batas {{ form.toleransi_keterlambatan }} menit dari jam masuk, sistem akan otomatis mencatat status <b>ALPA</b>.
            </p>
          </div>
        </div>
      </div>

      <div class="lg:col-span-7 space-y-8">
        <div class="card-eco overflow-hidden shadow-xl border-none">
          <div class="p-6 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/50 dark:bg-[#1a1d19]">
            <h3 class="kpi-label flex items-center gap-2 !mb-0 !text-emerald-600 font-bold">
              <MapPin class="w-4 h-4" /> Perimeter Geofencing
            </h3>
            <button type="button" @click="getCurrentLocation" class="btn-refresh-eco !py-2 !px-4 !text-[9px] shadow-sm">
              <Navigation class="w-3 h-3 mr-2" /> Deteksi Lokasi Saya
            </button>
          </div>
          
          <div id="map" class="h-[430px] w-full z-10 bg-slate-100 border-b border-slate-100 dark:border-slate-800"></div>

          <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6 font-poppins bg-white dark:bg-[#121512]">
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Latitude</label>
              <input type="text" v-model="form.company_latitude" @input="syncMapGraphic" class="input-field-eco font-mono text-xs" />
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Longitude</label>
              <input type="text" v-model="form.company_longitude" @input="syncMapGraphic" class="input-field-eco font-mono text-xs" />
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest block">Radius (m)</label>
              <input type="number" v-model="form.company_radius_meters" @input="syncMapGraphic" class="input-field-eco font-bold text-emerald-600" />
            </div>
          </div>
        </div>

        <button type="submit" :disabled="isSubmitting" class="btn-refresh-eco w-full justify-center py-6 rounded-3xl text-[11px] tracking-[0.3em] shadow-2xl shadow-emerald-500/20 font-black uppercase transition-all hover:-translate-y-1">
          <span v-if="isSubmitting" class="flex items-center gap-3 animate-pulse">
            <RefreshCw class="w-5 h-5 animate-spin" /> Sedang Menyimpan...
          </span>
          <span v-else class="flex items-center gap-2">
            <Save class="w-5 h-5" /> Simpan Konfigurasi Sistem
          </span>
        </button>
      </div>
    </form>

    <canvas id="hidden-qr-canvas" style="display: none;"></canvas>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import QrcodeVue from 'qrcode.vue';
import L from 'leaflet';
import 'leaflet/dist/leaflet.css';
import api from '@/services/api';
import axios from 'axios';
import Swal from 'sweetalert2';
import { 
  Settings, Clock, QrCode, Download, MapPin, 
  Navigation, Save, RefreshCw, Loader2, Keyboard, AlertCircle 
} from 'lucide-vue-next';

let map, marker, circle;
const isLoading = ref(true);
const isSubmitting = ref(false);
const successMessage = ref(null);
const errorMessage = ref(null);
const qrContainer = ref(null);

const form = ref({
    static_qr_code: '',
    jam_masuk_kantor: '08:00:00',
    jam_pulang_kantor: '17:00:00',
    toleransi_keterlambatan: 0,
    company_latitude: -6.680611,
    company_longitude: 107.517056,
    company_radius_meters: 50,
});

const createCustomMarker = () => {
    return L.divIcon({
        html: `<div class="marker-pin"></div><div class="marker-dot"></div>`,
        className: 'custom-div-icon',
        iconSize: [30, 42],
        iconAnchor: [15, 42]
    });
};

const initMap = async () => {
    await nextTick();
    const container = document.getElementById('map');
    if (!container || map) return;

    const lat = parseFloat(form.value.company_latitude) || -6.680611;
    const lng = parseFloat(form.value.company_longitude) || 107.517056;

    map = L.map('map', { zoomControl: true, attributionControl: false }).setView([lat, lng], 17);
    L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

    marker = L.marker([lat, lng], { draggable: true, icon: createCustomMarker() }).addTo(map);
    circle = L.circle([lat, lng], {
        radius: parseInt(form.value.company_radius_meters),
        color: '#2d4a3e', fillColor: '#10b981', fillOpacity: 0.15, weight: 2
    }).addTo(map);

    marker.on('dragend', (e) => {
        const { lat, lng } = e.target.getLatLng();
        form.value.company_latitude = lat.toFixed(8);
        form.value.company_longitude = lng.toFixed(8);
        syncMapGraphic();
    });

    map.on('click', (e) => {
        form.value.company_latitude = e.latlng.lat.toFixed(8);
        form.value.company_longitude = e.latlng.lng.toFixed(8);
        syncMapGraphic();
    });
};

const syncMapGraphic = () => {
    if (!marker || !circle || !map) return;
    const lat = parseFloat(form.value.company_latitude);
    const lng = parseFloat(form.value.company_longitude);
    if (!isNaN(lat) && !isNaN(lng)) {
        const latlng = [lat, lng];
        marker.setLatLng(latlng);
        circle.setLatLng(latlng);
        circle.setRadius(parseInt(form.value.company_radius_meters) || 0);
        map.panTo(latlng);
    }
};

const getCurrentLocation = () => {
    if (!navigator.geolocation) {
      Swal.fire({
        icon: 'warning',
        title: 'GPS Tidak Didukung',
        text: 'Peramban atau perangkat Anda tidak mendukung fitur Geolocation.',
        confirmButtonColor: '#2d4a3e',
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
        }
      });
      return;
    }

    navigator.geolocation.getCurrentPosition(
      (pos) => {
        form.value.company_latitude = pos.coords.latitude.toFixed(8);
        form.value.company_longitude = pos.coords.longitude.toFixed(8);
        syncMapGraphic();
        if(map) map.setView([pos.coords.latitude, pos.coords.longitude], 18);

        Swal.fire({
          icon: 'success',
          title: 'Lokasi Terdeteksi',
          text: 'Titik koordinat berhasil diperbarui sesuai lokasi GPS Anda.',
          timer: 1800,
          showConfirmButton: false,
          customClass: {
            popup: 'rounded-[2rem] font-poppins',
            title: 'text-[16px] font-bold',
            htmlContainer: 'text-[12px]'
          }
        });
      }, 
      (err) => {
        Swal.fire({
          icon: 'error',
          title: 'Deteksi Lokasi Gagal',
          text: err.message || 'Tidak dapat mengambil koordinat GPS saat ini. Pastikan izin lokasi aktif di browser Anda.',
          confirmButtonColor: '#2d4a3e',
          customClass: {
            popup: 'rounded-[2rem] font-poppins',
            confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
          }
        });
      }, 
      { enableHighAccuracy: true, timeout: 15000, maximumAge: 0 }
    );
};

const fetchSettings = async () => {
    isLoading.value = true;
    try {
        const res = await api.get('/admin/absensi/settings');
        const d = res.data.data;
        form.value = {
            static_qr_code: d.static_qr_code || '',
            jam_masuk_kantor: d.jam_masuk_kantor || '08:00:00',
            jam_pulang_kantor: d.jam_pulang_kantor || '17:00:00',
            toleransi_keterlambatan: parseInt(d.toleransi_keterlambatan) || 0,
            company_latitude: parseFloat(d.company_latitude) || -6.680611,
            company_longitude: parseFloat(d.company_longitude) || 107.517056,
            company_radius_meters: parseInt(d.company_radius_meters) || 50,
        };
    } catch (e) {
        console.error(e);
        Swal.fire({
          icon: 'error',
          title: 'Gagal Memuat Konfigurasi',
          text: 'Tidak dapat mengambil konfigurasi pengaturan dari server.',
          confirmButtonColor: '#2d4a3e',
          customClass: {
            popup: 'rounded-[2rem] font-poppins',
            confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
          }
        });
    } finally {
        setTimeout(() => { 
            isLoading.value = false;
            initMap(); 
        }, 500);
    }
};

const submitForm = async () => {
    isSubmitting.value = true;
    successMessage.value = null;
    errorMessage.value = null;

    try {
      const backendBase = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';
      await axios.get(`${backendBase}/sanctum/csrf-cookie`, { withCredentials: true });
      const payload = { ...form.value };
      const res = await api.put('/admin/absensi/settings', payload);
      
      const msg = res.data?.message || 'Konfigurasi sistem berhasil disimpan & disinkronkan.';
      successMessage.value = 'KONFIGURASI BERHASIL DISIMPAN';
      setTimeout(() => (successMessage.value = null), 4000);

      Swal.fire({
        icon: 'success',
        title: 'Berhasil Disimpan',
        text: msg,
        timer: 2000,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]'
        }
      });
    } catch (e) {
      console.error("Save settings error:", e);
      const errMsg = e.response?.data?.message || 'Gagal menyimpan perubahan konfigurasi sistem.';
      errorMessage.value = 'GAGAL MENYIMPAN PERUBAHAN';
      setTimeout(() => (errorMessage.value = null), 4000);

      Swal.fire({
        icon: 'error',
        title: 'Gagal Menyimpan',
        text: errMsg,
        confirmButtonColor: '#2d4a3e',
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
        }
      });
    } finally {
      isSubmitting.value = false;
    }
};

const downloadQRHighRes = () => {
    const originalCanvas = qrContainer.value?.querySelector('canvas');
    if (!originalCanvas) {
      Swal.fire({
        icon: 'error',
        title: 'Gagal Mengunduh',
        text: 'QR Code belum siap untuk diunduh.',
        confirmButtonColor: '#2d4a3e',
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
        }
      });
      return;
    }

    try {
      const totalSize = 1080; 
      const padding = 120; 
      const qrSize = totalSize - (padding * 2); 

      const highResCanvas = document.createElement('canvas');
      highResCanvas.width = totalSize;
      highResCanvas.height = totalSize;
      const ctx = highResCanvas.getContext('2d');

      ctx.fillStyle = '#FFFFFF';
      ctx.fillRect(0, 0, totalSize, totalSize);

      ctx.imageSmoothingEnabled = false;
      ctx.drawImage(
          originalCanvas, 
          padding, padding, 
          qrSize, qrSize   
      );

      const link = document.createElement('a');
      link.download = `QR-PERMANEN-CIC-HD-WHITE-SPACE.png`;
      link.href = highResCanvas.toDataURL("image/png", 1.0);
      link.click();

      Swal.fire({
        icon: 'success',
        title: 'Berhasil Diunduh',
        text: 'File QR Code resolusi tinggi (1080px) telah diunduh.',
        timer: 1800,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]'
        }
      });
    } catch (err) {
      console.error(err);
      Swal.fire({
        icon: 'error',
        title: 'Gagal Mengunduh',
        text: 'Terjadi kesalahan saat memproses gambar QR.',
        confirmButtonColor: '#2d4a3e',
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
        }
      });
    }
};

onMounted(fetchSettings);
</script>

<style scoped lang="postcss">
.card-eco { @apply rounded-[2.5rem] border border-slate-100 transition-all duration-300; }
.kpi-label { @apply text-[11px] uppercase tracking-[0.2em] opacity-80; }
.input-field-eco { @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-4 text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition-all; }
.btn-refresh-eco { @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest shadow-lg hover:shadow-emerald-900/20 transition-all active:scale-95; }

#map { border-radius: 1.5rem; }

:deep(.marker-pin) { 
  width: 30px; height: 30px; border-radius: 50% 50% 50% 0; 
  background: #2d4a3e; position: absolute; transform: rotate(-45deg); 
  left: 50%; top: 50%; margin: -15px 0 0 -15px; border: 3px solid #ffffff; 
}
:deep(.marker-dot) { 
  background: #ffffff; width: 8px; height: 8px; border-radius: 50%; 
  position: absolute; top: 50%; left: 50%; margin: -4px 0 0 -4px; 
}

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>