<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-24 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-all">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Detail Informasi</h1>
        <div class="w-10"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#121512] rounded-[3rem] p-12 text-center shadow-xl border border-white dark:border-white/5">
        <Loader2 class="w-10 h-10 animate-spin text-emerald-500 mx-auto mb-4" />
        <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Memuat Konten...</p>
      </div>

      <template v-else-if="pengumuman">
        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5 animate-fade-in-up">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
              <User class="w-6 h-6" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest leading-none mb-1">
                {{ pengumuman.user?.name || 'Administrator' }}
              </p>
              <p class="text-[10px] text-slate-400 font-medium italic">{{ formatDate(pengumuman.created_at) }}</p>
            </div>
          </div>

          <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight mb-4">
            {{ pengumuman.judul }}
          </h2>
          
          <div class="space-y-4">
            <p class="text-[10px] font-bold text-slate-300 uppercase tracking-[0.2em] border-b border-slate-50 pb-2 dark:border-white/5">
              Isi Pengumuman
            </p>
            <div class="text-sm text-slate-600 dark:text-slate-400 leading-relaxed font-medium whitespace-pre-line">
              {{ pengumuman.isi }}
            </div>
          </div>

          <div v-if="pengumuman.file_path" class="mt-8 pt-6 border-t border-slate-50 dark:border-white/5">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-3">Lampiran Dokumen</p>
            <a 
              :href="getFileUrl(pengumuman.file_path)" 
              target="_blank"
              class="flex items-center justify-between p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 hover:border-emerald-200 transition-all group"
            >
              <div class="flex items-center gap-3">
                <FileText class="w-5 h-5 text-emerald-600" />
                <span class="text-xs font-bold text-slate-700 dark:text-white uppercase tracking-tighter">Dokumen Terlampir</span>
              </div>
              <Download class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transition-colors" />
            </a>
          </div>
        </div>

        <div class="space-y-4">
          <button 
            v-if="!pengumuman.telah_dibaca"
            @click="tandaiDibaca" 
            class="w-full py-5 bg-[#2d4a3e] text-white rounded-[2.5rem] font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20 active:scale-95 transition-all flex items-center justify-center gap-3"
          >
            <CheckCircle2 class="w-5 h-5" />
            Konfirmasi Paham
          </button>
          
          <div 
            v-else 
            class="w-full py-5 bg-emerald-50 dark:bg-emerald-500/10 rounded-[2.5rem] text-center"
          >
            <span class="text-[10px] font-black text-emerald-600 uppercase tracking-widest">Anda Sudah Membaca Informasi Ini</span>
          </div>

          <button @click="$router.back()" class="w-full text-[10px] font-bold text-slate-300 uppercase tracking-[0.3em] active:scale-95 transition-all">
            ← Kembali
          </button>
        </div>
      </template>

      <div v-if="error" class="bg-rose-50 dark:bg-rose-500/10 p-8 rounded-[3rem] text-center border border-rose-100">
        <AlertCircle class="w-12 h-12 text-rose-500 mx-auto mb-3" />
        <p class="text-sm font-bold text-rose-700 dark:text-rose-400">{{ error }}</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, Loader2, User, FileText, 
  Download, CheckCircle2, AlertCircle 
} from 'lucide-vue-next';

const route = useRoute();
const pengumuman = ref(null);
const loading = ref(true);
const error = ref(null);

const getDetailPengumuman = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/karyawan/pengumuman/${route.params.id}`);
    pengumuman.value = response.data.data;
  } catch (err) {
    error.value = "Gagal memuat detail pengumuman";
  } finally {
    setTimeout(() => { loading.value = false; }, 450);
  }
};

const tandaiDibaca = async () => {
  try {
    await api.post(`/karyawan/pengumuman/${pengumuman.value.id}/baca`);
    pengumuman.value.telah_dibaca = true;
  } catch (err) {
    console.error("Gagal menandai pengumuman:", err);
  }
};

const formatDate = (date) => {
  if (!date) return '-';
  return new Date(date).toLocaleDateString('id-ID', {
    day: 'numeric', month: 'long', year: 'numeric'
  });
};

const getFileUrl = (path) => {
  const baseUrl = import.meta.env.VITE_API_URL || "http://localhost:8000";
  return `${baseUrl}/storage/${path}`;
};

onMounted(() => {
  getDetailPengumuman();
});
</script>

<style scoped lang="postcss">
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
</style>