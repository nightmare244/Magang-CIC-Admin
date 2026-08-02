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
            <p class="text-[10px] font-medium text-emerald-400/90 leading-none mb-1 tracking-widest capitalize">Portal Logistik</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Rincian Aset</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-bold text-slate-400 tracking-widest capitalize">Sinkronisasi Data...</p>
      </div>

      <div v-else-if="apiError" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-10 text-center shadow-sm border border-rose-100 dark:border-white/5 animate-fade-in">
          <div class="w-16 h-16 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-rose-500">
            <AlertCircle class="w-8 h-8" />
          </div>
          <p class="text-[12px] font-bold text-slate-700 dark:text-white mb-6 capitalize">{{ apiError }}</p>
          <button @click="loadItem" class="btn-cic-secondary w-full py-4 text-[10px] font-bold tracking-widest capitalize">Coba Sinkronisasi Lagi</button>
      </div>

      <div v-else-if="item.id" class="space-y-6 animate-fade-in-up">
        
        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-3 shadow-sm border border-slate-100 dark:border-white/5 overflow-hidden group">
            <div class="relative overflow-hidden rounded-[2rem]">
              <img 
                  :src="getPhotoUrl(item.foto_barang)"
                  alt="Foto Barang"
                  class="w-full h-72 object-cover transition-transform duration-700 group-hover:scale-110"
                  @error="(e) => (e.target.src = '/img/default-inventaris.png')"
              />
              <div class="absolute top-4 right-4">
                <span :class="badgeClass(item.status_ketersediaan)" class="text-[9px] font-bold px-4 py-2 rounded-xl border shadow-lg tracking-wider capitalize backdrop-blur-md">
                  {{ item.status_ketersediaan ? item.status_ketersediaan.replace('_', ' ') : 'Status' }}
                </span>
              </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 relative overflow-hidden">
            <div class="absolute -top-6 -right-6 opacity-[0.03] dark:opacity-[0.05]">
              <Package class="w-32 h-32 text-emerald-900 dark:text-white" />
            </div>

            <div class="relative z-10">
              <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 tracking-[0.3em] mb-2">
                Sku: {{ item.kode_barang }}
              </p>
              <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight capitalize">
                {{ item.nama_barang }}
              </h2>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 space-y-8">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-[10px] font-bold text-slate-400 tracking-widest mb-1 capitalize">Jumlah Pinjam</p>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold italic capitalize">Tersedia: {{ item.quantity }} Unit</p>
              </div>
              
              <div class="flex items-center gap-4 bg-slate-50 dark:bg-white/5 p-1.5 rounded-2xl border border-slate-100 dark:border-white/5">
                <button 
                  @click="quantityToPinjam > 1 ? quantityToPinjam-- : null"
                  class="w-11 h-11 flex items-center justify-center bg-white dark:bg-white/10 rounded-xl shadow-sm text-slate-600 dark:text-white active:scale-90 transition-all border border-slate-100 dark:border-white/5"
                >
                  <Minus class="w-5 h-5" />
                </button>
                <span class="w-8 text-center font-bold text-lg text-slate-800 dark:text-white">{{ quantityToPinjam }}</span>
                <button 
                  @click="quantityToPinjam < item.quantity ? quantityToPinjam++ : null"
                  class="w-11 h-11 flex items-center justify-center bg-white dark:bg-white/10 rounded-xl shadow-sm text-slate-600 dark:text-white active:scale-90 transition-all border border-slate-100 dark:border-white/5"
                >
                  <Plus class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div class="space-y-4">
              <button 
                  v-if="item.status_ketersediaan === 'tersedia' && item.quantity > 0"
                  @click="addToCartAndCheckout"
                  class="btn-cic-primary w-full py-5 flex items-center justify-center gap-3"
              >
                  <ShoppingCart class="w-5 h-5" />
                  <span class="tracking-widest capitalize">Tambahkan Ke Keranjang</span>
              </button>
              
              <div v-else class="p-6 bg-slate-50 dark:bg-white/5 rounded-[2rem] text-center border border-dashed border-slate-200 dark:border-white/10">
                  <p class="text-[10px] font-bold text-slate-400 tracking-[0.2em] capitalize">
                    Aset Sedang Tidak Tersedia
                  </p>
              </div>

              <p v-if="quantityError" class="text-[9px] text-rose-500 font-bold text-center tracking-widest animate-bounce capitalize">
                <AlertCircle class="w-3 h-3 inline mr-1" /> {{ quantityError }}
              </p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 space-y-6">
            <div class="flex justify-between items-center bg-emerald-50/50 dark:bg-emerald-500/5 p-5 rounded-[1.5rem] border border-emerald-100/50 dark:border-emerald-500/10">
              <span class="text-[10px] font-bold text-slate-400 tracking-widest capitalize">Nilai Administrasi</span>
              <span class="text-base font-bold text-emerald-700 dark:text-emerald-400">{{ formatCurrency(item.harga_satuan) }}</span>
            </div>
            
            <div class="px-2">
              <span class="text-[10px] font-bold text-slate-400 tracking-widest block mb-3 flex items-center gap-2 capitalize">
                <FileText class="w-3.5 h-3.5" /> Deskripsi Teknis
              </span>
              <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-relaxed font-medium bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border border-slate-50 dark:border-white/5 italic">
                "{{ item.deskripsi || 'Tidak Ada Spesifikasi Tambahan Untuk Aset Ini.' }}"
              </p>
            </div>
        </div>

      </div>
    </div>
    
    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-[0.5em] capitalize">Ciwangun Indah Camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { useKeranjangStore } from '@/stores/keranjangStore';
import { 
  ChevronLeft, Package, ShoppingCart, 
  Minus, Plus, AlertCircle, FileText 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const cartStore = useKeranjangStore();

const loading = ref(true);
const item = ref({});
const apiError = ref(null);
const quantityToPinjam = ref(1);
const quantityError = ref(null);

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const loadItem = async () => {
    loading.value = true;
    apiError.value = null;
    try {
        const res = await api.get(`/karyawan/inventaris/${route.params.id}`); 
        item.value = res.data.data;
        quantityToPinjam.value = item.value.quantity > 0 ? 1 : 0;
    } catch (error) {
        apiError.value = error.response?.data?.message || 'Gagal memuat rincian aset.';
    } finally {
        setTimeout(() => { loading.value = false; }, 600);
    }
};

function getPhotoUrl(path) {
    if (!path) return '/img/default-inventaris.png';
    const cleanPath = path.replace(/^\/?storage\//i, '').replace(/^\/?public\//i, '');
    return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
}

function formatCurrency(value) {
    const num = Number(value);
    if (isNaN(num)) return '-';
    return num.toLocaleString("id-ID", { style: "currency", currency: "IDR", minimumFractionDigits: 0 });
}

function badgeClass(status) {
    const lowerStatus = status ? status.toLowerCase() : 'tidak_tersedia';
    if (lowerStatus === 'tersedia') return 'bg-emerald-500 text-white border-emerald-400';
    if (lowerStatus === 'dipinjam') return 'bg-amber-500 text-white border-amber-400';
    return 'bg-rose-500 text-white border-rose-400';
}

const addToCartAndCheckout = () => {
    if (quantityToPinjam.value <= 0 || quantityToPinjam.value > item.value.quantity) {
        quantityError.value = `Kuantitas Melebihi Batas Stok`;
        return;
    }
    const itemWithQuantity = { ...item.value, quantity_pinjam: quantityToPinjam.value };
    cartStore.addItem(itemWithQuantity);
    router.push('/karyawan/peminjaman/keranjang');
};

onMounted(loadItem);
</script>

<style scoped lang="postcss">
.btn-cic-primary {
    @apply bg-[#1e332a] text-white rounded-[1.5rem] font-bold text-[10px] 
           tracking-[0.2em] shadow-xl active:scale-95 transition-all;
}

.btn-cic-secondary {
    @apply bg-slate-50 dark:bg-white/5 text-slate-600 dark:text-slate-300 
           rounded-[1.5rem] font-bold text-[10px] tracking-widest 
           py-4 shadow-sm border border-slate-100 dark:border-white/5 
           active:scale-95 transition-all;
}

.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.8s ease-out forwards; }

* { -webkit-tap-highlight-color: transparent; }
</style>