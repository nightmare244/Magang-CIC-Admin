<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4 mb-4">
          <div class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/10 shadow-xl">
            <CalendarRange class="w-6 h-6 text-white" />
          </div>
          <div>
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">Portal Izin</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Daftar Izin</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-7">
      
      <div class="bg-white/95 dark:bg-[#151815]/95 backdrop-blur-md p-2 rounded-[2.5rem] shadow-2xl shadow-black/5 border border-white/20 transition-transform active:scale-95">
        <router-link
          to="/karyawan/izin/ajukan"
          class="flex items-center justify-center gap-3 bg-[#1e332a] hover:bg-[#2d4a3e] text-white py-4 rounded-[2rem] transition-all shadow-lg shadow-emerald-900/20 group"
        >
          <PlusCircle class="w-5 h-5 text-emerald-400" />
          <span class="text-[13px] font-bold capitalize tracking-tight">Ajukan izin baru</span>
        </router-link>
      </div>

      <section class="animate-fade-in-up space-y-4">
        <h3 class="text-[12px] font-bold text-slate-800 dark:text-slate-400 ml-2 capitalize">Filter status</h3>
        <FilterIzin @filter="applyFilter" />
      </section>

      <section class="animate-fade-in-up space-y-4" style="animation-delay: 150ms">
        <div class="flex items-center justify-between px-2">
          <h3 class="text-[12px] font-bold text-slate-800 dark:text-slate-400 capitalize">Daftar arsip</h3>
          <span v-if="izinList.total" class="text-[10px] font-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-100/50 dark:border-none">
            {{ izinList.total }} Berkas
          </span>
        </div>

        <div v-if="loading" class="space-y-4">
          <div v-for="i in 3" :key="i" class="h-32 bg-white dark:bg-[#111311] rounded-[2.5rem] border border-slate-100 dark:border-white/5 animate-pulse shadow-sm"></div>
        </div>

        <div v-else-if="!izinList.data || izinList.data.length === 0" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-16 text-center shadow-sm border border-slate-100 dark:border-white/5">
          <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-4">
            <ClipboardX class="w-8 h-8 text-slate-300" />
          </div>
          <p class="text-[11px] font-medium text-slate-400 capitalize tracking-wide">Belum ada data pengajuan</p>
        </div>

        <div v-else class="space-y-4 pb-10">
          <IzinCard
            v-for="izin in izinList.data"
            :key="izin.id"
            :izin="izin"
            @click="goDetail(izin.id)"
            class="transition-all active:scale-[0.97] shadow-sm border border-slate-100 dark:border-white/5"
          />

          <div v-if="izinList.last_page > 1" class="flex items-center justify-between pt-6 px-2">
            <button
              @click="fetchData(izinList.prev_page_url)"
              :disabled="!izinList.prev_page_url"
              class="p-4 bg-white dark:bg-[#111311] rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 disabled:opacity-30 transition-all active:scale-90"
            >
              <ChevronLeft class="w-5 h-5 text-slate-400 dark:text-emerald-500" />
            </button>
            
            <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 tracking-widest uppercase">
              {{ izinList.current_page }} <span class="mx-1 text-slate-200">/</span> {{ izinList.last_page }}
            </span>

            <button
              @click="fetchData(izinList.next_page_url)"
              :disabled="!izinList.next_page_url"
              class="p-4 bg-white dark:bg-[#111311] rounded-2xl shadow-sm border border-slate-100 dark:border-white/5 disabled:opacity-30 transition-all active:scale-90"
            >
              <ChevronRight class="w-5 h-5 text-slate-400 dark:text-emerald-500" />
            </button>
          </div>
        </div>
      </section>

      <footer class="pt-10 pb-6 text-center">
        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest capitalize">ciwangun indah camp</p>
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

const fetchData = async (url = "/karyawan/izin") => {
  loading.value = true;
  try {
    const cleanUrl = url.replace(/^(?:\/\/|[^\/]+)*\//, "/");
    const { data } = await api.get(cleanUrl, {
      params: { 
        status: filterStatus.value || undefined 
      },
    });
    izinList.value = data;
  } catch (e) {
    console.error("Fetch izin error:", e);
  } finally {
    // Memberikan jeda sedikit agar transisi loading terasa smooth seperti dashboard
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const applyFilter = (status) => {
  filterStatus.value = status;
  fetchData();
};

const goDetail = (id) => {
  router.push(`/karyawan/izin/${id}`);
};

onMounted(fetchData);
</script>

<style scoped lang="postcss">
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}

@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

/* meniadakan highlight abu-abu saat klik di mobile */
* {
  -webkit-tap-highlight-color: transparent;
}
</style>