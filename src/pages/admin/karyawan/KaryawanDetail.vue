<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <UserCircle class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Profil Karyawan
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            Informasi Personal & Otoritas Akses Personel
          </p>
        </div>
      </div>

      <button 
        @click="router.push('/admin/karyawan')" 
        class="btn-back-eco"
      >
        <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
      </button>
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Mengakses Berkas Personel...</p>
    </div>

    <div v-else-if="karyawan" class="max-w-4xl mx-auto space-y-8 animate-fade-in">
      <ProfilCard :data="karyawan" :backend-url="BACKEND_URL" class="shadow-2xl" />

      <div class="flex flex-col sm:flex-row justify-end gap-4 pt-6 border-t border-gray-100 dark:border-gray-800">
        <button 
          @click="router.push('/admin/karyawan')" 
          class="btn-back-eco min-w-[160px] justify-center"
        >
          <Users class="w-4 h-4 mr-2" /> Daftar Personel
        </button>
        
        <router-link 
          :to="`/admin/karyawan/${karyawan.id}/edit`" 
          class="btn-refresh-eco min-w-[180px] justify-center shadow-lg shadow-[#2d4a3e]/20"
        >
          <Edit3 class="w-4 h-4 mr-2" /> Perbarui Profil
        </router-link>
      </div>
    </div>

    <div v-else class="py-40 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
      <div class="opacity-10 mb-4 text-slate-400">
        <UserX class="w-16 h-16" />
      </div>
      <p class="kpi-label !text-slate-400 uppercase tracking-widest font-bold">Data Personel Tidak Ditemukan</p>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { 
  UserCircle, 
  ChevronLeft, 
  Edit3, 
  Users, 
  UserX 
} from 'lucide-vue-next';

// IMPORT ProfilCard dengan path relatif
import ProfilCard from './components/ProfilCard.vue';

const route = useRoute();
const router = useRouter();
const karyawan = ref(null);
const loading = ref(true);

const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const getProfil = async () => {
  loading.value = true;
  try {
    const res = await api.get(`/admin/karyawan/${route.params.id}`);
    const data = res.data.data;

    /**
     * PERBAIKAN TOTAL: 
     * Menggunakan substring(0, 10) adalah cara paling aman untuk 
     * mengambil format YYYY-MM-DD murni dari database tanpa 
     * intervensi zona waktu browser.
     */
    if (data.tanggal_lahir) {
      // Data asli DB: "2004-05-01" atau "2004-05-01T00:00:00.000000Z"
      // Hasil substring: "2004-05-01" (Tetap Konsisten)
      data.tanggal_lahir = data.tanggal_lahir.substring(0, 10);
    }

    karyawan.value = data;
    
    // Pastikan status aktif berupa boolean murni
    if (karyawan.value) {
      karyawan.value.is_active = Boolean(karyawan.value.is_active);
    }
  } catch (error) {
    console.error("Gagal sinkronisasi profil:", error);
  } finally {
    // Memberikan jeda visual premium
    setTimeout(() => { loading.value = false; }, 400);
  }
};

onMounted(getProfil);
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
         hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins;
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