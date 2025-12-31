<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <UserPlus class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Tambah Karyawan
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            Registrasi Personel & Alokasi Unit Operasional
          </p>
        </div>
      </div>

      <button @click="router.push('/admin/karyawan')" class="btn-back-eco">
        <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
      </button>
    </header>

    <div v-if="loading && !departemens.length" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse">Menghubungkan ke basis data...</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="max-w-5xl mx-auto space-y-8 font-poppins">
      
      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm space-y-8 shadow-xl border-none">
        <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
          <div class="p-2 bg-[#2d4a3e]/10 rounded-lg">
            <ShieldCheck class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
          </div>
          <h2 class="text-lg font-bold text-slate-700 dark:text-white uppercase tracking-wider">Data Esensial</h2>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Nama Lengkap <span class="text-rose-500">*</span></label>
            <input type="text" v-model="form.nama" class="input-field-eco" placeholder="Nama Karyawan" required />
            <p v-if="errors.nama" class="text-[10px] font-bold text-rose-500 uppercase mt-1 italic">{{ errors.nama[0] }}</p>
          </div>
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Email <span class="text-rose-500">*</span></label>
            <input type="email" v-model="form.email" class="input-field-eco" placeholder="email@perusahaan.com" required />
            <p v-if="errors.email" class="text-[10px] font-bold text-rose-500 uppercase mt-1 italic">{{ errors.email[0] }}</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Tanggal Lahir <span class="text-rose-500">*</span></label>
            <input type="date" v-model="form.tanggal_lahir" @change="generatePassword" class="input-field-eco" required />
          </div>
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Tempat Lahir <span class="text-rose-500">*</span></label>
            <input type="text" v-model="form.tempat_lahir" class="input-field-eco" placeholder="Kota Kelahiran" required />
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">NIP (Auto-Generated) <span class="text-rose-500">*</span></label>
            <div class="relative">
              <input type="text" v-model="form.nip" class="input-field-eco !bg-slate-50 dark:!bg-slate-900/50 cursor-not-allowed font-mono" disabled />
              <Lock class="w-4 h-4 absolute right-4 top-1/2 -translate-y-1/2 text-slate-300" />
            </div>
          </div>
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Departemen <span class="text-rose-500">*</span></label>
            <select v-model="form.departemen_id" class="input-field-eco" required>
              <option value="">-- Pilih Unit --</option>
              <option v-for="d in departemens" :key="d.id" :value="d.id">
                {{ d.nama_departemen || d.nama }}
              </option>
            </select>
          </div>
          <div class="space-y-2">
            <label class="kpi-label !text-slate-500">Level Akses <span class="text-rose-500">*</span></label>
            <select v-model="form.role" class="input-field-eco" required>
              <option value="karyawan">Karyawan (Unit)</option>
              <option value="admin">Administrator</option>
            </select>
          </div>
        </div>
      </div>
      
      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm space-y-6 shadow-xl border-none">
        <div class="flex items-center gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
          <div class="p-2 bg-amber-500/10 rounded-lg">
            <Fingerprint class="w-5 h-5 text-amber-600" />
          </div>
          <h2 class="text-lg font-bold text-slate-700 dark:text-white uppercase tracking-wider">Akses Kredensial</h2>
        </div>
        
        <div class="space-y-4">
          <label class="kpi-label !text-slate-500">Password Default (YYYYMMDD)</label>
          <div class="relative max-w-md">
            <input
              :type="showPassword ? 'text' : 'password'"
              v-model="form.password"
              class="input-field-eco !bg-slate-50 dark:!bg-slate-900/50 font-mono text-lg tracking-widest"
              disabled
            />
            <button type="button" @click="showPassword = !showPassword" class="absolute right-4 top-1/2 -translate-y-1/2 text-slate-400 hover:text-[#2d4a3e] transition-colors">
              <Eye v-if="!showPassword" class="w-5 h-5" />
              <EyeOff v-else class="w-5 h-5" />
            </button>
          </div>
          <p class="text-[10px] text-slate-400 italic font-medium flex items-center gap-2">
            <Info class="w-3 h-3" /> Sistem mengunci password awal berdasarkan tanggal lahir untuk keamanan sinkronisasi.
          </p>
        </div>
      </div>

      <Transition name="slide-fade">
        <div v-if="successMessage" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-r-xl font-bold text-sm uppercase tracking-widest">
            {{ successMessage }}
        </div>
      </Transition>
      <Transition name="slide-fade">
        <div v-if="apiError" class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 rounded-r-xl font-bold text-sm uppercase tracking-widest">
            {{ apiError }}
        </div>
      </Transition>

      <div class="pt-6 flex justify-end gap-4 border-t border-gray-100 dark:border-gray-800">
        <button type="button" @click="router.push('/admin/karyawan')" class="btn-back-eco min-w-[140px] justify-center">
            Batalkan
        </button>
        <button type="submit" :disabled="loading" class="btn-refresh-eco min-w-[240px] justify-center !py-4 shadow-lg shadow-[#2d4a3e]/20">
          <RefreshCw v-if="loading" class="animate-spin h-5 w-5 mr-2" />
          <Save v-else class="w-5 h-5 mr-2" />
          {{ loading ? 'MENGIRIM DATA...' : 'SIMPAN & AKTIVASI AKUN' }}
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, watch } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  UserPlus, ChevronLeft, ShieldCheck, 
  Fingerprint, Lock, Eye, EyeOff, 
  Info, Save, RefreshCw 
} from 'lucide-vue-next';

const router = useRouter();

const loading = ref(false);
const departemens = ref([]);
const successMessage = ref(null);
const apiError = ref(null);
const showPassword = ref(false);

const form = ref({
  nama: "",
  email: "",
  nip: "",
  departemen_id: "",
  tempat_lahir: "",
  tanggal_lahir: "",
  role: "karyawan",
  password: null,
});

const errors = ref({});

const generateNIP = () => {
    const currentYear = new Date().getFullYear();
    const randomDigits = Math.floor(10000 + Math.random() * 90000);
    form.value.nip = `${currentYear}${randomDigits}`;
};

const generatePassword = () => {
    const dob = form.value.tanggal_lahir;
    if (dob) {
        form.value.password = dob.replace(/-/g, '');
    } else {
        form.value.password = null;
    }
};

watch(() => form.value.tanggal_lahir, (newVal) => {
    generatePassword();
}, { immediate: true });


const fetchDepartemen = async () => {
  try {
    const res = await api.get("/admin/departemens");
    departemens.value = res.data.data || [];
  } catch (error) {
    console.error("Fetch Departemen Error:", error);
    apiError.value = "Gagal memuat daftar departemen.";
  }
};

const submitForm = async () => {
  loading.value = true;
  errors.value = {};
  successMessage.value = null;
  apiError.value = null;

  if (!form.value.nip) generateNIP();
  if (!form.value.password) generatePassword();
  
  if (!form.value.password) {
      apiError.value = "TANGGAL LAHIR DIBUTUHKAN UNTUK OTENTIKASI AWAL.";
      loading.value = false;
      return;
  }
  
  const payload = { ...form.value }; 

  try {
    await api.post("/admin/karyawan", payload); 
    successMessage.value = 'REGISTRASI BERHASIL. MENGALIHKAN...';
    setTimeout(() => { router.push("/admin/karyawan"); }, 1500);
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors;
      apiError.value = "VALIDASI GAGAL. PERIKSA KEMBALI INPUTAN ANDA.";
    } else {
        apiError.value = err.response?.data?.message || "GANGGUAN KONEKSI KE COMMAND CENTER.";
    }
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  fetchDepartemen();
  generateNIP();
});
</script>

<style scoped lang="postcss">
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
  @apply inline-flex items-center px-6 py-3.5 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins disabled:opacity-50;
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

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>