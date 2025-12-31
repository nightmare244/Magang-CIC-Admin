<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-32 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden mb-12">
      <div class="absolute -right-10 -top-10 w-72 h-72 bg-emerald-500/20 rounded-full blur-[90px]"></div>
      <div class="absolute -left-10 -bottom-10 w-48 h-48 bg-emerald-400/10 rounded-full blur-[70px]"></div>
      
      <div class="relative z-10 flex flex-col items-center">
        <div class="w-16 h-16 bg-white/10 backdrop-blur-xl rounded-2xl flex items-center justify-center mb-5 border border-white/20 shadow-inner animate-fade-in">
          <PackageSearch class="w-8 h-8 text-emerald-300" />
        </div>
        <p class="text-[10px] font-black uppercase tracking-[0.5em] text-emerald-400 mb-2">Sistem Logistik</p>
        <h1 class="text-3xl font-bold tracking-tight text-center">Katalog Inventaris</h1>
        <p class="text-[11px] opacity-60 mt-3 font-medium italic text-emerald-50 tracking-wide text-center max-w-[250px]">
          Ciwangun Indah Camp - Asset Management & Internal Resources
        </p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-20 relative z-20 space-y-8">
      
      <div class="bg-white dark:bg-[#121512] p-6 rounded-[2.5rem] shadow-2xl border border-white dark:border-white/5 space-y-4">
        <div class="relative group">
            <input
              v-model="search"
              type="text"
              placeholder="Cari nama barang atau SKU..."
              class="input-cic w-full pl-12 pr-6"
            />
            <div class="absolute left-4 top-1/2 -translate-y-1/2 text-emerald-600 group-focus-within:text-emerald-400 transition-colors">
              <Search class="w-5 h-5" />
            </div>
        </div>
        
        <button @click="openScanner" class="btn-cic-secondary w-full group">
            <div class="flex items-center justify-center gap-3">
              <QrCode class="w-5 h-5 group-hover:rotate-12 transition-transform" />
              <span class="tracking-[0.2em]">IDENTIFIKASI QR CODE</span>
            </div>
        </button>
      </div>

      <Transition name="slide-up">
        <div v-if="apiError" class="p-5 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 rounded-3xl border border-rose-100 dark:border-rose-900/30 text-[10px] font-black uppercase tracking-widest text-center">
            <AlertCircle class="w-4 h-4 inline mr-2 mb-0.5" />
            {{ apiError }}
        </div>
      </Transition>

      <div v-if="loading" class="text-center py-20 animate-fade-in">
          <div class="relative w-24 h-24 mx-auto mb-6">
            <div class="absolute inset-0 rounded-full border-[3px] border-emerald-500/10"></div>
            <div class="absolute inset-0 rounded-full border-[3px] border-emerald-500 border-t-transparent animate-spin"></div>
            <Package class="w-8 h-8 absolute inset-0 m-auto text-emerald-500/20 animate-pulse" />
          </div>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.4em]">Sinkronisasi Data...</p>
      </div>

      <div v-else-if="filteredInventaris.length > 0" class="space-y-5 animate-fade-in-up">
        <InventarisCard
          v-for="item in filteredInventaris"
          :key="item.id"
          :item="item"
          @addToCartEvent="handleAddToCart"
        />
      </div>
      
      <div v-else class="text-center py-24 bg-white dark:bg-[#121512] rounded-[3rem] shadow-xl border border-dashed border-slate-200 dark:border-white/10 animate-fade-in">
          <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4">
            <SearchX class="w-10 h-10 text-slate-300" />
          </div>
          <p class="text-[11px] font-black text-slate-400 uppercase tracking-widest">Aset Tidak Ditemukan</p>
          <p class="text-[10px] text-slate-400/60 mt-3 italic font-medium px-12 leading-relaxed">
            Periksa kembali kata kunci atau hubungi admin logistik untuk bantuan.
          </p>
      </div>
    </div>

    <div v-if="isScannerOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md animate-fade-in">
        <div class="bg-white dark:bg-[#121512] w-full max-w-sm rounded-[3rem] p-10 shadow-2xl border border-white dark:border-white/5 animate-scale-up relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1.5 bg-gradient-to-r from-transparent via-emerald-500 to-transparent"></div>
            
            <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-[2.2rem] flex items-center justify-center mx-auto mb-6">
              <ScanLine class="w-10 h-10 text-emerald-600" />
            </div>

            <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 text-center">Pindai Aset</h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 text-center mb-8 leading-relaxed px-4 font-medium italic">
                Arahkan kamera ke barcode atau masukkan kode identifikasi secara manual:
            </p>
            
            <div class="space-y-4">
              <input 
                  v-model="scannedCode" 
                  @keyup.enter="handleScanSubmit" 
                  placeholder="ID BARANG (MISAL: HT-01)"
                  class="input-cic w-full text-center uppercase tracking-[0.3em]" 
              />
              
              <div class="pt-2 space-y-3">
                  <button @click="handleScanSubmit" class="btn-cic-primary w-full shadow-emerald-900/40" :disabled="!scannedCode">
                    PROSES VERIFIKASI
                  </button>
                  <button @click="closeScanner" class="w-full py-2 text-[10px] font-black text-slate-400 uppercase tracking-widest hover:text-rose-500 transition-colors">
                    Batalkan
                  </button>
              </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue';
import api from '@/services/api';
import { useRouter } from 'vue-router';
import { useKeranjangStore } from '@/stores/keranjangStore'; 
import InventarisCard from './components/InventarisCard.vue'; 
import { 
  PackageSearch, Search, QrCode, AlertCircle, 
  Package, SearchX, ScanLine, AlertTriangle 
} from 'lucide-vue-next';

const router = useRouter();
const cartStore = useKeranjangStore();
const search = ref('');
const loading = ref(true);
const inventaris = ref([]);
const apiError = ref(null);
const isScannerOpen = ref(false);
const scannedCode = ref('');
const scanError = ref(null);

onMounted(async () => {
  try {
    const res = await api.get('/karyawan/inventaris'); 
    inventaris.value = res.data.data;
  } catch (error) {
    apiError.value = error.response?.data?.message || 'Gagal memuat katalog inventaris.';
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
});

const filteredInventaris = computed(() => {
    const query = search.value.toLowerCase();
    return inventaris.value.filter(item =>
        item.nama_barang.toLowerCase().includes(query) ||
        item.kode_barang.toLowerCase().includes(query)
    );
});

const handleDetailClick = (kode_barang) => {
    router.push(`/karyawan/inventaris/${kode_barang}`);
    isScannerOpen.value = false; 
};

const handleScanSubmit = () => {
    if (!scannedCode.value) {
        scanError.value = "KODE HARUS DIISI";
        return;
    }
    handleDetailClick(scannedCode.value);
    scannedCode.value = '';
};

const openScanner = () => {
    scannedCode.value = '';
    scanError.value = null;
    isScannerOpen.value = true;
};

const closeScanner = () => {
    isScannerOpen.value = false;
};

const handleAddToCart = (item) => {
    router.push(`/karyawan/inventaris/${item.kode_barang}`);
};

watch(scannedCode, () => { scanError.value = null; });
</script>

<style scoped lang="postcss">
.input-cic {
    @apply bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
           rounded-[1.5rem] px-6 py-4 text-xs outline-none font-bold 
           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all 
           dark:text-white placeholder:text-slate-300 placeholder:font-medium;
}

.btn-cic-primary {
    @apply bg-[#2d4a3e] text-white rounded-[1.5rem] font-black text-[10px] 
           uppercase tracking-[0.2em] py-5 shadow-2xl active:scale-[0.98] transition-all 
           disabled:opacity-40 disabled:grayscale;
}

.btn-cic-secondary {
    @apply bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 
           rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.2em] 
           py-5 shadow-sm border border-emerald-100 dark:border-emerald-500/20 
           active:scale-[0.98] transition-all hover:bg-emerald-100/50;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(50px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.9s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }

@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-scale-up { animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

.slide-up-enter-active { transition: all 0.4s ease-out; }
.slide-up-enter-from { opacity: 0; transform: translateY(20px); }
</style>