<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(2px);" 
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
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 tracking-wide">Portal Logistik</p>
            <h1 class="text-xl font-bold tracking-tight text-white">Katalog Inventaris</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 space-y-5 animate-fade-in-up">
        
        <div class="relative group">
          <input
            v-model="search"
            type="text"
            placeholder="Cari Nama Barang Atau Sku..."
            class="input-cic w-full pl-12 pr-6"
          />
          <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 transition-colors">
            <Search class="w-5 h-5 opacity-60" />
          </div>
        </div>
        
        <button @click="openScanner" class="btn-cic-secondary w-full group">
          <div class="flex items-center justify-center gap-3">
            <QrCode class="w-5 h-5 group-hover:rotate-12 transition-transform" />
            <span class="tracking-[0.2em]">Identifikasi Qr Code</span>
          </div>
        </button>
      </div>

      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-bold text-slate-400 tracking-widest">Sinkronisasi Data...</p>
      </div>

      <div v-else-if="filteredInventaris.length > 0" class="space-y-5 animate-fade-in-up">
        <InventarisCard
          v-for="item in filteredInventaris"
          :key="item.id"
          :item="item"
          @addToCartEvent="handleAddToCart"
        />
      </div>
      
      <div v-else class="text-center py-20 bg-white dark:bg-[#111311] rounded-[3rem] shadow-sm border border-dashed border-slate-200 dark:border-white/10 animate-fade-in">
        <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-4">
          <SearchX class="w-10 h-10 text-slate-300" />
        </div>
        <p class="text-[11px] font-bold text-slate-400 tracking-widest">Aset Tidak Ditemukan</p>
        <p class="text-[10px] text-slate-400/60 mt-3 font-medium px-12 leading-relaxed italic">
          Periksa Kembali Kata Kunci Anda Atau Hubungi Admin Logistik Untuk Bantuan.
        </p>
      </div>
    </div>

    <Transition name="overlay-fade">
      <div v-if="isScannerOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md">
        <div class="bg-white dark:bg-[#111311] w-full max-w-sm rounded-[3rem] p-10 shadow-2xl border border-white dark:border-white/5 animate-scale-up relative overflow-hidden">
          <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
          
          <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-[2.2rem] flex items-center justify-center mx-auto mb-6">
            <ScanLine class="w-10 h-10 text-emerald-600" />
          </div>

          <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 text-center">Pindai Aset</h3>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 text-center mb-8 leading-relaxed px-4 font-medium italic">
            Silahkan Masukkan Kode Identifikasi Secara Manual Untuk Verifikasi:
          </p>
          
          <div class="space-y-4">
            <input 
              v-model="scannedCode" 
              @keyup.enter="handleScanSubmit" 
              placeholder="ID BARANG (MISAL: HT-01)"
              class="input-cic w-full text-center uppercase tracking-[0.2em]" 
            />
            
            <div class="pt-2 space-y-3">
              <button @click="handleScanSubmit" class="btn-cic-primary w-full shadow-emerald-900/40" :disabled="!scannedCode">
                Proses Verifikasi
              </button>
              <button @click="closeScanner" class="w-full py-2 text-[10px] font-bold text-slate-400 tracking-widest hover:text-rose-500 transition-colors">
                Batalkan
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-[0.5em]">Ciwangun Indah Camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue';
import api from '@/services/api';
import { useRouter } from 'vue-router';
import InventarisCard from './components/InventarisCard.vue'; 
import { 
  ChevronLeft, Search, QrCode, AlertCircle, 
  Package, SearchX, ScanLine 
} from 'lucide-vue-next';

const router = useRouter();
const search = ref('');
const loading = ref(true);
const inventaris = ref([]);
const apiError = ref(null);
const isScannerOpen = ref(false);
const scannedCode = ref('');

onMounted(async () => {
  try {
    const res = await api.get('/karyawan/inventaris'); 
    inventaris.value = res.data.data;
  } catch (error) {
    apiError.value = error.response?.data?.message || 'Gagal Memuat Katalog Inventaris.';
  } finally {
    setTimeout(() => { loading.value = false; }, 600);
  }
});

const filteredInventaris = computed(() => {
    const query = search.value.toLowerCase();
    return inventaris.value.filter(item =>
        item.nama_barang.toLowerCase().includes(query) ||
        item.kode_barang.toLowerCase().includes(query)
    );
});

const handleScanSubmit = () => {
    if (!scannedCode.value) return;
    router.push(`/karyawan/inventaris/${scannedCode.value}`);
    isScannerOpen.value = false;
    scannedCode.value = '';
};

const openScanner = () => {
    scannedCode.value = '';
    isScannerOpen.value = true;
};

const closeScanner = () => isScannerOpen.value = false;

const handleAddToCart = (item) => {
    router.push(`/karyawan/inventaris/${item.kode_barang}`);
};
</script>

<style scoped lang="postcss">
.input-cic {
    @apply bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
           rounded-[1.5rem] px-6 py-4 text-xs outline-none font-bold 
           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all 
           dark:text-white placeholder:text-slate-300 placeholder:font-medium;
}

.btn-cic-primary {
    @apply bg-[#1e332a] text-white rounded-[1.5rem] font-bold text-[10px] 
           tracking-[0.2em] py-5 shadow-2xl active:scale-95 transition-all 
           disabled:opacity-40;
}

.btn-cic-secondary {
    @apply bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 
           rounded-[1.5rem] font-bold text-[10px] tracking-[0.2em] 
           py-5 shadow-sm border border-emerald-100 dark:border-emerald-500/20 
           active:scale-95 transition-all hover:bg-emerald-100/50;
}

/* Animasi Fade-In-Up Sesuai Acuan */
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

/* Animasi Scale-Up Modal */
@keyframes scaleUp {
  from { transform: scale(0.95); opacity: 0; }
  to { transform: scale(1); opacity: 1; }
}
.animate-scale-up { animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }

.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from, .overlay-fade-leave-to { opacity: 0; }

* { -webkit-tap-highlight-color: transparent; }
</style>