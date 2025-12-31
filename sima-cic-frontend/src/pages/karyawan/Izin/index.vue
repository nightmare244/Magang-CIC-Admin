<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#0a0c0a] font-poppins pb-36 overflow-x-hidden transition-colors duration-300">
    
    <header class="relative pt-16 pb-28 px-8 rounded-b-[4rem] overflow-hidden animate-header-slide shadow-2xl shadow-emerald-900/20">
      <div class="absolute inset-0 bg-gradient-to-br from-[#2d4a3e] to-[#1e332a] dark:from-[#1a2e26] dark:to-[#0a0c0a]"></div>
      
      <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/10 rounded-full blur-[80px]"></div>
      <div class="absolute left-4 top-12 opacity-10 -rotate-12">
        <CalendarRange class="w-32 h-32 text-emerald-200" />
      </div>
      
      <div class="relative z-10 flex flex-col items-center text-center">
        <p class="text-[10px] font-black uppercase tracking-[0.4em] text-emerald-300 mb-2">Portal Karyawan</p>
        <h1 class="text-3xl font-bold tracking-tight text-white">Daftar Izin</h1>
        <p class="text-[11px] opacity-60 mt-1 font-medium italic text-emerald-50">Ciwangun Indah Camp • Manajemen Kehadiran</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-7">
      
      <div class="bg-white dark:bg-[#121512] p-2 rounded-[3rem] shadow-xl border border-white dark:border-white/5 transition-transform active:scale-95">
        <router-link
          to="/karyawan/izin/ajukan"
          class="flex items-center justify-center gap-3 bg-[#2d4a3e] hover:bg-[#1e332a] text-white py-4 rounded-[2.5rem] transition-all shadow-lg shadow-emerald-900/20"
        >
          <PlusCircle class="w-5 h-5" />
          <span class="text-[11px] font-black uppercase tracking-widest">Ajukan Izin Baru</span>
        </router-link>
      </div>

<section class="animate-fade-in-up">
  <div class="flex items-center gap-2.5 mb-4 px-2">
    <div class="w-1.5 h-4 bg-emerald-500 rounded-full shadow-sm"></div>
    <h3 class="text-[12px] font-black text-slate-700 dark:text-emerald-400 uppercase tracking-widest">Filter Status</h3>
  </div>
  
  <FilterIzin @filter="applyFilter" />
</section>

      <section class="animate-fade-in-up" style="animation-delay: 150ms">
        <div class="flex items-center justify-between mb-4 px-2">
          <h3 class="text-[12px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-widest">Daftar Arsip</h3>
          <span v-if="izinList.total" class="text-[10px] font-bold text-slate-400 bg-slate-100 dark:bg-white/5 px-3 py-1 rounded-full">
            {{ izinList.total }} Berkas
          </span>
        </div>

        <div v-if="loading" class="space-y-5">
          <div v-for="i in 3" :key="i" class="h-32 bg-white dark:bg-white/5 rounded-[2.5rem] border border-slate-50 dark:border-white/5 animate-pulse"></div>
        </div>

        <div v-else-if="!izinList.data || izinList.data.length === 0" class="bg-white dark:bg-[#121512] rounded-[3rem] p-14 text-center shadow-xl border border-dashed border-slate-200 dark:border-white/10">
          <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
            <ClipboardX class="w-8 h-8" />
          </div>
          <p class="text-[11px] font-black text-slate-400 uppercase tracking-[0.2em] leading-relaxed">Belum ada<br>data pengajuan</p>
        </div>

        <div v-else class="space-y-4 pb-10">
          <IzinCard
            v-for="izin in izinList.data"
            :key="izin.id"
            :izin="izin"
            @click="goDetail(izin.id)"
            class="transition-transform active:scale-[0.98]"
          />

          <div v-if="izinList.last_page > 1" class="flex items-center justify-between pt-6 px-2">
            <button
              @click="fetchData(izinList.prev_page_url)"
              :disabled="!izinList.prev_page_url"
              class="p-4 bg-white dark:bg-[#121512] rounded-2xl shadow-lg border border-slate-50 dark:border-white/5 disabled:opacity-20 transition-all active:scale-90"
            >
              <ChevronLeft class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
            </button>
            
            <span class="text-[10px] font-black text-slate-400 tracking-[0.3em] uppercase">
              Hal. {{ izinList.current_page }} / {{ izinList.last_page }}
            </span>

            <button
              @click="fetchData(izinList.next_page_url)"
              :disabled="!izinList.next_page_url"
              class="p-4 bg-white dark:bg-[#121512] rounded-2xl shadow-lg border border-slate-50 dark:border-white/5 disabled:opacity-20 transition-all active:scale-90"
            >
              <ChevronRight class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
            </button>
          </div>
        </div>
      </section>

      <footer class="pt-4 pb-8 text-center opacity-30">
        <p class="text-[9px] text-slate-400 font-black uppercase tracking-[0.5em]">PT Ciwangun Indah Camp • Riwayat</p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import api from "@/services/api";
import { 
  PlusCircle, CalendarRange, ClipboardX, 
  ChevronLeft, ChevronRight 
} from "lucide-vue-next";

// Import Komponen Pendukung
import FilterIzin from "./components/FilterIzin.vue";
import IzinCard from "./components/IzinCard.vue";

const router = useRouter();
const izinList = ref({
  data: [],
  current_page: 1,
  last_page: 1,
  total: 0
});
const loading = ref(true);
const filterStatus = ref("");

/**
 * Fungsi Fetch Data: 
 * Menggunakan URL dinamis untuk pagination dan filter status
 */
const fetchData = async (url = "/karyawan/izin") => {
  loading.value = true;
  try {
    // Pastikan URL bersih dari base URL jika menerima link pagination utuh
    const cleanUrl = url.replace(/^(?:\/\/|[^\/]+)*\//, "/");
    
    const { data } = await api.get(cleanUrl, {
      params: { 
        // Mengirimkan status ke backend sesuai pilihan filter
        status: filterStatus.value || undefined 
      },
    });
    izinList.value = data;
  } catch (e) {
    console.error("Fetch izin error:", e);
  } finally {
    // Delay halus agar skeleton tidak flickering
    setTimeout(() => { loading.value = false; }, 400);
  }
};

/**
 * Handler Filter:
 * Dipanggil saat tombol di komponen FilterIzin diklik
 */
const applyFilter = (status) => {
  filterStatus.value = status; // Update state filter
  fetchData(); // Refresh data dengan parameter status baru
};

const goDetail = (id) => {
  router.push(`/karyawan/izin/${id}`);
};

onMounted(fetchData);
</script>

<style scoped lang="postcss">
/* Animasi Slide Down untuk Header */
.animate-header-slide { 
  animation: headerSlide 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; 
}
@keyframes headerSlide { 
  from { transform: translateY(-40px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

/* Animasi Fade In Up untuk konten */
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* Sembunyikan Scrollbar */
.overflow-x-hidden {
  scrollbar-width: none;
  -ms-overflow-style: none;
}
.overflow-x-hidden::-webkit-scrollbar {
  display: none;
}
</style>