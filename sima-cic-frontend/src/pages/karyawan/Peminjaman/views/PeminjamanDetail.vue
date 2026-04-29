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
            <p class="text-[10px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-[0.2em]">Inventaris Sistem</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Detail Pinjaman Barang</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Sinkronisasi Data...</p>
      </div>

      <template v-else-if="detail">
        <div v-if="detail.status === 'disetujui'" class="bg-[#1e332a] text-white rounded-[2.5rem] p-7 shadow-xl relative overflow-hidden border border-white/10 animate-fade-in-up">
          <div class="absolute right-0 top-0 opacity-10">
            <Clock class="w-24 h-24 -mr-4 -mt-4" />
          </div>
          <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-400 mb-4">Masa Pakai Aset Aktif</p>
          <div class="flex gap-5 items-center relative z-10">
              <div class="text-center">
                <span class="text-3xl font-black block tracking-tighter">{{ countdown.days }}</span>
                <span class="text-[8px] uppercase font-black opacity-40 tracking-widest">Hari</span>
              </div>
              <span class="text-xl opacity-20 font-light">:</span>
              <div class="text-center">
                <span class="text-3xl font-black block tracking-tighter">{{ countdown.hours }}</span>
                <span class="text-[8px] uppercase font-black opacity-40 tracking-widest">Jam</span>
              </div>
              <span class="text-xl opacity-20 font-light">:</span>
              <div class="text-center">
                <span class="text-3xl font-black block tracking-tighter">{{ countdown.minutes }}</span>
                <span class="text-[8px] uppercase font-black opacity-40 tracking-widest">Menit</span>
              </div>
          </div>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 space-y-5 animate-fade-in-up">
          
          <div :class="statusTheme(detail.status).card" class="p-5 rounded-[2rem] flex items-center justify-between border transition-all duration-500">
            <div class="flex items-center gap-4">
              <div :class="statusTheme(detail.status).iconBg" class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-lg text-white">
                <Package class="w-6 h-6" />
              </div>
              <div class="flex flex-col">
                <span class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight capitalize">Logistik Aset</span>
                <span class="text-[10px] text-slate-400 font-black uppercase tracking-wider">Status Administrasi</span>
              </div>
            </div>
            <span :class="statusTheme(detail.status).badge" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-sm">
              {{ detail.status }}
            </span>
          </div>

          <div class="bg-slate-50 dark:bg-white/5 rounded-[2rem] p-5 border border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-5">
              <div class="w-20 h-20 bg-white dark:bg-white/5 rounded-3xl overflow-hidden flex-shrink-0 shadow-sm border border-slate-100 dark:border-white/10">
                <img :src="getPhotoUrl(detail.inventaris?.foto_barang)" class="w-full h-full object-cover" @error="handleImageError" />
              </div>
              <div class="flex-1 min-w-0">
                <p class="text-sm font-bold text-slate-800 dark:text-white truncate leading-tight capitalize">{{ detail.inventaris?.nama_barang }}</p>
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mt-1 italic">{{ detail.inventaris?.kode_barang }}</p>
                <div class="mt-2 inline-flex items-center px-2.5 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg border border-emerald-500/20">
                   <span class="text-[9px] font-black uppercase tracking-tighter">{{ detail.quantity }} Unit Dipinjam</span>
                </div>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
              <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Periode Mulai</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200 capitalize">{{ formatDate(detail.tanggal_mulai) }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
              <p class="text-[10px] font-black text-slate-400 uppercase mb-1 tracking-widest">Batas Kembali</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200 capitalize">{{ formatDate(detail.tanggal_selesai) }}</p>
            </div>
          </div>

          <div class="bg-slate-50 dark:bg-white/5 rounded-[2rem] p-6 border border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-3 mb-3">
              <Hash class="w-4 h-4 text-emerald-500 opacity-60" />
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Memo Operasional</p>
            </div>
            <p class="text-[12px] text-slate-600 dark:text-slate-300 font-medium italic leading-relaxed capitalize">
              "{{ detail.keterangan || 'Tidak Ada Catatan Tambahan Untuk Otorisasi Ini.' }}"
            </p>
          </div>

        </div>

        <div v-if="detail.status === 'disetujui'" class="pt-4 animate-fade-in space-y-6">
          <button 
            @click="isReturnModalOpen = true" 
            class="w-full py-5 bg-[#1e332a] text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl active:scale-95 transition-all flex items-center justify-center gap-4 shadow-emerald-900/20 border border-white/10"
          >
            <ArrowUpCircle class="w-5 h-5 text-emerald-400" />
            <span>Kembalikan Barang</span>
          </button>
        </div>
      </template>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-widest capitalize">Ciwangun Indah Camp</p>
    </footer>

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
  ChevronLeft, Package, Clock, 
  ArrowUpCircle, Hash 
} from 'lucide-vue-next';

import ModalReturn from '../components/ModalReturnPeminjaman.vue';

const route = useRoute();
const router = useRouter();
const detail = ref(null);
const loading = ref(true);
const isReturnModalOpen = ref(false);
const countdown = ref({ days: 0, hours: 0, minutes: 0 });
let timer = null;

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const fetchDetail = async () => {
  loading.value = true;
  try {
    const res = await api.get(`/karyawan/peminjaman/${route.params.id}`);
    detail.value = res.data.data;
    if (detail.value.status === 'disetujui') { startCountdown(); }
  } catch (err) {
    console.error("fetch detail error:", err);
  } finally {
    setTimeout(() => { loading.value = false; }, 600);
  }
};

const statusTheme = (status) => {
  const s = status?.toLowerCase() || '';
  if (s === 'disetujui') return { 
    card: 'bg-emerald-50/50 dark:bg-emerald-500/5 border-emerald-100/50 dark:border-emerald-500/10',
    iconBg: 'bg-emerald-500 shadow-emerald-200/50',
    badge: 'bg-emerald-500 text-white',
  };
  if (s === 'ditolak') return { 
    card: 'bg-rose-50/50 dark:bg-rose-500/5 border-rose-100/50 dark:border-rose-500/10',
    iconBg: 'bg-rose-500 shadow-rose-200/50',
    badge: 'bg-rose-500 text-white',
  };
  if (s === 'selesai') return { 
    card: 'bg-indigo-50/50 dark:bg-indigo-500/5 border-indigo-100/50 dark:border-indigo-500/10',
    iconBg: 'bg-indigo-500 shadow-indigo-200/50',
    badge: 'bg-indigo-500 text-white',
  };
  return { 
    card: 'bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10',
    iconBg: 'bg-amber-500 shadow-amber-200/50',
    badge: 'bg-amber-500 text-white',
  };
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
  timer = setInterval(updateTimer, 60000);
};

const getPhotoUrl = (path) => {
  if (!path) return '/img/default-inventaris.png';
  const cleanPath = path.replace(/^\/?storage\//i, '').replace(/^\/?public\//i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

const handleImageError = (e) => { e.target.src = '/img/default-inventaris.png'; };

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

const handleSuccess = () => { fetchDetail(); };

onMounted(fetchDetail);
onUnmounted(() => { if (timer) clearInterval(timer); });
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
@keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fadeIn 0.5s ease-out forwards; }

* { -webkit-tap-highlight-color: transparent; }
</style>