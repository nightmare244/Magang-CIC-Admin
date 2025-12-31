<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] pb-32 font-poppins overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-28 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px]"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-md hover:bg-white/20 rounded-2xl transition-all border border-white/20 shadow-inner">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Detail Barang</h1>
        <div class="w-12"></div> 
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#121512] rounded-[3rem] p-20 text-center shadow-2xl border border-white dark:border-white/5 animate-fade-in">
          <div class="relative w-16 h-16 mx-auto mb-6">
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500/10"></div>
            <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
          </div>
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">Mengambil Data...</p>
      </div>

      <div v-else-if="apiError" class="bg-white dark:bg-[#121512] rounded-[3rem] p-10 text-center shadow-2xl border border-rose-100 animate-fade-in">
          <div class="w-16 h-16 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-4 text-rose-500">
            <AlertCircle class="w-8 h-8" />
          </div>
          <p class="text-sm font-bold text-slate-700 dark:text-white mb-6">{{ apiError }}</p>
          <button @click="loadItem" class="btn-cic-secondary w-full py-4 text-[10px] font-black uppercase tracking-widest">Coba Sinkronisasi Lagi</button>
      </div>

      <div v-else-if="item.id" class="space-y-6 animate-fade-in-up">
        
        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-3 shadow-2xl border border-white dark:border-white/5 overflow-hidden group">
            <div class="relative overflow-hidden rounded-[2.5rem]">
              <img 
                  :src="getPhotoUrl(item.foto_barang)"
                  alt="Foto Barang"
                  class="w-full h-72 object-cover transition-transform duration-700 group-hover:scale-110"
                  @error="(e) => (e.target.src = '/img/default-inventaris.png')"
              />
              <div class="absolute top-4 right-4">
                <span :class="badgeClass(item.status_ketersediaan)" class="text-[9px] font-black px-4 py-2 rounded-xl border shadow-lg uppercase tracking-wider backdrop-blur-md">
                  {{ item.status_ketersediaan ? item.status_ketersediaan.replace('_', ' ') : 'Status' }}
                </span>
              </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-2xl border border-white dark:border-white/5 relative overflow-hidden">
            <div class="absolute -top-6 -right-6 opacity-[0.03] dark:opacity-[0.05]">
              <Package class="w-32 h-32 text-emerald-900 dark:text-white" />
            </div>

            <div class="relative z-10">
              <p class="text-[10px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.3em] mb-2">
                SKU: {{ item.kode_barang }}
              </p>
              <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight">
                {{ item.nama_barang }}
              </h2>
            </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-2xl border border-white dark:border-white/5 space-y-8">
            <div class="flex items-center justify-between">
              <div>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-1">Jumlah Pinjam</p>
                <p class="text-[11px] text-emerald-600 dark:text-emerald-400 font-bold italic">Tersedia: {{ item.quantity }} Unit</p>
              </div>
              
              <div class="flex items-center gap-4 bg-slate-50 dark:bg-white/5 p-1.5 rounded-2xl border border-slate-100 dark:border-white/5">
                <button 
                  @click="quantityToPinjam > 1 ? quantityToPinjam-- : null"
                  class="w-11 h-11 flex items-center justify-center bg-white dark:bg-white/10 rounded-xl shadow-md text-slate-600 dark:text-white active:scale-90 transition-all border border-slate-100 dark:border-white/5"
                >
                  <Minus class="w-5 h-5" />
                </button>
                <span class="w-8 text-center font-black text-lg text-slate-800 dark:text-white">{{ quantityToPinjam }}</span>
                <button 
                  @click="quantityToPinjam < item.quantity ? quantityToPinjam++ : null"
                  class="w-11 h-11 flex items-center justify-center bg-white dark:bg-white/10 rounded-xl shadow-md text-slate-600 dark:text-white active:scale-90 transition-all border border-slate-100 dark:border-white/5"
                >
                  <Plus class="w-5 h-5" />
                </button>
              </div>
            </div>

            <div class="space-y-4">
              <button 
                  v-if="item.status_ketersediaan === 'tersedia' && item.quantity > 0"
                  @click="addToCartAndCheckout"
                  class="btn-cic-primary w-full py-5 flex items-center justify-center gap-3 shadow-emerald-900/30"
              >
                  <ShoppingCart class="w-5 h-5" />
                  <span class="tracking-widest">TAMBAHKAN KE KERANJANG</span>
              </button>
              
              <div v-else class="p-6 bg-slate-50 dark:bg-white/5 rounded-[2rem] text-center border border-dashed border-slate-200 dark:border-white/10">
                  <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">
                    Aset Sedang Tidak Tersedia
                  </p>
              </div>

              <p v-if="quantityError" class="text-[9px] text-rose-500 font-black text-center uppercase tracking-widest animate-bounce">
                <AlertCircle class="w-3 h-3 inline mr-1" /> {{ quantityError }}
              </p>
            </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-2xl border border-white dark:border-white/5 space-y-6">
            <div class="flex justify-between items-center bg-emerald-50/50 dark:bg-emerald-500/5 p-5 rounded-3xl border border-emerald-100/50 dark:border-emerald-500/10">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Nilai Administrasi</span>
              <span class="text-base font-black text-emerald-700 dark:text-emerald-400">{{ formatCurrency(item.harga_satuan) }}</span>
            </div>
            
            <div class="px-2">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest block mb-3 flex items-center gap-2">
                <FileText class="w-3.5 h-3.5" /> Deskripsi Teknis
              </span>
              <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed font-medium bg-slate-50 dark:bg-white/5 p-5 rounded-2xl border border-slate-50 dark:border-white/5">
                {{ item.deskripsi || 'Tidak ada spesifikasi tambahan untuk aset ini.' }}
              </p>
            </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { useKeranjangStore } from '@/stores/keranjangStore';
import { 
  ChevronLeft, Package, ShoppingCart, 
  Minus, Plus, AlertCircle, FileText 
} from 'lucide-vue-next';

// Logic Anda tetap sama, saya hanya merapikan cara memanggil data dan helper
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
        setTimeout(() => { loading.value = false; }, 400);
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
    return {
      'tersedia': 'bg-emerald-500 text-white border-emerald-400',
      'dipinjam': 'bg-amber-500 text-white border-amber-400',
      'tidak_tersedia': 'bg-rose-500 text-white border-rose-400',
    }[lowerStatus] || 'bg-slate-500 text-white';
}

const addToCartAndCheckout = () => {
    if (quantityToPinjam.value <= 0 || quantityToPinjam.value > item.value.quantity) {
        quantityError.value = `KUANTITAS MELEBIHI BATAS STOK`;
        return;
    }
    
    const itemWithQuantity = {
        ...item.value,
        quantity_pinjam: quantityToPinjam.value
    };

    cartStore.addItem(itemWithQuantity);
    router.push('/karyawan/peminjaman/keranjang');
};

onMounted(loadItem);
</script>

<style scoped lang="postcss">
.btn-cic-primary {
    @apply bg-[#2d4a3e] text-white rounded-[1.5rem] font-black text-[11px] 
           uppercase tracking-[0.2em] shadow-2xl shadow-emerald-900/20 
           active:scale-[0.97] transition-all duration-300;
}

.btn-cic-secondary {
    @apply bg-slate-50 dark:bg-white/10 text-slate-600 dark:text-slate-200 
           rounded-[1.5rem] font-black text-[10px] uppercase tracking-[0.1em] 
           py-4 shadow-sm border border-slate-100 dark:border-white/5 
           active:scale-95 transition-all;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
</style>