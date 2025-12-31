<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Activity class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Kelola Status Peminjaman
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            Asset Tracking & Otorisasi Workflow
          </p>
        </div>
      </div>

      <router-link to="/admin/peminjaman" class="btn-back-eco">
        <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
      </router-link>
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Sinkronisasi Data Aset...</p>
    </div>

    <div v-else class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-12 gap-8 animate-fade-in">
      
      <div class="lg:col-span-4 space-y-6">
        <div class="card-eco p-8 text-center bg-white dark:bg-[#121512] shadow-xl border-none relative overflow-hidden">
          <h3 class="kpi-label !text-slate-400 mb-8 border-b border-slate-50 dark:border-slate-800 pb-3">Informasi Aset</h3>
          
          <div class="flex flex-col items-center">
            <div class="w-24 h-24 rounded-[2rem] bg-[#2d4a3e]/5 flex items-center justify-center text-[#2d4a3e] dark:text-emerald-500 mb-6 border-2 border-[#2d4a3e]/10 shadow-inner">
              <Package class="w-12 h-12" />
            </div>
            <p class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight uppercase font-poppins">
              {{ data.inventaris_name || 'Nama Aset' }}
            </p>
            <div class="mt-4 flex items-center gap-2 px-4 py-1.5 bg-slate-100 dark:bg-white/5 rounded-xl border border-slate-200 dark:border-white/5">
              <span class="text-[10px] font-mono text-emerald-600 font-black tracking-widest">ID: {{ data.kode_peminjaman }}</span>
            </div>
          </div>

          <div class="mt-8 space-y-4 pt-6 border-t border-slate-50 dark:border-slate-800">
            <div class="flex justify-between items-center">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Peminjam</span>
              <span class="text-xs font-bold text-slate-700 dark:text-slate-200 italic">{{ data.user_name }}</span>
            </div>
            <div class="flex justify-between items-center">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kuantitas</span>
              <span class="text-sm font-black text-[#2d4a3e] dark:text-emerald-500 tracking-tighter">{{ data.quantity }} Unit</span>
            </div>
          </div>
          
          <Box class="absolute -right-10 -bottom-10 w-32 h-32 opacity-[0.03] dark:opacity-[0.05]" />
        </div>
      </div>

      <div class="lg:col-span-8 space-y-8">
        <div class="card-eco p-10 bg-white dark:bg-[#121512] shadow-xl border-none">
          <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-4 mb-8 flex items-center gap-2">
            <GitPullRequest class="w-4 h-4" /> Pembaruan Workflow Strategis
          </h3>
          
          <div class="space-y-6">
            <label class="kpi-label !text-slate-400 ml-1">Pilih Status Otoritas Baru</label>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <button 
                v-for="opt in statusOptions" 
                :key="opt.value"
                type="button"
                @click="status = opt.value"
                :class="[
                  'flex items-center justify-between p-5 rounded-2xl border-2 transition-all duration-500 font-bold text-[10px] uppercase tracking-[0.2em]',
                  status === opt.value 
                    ? opt.activeClass 
                    : 'bg-white dark:bg-[#1a1d19] border-slate-100 dark:border-slate-800 text-slate-400 hover:border-[#2d4a3e]/30'
                ]"
              >
                <div class="flex items-center gap-3">
                  <div class="w-2 h-2 rounded-full shadow-[0_0_8px_rgba(0,0,0,0.1)]" :class="status === opt.value ? 'bg-white animate-pulse' : 'bg-slate-300'"></div>
                  {{ opt.label }}
                </div>
                <Check v-if="status === opt.value" class="w-4 h-4 text-white" />
              </button>
            </div>
          </div>

          <div class="mt-10 p-6 bg-slate-50 dark:bg-white/[0.02] rounded-3xl border-l-[6px] border-[#2d4a3e] text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed font-medium italic">
            <Info class="w-4 h-4 inline mr-2 mb-1 not-italic" />
            "Setiap modifikasi status akan diarsipkan secara otomatis dalam log riwayat aset dan akan menyesuaikan sinkronisasi stok inventaris global di dalam basis data Command Center."
          </div>
        </div>

        <div class="flex flex-col sm:flex-row gap-4 pt-2">
          <button
            @click="updateStatus"
            :disabled="submitting"
            class="btn-refresh-eco flex-1 justify-center !py-5 shadow-emerald-500/20 text-sm tracking-[0.2em]"
          >
            <span v-if="!submitting" class="flex items-center gap-2">
              <Save class="w-5 h-5" /> SIMPAN PERUBAHAN
            </span>
            <span v-else class="flex items-center justify-center">
              <RefreshCw class="animate-spin h-5 w-5 mr-3" />
              SINKRONISASI...
            </span>
          </button>

          <router-link
            class="btn-back-eco flex-1 justify-center !py-5 text-sm tracking-[0.2em] font-bold"
            to="/admin/peminjaman"
          >
            BATALKAN OPERASI
          </router-link>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "../../../services/api";
import { 
  Activity, ChevronLeft, Package, Box, GitPullRequest, 
  Check, Info, Save, RefreshCw 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const data = ref({});
const status = ref("");
const loading = ref(true);
const submitting = ref(false);

const statusOptions = [
  { label: 'Menunggu', value: 'menunggu', activeClass: 'bg-amber-500 border-amber-500 text-white shadow-lg shadow-amber-500/30 scale-[1.02]' },
  { label: 'Dipinjam', value: 'dipinjam', activeClass: 'bg-[#2d4a3e] border-[#2d4a3e] text-white shadow-lg shadow-[#2d4a3e]/30 scale-[1.02]' },
  { label: 'Selesai', value: 'selesai', activeClass: 'bg-sky-600 border-sky-600 text-white shadow-lg shadow-sky-600/30 scale-[1.02]' },
  { label: 'Ditolak', value: 'ditolak', activeClass: 'bg-rose-600 border-rose-600 text-white shadow-lg shadow-rose-600/30 scale-[1.02]' },
];

const loadData = async () => {
  try {
    const res = await api.get(`/admin/peminjaman/${id}`);
    data.value = res.data;
    status.value = res.data.status;
  } catch (e) {
    console.error("Gagal memuat detail peminjaman:", e);
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const updateStatus = async () => {
  submitting.value = true;
  try {
    await api.put(`/admin/peminjaman/${id}/status`, { status: status.value });
    router.push("/admin/peminjaman");
  } catch (e) {
    console.error(e);
    alert("Kritikal: Terjadi kegagalan sinkronisasi otoritas status.");
  } finally {
    submitting.value = false;
  }
};

onMounted(loadData);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-6 py-3.5 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins disabled:bg-slate-400;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3.5 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 
         dark:hover:bg-slate-800 transition-all active:scale-95 font-poppins;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>