<template>
  <div class="card-eco-premium font-poppins">
    <div class="flex items-center justify-between mb-6 px-2">
      <div>
        <h3 class="text-lg font-bold text-slate-800 dark:text-white tracking-tight">Aktivitas Terbaru</h3>
        <p class="kpi-label-small !text-slate-400 mt-0.5">Operasional Node Log</p>
      </div>
      <div class="px-3 py-1 bg-slate-100 dark:bg-white/5 rounded-full text-[10px] font-black text-slate-500 uppercase tracking-widest border border-slate-200 dark:border-white/5">
        24h Monitor
      </div>
    </div>

    <div class="space-y-1">
      
      <div 
        v-for="(activity, i) in rows" 
        :key="i" 
        class="group relative flex items-start gap-4 p-4 rounded-2xl transition-all duration-300 hover:bg-slate-50 dark:hover:bg-white/[0.02]"
      >
        <div class="relative z-10">
          <div class="w-10 h-10 rounded-xl bg-white dark:bg-[#1a1d19] border border-slate-100 dark:border-slate-800 flex items-center justify-center shadow-sm group-hover:scale-110 transition-transform duration-500">
            <component 
              :is="getActivityIcon(activity.title)" 
              class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500 opacity-80"
            />
          </div>
          <div 
            v-if="i !== rows.length - 1" 
            class="absolute top-10 left-1/2 -translate-x-1/2 w-px h-8 bg-slate-100 dark:bg-slate-800"
          ></div>
        </div>

        <div class="flex-1 min-w-0 pt-0.5">
          <div class="flex items-start justify-between gap-4">
            <div class="min-w-0">
              <div class="text-sm font-bold text-slate-800 dark:text-white truncate tracking-tight">
                {{ activity.title }}
              </div>
              <div class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1 font-medium italic">
                "{{ activity.detail }}"
              </div>
            </div>

            <div class="flex flex-col items-end gap-1 flex-shrink-0">
              <span class="text-[10px] font-mono font-bold text-[#2d4a3e] dark:text-emerald-500 bg-[#2d4a3e]/5 px-2 py-0.5 rounded-md">
                {{ activity.time }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <div 
        v-if="rows.length === 0 && !loading" 
        class="py-12 text-center"
      >
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-slate-50 dark:bg-white/5 mb-4 text-slate-300">
           <Activity class="w-8 h-8 opacity-20" />
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em]">Data Log Tidak Ditemukan</p>
      </div>

      <div v-if="loading" class="py-12 text-center">
        <RefreshCw class="w-6 h-6 animate-spin mx-auto text-slate-300 mb-2" />
        <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Sinkronisasi Log...</p>
      </div>

    </div>

    <div class="mt-6 pt-4 border-t border-slate-50 dark:border-white/5">
      <button class="w-full py-2 text-[10px] font-black text-slate-400 hover:text-[#2d4a3e] dark:hover:text-emerald-500 uppercase tracking-[0.2em] transition-colors">
        Lihat Log Audit Lengkap
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { 
  Activity, 
  UserPlus, 
  Key, 
  Package, 
  ClipboardCheck, 
  RefreshCw,
  Bell
} from 'lucide-vue-next';

const rows = ref([]);
const loading = ref(true);

// Fungsi untuk menentukan ikon berdasarkan kata kunci judul
const getActivityIcon = (title) => {
  const t = title.toLowerCase();
  if (t.includes('karyawan') || t.includes('personel')) return UserPlus;
  if (t.includes('login') || t.includes('akses')) return Key;
  if (t.includes('inventaris') || t.includes('barang')) return Package;
  if (t.includes('izin') || t.includes('approve')) return ClipboardCheck;
  if (t.includes('pengumuman')) return Bell;
  return Activity;
};

const fetchActivities = async () => {
  loading.value = true;
  try {
    const res = await api.get("/admin/aktivitas");
    rows.value = Array.isArray(res.data)
      ? res.data
      : res.data?.data ?? [];
  } catch (error) {
    console.error("Gagal memuat aktivitas:", error);
  } finally {
    setTimeout(() => { loading.value = false; }, 500);
  }
};

onMounted(() => fetchActivities());
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco-premium {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] p-6 border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label-small {
  @apply text-[9px] font-black uppercase tracking-[0.2em];
}

.line-clamp-1 {
  display: -webkit-box;
  -webkit-line-clamp: 1;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>