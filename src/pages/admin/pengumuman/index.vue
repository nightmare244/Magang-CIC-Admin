<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Megaphone class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Manajemen Pengumuman
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Administrasi internal: Publikasi informasi dan arsip digital karyawan.
          </p>
        </div>
      </div>

      <router-link
        to="/admin/pengumuman/create"
        class="btn-refresh-eco group"
      >
        <Plus class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" />
        Buat Pengumuman Baru
      </router-link>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="kpi-card-new group">
        <div class="relative z-10">
          <p class="kpi-label text-blue-600">Total Informasi</p>
          <h3 class="kpi-value">{{ pengumumans.length }}</h3>
          <p class="kpi-sub">Dokumen Terbit</p>
        </div>
        <div class="kpi-icon-wrapper bg-blue-50 dark:bg-blue-900/20">
          <BookOpen class="w-8 h-8 opacity-80 text-blue-600" />
        </div>
      </div>
    </div>

    <div class="card-eco overflow-hidden border-none shadow-xl">
      <div v-if="loading" class="text-center py-40">
        <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyusun arsip pengumuman...</p>
      </div>

      <div v-else class="animate-fade-in">
        <div v-if="pengumumans.length === 0" class="py-40 text-center font-poppins">
          <div class="flex flex-col items-center opacity-20">
            <BellOff class="w-16 h-16 mb-4" />
            <p class="kpi-label uppercase tracking-[0.2em]">Arsip Kosong</p>
          </div>
          <p class="text-slate-400 max-w-xs mx-auto text-xs mt-2 font-medium">Belum ada pengumuman resmi yang diterbitkan untuk karyawan.</p>
        </div>

        <div v-else class="p-6 grid grid-cols-1 gap-6">
          <div v-for="pengumuman in pengumumans" :key="pengumuman.id" class="transition-all duration-500 hover:-translate-y-1">
            <PengumumanCard 
              :pengumuman="pengumuman" 
              class="card-eco border border-gray-100 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all"
              @delete="openDeleteModal(pengumuman)" 
            />
          </div>
        </div>
      </div>
    </div>

    <DeleteModal 
      v-if="showDeleteModal" 
      :pengumuman="selectedPengumuman" 
      @close="closeDeleteModal" 
      @confirm="handleDeleted"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { Megaphone, Plus, BellOff, BookOpen } from 'lucide-vue-next';
import PengumumanCard from "./components/PengumumanCard.vue";
import DeleteModal from "./components/DeleteModal.vue";

const pengumumans = ref([]);
const loading = ref(true);
const showDeleteModal = ref(false);
const selectedPengumuman = ref(null);

const loadPengumumans = async () => {
  loading.value = true;
  try {
    const response = await api.get("/admin/pengumuman");
    pengumumans.value = response.data.data || response.data;
  } catch (error) {
    console.error("Gagal memuat data pengumuman:", error);
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const openDeleteModal = (pengumuman) => {
  selectedPengumuman.value = pengumuman;
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  selectedPengumuman.value = null;
};

const handleDeleted = () => {
  closeDeleteModal();
  loadPengumumans(); 
};

onMounted(loadPengumumans);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-card-new {
  @apply bg-white dark:bg-[#121512] p-6 rounded-[1.8rem] border border-gray-100 
         dark:border-gray-800 shadow-sm relative overflow-hidden transition-all 
         duration-500 hover:shadow-xl hover:-translate-y-1 flex items-center justify-between;
}

.kpi-label {
  @apply text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60;
}

.kpi-value {
  @apply text-3xl font-bold text-slate-800 dark:text-white;
}

.kpi-sub {
  @apply text-[10px] text-slate-400 mt-1 font-medium;
}

.kpi-icon-wrapper {
  @apply w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110;
}

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>