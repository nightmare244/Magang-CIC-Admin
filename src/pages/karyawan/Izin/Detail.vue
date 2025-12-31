<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-24 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-all active:scale-90">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight uppercase tracking-[0.2em]">Detail Izin</h1>
        <div class="w-10"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6 animate-fade-in-up">
      
      <div v-if="loading" class="card-eco p-12 text-center bg-white dark:bg-[#121512]">
        <div class="relative w-16 h-16 mx-auto mb-4">
          <div class="absolute inset-0 rounded-full border-4 border-emerald-500/10"></div>
          <div class="absolute inset-0 rounded-full border-4 border-emerald-500 border-t-transparent animate-spin"></div>
        </div>
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em] animate-pulse">Sinkronisasi Data...</p>
      </div>

      <template v-else-if="izin">
        <div class="card-eco p-8 text-center bg-white dark:bg-[#121512] relative">
          <div :class="statusTheme(izin.status).bg" class="w-20 h-20 rounded-[2rem] flex items-center justify-center mx-auto mb-6 transition-transform hover:rotate-6 duration-500">
            <component :is="statusTheme(izin.status).icon" class="w-10 h-10 text-white" />
          </div>
          <h2 class="text-2xl font-black text-slate-800 dark:text-white capitalize mb-1 tracking-tight">{{ izin.tipe_izin }}</h2>
          <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
             <span :class="statusTheme(izin.status).text" class="text-[10px] font-black uppercase tracking-[0.2em]">
               {{ izin.status }}
             </span>
          </div>
        </div>

        <div class="card-eco p-8 bg-white dark:bg-[#121512] space-y-8">
          <div class="grid grid-cols-2 gap-6 pb-6 border-b border-slate-50 dark:border-white/5">
            <div>
              <p class="kpi-label">Mulai</p>
              <p class="text-sm font-bold text-slate-700 dark:text-emerald-400 font-mono">{{ formatDate(izin.tanggal_mulai) }}</p>
            </div>
            <div>
              <p class="kpi-label">Selesai</p>
              <p class="text-sm font-bold text-slate-700 dark:text-emerald-400 font-mono">{{ formatDate(izin.tanggal_selesai) }}</p>
            </div>
          </div>

          <div>
            <p class="kpi-label mb-3">Alasan Pengajuan</p>
            <div class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium italic bg-slate-50 dark:bg-[#1a1d19] p-5 rounded-[1.5rem] border border-slate-100 dark:border-white/5">
              "{{ izin.keterangan }}"
            </div>
          </div>

          <div v-if="izin.status === 'ditolak' || izin.alasan_penolakan" class="bg-rose-50 dark:bg-rose-500/10 p-6 rounded-[1.8rem] border border-rose-100 dark:border-rose-500/20">
            <div class="flex items-center gap-2 mb-3 text-rose-600">
              <AlertCircle class="w-4 h-4" />
              <p class="text-[10px] font-bold uppercase tracking-widest">Catatan Penolakan Admin</p>
            </div>
            <p class="text-xs text-rose-700 dark:text-rose-300 font-bold leading-relaxed">
              {{ izin.alasan_penolakan || 'Mohon maaf, pengajuan belum dapat disetujui untuk saat ini.' }}
            </p>
          </div>

          <div v-if="izin.file_pendukung">
            <p class="kpi-label mb-3">Dokumen Lampiran</p>
            <a 
              :href="imageUrl(izin.file_pendukung)" 
              target="_blank"
              class="flex items-center justify-between p-5 bg-emerald-50 dark:bg-emerald-500/5 rounded-[1.5rem] border border-emerald-100 dark:border-emerald-500/20 group transition-all active:scale-95"
            >
              <div class="flex items-center gap-4">
                <div class="p-3 bg-[#2d4a3e] rounded-xl text-white shadow-lg shadow-emerald-900/20">
                  <FileText class="w-5 h-5" />
                </div>
                <div>
                  <span class="text-[11px] font-bold text-slate-700 dark:text-emerald-400 uppercase tracking-widest block">Lihat Bukti Digital</span>
                  <span class="text-[9px] text-slate-400 uppercase font-medium">Format: PDF / Image</span>
                </div>
              </div>
              <ExternalLink class="w-5 h-5 text-emerald-400 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" />
            </a>
          </div>
        </div>

<button 
  @click="$router.push('/karyawan/izin')" 
  class="btn-refresh-eco w-full justify-center !bg-slate-100 !text-slate-400 dark:!bg-white/5 hover:!bg-slate-200 transition-all shadow-none mt-4"
>
  <ChevronLeft class="w-4 h-4 mr-2" /> Kembali ke Daftar Izin
</button>
      </template>

      <div v-else class="card-eco p-12 text-center bg-white dark:bg-[#121512]">
        <SearchX class="w-16 h-16 text-slate-200 mx-auto mb-4" />
        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.3em]">Data Tidak Ditemukan</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, CheckCircle2, XCircle, 
  Clock, FileText, ExternalLink, 
  AlertCircle, SearchX 
} from 'lucide-vue-next';

const route = useRoute();
const izin = ref(null);
const loading = ref(true);

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const fetchIzin = async () => {
  console.log("Mengambil ID:", route.params.id); // Cek apakah ID muncul di console browser
  try {
    const { data } = await api.get(`/karyawan/izin/${route.params.id}`);
    // Jika backend menggunakan Resource, pastikan mengambil dari data.data
    izin.value = data.data || data; 
  } catch (err) {
    console.error('Data gagal dimuat:', err.response?.status);
    izin.value = null; // Memicu tampilan "Data Tidak Ditemukan"
  } finally {
    loading.value = false;
  }
};

const statusTheme = (status) => {
  switch (status?.toLowerCase()) {
    case 'disetujui': return { 
      bg: 'bg-emerald-500 shadow-xl shadow-emerald-500/30', 
      icon: CheckCircle2, 
      text: 'text-emerald-600' 
    };
    case 'ditolak': return { 
      bg: 'bg-rose-500 shadow-xl shadow-rose-500/30', 
      icon: XCircle, 
      text: 'text-rose-600' 
    };
    default: return { 
      bg: 'bg-amber-500 shadow-xl shadow-amber-500/30', 
      icon: Clock, 
      text: 'text-amber-600' 
    };
  }
};

const formatDate = (date) => {
  if (!date) return '---';
  return new Date(date).toLocaleDateString('id-ID', {
    day: '2-digit',
    month: 'long',
    year: 'numeric'
  });
};

const imageUrl = (path) => {
  if (!path) return '#';
  // Membersihkan path agar tidak double storage/
  const cleanPath = path.replace(/^(public\/|storage\/)/i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

onMounted(fetchIzin);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply rounded-[2.5rem] border border-white dark:border-white/5 shadow-xl transition-all;
}

.kpi-label {
  @apply text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em];
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-5 bg-[#2d4a3e] text-white rounded-[2.5rem] text-[10px] font-bold uppercase tracking-widest transition-all active:scale-95;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
</style>