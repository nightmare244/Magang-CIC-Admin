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
            @click="router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>

          <div>
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">Privasi akun</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Ganti password</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-5 shadow-sm border border-slate-100 dark:border-white/5 space-y-6 animate-fade-in-up">
        
        <div class="bg-emerald-50/50 dark:bg-emerald-500/5 p-5 rounded-[2rem] flex items-center gap-4 border border-emerald-100/50 dark:border-emerald-500/10">
          <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center shadow-lg shadow-emerald-200/50 text-white">
            <KeyRound class="w-6 h-6" />
          </div>
          <div class="flex flex-col">
            <span class="text-[14px] font-bold text-slate-800 dark:text-white leading-tight capitalize">Keamanan sandi</span>
            <span class="text-[10px] text-slate-400 font-medium capitalize">perbarui data berkala</span>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-5 px-1">
          
          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 capitalize ml-4">Password saat ini</label>
            <div class="relative">
              <input 
                v-model="form.current_password" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                placeholder="Ketik password lama" 
              />
              <Lock class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
            <p v-if="errors.current_password" class="text-[10px] text-rose-500 font-medium ml-4 mt-1 italic">{{ errors.current_password[0] }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 capitalize ml-4">Password baru</label>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                placeholder="Minimal 8 karakter" 
              />
              <ShieldCheck class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
            <p v-if="errors.password" class="text-[10px] text-rose-500 font-medium ml-4 mt-1 italic">{{ errors.password[0] }}</p>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 capitalize ml-4">Ulangi password baru</label>
            <div class="relative">
              <input 
                v-model="form.password_confirmation" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                placeholder="Ketik ulang password" 
              />
              <CheckCircle2 class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
          </div>

          <div class="flex justify-end pr-4">
            <button 
              type="button" 
              @click="showPassword = !showPassword"
              class="text-[10px] font-bold text-emerald-500 capitalize hover:opacity-70 transition-opacity"
            >
              {{ showPassword ? 'Sembunyikan sandi' : 'Lihat sandi' }}
            </button>
          </div>

          <div class="pt-4 space-y-4">
            <button
              type="submit"
              :disabled="loading"
              class="relative overflow-hidden group w-full bg-white dark:bg-[#151815] p-1 rounded-3xl flex items-center shadow-lg border border-slate-100 dark:border-white/10 active:scale-95 transition-all disabled:opacity-70"
            >
              <div class="w-12 h-12 bg-emerald-500 rounded-2xl flex items-center justify-center text-white m-1 shadow-lg shadow-emerald-500/20 group-hover:rotate-12 transition-transform">
                <div v-if="loading" class="w-5 h-5 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
                <Save v-else class="w-5 h-5" />
              </div>
              <div class="flex-1 px-3 text-left">
                <p class="text-[13px] font-bold text-slate-800 dark:text-white leading-none mb-1">Update sandi</p>
                <p class="text-[10px] font-medium text-slate-400 leading-none">Simpan perubahan sekarang</p>
              </div>
              <div class="pr-6">
                <ChevronRight class="w-5 h-5 text-slate-300 group-hover:translate-x-1 transition-transform" />
              </div>
            </button>
            
            <button 
              type="button"
              @click="router.back()"
              class="w-full text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] active:scale-95 transition-all py-2"
            >
              Kembali ke profil
            </button>
          </div>
        </form>
      </div>

      <Transition name="fade">
        <div v-if="generalError" class="bg-rose-50/50 dark:bg-rose-500/5 border border-rose-100/50 dark:border-rose-500/10 rounded-[2rem] p-5 flex items-start gap-4 animate-fade-in-up">
          <AlertCircle class="w-5 h-5 text-rose-500 flex-shrink-0" />
          <p class="text-[11px] text-rose-600 dark:text-rose-400 font-medium leading-relaxed">
            {{ generalError }}
          </p>
        </div>
      </Transition>

    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  ChevronLeft, KeyRound, Save, Lock, 
  ShieldCheck, CheckCircle2, AlertCircle,
  ChevronRight
} from "lucide-vue-next";

const router = useRouter();
const form = ref({
  current_password: "", 
  password: "", 
  password_confirmation: "",
});

const loading = ref(false);
const errors = ref({}); 
const generalError = ref("");
const showPassword = ref(false);

const submit = async () => {
  errors.value = {};
  generalError.value = "";

  if (form.value.password !== form.value.password_confirmation) {
    generalError.value = "Konfirmasi sandi tidak sesuai.";
    return;
  }

  loading.value = true;
  try {
    const res = await api.post("/karyawan/profil/ganti-password", form.value);
    if (res.data.success) {
      router.push("/karyawan/profil");
    }
  } catch (err) {
    if (err.response?.status === 422) {
      errors.value = err.response.data.errors || {};
      generalError.value = "Terjadi kesalahan validasi data.";
    } else {
      generalError.value = err.response?.data?.message || "Sistem sedang sibuk.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped lang="postcss">
.input-cic {
    @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 
           rounded-[1.8rem] px-6 py-4 text-[13px] outline-none font-medium 
           focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-500/50 
           transition-all dark:text-white placeholder:text-slate-300 dark:placeholder:text-slate-600 shadow-inner;
}

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

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>