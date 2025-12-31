<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-28 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px]"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-inner active:scale-90 transition-all">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Detail Pinjaman</h1>
        <div class="w-12"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6">
      <div v-if="loading" class="bg-white dark:bg-[#121512] rounded-[3rem] p-20 text-center shadow-xl border border-white dark:border-white/5 animate-pulse">
        <Loader2 class="w-10 h-10 animate-spin mx-auto text-emerald-500 mb-4" />
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sinkronisasi Data...</p>
      </div>

      <div v-else-if="detail" class="animate-fade-in-up space-y-6">
        <div v-if="detail.status === 'disetujui'" class="bg-[#2d4a3e] text-white rounded-[2.5rem] p-7 shadow-xl relative overflow-hidden border border-white/10">
          <div class="absolute right-0 top-0 opacity-10">
            <Clock class="w-24 h-24 -mr-4 -mt-4" />
          </div>
          <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-300 mb-4">Masa Pakai Aset Aktif</p>
          <div class="flex gap-5 items-center">
             <div class="text-center">
               <span class="text-3xl font-black block tracking-tighter">{{ countdown.days }}</span>
               <span class="text-[8px] uppercase font-bold opacity-60 tracking-widest">Hari</span>
             </div>
             <span class="text-xl opacity-30 font-light">:</span>
             <div class="text-center">
               <span class="text-3xl font-black block tracking-tighter">{{ countdown.hours }}</span>
               <span class="text-[8px] uppercase font-bold opacity-60 tracking-widest">Jam</span>
             </div>
             <span class="text-xl opacity-30 font-light">:</span>
             <div class="text-center">
               <span class="text-3xl font-black block tracking-tighter">{{ countdown.minutes }}</span>
               <span class="text-[8px] uppercase font-bold opacity-60 tracking-widest">Menit</span>
             </div>
          </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[2.5rem] p-6 shadow-xl flex items-center justify-between border border-white dark:border-white/5">
           <div>
             <p class="text-[9px] font-black text-slate-400 uppercase tracking-widest mb-2">Status Administrasi</p>
             <span :class="statusClass(detail.status)" class="text-[10px] font-black px-4 py-2 rounded-xl border uppercase tracking-widest shadow-sm">
               {{ detail.status }}
             </span>
           </div>
           <div class="w-12 h-12 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center">
             <Package class="w-6 h-6 text-slate-300 dark:text-slate-600" />
           </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5 space-y-6">
          <div class="flex items-center gap-2 mb-2">
            <div class="w-1.5 h-4 bg-emerald-500 rounded-full"></div>
            <h3 class="text-[10px] font-black text-slate-800 dark:text-white uppercase tracking-[0.2em]">Logistik Aset</h3>
          </div>
          
          <div class="flex items-center gap-5 border-b border-slate-50 dark:border-white/5 pb-6">
             <div class="w-20 h-20 bg-slate-100 dark:bg-white/5 rounded-3xl overflow-hidden flex-shrink-0 shadow-inner border border-slate-100 dark:border-white/10">
               <img :src="getPhotoUrl(detail.inventaris?.foto_barang)" class="w-full h-full object-cover transition-transform duration-500 hover:scale-110" @error="handleImageError" />
             </div>
             <div class="flex-1 min-w-0">
               <p class="text-sm font-bold text-slate-800 dark:text-white truncate leading-tight">{{ detail.inventaris?.nama_barang }}</p>
               <p class="text-[10px] font-medium text-slate-400 uppercase tracking-widest mt-1 italic">{{ detail.inventaris?.kode_barang }}</p>
               <div class="mt-2 inline-flex items-center px-2.5 py-1 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 rounded-lg border border-emerald-100 dark:border-emerald-500/20">
                  <span class="text-[10px] font-black uppercase tracking-tighter">{{ detail.quantity }} UNIT DIPINJAM</span>
               </div>
             </div>
          </div>
          
          <div class="grid grid-cols-2 gap-8">
            <div class="space-y-1">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.15em]">Periode Mulai</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-white">{{ formatDate(detail.tanggal_mulai) }}</p>
            </div>
            <div class="space-y-1 text-right">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.15em]">Batas Kembali</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-white">{{ formatDate(detail.tanggal_selesai) }}</p>
            </div>
          </div>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5">
          <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-3">Memo Operasional</p>
          <div class="p-5 bg-slate-50 dark:bg-white/5 rounded-[2rem] border-l-[6px] border-[#2d4a3e] italic shadow-inner">
            <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
              "{{ detail.keterangan || 'Tidak ada catatan tambahan untuk otorisasi ini.' }}"
            </p>
          </div>
        </div>

        <div v-if="detail.status === 'disetujui'" class="pt-4 animate-fade-in">
          <button 
            @click="isReturnModalOpen = true" 
            :disabled="submitting"
            class="btn-primary-eco"
          >
            <ArrowUpCircle class="w-5 h-5" />
            <span class="tracking-[0.2em]">KEMBALIKAN BARANG</span>
          </button>
          <p class="text-[9px] text-center text-slate-400 mt-6 px-10 uppercase font-black tracking-widest leading-relaxed opacity-60 italic">
            Harap pastikan integritas aset terjaga saat proses pengembalian.
          </p>
        </div>
      </div>
    </div>

    <ModalReturn 
      :isOpen="isReturnModalOpen" 
      :peminjaman="detail" 
      @close="isReturnModalOpen = false" 
      @success="handleSuccess" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, Loader2, Package, Clock, 
  ArrowUpCircle, RefreshCw 
} from 'lucide-vue-next';

// Pastikan file ini sudah Anda buat sebelumnya
import ModalReturn from '../components/ModalReturnPeminjaman.vue';

const route = useRoute();
const router = useRouter();
const detail = ref(null);
const loading = ref(true);
const submitting = ref(false);
const isReturnModalOpen = ref(false);
const countdown = ref({ days: 0, hours: 0, minutes: 0 });
let timer = null;

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const fetchDetail = async () => {
  loading.value = true;
  try {
    const res = await api.get(`/karyawan/peminjaman/${route.params.id}`);
    detail.value = res.data.data;
    if (detail.value.status === 'disetujui') {
      startCountdown();
    }
  } catch (err) {
    console.error("Fetch Detail Error:", err);
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const handleSuccess = () => {
  fetchDetail(); // Refresh data ke status 'selesai'
};

const startCountdown = () => {
  const updateTimer = () => {
    const now = new Date().getTime();
    const end = new Date(detail.value.tanggal_selesai).getTime();
    const diff = end - now;

    if (diff > 0) {
      countdown.value.days = Math.floor(diff / (1000 * 60 * 60 * 24));
      countdown.value.hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
      countdown.value.minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
    } else {
      countdown.value = { days: 0, hours: 0, minutes: 0 };
      clearInterval(timer);
    }
  };
  
  updateTimer();
  timer = setInterval(updateTimer, 60000); // Sinkronisasi setiap 1 menit
};

const getPhotoUrl = (path) => {
  if (!path) return '/img/default-inventaris.png';
  const cleanPath = path.replace(/^\/?storage\//i, '').replace(/^\/?public\//i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

const handleImageError = (e) => {
  e.target.src = '/img/default-inventaris.png';
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

const statusClass = (status) => {
  const s = status?.toLowerCase();
  switch (s) {
    case 'pending': return 'bg-amber-50 text-amber-600 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20';
    case 'disetujui': return 'bg-emerald-50 text-emerald-600 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20';
    case 'ditolak': return 'bg-rose-50 text-rose-600 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20';
    case 'selesai': return 'bg-indigo-50 text-indigo-600 border-indigo-100 dark:bg-indigo-500/10 dark:text-indigo-400 dark:border-indigo-500/20';
    default: return 'bg-slate-50 text-slate-500 border-slate-200 dark:bg-white/5 dark:border-white/10';
  }
};

onMounted(fetchDetail);
onUnmounted(() => {
  if (timer) clearInterval(timer);
});
</script>

<style scoped lang="postcss">
.btn-primary-eco {
    @apply w-full py-5 bg-[#2d4a3e] text-white rounded-[2rem] font-black text-[11px] 
           uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all 
           flex items-center justify-center gap-4 hover:bg-[#385b4d] shadow-emerald-900/20;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.7s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
</style>