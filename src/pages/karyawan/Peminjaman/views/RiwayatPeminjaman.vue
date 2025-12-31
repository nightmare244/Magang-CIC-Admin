<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="absolute left-4 top-12 opacity-10">
        <History class="w-24 h-24" />
      </div>
      
      <div class="relative z-10 flex flex-col items-center text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300 mb-2">Inventory System</p>
        <h1 class="text-3xl font-bold tracking-tight">Riwayat Pinjam</h1>
        <p class="text-[11px] opacity-70 mt-1 font-medium italic text-emerald-50">Ciwangun Indah Camp - Asset Management</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20 space-y-6">
      
      <div class="bg-white dark:bg-[#121512] p-2 rounded-[3rem] shadow-xl border border-white dark:border-white/5">
        <router-link
          to="/karyawan/inventaris"
          class="flex items-center justify-center gap-3 bg-[#2d4a3e] hover:bg-[#1e332a] text-white py-4 rounded-[2.5rem] transition-all active:scale-[0.98] shadow-lg shadow-emerald-900/20"
        >
          <PlusCircle class="w-5 h-5" />
          <span class="text-xs font-bold uppercase tracking-widest">Pinjam Barang Baru</span>
        </router-link>
      </div>

      <section class="animate-fade-in-up">
        <div class="flex items-center gap-2.5 mb-3 px-2">
          <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
          <h3 class="text-xs font-bold text-slate-700 dark:text-emerald-400 uppercase tracking-wider">Filter Status</h3>
        </div>
        <FilterPeminjaman @filter="applyFilter" />
      </section>

      <section class="animate-fade-in-up" style="animation-delay: 150ms">
        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-40 bg-white dark:bg-white/5 rounded-[2.5rem] animate-pulse border border-slate-50 dark:border-white/5"></div>
        </div>

        <div v-else-if="!peminjaman.length" class="bg-white dark:bg-[#121512] rounded-[3rem] p-12 text-center shadow-md border border-dashed border-slate-200 dark:border-white/10">
          <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
            <PackageSearch class="w-8 h-8" />
          </div>
          <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest leading-relaxed">Belum ada data<br>peminjaman.</p>
        </div>

        <div v-else class="space-y-4">
          <PeminjamanCard
            v-for="item in peminjaman"
            :key="item.id"
            :peminjaman="item"
            @click="goDetail(item.id)"
          />
        </div>
      </section>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { PlusCircle, History, PackageSearch } from 'lucide-vue-next';

// Sesuaikan path import dengan struktur folder Anda
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
    console.error("Fetch Error:", err);
  } finally {
    setTimeout(() => { loading.value = false; }, 450);
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

<style scoped lang="postcss">
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
</style>