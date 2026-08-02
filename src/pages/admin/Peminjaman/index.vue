<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <ClipboardList class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Persetujuan Peminjaman
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            Otorisasi Aset & Manajemen Logistik Operasional
          </p>
        </div>
      </div>
    </header>

    <div class="card-eco p-6 flex flex-col lg:flex-row gap-6 items-center bg-white/50 backdrop-blur-sm border-none shadow-sm font-poppins">
      
      <div class="flex flex-wrap gap-2 flex-grow">
        <button 
          v-for="opt in statusOptions" 
          :key="opt.value"
          @click="filters.status = opt.value"
          :class="[
            'px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 border',
            filters.status === opt.value 
              ? 'bg-[#2d4a3e] text-white border-[#2d4a3e] shadow-lg shadow-[#2d4a3e]/20 scale-105' 
              : 'bg-white dark:bg-[#1a1d19] text-slate-400 border-slate-100 dark:border-slate-800 hover:border-[#2d4a3e] hover:text-[#2d4a3e]'
          ]"
        >
          {{ opt.label }}
        </button>
      </div>

      <div class="relative w-full lg:w-96">
        <input
          type="text"
          v-model="searchQuery"
          placeholder="Cari pemohon atau nama barang..."
          class="input-field-eco !pl-12 w-full"
          @input="onSearch"
        />
        <Search class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300" />
      </div>
    </div>

    <Transition name="slide-fade">
      <div v-if="apiError" class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 rounded-r-xl font-bold text-sm uppercase tracking-widest flex items-center gap-3 shadow-sm">
        <AlertTriangle class="w-5 h-5" /> {{ apiError }}
      </div>
    </Transition>

    <div v-if="isLoading" class="py-40 text-center card-eco bg-transparent border-none">
        <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyinkronkan Basis Data Logistik...</p>
    </div>

    <div v-else class="animate-fade-in space-y-6">
        <div v-if="peminjamans.data.length > 0" class="grid grid-cols-1 gap-6 font-poppins">
            <PeminjamanCard 
                v-for="item in peminjamans.data" 
                :key="item.id" 
                :item="item"
                class="card-eco-premium"
            />
        </div>

        <div v-else class="py-40 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
            <div class="opacity-10 mb-6 text-slate-400">
                <PackageX class="w-20 h-20" />
            </div>
            <p class="kpi-label !text-slate-400 uppercase tracking-[0.2em]">Data Pengajuan Tidak Terdeteksi</p>
            <p class="text-xs text-slate-400 mt-2 italic font-medium">Coba sesuaikan kriteria filter atau kata kunci pencarian Anda.</p>
        </div>
    </div>

    <div v-if="!isLoading && peminjamans.meta && peminjamans.meta.last_page > 1" class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-100 dark:border-gray-800 gap-6 font-poppins">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Halaman <span class="text-[#2d4a3e] dark:text-emerald-500 font-black">{{ peminjamans.meta.current_page }}</span> dari {{ peminjamans.meta.last_page }}
        </p>
        <div class="flex gap-3">
            <button 
              @click="changePage(peminjamans.meta.current_page - 1)" 
              :disabled="peminjamans.meta.current_page === 1" 
              class="btn-pagination-eco"
            >
                <ChevronLeft class="w-4 h-4 mr-1" /> Sebelumnya
            </button>
            <button 
              @click="changePage(peminjamans.meta.current_page + 1)" 
              :disabled="peminjamans.meta.current_page === peminjamans.meta.last_page" 
              class="btn-pagination-eco"
            >
                Berikutnya <ChevronRight class="w-4 h-4 ml-1" />
            </button>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, reactive, watch } from 'vue';
import api from '@/services/api';
import { 
  ClipboardList, Search, AlertTriangle, 
  PackageX, ChevronLeft, ChevronRight 
} from 'lucide-vue-next';
import PeminjamanCard from './components/PeminjamanCard.vue';

const isLoading = ref(true);
const apiError = ref(null);
const peminjamans = ref({ data: [], meta: { current_page: 1, last_page: 1, per_page: 15 } }); 
const searchQuery = ref('');
let debounceTimer = null;

const filters = reactive({
    status: 'pending',
});

const statusOptions = [
  { label: 'Menunggu', value: 'pending' },
  { label: 'Aktif (Dipinjam)', value: 'disetujui' },
  { label: 'Ditolak', value: 'ditolak' },
  { label: 'Selesai', value: 'selesai' },
];

const fetchPeminjamans = async (page = 1) => {
    isLoading.value = true;
    apiError.value = null;
    try {
        const res = await api.get('/admin/persetujuan-peminjaman', {
            params: { 
                status: filters.status, 
                page, 
                search: searchQuery.value || undefined 
            }
        });
        peminjamans.value = res.data;
    } catch (error) {
        apiError.value = "OTORITAS GAGAL: Sinkronisasi data ke server pusat terputus.";
    } finally {
        setTimeout(() => { isLoading.value = false; }, 400);
    }
};

const changePage = (page) => {
    if (page >= 1 && page <= peminjamans.value.meta.last_page) {
        fetchPeminjamans(page);
    }
};

const onSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => fetchPeminjamans(1), 500);
};

watch(() => filters.status, () => fetchPeminjamans(1));

onMounted(() => fetchPeminjamans());
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.card-eco-premium {
  @apply transition-all duration-500 hover:shadow-2xl hover:-translate-y-1;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.input-field-eco {
  @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white font-poppins;
}

.btn-pagination-eco {
  @apply flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-[#2d4a3e] 
         hover:text-white transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed font-poppins;
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>