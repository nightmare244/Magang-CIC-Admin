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
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">portal izin</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Detail Izin</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="loading" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-12 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-bold text-slate-400 capitalize tracking-widest">sinkronisasi...</p>
      </div>

      <template v-else-if="izin">
        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 space-y-5 animate-fade-in-up">
          
          <div :class="statusTheme(izin.status).card" class="p-5 rounded-[2rem] flex items-center justify-between border transition-all duration-500">
            <div class="flex items-center gap-4">
              <div :class="statusTheme(izin.status).iconBg" class="w-12 h-12 rounded-2xl flex items-center justify-center shadow-sm text-white">
                <component :is="statusTheme(izin.status).icon" class="w-6 h-6" />
              </div>
              <div class="flex flex-col">
                <span class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight capitalize">{{ izin.tipe_izin }}</span>
                <span class="text-[10px] text-slate-400 font-medium capitalize">status pengajuan</span>
              </div>
            </div>
            <span :class="statusTheme(izin.status).badge" class="px-4 py-2 rounded-xl text-[10px] font-bold capitalize shadow-sm">
              {{ izin.status }}
            </span>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
              <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Mulai</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200">{{ formatDate(izin.tanggal_mulai) }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-[2rem] border border-slate-100 dark:border-white/5">
              <p class="text-[10px] font-bold text-slate-400 capitalize mb-1">Selesai</p>
              <p class="text-[12px] font-bold text-slate-700 dark:text-slate-200">{{ formatDate(izin.tanggal_selesai) }}</p>
            </div>
          </div>

          <div class="bg-slate-50 dark:bg-white/5 rounded-[2rem] p-6 border border-slate-100 dark:border-white/5">
            <div class="flex items-center gap-3 mb-3">
              <Hash class="w-4 h-4 text-emerald-500 opacity-60" />
              <p class="text-[10px] font-bold text-slate-400 capitalize">Alasan & keterangan</p>
            </div>
            <p class="text-[13px] text-slate-600 dark:text-slate-300 font-medium italic leading-relaxed">
              "{{ izin.keterangan }}"
            </p>
          </div>

          <div v-if="izin.file_pendukung" class="space-y-4 pt-2">
            <h3 class="text-[12px] font-bold text-slate-800 dark:text-slate-400 ml-2 capitalize">Preview lampiran</h3>
            
            <div class="relative overflow-hidden rounded-[2.5rem] bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 aspect-[4/3] flex items-center justify-center shadow-inner">
              <img 
                v-if="isImage(izin.file_pendukung)" 
                :src="imageUrl(izin.file_pendukung)" 
                class="w-full h-full object-cover"
                alt="Lampiran"
              />
              <div v-else class="flex flex-col items-center gap-3">
                <FileText class="w-10 h-10 text-slate-300" />
                <p class="text-[11px] font-medium text-slate-400 capitalize">berkas dokumen</p>
              </div>
            </div>

            <a 
              :href="imageUrl(izin.file_pendukung)" 
              target="_blank"
              class="relative overflow-hidden group w-full bg-white dark:bg-[#151815] p-1 rounded-3xl flex items-center shadow-lg border border-slate-100 dark:border-white/10 active:scale-95 transition-all"
            >
              <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white m-1 shadow-lg shadow-emerald-500/20 group-hover:rotate-12 transition-transform">
                <ExternalLink class="w-5 h-5" />
              </div>
              <div class="flex-1 px-3 text-left">
                <p class="text-[13px] font-bold text-slate-800 dark:text-white leading-none mb-1">Buka berkas asli</p>
                <p class="text-[10px] font-medium text-slate-400 leading-none">lihat lampiran lebih jelas</p>
              </div>
              <div class="pr-6">
                <ChevronRight class="w-5 h-5 text-slate-300 group-hover:translate-x-1 transition-transform" />
              </div>
            </a>
          </div>

        </div>
      </template>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, CheckCircle, XCircle, 
  Clock, FileText, ExternalLink, 
  AlertCircle, SearchX, Hash, Image as ImageIcon
} from 'lucide-vue-next';

const route = useRoute();
const izin = ref(null);
const loading = ref(true);

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const fetchIzin = async () => {
  try {
    const { data } = await api.get(`/karyawan/izin/${route.params.id}`);
    izin.value = data.data || data; 
  } catch (err) {
    izin.value = null;
  } finally {
    setTimeout(() => { loading.value = false; }, 600);
  }
};

const statusTheme = (status) => {
  const s = status?.toLowerCase() || '';
  if (s.includes('setuju')) return { 
    card: 'bg-emerald-50/50 dark:bg-emerald-500/5 border-emerald-100/50 dark:border-emerald-500/10',
    iconBg: 'bg-emerald-500 text-white shadow-lg shadow-emerald-200/50',
    badge: 'bg-emerald-500 text-white',
    icon: CheckCircle 
  };
  if (s.includes('tolak')) return { 
    card: 'bg-rose-50/50 dark:bg-rose-500/5 border-rose-100/50 dark:border-rose-500/10',
    iconBg: 'bg-rose-500 text-white shadow-lg shadow-rose-200/50',
    badge: 'bg-rose-500 text-white',
    icon: XCircle 
  };
  return { 
    card: 'bg-amber-50/50 dark:bg-amber-500/5 border-amber-100/50 dark:border-amber-500/10',
    iconBg: 'bg-amber-500 text-white shadow-lg shadow-amber-200/50',
    badge: 'bg-amber-500 text-white',
    icon: Clock 
  };
};

const isImage = (path) => {
  if (!path) return false;
  const ext = path.split('.').pop().toLowerCase();
  return ['jpg', 'jpeg', 'png', 'gif', 'webp'].includes(ext);
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
  const cleanPath = path.replace(/^(public\/|storage\/)/i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

onMounted(fetchIzin);
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

* {
  -webkit-tap-highlight-color: transparent;
}
</style>