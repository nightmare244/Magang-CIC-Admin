<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4 mb-4">
          <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 shadow-xl">
            <QrCode class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">portal kehadiran</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Scan Absensi</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30">
      
      <div v-if="step === 'scan'" class="animate-fade-in-up space-y-5">
        
        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-4 shadow-sm border border-slate-100 dark:border-white/5 space-y-5">
          
          <div class="relative overflow-hidden rounded-[2rem] bg-black aspect-square shadow-inner border-4 border-slate-50 dark:border-white/5">
            <button @click="toggleCamera" class="absolute top-4 right-4 z-50 bg-[#1e332a]/40 backdrop-blur-md p-2.5 rounded-2xl border border-white/20 text-white active:scale-95 transition-all">
              <SwitchCamera class="w-5 h-5" />
            </button>

            <qrcode-stream
              :constraints="{ facingMode: facingMode }"
              :formats="['qr_code']"
              :track="paintOutline"
              @detect="onDetect"
              @error="onCameraError"
              class="h-full w-full"
            >
              <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                <div class="w-64 h-64 border-2 border-white/10 rounded-[2.5rem] relative">
                  <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 shadow-[0_0_20px_#10b981] animate-scan-line"></div>
                  <div class="absolute -top-1 -left-1 w-10 h-10 border-t-4 border-l-4 border-emerald-500 rounded-tl-3xl opacity-60"></div>
                  <div class="absolute -top-1 -right-1 w-10 h-10 border-t-4 border-r-4 border-emerald-500 rounded-tr-3xl opacity-60"></div>
                  <div class="absolute -bottom-1 -left-1 w-10 h-10 border-b-4 border-l-4 border-emerald-500 rounded-bl-3xl opacity-60"></div>
                  <div class="absolute -bottom-1 -right-1 w-10 h-10 border-b-4 border-r-4 border-emerald-500 rounded-br-3xl opacity-60"></div>
                </div>
              </div>
            </qrcode-stream>

            <div v-if="cameraFailed" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 dark:bg-slate-900 p-8 text-center">
              <AlertCircle class="w-10 h-10 text-rose-500 mb-3" />
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest leading-tight">gagal memuat kamera</p>
            </div>
          </div>

          <div class="relative flex items-center justify-center py-2">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-slate-100 dark:border-white/5"></div>
            </div>
            <span class="relative px-4 bg-white dark:bg-[#111311] text-[10px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">atau</span>
          </div>

          <div class="space-y-4 pb-2">
            <div class="relative group">
              <div class="absolute left-5 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-emerald-500 transition-colors">
                <Hash class="w-5 h-5 opacity-40" />
              </div>
              <input 
                v-model="qrCodeManual" 
                type="text" 
                class="w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 rounded-2xl pl-14 pr-6 py-4.5 text-sm font-bold outline-none focus:border-emerald-500/50 transition-all dark:text-white uppercase tracking-[0.3em]" 
                placeholder="KODE-AKSES" 
              />
            </div>

            <button 
              @click="handleManualInput" 
              class="relative group overflow-hidden w-full bg-[#1e332a] text-white py-5 rounded-[2rem] font-bold text-[13px] uppercase tracking-[0.15em] transition-all duration-300 active:scale-95 shadow-lg dark:shadow-emerald-900/20"
            >
              <div class="flex items-center justify-center gap-3 relative z-10">
                <span class="tracking-[0.2em] font-black text-[11px]">konfirmasi kehadiran</span>
                <ArrowRight class="w-4 h-4 text-emerald-400" />
              </div>
            </button>
          </div>
        </div>

        <div class="bg-blue-50/50 dark:bg-blue-500/5 border border-blue-100 dark:border-blue-500/10 rounded-[2rem] p-5 flex items-center gap-4">
          <div class="w-10 h-10 bg-blue-500 rounded-xl flex items-center justify-center text-white shrink-0 shadow-lg shadow-blue-500/20">
            <MapPin class="w-5 h-5" />
          </div>
          <p class="text-[10px] text-blue-900/70 dark:text-blue-400 font-black leading-relaxed lowercase tracking-wider">
            lokasi gps aktif. pastikan anda berada di area kerja ciwangun indah camp.
          </p>
        </div>
      </div>

      <div v-else class="animate-fade-in-up">
        <div class="bg-white dark:bg-[#111311] rounded-[3rem] p-10 text-center shadow-sm border border-slate-50 dark:border-white/5 min-h-[400px] flex flex-col justify-center items-center">
          <div v-if="step === 'process'" class="space-y-6">
            <div class="w-16 h-16 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto"></div>
            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest">{{ processingText }}</p>
          </div>

          <div v-if="step === 'result' && apiResult" class="w-full space-y-8 animate-fade-in">
            <div class="flex flex-col items-center">
              <div :class="apiResult.type === 'success' ? 'bg-emerald-500/10 text-emerald-600' : 'bg-rose-500/10 text-rose-500'" class="w-20 h-20 rounded-[2rem] flex items-center justify-center mb-6 border border-current/10">
                <component :is="apiResult.type === 'success' ? CheckCircle : XCircle" class="w-10 h-10" />
              </div>
              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight uppercase">{{ apiResult.type === 'success' ? 'berhasil' : 'gagal' }}</h2>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 rounded-[2rem] p-6 text-left border border-slate-100 dark:border-white/5">
              <p class="text-[12px] text-slate-600 dark:text-slate-300 font-medium italic">"{{ apiResult.message }}"</p>
            </div>
            <button @click="resetFlow" class="w-full bg-[#1e332a] text-white py-4.5 rounded-[2rem] font-bold text-[11px] uppercase tracking-widest active:scale-95 transition-all">tutup</button>
          </div>
        </div>
      </div>

    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-[0.3em] uppercase">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { QrcodeStream } from "vue-qrcode-reader";
import api from "@/services/api";
import Swal from 'sweetalert2';
import { 
  RefreshCw, AlertCircle, MapPin, CheckCircle, XCircle, 
  SwitchCamera, Hash, ArrowRight, QrCode
} from "lucide-vue-next";

const step = ref('scan');
const cameraFailed = ref(false);
const qrCodeManual = ref('');
const apiResult = ref(null);
const processingText = ref('');
const isProcessing = ref(false);
const facingMode = ref('environment');

const toggleCamera = () => { facingMode.value = facingMode.value === 'environment' ? 'user' : 'environment'; };

const onDetect = async (detectedCodes) => {
  if (isProcessing.value || !detectedCodes.length) return;
  const qrValue = detectedCodes[0].rawValue || detectedCodes[0].content;
  if (qrValue) {
    isProcessing.value = true;
    if (navigator.vibrate) navigator.vibrate(100);
    processAbsensi(qrValue.trim().toUpperCase());
  }
};

const onCameraError = () => { cameraFailed.value = true; };

const handleManualInput = () => {
  if (!qrCodeManual.value.trim() || isProcessing.value) return;
  isProcessing.value = true;
  processAbsensi(qrCodeManual.value.toUpperCase().trim());
};

const processAbsensi = (qrValue) => {
  step.value = 'process';
  processingText.value = 'menentukan lokasi...';
  
  navigator.geolocation.getCurrentPosition(async (pos) => {
    try {
      processingText.value = 'sinkronisasi server...';
      const response = await api.post("/karyawan/absensi", {
        qr_code: qrValue,
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude
      });
      
      const action = response.data.action === 'in' ? 'Masuk' : 'Pulang';
      apiResult.value = { 
        type: 'success', 
        message: `Presensi ${action} anda telah divalidasi sistem pada pukul ${response.data.data.jam_masuk || response.data.data.jam_pulang}.` 
      };
    } catch (e) {
      apiResult.value = { 
        type: 'error', 
        message: e.response?.data?.message || 'Otorisasi gagal. Pastikan kode benar dan anda di lokasi.' 
      };
    } finally { step.value = 'result'; }
  }, () => {
    apiResult.value = { type: 'error', message: 'Izin lokasi (GPS) wajib aktif.' };
    step.value = 'result';
  }, { enableHighAccuracy: true });
};

const resetFlow = () => { 
  step.value = 'scan'; 
  isProcessing.value = false; 
  qrCodeManual.value = ''; 
};
</script>

<style scoped lang="postcss">
@keyframes scan-line { 0% { top: 0%; opacity: 0; } 20%, 80% { opacity: 1; } 100% { top: 100%; opacity: 0; } }
.animate-scan-line { animation: scan-line 2.5s ease-in-out infinite; }
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }
:deep(video) { width: 100% !important; height: 100% !important; object-fit: cover !important; border-radius: 1.8rem !important; }
* { -webkit-tap-highlight-color: transparent; }
</style>