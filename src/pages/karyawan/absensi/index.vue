<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden transition-colors duration-300">
    
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10 text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300 mb-2">Internal Service</p>
        <h1 class="text-3xl font-bold tracking-tight">Absensi Karyawan</h1>
        <p class="text-[11px] opacity-70 mt-1 font-medium italic">Ciwangun Indah Camp</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20">
      
      <div v-if="step === 'scan'" class="animate-fade-in-up">
        <div class="bg-white dark:bg-[#121512] rounded-[3.5rem] p-8 shadow-2xl border border-slate-50 dark:border-white/5 text-center">
          
          <div class="flex bg-slate-100 dark:bg-white/5 p-1 rounded-2xl mb-6">
            <button @click="showManual = false" :class="!showManual ? 'bg-white dark:bg-emerald-600 shadow-sm text-[#2d4a3e] dark:text-white' : 'text-slate-400'" class="flex-1 py-2 text-[10px] font-bold uppercase rounded-xl transition-all">Scan QR</button>
            <button @click="showManual = true" :class="showManual ? 'bg-white dark:bg-emerald-600 shadow-sm text-[#2d4a3e] dark:text-white' : 'text-slate-400'" class="flex-1 py-2 text-[10px] font-bold uppercase rounded-xl transition-all">Input Manual</button>
          </div>

          <div v-if="!showManual" class="space-y-4">
            <div class="relative overflow-hidden rounded-[3rem] bg-black aspect-square shadow-xl border-[8px] border-[#F8FAFC] dark:border-[#1a1d19]">
              <qrcode-stream
                :constraints="{ facingMode: 'user' }"
                :formats="['qr_code']"
                :track="paintOutline"
                @detect="onDetect"
                @error="onCameraError"
                class="h-full w-full"
              >
                <div class="absolute inset-0 flex items-center justify-center">
                  <div class="w-64 h-64 border-2 border-emerald-500/30 rounded-3xl relative overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-emerald-400 shadow-[0_0_15px_#10b981] animate-scan-line"></div>
                  </div>
                </div>
              </qrcode-stream>

              <div v-if="cameraFailed" class="absolute inset-0 flex flex-col items-center justify-center bg-slate-100 p-8">
                 <AlertCircle class="w-12 h-12 text-rose-500 mb-2" />
                 <p class="text-[10px] font-bold uppercase text-slate-400 text-center">Kamera Tidak Aktif atau Izin Ditolak</p>
              </div>
            </div>

            <div :class="statusClass" class="py-3 px-6 rounded-2xl transition-all duration-300 flex items-center justify-center gap-3 border min-h-[44px]">
              <div v-if="qrStatus === 'searching'" class="w-2 h-2 bg-slate-400 rounded-full animate-pulse"></div>
              <div v-if="qrStatus === 'found'" class="w-2 h-2 bg-amber-500 rounded-full animate-ping"></div>
              <div v-if="qrStatus === 'detected'" class="w-2 h-2 bg-emerald-500 rounded-full animate-bounce"></div>
              <span class="text-[10px] font-bold uppercase tracking-widest">{{ statusText }}</span>
            </div>
          </div>

          <div v-else class="animate-fade-in py-4">
             <div class="bg-emerald-50 dark:bg-emerald-500/5 p-6 rounded-[2.5rem] border border-emerald-100 dark:border-emerald-500/10">
                <Keyboard class="w-10 h-10 text-emerald-600 mx-auto mb-4" />
                <p class="text-[11px] text-slate-500 dark:text-slate-400 mb-6 leading-relaxed font-medium">
                   Masukkan kode akses yang diberikan oleh admin kantor.
                </p>
                <input 
                  v-model="qrCodeManual" 
                  type="text" 
                  class="input-cic mb-4 uppercase" 
                  placeholder="KODE MANUAL" 
                />
                <button @click="handleManualInput" class="btn-cic-primary w-full py-5 shadow-lg shadow-emerald-900/20">
                  Konfirmasi Kehadiran
                </button>
             </div>
          </div>
          
          <button @click="refreshPage" class="mt-8 text-[10px] font-bold text-slate-400 uppercase tracking-widest flex items-center justify-center gap-2 mx-auto transition-opacity hover:opacity-70">
             <RefreshCw class="w-3 h-3" /> Muat Ulang Kamera
          </button>
        </div>
      </div>

      <div v-else class="animate-fade-in-up bg-white dark:bg-[#121512] rounded-[3.5rem] p-12 text-center shadow-2xl min-h-[450px] flex flex-col justify-center items-center border border-white dark:border-white/5">
        
        <div v-if="step === 'process'" class="space-y-8">
          <div class="relative w-28 h-28 mx-auto">
            <div class="absolute inset-0 rounded-full border-4 border-[#2d4a3e]/10"></div>
            <div class="absolute inset-0 rounded-full border-4 border-[#2d4a3e] border-t-transparent animate-spin"></div>
            <MapPin class="w-8 h-8 absolute inset-0 m-auto text-[#2d4a3e] animate-bounce" />
          </div>
          <p class="text-[11px] font-bold uppercase tracking-widest text-slate-400 animate-pulse">{{ processingText }}</p>
        </div>

        <div v-if="step === 'result' && apiResult" class="space-y-10 w-full">
          <div :class="apiResult.type === 'success' ? 'text-emerald-500' : 'text-rose-500'">
            <CheckCircle v-if="apiResult.type === 'success'" class="w-24 h-24 mx-auto" />
            <XCircle v-else class="w-24 h-24 mx-auto" />
          </div>
          <div>
            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 uppercase tracking-tighter">{{ apiResult.type === 'success' ? 'Otorisasi Berhasil' : 'Otorisasi Gagal' }}</h3>
            <div class="bg-slate-50 dark:bg-white/5 p-6 rounded-[2rem] text-[12px] text-slate-500 font-medium leading-relaxed">
              {{ apiResult.message }}
            </div>
          </div>
          <button @click="resetFlow" class="btn-cic-primary w-full py-5 uppercase tracking-[0.2em]">Kembali</button>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, computed } from "vue";
import { QrcodeStream } from "vue-qrcode-reader";
import api from "@/services/api";
import Swal from 'sweetalert2';
import { 
  RefreshCw, TreePine, AlertCircle, 
  MapPin, CheckCircle, XCircle, Keyboard 
} from "lucide-vue-next";

// --- STATE ---
const step = ref('scan');
const showManual = ref(false);
const cameraFailed = ref(false);
const qrCodeManual = ref('');
const apiResult = ref(null);
const processingText = ref('');
const isProcessing = ref(false);
const qrStatus = ref('searching'); // searching, found, detected

// --- COMPUTED STATUS ---
const statusText = computed(() => {
  if (qrStatus.value === 'searching') return 'Mencari QR Code...';
  if (qrStatus.value === 'found') return 'QR Ditemukan! Menstabilkan...';
  if (qrStatus.value === 'detected') return 'QR Berhasil Dibaca!';
  return '';
});

const statusClass = computed(() => {
  if (qrStatus.value === 'searching') return 'bg-slate-50 border-slate-100 text-slate-400';
  if (qrStatus.value === 'found') return 'bg-amber-50 border-amber-200 text-amber-600';
  if (qrStatus.value === 'detected') return 'bg-emerald-50 border-emerald-200 text-emerald-600';
  return '';
});

// --- METHODS ---
const paintOutline = (detectedCodes, ctx) => {
  if (detectedCodes.length > 0) {
    if (qrStatus.value !== 'detected') qrStatus.value = 'found';
    for (const { boundingBox: { x, y, width, height } } of detectedCodes) {
      ctx.lineWidth = 6;
      ctx.strokeStyle = '#10b981';
      ctx.strokeRect(x, y, width, height);
    }
  } else {
    if (!isProcessing.value) qrStatus.value = 'searching';
  }
};

const onDetect = async (detectedCodes) => {
  if (isProcessing.value || !detectedCodes.length) return;

  const result = detectedCodes[0];
  const qrValue = result.rawValue || result.content;

  if (qrValue) {
    qrStatus.value = 'detected';
    isProcessing.value = true;
    
    // Feedback visual getar jika di HP
    if (navigator.vibrate) navigator.vibrate(100);

    setTimeout(() => {
      processAbsensi(qrValue.trim().toUpperCase());
    }, 800);
  }
};

const onCameraError = (err) => {
  console.error(err);
  cameraFailed.value = true;
};

const handleManualInput = () => {
  if (!qrCodeManual.value.trim() || isProcessing.value) return;
  isProcessing.value = true;
  processAbsensi(qrCodeManual.value.toUpperCase().trim());
};

const processAbsensi = (qrValue) => {
  step.value = 'process';
  processingText.value = 'Sinkronisasi GPS...';
  
  navigator.geolocation.getCurrentPosition(async (pos) => {
    try {
      processingText.value = 'Menyiapkan Keamanan...';
      await api.get('http://localhost:8000/sanctum/csrf-cookie');
      
      processingText.value = 'Memproses Absensi...';
      const response = await api.post("/karyawan/absensi", {
        qr_code: qrValue,
        latitude: pos.coords.latitude,
        longitude: pos.coords.longitude
      });
      
      const action = response.data.action === 'in' ? 'Masuk' : 'Pulang';
      apiResult.value = { 
        type: 'success', 
        message: `Absensi ${action} berhasil dicatat pada pukul ${response.data.data.jam_masuk || response.data.data.jam_pulang}` 
      };
      
      Swal.fire({
        title: 'Berhasil!',
        text: `Absen ${action} sukses.`,
        icon: 'success',
        confirmButtonColor: '#2d4a3e'
      });

    } catch (e) {
      apiResult.value = { 
        type: 'error', 
        message: e.response?.data?.message || 'Gagal sinkronisasi data ke server.' 
      };
    } finally {
      step.value = 'result';
    }
  }, (err) => {
    apiResult.value = { 
      type: 'error', 
      message: 'Gagal mendapatkan lokasi. Pastikan GPS aktif dan izin diberikan.' 
    };
    step.value = 'result';
  }, { 
    enableHighAccuracy: false,
    timeout: 10000 
  });
};

const resetFlow = () => {
  step.value = 'scan';
  isProcessing.value = false;
  qrStatus.value = 'searching';
  qrCodeManual.value = '';
};

const refreshPage = () => window.location.reload();
</script>

<style scoped lang="postcss">
.input-cic { 
  @apply w-full bg-slate-50 dark:bg-white/5 border-2 border-slate-100 dark:border-white/10 rounded-2xl px-6 py-4 text-center font-black outline-none focus:border-emerald-500 transition-all dark:text-white; 
}

.btn-cic-primary { 
  @apply bg-[#2d4a3e] text-white rounded-[2rem] font-bold text-[10px] tracking-[0.2em] transition-all uppercase shadow-md active:scale-95; 
}

@keyframes scan-line { 
  0% { top: 0%; } 
  100% { top: 100%; } 
}

.animate-scan-line { 
  position: absolute; 
  animation: scan-line 2.5s linear infinite; 
}

.animate-fade-in-up {
  animation: fadeInUp 0.5s ease-out forwards;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}

:deep(video) { 
  width: 100% !important; 
  height: 100% !important; 
  object-fit: cover !important; 
  border-radius: 2.5rem !important; 
}
</style>