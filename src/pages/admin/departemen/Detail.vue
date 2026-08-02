<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <div v-if="loading" class="space-y-8">
      <div class="h-24 w-full bg-slate-200 dark:bg-slate-800/50 rounded-[1.8rem] animate-pulse"></div>
      <div class="h-40 w-full bg-slate-200 dark:bg-slate-800/50 rounded-[1.8rem] animate-pulse"></div>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div v-for="i in 3" :key="i" class="h-24 bg-slate-200 dark:bg-slate-800/50 rounded-2xl animate-pulse"></div>
      </div>
    </div>

    <div v-else-if="apiError" class="p-6 bg-rose-50 border-l-4 border-rose-500 rounded-2xl shadow-sm dark:bg-rose-900/10 mb-6 flex items-center gap-4">
      <AlertTriangle class="w-6 h-6 text-rose-600" />
      <div>
        <p class="text-[10px] font-black uppercase tracking-widest text-rose-700 dark:text-rose-400">Otoritas Gagal</p>
        <p class="text-xs text-rose-600/80">{{ apiError }}</p>
      </div>
    </div>

    <div v-else class="space-y-8">
      <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
        <div class="flex items-center gap-5">
          <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
            <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
            <Building2 class="w-7 h-7 text-white relative z-10" />
          </div>
          <div>
            <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
              {{ data.nama_departemen }}
            </h1>
            <p class="text-[10px] md:text-xs font-semibold text-slate-400 uppercase tracking-[0.2em] mt-1 italic">
              Arsip Internal • Unit Operasional CIC
            </p>
          </div>
        </div>
        
        <div class="flex gap-3 w-full md:w-auto">
          <button 
            @click="router.push({ name: 'admin.departemen.edit', params: { id: data.id } })"
            class="btn-refresh-eco flex-1 md:flex-none justify-center"
          >
            <Edit3 class="w-4 h-4 mr-2" /> Edit Divisi
          </button>
          <button 
            @click="router.push({ name: 'admin.departemen.index' })"
            class="btn-back-eco flex-1 md:flex-none justify-center"
          >
            <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
          </button>
        </div>
      </header>

      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm border-l-[6px] border-l-[#2d4a3e] relative overflow-hidden">
        <div class="relative z-10">
          <h3 class="kpi-label mb-4 !text-emerald-600 dark:!text-emerald-400 flex items-center gap-2">
            <Info class="w-4 h-4" /> Fungsi & Deskripsi Strategis
          </h3>
          <p class="text-slate-600 dark:text-slate-300 leading-relaxed italic text-sm md:text-base opacity-90 font-medium">
            "{{ data.deskripsi || 'Sistem belum mencatat rincian deskripsi operasional untuk unit departemen ini.' }}"
          </p>
        </div>
        <Building2 class="absolute -right-10 -bottom-10 w-40 h-40 opacity-[0.03] dark:opacity-[0.05]" />
      </div>

      <div class="space-y-6">
        <div class="flex items-center justify-between px-2">
          <div class="flex items-center gap-3">
            <Users class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
            <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight font-poppins">
              Personel Aktif
            </h2>
          </div>
          <span class="px-4 py-1.5 bg-[#2d4a3e]/10 text-[#2d4a3e] dark:text-emerald-400 text-[10px] font-bold rounded-full border border-[#2d4a3e]/10 uppercase tracking-widest">
            {{ data.users ? data.users.length : 0 }} Anggota
          </span>
        </div>

        <div v-if="data.users && data.users.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          <div 
            v-for="u in data.users" 
            :key="u.id" 
            class="personnel-card group"
            @click="router.push({ name: 'admin.karyawan.detail', params: { id: u.id } })"
          >
            <div class="flex items-center gap-4">
              <div class="relative flex-shrink-0">
                <img
                  v-if="u.foto_profil"
                  :src="getProfileUrl(u.foto_profil)"
                  class="w-14 h-14 rounded-2xl object-cover border-2 border-white dark:border-gray-800 shadow-md group-hover:scale-105 transition-transform duration-300"
                  @error="onImageError"
                />
                
                <div 
                  v-else
                  class="w-14 h-14 rounded-2xl border-2 border-white dark:border-gray-800 shadow-md group-hover:scale-105 transition-transform duration-300 bg-slate-100 dark:bg-slate-800 flex items-center justify-center"
                >
                  <span class="text-[#2d4a3e] dark:text-emerald-500 font-bold text-xl">
                    {{ getInitials(u.name) }}
                  </span>
                </div>
                
                <div v-if="u.role === 'admin'" class="absolute -top-1 -right-1 bg-sky-500 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-slate-900 shadow-sm" title="Administrator"></div>
              </div>

              <div class="flex-1 min-w-0 font-poppins">
                <p class="font-bold text-slate-800 dark:text-white truncate leading-tight group-hover:text-[#2d4a3e] dark:group-hover:text-emerald-400 transition-colors">
                  {{ u.name }}
                </p>
                <p class="text-[10px] font-mono text-slate-400 mt-1 uppercase tracking-tighter">
                  ID: {{ u.nip }}
                </p>
              </div>

              <div class="text-right flex-shrink-0">
                <span :class="u.role === 'admin' ? 'badge-role-admin' : 'badge-role-user'">
                  {{ u.role === 'admin' ? 'Admin' : 'Unit' }}
                </span>
              </div>
            </div>
          </div>
        </div>

        <div v-else class="py-20 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
          <div class="opacity-10 mb-4 text-slate-400">
            <UserX class="w-16 h-16" />
          </div>
          <p class="kpi-label !text-slate-400 uppercase tracking-widest">Belum ada personel terdaftar</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { useRoute, useRouter } from 'vue-router';
import { 
  Building2, Edit3, Users, AlertTriangle, 
  UserX, ChevronLeft, Info 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const data = ref({ 
    users: [],
    nama_departemen: '',
    deskripsi: ''
});
const loading = ref(true);
const apiError = ref(null);

const STORAGE_BASE_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const getProfileUrl = (path) => {
  if (!path) return null;
  if (path.startsWith('http')) return path;
  const cleanPath = path.startsWith('/') ? path.substring(1) : path;
  return `${STORAGE_BASE_URL}/storage/${cleanPath}`;
};

const onImageError = (e) => {
  e.target.style.display = 'none';
};

const load = async () => {
    loading.value = true;
    apiError.value = null;
    try {
        const res = await api.get(`/admin/departemens/${route.params.id}`);
        data.value = res.data.data;
    } catch (error) {
        apiError.value = error.response?.data?.message || 'Gagal memuat detail departemen.';
    } finally {
        setTimeout(() => { loading.value = false; }, 500);
    }
};

const getInitials = (name) => {
    if (!name) return '??';
    const names = name.trim().split(' ');
    if (names.length === 1) return names[0].substring(0, 2).toUpperCase();
    return (names[0][0] + names[names.length - 1][0]).toUpperCase();
};

onMounted(load);
</script>

<style scoped lang="postcss">
/* Import Poppins di sini jika belum ada di global.css */
@import url('https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,300;0,400;0,500;0,600;0,700;1,400&display=swap');

.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.personnel-card {
  @apply p-5 bg-white dark:bg-[#121512] border border-gray-100 dark:border-gray-800 rounded-2xl shadow-sm 
         cursor-pointer transition-all duration-300 hover:shadow-xl hover:border-emerald-500/30 hover:-translate-y-1 font-poppins;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-6 py-3 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 
         dark:hover:bg-slate-800 transition-all active:scale-95 font-poppins;
}

.badge-role-admin {
  @apply text-[9px] font-black px-2.5 py-1 bg-sky-50 text-sky-600 rounded-lg uppercase tracking-tighter
         dark:bg-sky-500/10 dark:text-sky-400 border border-sky-100 dark:border-sky-500/20;
}

.badge-role-user {
  @apply text-[9px] font-black px-2.5 py-1 bg-emerald-50 text-emerald-600 rounded-lg uppercase tracking-tighter
         dark:bg-emerald-500/10 dark:text-emerald-400 border border-emerald-100 dark:border-emerald-500/20;
}

.animate-fade-in {
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
  from { opacity: 0; transform: translateY(20px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>