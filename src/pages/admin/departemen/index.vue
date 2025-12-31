<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Building2 class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Daftar Departemen
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Manajemen struktur organisasi dan pembagian divisi kerja.
          </p>
        </div>
      </div>

      <button @click="goCreate" class="btn-refresh-eco group">
        <Plus class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" />
        Tambah Divisi Baru
      </button>
    </header>

    <div class="flex flex-col md:flex-row gap-6 items-center">
      <div class="relative w-full md:w-96 group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <Search class="w-5 h-5 text-slate-400 group-focus-within:text-[#2d4a3e] transition-colors" />
        </div>
        <input
          v-model="searchQuery"
          type="text"
          placeholder="Cari nama departemen..."
          class="input-field-eco !pl-12"
        />
      </div>

      <Transition name="slide-fade">
        <div v-if="apiError" class="flex items-center gap-3 px-4 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl animate-bounce">
          <AlertTriangle class="w-4 h-4 text-rose-600" />
          <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ apiError }}</span>
        </div>
      </Transition>
    </div>

    <div v-if="loading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <div v-for="i in 6" :key="i" class="h-56 bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 p-6 space-y-4">
        <div class="flex justify-between items-start">
          <div class="h-6 w-3/4 bg-slate-200 dark:bg-slate-800 rounded animate-pulse"></div>
          <div class="h-10 w-10 bg-slate-200 dark:bg-slate-800 rounded-xl animate-pulse"></div>
        </div>
        <div class="h-4 w-full bg-slate-100 dark:bg-slate-800/50 rounded animate-pulse"></div>
        <div class="h-4 w-2/3 bg-slate-100 dark:bg-slate-800/50 rounded animate-pulse"></div>
        <div class="pt-4 flex justify-end gap-2">
          <div class="h-8 w-8 bg-slate-200 dark:bg-slate-800 rounded-lg animate-pulse"></div>
          <div class="h-8 w-8 bg-slate-200 dark:bg-slate-800 rounded-lg animate-pulse"></div>
        </div>
      </div>
    </div>

    <div v-if="!loading && paginatedDepartemens.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
      <DepartemenCard
        v-for="item in paginatedDepartemens"
        :key="item.id"
        :departemen="item"
        class="kpi-card-new !p-0 overflow-hidden" 
        @detail="goDetail(item.id)"
        @edit="goEdit(item.id)"
        @delete="openDelete(item.id)"
      />
    </div>
    
    <div v-if="!loading && filteredDepartemens.length === 0" class="py-40 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
      <div class="opacity-10 mb-4 text-slate-400">
        <Building2 class="w-16 h-16" />
      </div>
      <p class="kpi-label !text-slate-400 uppercase tracking-widest">Unit departemen tidak ditemukan</p>
    </div>

    <div v-if="!loading && totalPages > 1" class="flex flex-col sm:flex-row justify-between items-center pt-8 border-t border-gray-100 dark:border-gray-800 gap-6">
      <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">
        Halaman <span class="text-[#2d4a3e] dark:text-emerald-500">{{ currentPage }}</span> dari {{ totalPages }}
      </p>
      
      <div class="flex gap-3">
        <button @click="previousPage" :disabled="currentPage === 1" class="btn-pagination-eco">
          <ChevronLeft class="w-4 h-4 mr-2" /> Sebelumnya
        </button>
        <button @click="nextPage" :disabled="currentPage === totalPages" class="btn-pagination-eco">
          Berikutnya <ChevronRight class="w-4 h-4 ml-2" />
        </button>
      </div>
    </div>

    <DeleteModal
      :show="deleteId !== null"
      :id="deleteId"
      message="Apakah Anda yakin ingin menghapus data departemen ini dari sistem?"
      @close="deleteId = null"
      @confirm="deleteDepartemen"
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from 'vue'; 
import { useRouter } from 'vue-router';
import api from '@/services/api'; 
import { 
  Building2, Plus, Search, AlertTriangle, 
  ChevronLeft, ChevronRight 
} from 'lucide-vue-next';

import DepartemenCard from './components/DepartemenCard.vue';
import DeleteModal from '@/pages/admin/departemen/components/DeleteModal.vue';

const router = useRouter();
const departemens = ref([]);
const deleteId = ref(null);
const loading = ref(false);
const searchQuery = ref('');
const currentPage = ref(1);
const pageSize = 9; 
const apiError = ref(null);

const filteredDepartemens = computed(() => {
  const query = searchQuery.value.toLowerCase();
  return departemens.value.filter(departemen =>
    departemen.nama_departemen.toLowerCase().includes(query)
  );
});

const totalPages = computed(() => Math.ceil(filteredDepartemens.value.length / pageSize));
const paginatedDepartemens = computed(() => {
  const start = (currentPage.value - 1) * pageSize;
  return filteredDepartemens.value.slice(start, start + pageSize);
});

const loadData = async () => {
  loading.value = true;
  apiError.value = null;
  try {
    const res = await api.get('/admin/departemens'); 
    departemens.value = res.data.data;
  } catch (error) {
    apiError.value = error.response?.data?.message || "Gagal sinkronisasi data.";
  } finally {
    // Beri jeda halus untuk loading state
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const goCreate = () => router.push({ name: 'admin.departemen.create' });
const goDetail = (id) => router.push({ name: 'admin.departemen.detail', params: { id } });
const goEdit = (id) => router.push({ name: 'admin.departemen.edit', params: { id } });
const openDelete = (id) => deleteId.value = id;

const deleteDepartemen = async () => {
  try {
    await api.delete(`/admin/departemens/${deleteId.value}`);
    loadData();
  } catch (error) {
    apiError.value = 'Otoritas penghapusan gagal.';
  } finally {
    deleteId.value = null;
  }
};

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const previousPage = () => { if (currentPage.value > 1) currentPage.value--; };

onMounted(loadData);
watch(searchQuery, () => { currentPage.value = 1; });
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-card-new {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 
         dark:border-gray-800 shadow-sm transition-all duration-500 
         hover:shadow-xl hover:-translate-y-2 hover:border-emerald-500/30;
}

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.input-field-eco {
  @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white font-poppins;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all disabled:opacity-50 cursor-pointer;
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

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>