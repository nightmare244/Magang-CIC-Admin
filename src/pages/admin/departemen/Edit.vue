<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Edit3 class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Edit Departemen
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Konfigurasi rincian unit kerja dan fungsi strategis operasional.
          </p>
        </div>
      </div>

      <Transition name="slide-fade">
        <div v-if="apiError" class="flex items-center gap-3 px-4 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl animate-bounce">
          <AlertTriangle class="w-4 h-4 text-rose-600" />
          <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ apiError }}</span>
        </div>
      </Transition>
    </header>

    <div v-if="initialLoading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse">Sinkronisasi data node...</p>
    </div>

    <form v-else @submit.prevent="update" class="max-w-3xl mx-auto space-y-8">
      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm space-y-8 shadow-xl border-none">
        
        <div class="space-y-3">
          <label class="kpi-label !text-slate-500">Nama Departemen <span class="text-rose-500">*</span></label>
          <div class="relative group">
            <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
              <Building2 class="w-5 h-5 text-slate-300 group-focus-within:text-[#2d4a3e] transition-colors" />
            </div>
            <input 
              v-model="form.nama_departemen" 
              type="text"
              class="input-field-eco !pl-12" 
              required 
              placeholder="Contoh: Operasional Lapangan"
            />
          </div>
          <Transition name="slide-fade">
            <p v-if="errors.nama_departemen" class="text-[10px] font-bold text-rose-500 uppercase mt-2 ml-1 italic tracking-widest">
              {{ errors.nama_departemen[0] }}
            </p>
          </Transition>
        </div>

        <div class="space-y-3">
          <label class="kpi-label !text-slate-500">Deskripsi Fungsi Strategis</label>
          <textarea 
            v-model="form.deskripsi" 
            class="input-field-eco min-h-[160px] resize-none py-4 text-sm font-medium"
            placeholder="Jelaskan peran atau tanggung jawab departemen ini..."
          ></textarea>
          <Transition name="slide-fade">
            <p v-if="errors.deskripsi" class="text-[10px] font-bold text-rose-500 uppercase mt-2 ml-1 italic tracking-widest">
              {{ errors.deskripsi[0] }}
            </p>
          </Transition>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row justify-end gap-4 pt-4 border-t border-gray-100 dark:border-gray-800 font-poppins">
        <button 
          type="button" 
          @click="router.push({ name: 'admin.departemen.index' })"
          class="btn-back-eco min-w-[160px]"
        >
          Batalkan
        </button>
        <button 
          type="submit" 
          :disabled="loading" 
          class="btn-refresh-eco flex items-center justify-center min-w-[220px] !py-4 shadow-lg shadow-[#2d4a3e]/20"
        >
          <RefreshCw v-if="loading" class="animate-spin -ml-1 mr-3 h-4 w-4" />
          <Save v-else class="w-4 h-4 mr-2" />
          {{ loading ? 'SINKRONISASI...' : 'SIMPAN PERUBAHAN' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { reactive, ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router'; 
import api from '@/services/api';
import { 
  Edit3, Building2, AlertTriangle, 
  RefreshCw, Save 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter(); 

const form = reactive({
  nama_departemen: '',
  deskripsi: ''
});

const initialLoading = ref(true); 
const loading = ref(false);       
const apiError = ref(null);
const errors = ref({});           

const loadData = async () => {
  initialLoading.value = true;
  apiError.value = null;
  try {
    const res = await api.get(`/admin/departemens/${route.params.id}`);
    form.nama_departemen = res.data.data.nama_departemen;
    form.deskripsi = res.data.data.deskripsi;
  } catch (error) {
    apiError.value = "Data gagal ditarik dari Command Center.";
  } finally {
    // Memberikan jeda halus untuk transisi skeleton
    setTimeout(() => { initialLoading.value = false; }, 400);
  }
};

const update = async () => {
  loading.value = true;
  apiError.value = null;
  errors.value = {};

  try {
    await api.put(`/admin/departemens/${route.params.id}`, form);
    router.push({ name: 'admin.departemen.index' });
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors;
    } else {
      apiError.value = error.response?.data?.message || 'Gagal sinkronisasi data ke server.';
    }
  } finally {
    loading.value = false;
  }
};

onMounted(loadData);
</script>

<style scoped lang="postcss">
/* Font Poppins dari global.css */
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.input-field-eco {
  @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white font-poppins;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-6 py-3 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         hover:bg-[#385b4d] active:scale-95 transition-all disabled:opacity-50 cursor-pointer font-poppins;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
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

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>