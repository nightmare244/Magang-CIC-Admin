<template>
  <div class="min-h-screen bg-white dark:bg-[#080908] font-poppins pb-32 overflow-x-hidden transition-colors duration-500">
    
    <!-- Premium Moving Wave Header -->
    <header class="relative bg-gradient-to-br from-[#1b3329] via-[#2d4a3e] to-[#1e332a] dark:from-[#0a0f0d] dark:to-[#050505] pt-14 pb-40 px-6 overflow-hidden">
      <!-- Decorative Background -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-[80px] animate-pulse"></div>
      </div>

      <!-- Navigation & Title -->
      <div class="relative z-20 max-w-md mx-auto flex items-center justify-between animate-header-slide">
        <button 
          @click="$router.back()" 
          class="w-11 h-11 flex items-center justify-center bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 text-white active:scale-90 transition-all"
        >
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-lg font-black text-white uppercase tracking-[0.2em]">Keamanan</h1>
        <div class="w-11"></div>
      </div>

      <!-- Animated Waves (Seamless & Persistent) -->
      <div class="absolute bottom-0 left-0 w-full leading-[0]">
        <svg class="waves h-[100px] min-h-[100px] w-full" viewBox="0 24 150 28" preserveAspectRatio="none">
          <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
          </defs>
          <g class="parallax-waves">
            <use xlink:href="#gentle-wave" x="48" y="0" fill="currentColor" class="text-white/20 dark:text-[#080908]/20 animate-wave-1" />
            <use xlink:href="#gentle-wave" x="48" y="3" fill="currentColor" class="text-white/40 dark:text-[#080908]/40 animate-wave-2" />
            <use xlink:href="#gentle-wave" x="48" y="7" fill="currentColor" class="text-white dark:text-[#080908] animate-wave-4" />
          </g>
        </svg>
      </div>
    </header>

    <!-- Form Content -->
    <div class="max-w-md mx-auto px-6 -mt-16 relative z-30">
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-2xl shadow-slate-200/50 dark:shadow-none border border-slate-50 dark:border-white/5 animate-fade-in-up">
        
        <div class="flex items-center gap-4 mb-10">
          <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
            <KeyRound class="w-6 h-6" />
          </div>
          <div>
            <h2 class="text-xs font-black text-slate-400 uppercase tracking-[0.2em]">Ganti Password</h2>
            <p class="text-[10px] text-slate-300 font-bold uppercase mt-1">Lengkapi data di bawah ini</p>
          </div>
        </div>

        <form @submit.prevent="submit" class="space-y-6">
          
          <!-- Password Lama -->
          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Saat Ini</label>
            <div class="relative">
              <input 
                v-model="form.current_password" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                :class="{'input-error': errors.current_password}"
                placeholder="Masukkan password lama" 
              />
              <Lock class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
            <p v-if="errors.current_password" class="error-text">{{ errors.current_password[0] }}</p>
          </div>

          <div class="w-full h-px bg-slate-50 dark:bg-white/5 my-2"></div>

          <!-- Password Baru -->
          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Password Baru</label>
            <div class="relative">
              <input 
                v-model="form.password" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                :class="{'input-error': errors.password}"
                placeholder="Minimal 8 karakter" 
              />
              <ShieldCheck class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
            <p v-if="errors.password" class="error-text">{{ errors.password[0] }}</p>
          </div>

          <!-- Konfirmasi Password -->
          <div class="space-y-2">
            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Konfirmasi Password Baru</label>
            <div class="relative">
              <input 
                v-model="form.password_confirmation" 
                :type="showPassword ? 'text' : 'password'" 
                class="input-cic" 
                placeholder="Ulangi password baru" 
              />
              <CheckCircle2 class="absolute right-5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-300" />
            </div>
          </div>

          <!-- Lihat Password Toggle -->
          <div class="flex justify-end">
            <button 
              type="button" 
              @click="showPassword = !showPassword"
              class="text-[10px] font-black text-emerald-600 uppercase tracking-widest flex items-center gap-2 hover:opacity-70 transition-opacity"
            >
              {{ showPassword ? 'Sembunyikan' : 'Lihat Password' }}
            </button>
          </div>

          <!-- General Error Alert -->
          <Transition name="fade">
            <div v-if="generalError" class="flex items-start gap-3 p-4 bg-red-50 dark:bg-red-500/10 rounded-2xl border border-red-100 dark:border-red-500/20">
              <AlertCircle class="w-4 h-4 text-red-500 flex-shrink-0 mt-0.5" />
              <p class="text-[10px] font-bold text-red-600 dark:text-red-400 uppercase tracking-tight leading-relaxed">
                {{ generalError }}
              </p>
            </div>
          </Transition>

          <!-- Submit Button -->
          <div class="pt-4 space-y-4">
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:bg-emerald-800 text-white rounded-[2rem] py-5 font-black text-xs uppercase tracking-[0.3em] shadow-xl shadow-emerald-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3 overflow-hidden"
            >
              <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
              <ShieldCheck v-else class="w-4 h-4" />
              <span>{{ loading ? "Memproses..." : "Update Password" }}</span>
            </button>
            
            <button 
              type="button"
              @click="$router.back()"
              class="w-full text-[10px] font-black text-slate-300 uppercase tracking-[0.4em] active:scale-95 transition-all py-2"
            >
              Kembali
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  ChevronLeft, KeyRound, Loader2, ShieldCheck, 
  AlertCircle, Lock, CheckCircle2 
} from "lucide-vue-next";

const router = useRouter();
const form = ref({
  current_password: "", 
  password: "",         // Diubah dari new_password
  password_confirmation: "", // Diubah dari new_password_confirmation
});

const loading = ref(false);
const errors = ref({}); 
const generalError = ref("");
const showPassword = ref(false);

const submit = async () => {
  // Reset state
  errors.value = {};
  generalError.value = "";

  // Client-side simple check
  if (form.value.password !== form.value.password_confirmation) {
    generalError.value = "Konfirmasi password baru tidak cocok.";
    return;
  }

  loading.value = true;

  try {
    const res = await api.post("/karyawan/profil/ganti-password", form.value);
    
    if (res.data.success) {
      router.push("/karyawan/profil");
    }
  } catch (err) {
    if (err.response && err.response.status === 422) {
      errors.value = err.response.data.errors || {};
      generalError.value = err.response.data.message || "Validasi gagal. Silakan periksa kembali input Anda.";
      console.log("Validation Errors:", err.response.data.errors);
    } else {
      generalError.value = err.response?.data?.message || "Terjadi kesalahan sistem. Silakan coba lagi nanti.";
    }
  } finally {
    loading.value = false;
  }
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700;800;900&display=swap');

.font-poppins { font-family: 'Poppins', sans-serif; }

/* INPUT STYLES */
.input-cic {
  @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 
         rounded-2xl px-5 py-4 text-xs outline-none font-bold 
         focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all dark:text-white;
}

.input-error {
  @apply border-red-400 focus:ring-red-500/10 focus:border-red-500;
}

.error-text {
  @apply text-[9px] text-red-500 font-black uppercase tracking-widest ml-1 mt-1 italic;
}

/* ANIMATIONS */
.animate-header-slide { 
  animation: headerSlide 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes headerSlide { 
  from { transform: translateY(-20px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

.animate-fade-in-up { 
  opacity: 0; 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes fadeInUp { 
  from { transform: translateY(40px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

/* WAVE ANIMATIONS (Continuous) */
.animate-wave-1 { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-2 { animation: move-forever 18s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-4 { animation: move-forever 10s cubic-bezier(.55,.5,.45,.5) infinite; }

@keyframes move-forever {
  0% { transform: translate3d(-90px, 0, 0); }
  100% { transform: translate3d(85px, 0, 0); }
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>