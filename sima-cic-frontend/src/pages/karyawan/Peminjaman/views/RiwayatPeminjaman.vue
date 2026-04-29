<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
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
            <p class="text-[10px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-[0.2em]">inventaris sistem</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Riwayat pinjam</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <section class="bg-white/95 dark:bg-[#151815]/95 backdrop-blur-md rounded-[2.5rem] p-2 shadow-2xl shadow-black/10 border border-white/20">
        <router-link
          to="/karyawan/inventaris"
          class="flex items-center justify-center gap-3 bg-[#1e332a] text-white py-4 rounded-[2rem] transition-all active:scale-95 shadow-xl shadow-emerald-900/20"
        >
          <PlusCircle class="w-5 h-5 text-emerald-400" />
          <span class="text-[10px] font-black tracking-widest capitalize">Pinjam Barang Baru</span>
        </router-link>
      </section>

      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">sinkronisasi...</p>
      </div>

      <template v-else>
<section class="animate-fade-in-up">
        <div class="flex items-center justify-between mb-4 px-5">
          <div class="flex items-center gap-3">
            <div class="w-[3px] h-4 bg-emerald-500 rounded-full"></div>
            <h3 class="text-[10px] font-black text-slate-400 dark:text-slate-500 tracking-[0.2em] capitalize">Pilih Status</h3>
          </div>
          
          <div class="flex gap-1.5 items-center">
            <span class="text-[8px] font-black text-emerald-500/50 uppercase tracking-tighter mr-1">Filter</span>
            <div class="w-1.5 h-1.5 bg-emerald-500 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.6)]"></div>
            <div class="w-3 h-1 bg-emerald-500/20 rounded-full"></div>
          </div>
        </div>
        
        <div class="bg-white/50 dark:bg-[#111311]/50 backdrop-blur-sm rounded-[2.2rem] p-1.5 border border-slate-100 dark:border-white/5 shadow-sm">
          <FilterPeminjaman 
            @filter="applyFilter" 
            class="filter-custom-style"
          />
        </div>
      </section>

        <section class="animate-fade-in-up" style="animation-delay: 200ms">
          <div v-if="!peminjaman.length" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-16 text-center border border-slate-100 dark:border-white/5 shadow-sm">
            <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-slate-300">
              <PackageSearch class="w-8 h-8 opacity-20" />
            </div>
            <p class="text-[10px] font-black text-slate-400 tracking-[0.2em] uppercase leading-relaxed">belum ada data<br>peminjaman</p>
          </div>

          <div v-else class="space-y-4">
            <PeminjamanCard
              v-for="item in peminjaman"
              :key="item.id"
              :peminjaman="item"
              @click="goDetail(item.id)"
              class="transition-all active:scale-[0.97]"
            />
          </div>
        </section>
      </template>

      <footer class="pt-10 pb-6 text-center">
        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-widest uppercase">ciwangun indah camp</p>
      </footer>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { PlusCircle, History, PackageSearch, ChevronLeft } from 'lucide-vue-next';

import FilterPeminjaman from '../components/FilterPeminjaman.vue';
import PeminjamanCard from '../components/PeminjamanCard.vue';

const router = useRouter();
const peminjaman = ref([]);
const loading = ref(true);
const filterStatus = ref('');

const fetchData = async () => {
  loading.value = true;
  try {
    const res = await api.get('/karyawan/peminjaman', {
      params: { status: filterStatus.value || undefined }
    });
    peminjaman.value = res.data.data;
  } catch (err) {
    console.error("fetch error:", err);
  } finally {
    setTimeout(() => { loading.value = false; }, 600);
  }
};

const applyFilter = (status) => {
  filterStatus.value = status;
  fetchData();
};

const goDetail = (id) => {
  router.push(`/karyawan/peminjaman/${id}`);
};

onMounted(fetchData);
</script>

<style scoped>
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

* {
  -webkit-tap-highlight-color: transparent;
}

::-webkit-scrollbar { width: 4px; }
::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
</style>