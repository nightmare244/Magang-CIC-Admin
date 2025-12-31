<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <FileCheck class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Daftar Pengajuan Izin
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 uppercase tracking-widest">
            Manajemen Verifikasi & Otorisasi Personel
          </p>
        </div>
      </div>

      <FilterIzin @filter="applyFilter" class="w-full lg:w-auto" />
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyinkronkan Berkas Pengajuan...</p>
    </div>

    <div v-else class="space-y-6">
      <div v-if="izinList && izinList.length > 0" class="grid grid-cols-1 gap-6">
        <IzinCard
          v-for="izin in izinList"
          :key="izin.id"
          :izin="izin"
          class="card-eco-premium"
          @detail="goToDetail"
          @updateStatus="handleStatusUpdate"
        />
      </div>

      <div v-else class="py-40 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
        <div class="opacity-10 mb-6 text-slate-400">
          <ClipboardX class="w-20 h-20" />
        </div>
        <p class="kpi-label !text-slate-400 uppercase tracking-widest">Tidak ada berkas pengajuan terdeteksi</p>
      </div>

      <div v-if="hasPagination" class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-100 dark:border-gray-800 gap-6">
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
          Navigasi Halaman Operasional
        </p>

        <div class="flex gap-3">
          <button
            @click="fetchData(rawResponse.prev_page_url)"
            :disabled="!rawResponse.prev_page_url"
            class="btn-pagination-eco"
          >
            <ChevronLeft class="w-4 h-4 mr-2" /> Sebelumnya
          </button>

          <button
            @click="fetchData(rawResponse.next_page_url)"
            :disabled="!rawResponse.next_page_url"
            class="btn-pagination-eco"
          >
            Berikutnya <ChevronRight class="w-4 h-4 ml-2" />
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { 
  FileCheck, ClipboardX, ChevronLeft, 
  ChevronRight 
} from 'lucide-vue-next';

/* Components */
import FilterIzin from './components/FilterIzin.vue';
import IzinCard from './components/IzinCard.vue';

const router = useRouter();
const rawResponse = ref({}); // Menyimpan response utuh untuk pagination
const izinList = ref([]);    // Menyimpan array data izin
const loading = ref(true);
const filterStatus = ref('');

// Navigasi ke Halaman Detail
const goToDetail = (id) => {
  router.push({ 
    name: 'admin.izin.detail', 
    params: { id: id } 
  });
};

// Fungsi Aksi Cepat (Setujui/Tolak dari Card)
const handleStatusUpdate = async ({ id, status }) => {
  let alasan_penolakan = null;
  if (status === 'ditolak') {
    alasan_penolakan = prompt("Masukkan alasan penolakan:");
    if (alasan_penolakan === null) return;
  } else {
    if (!confirm("Otorisasi pengajuan ini?")) return;
  }

  try {
    await api.put(`/admin/persetujuan-izin/${id}`, { 
      status,
      alasan_penolakan 
    });
    fetchData(); // Refresh data setelah update
  } catch (e) {
    alert("Gagal memproses aksi.");
  }
};

const fetchData = async (url = '/admin/persetujuan-izin') => {
  loading.value = true;
  try {
    const { data } = await api.get(url, {
      params: { status: filterStatus.value || undefined },
    });
    
    // Penyesuaian Response Laravel simplePaginate
    rawResponse.value = data;
    izinList.value = data.data || data; 
  } catch (e) {
    console.error("Gagal sinkronisasi API.");
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const applyFilter = (status) => {
  filterStatus.value = status;
  fetchData();
};

const hasPagination = computed(() => {
  return rawResponse.value.prev_page_url || rawResponse.value.next_page_url;
});

onMounted(() => {
  fetchData();
});
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

.btn-pagination-eco {
  @apply flex items-center px-6 py-3 bg-white dark:bg-[#121512] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-[#2d4a3e] hover:text-white
         transition-all active:scale-95 disabled:opacity-30 disabled:cursor-not-allowed;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>