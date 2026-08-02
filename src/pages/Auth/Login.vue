<template>
  <div class="min-h-screen w-full flex flex-col items-center justify-center relative overflow-hidden transition-colors duration-500 font-poppins">
    
    <div 
      class="absolute inset-0 z-0 bg-cover bg-center scale-110"
      :style="{
        'background-image': 'url(' + backgroundImage + ')',
        'filter': 'blur(4px)'
      }"
    ></div>

    <div class="absolute inset-0 z-10 bg-gradient-to-b from-[#1a2b22]/30 via-[#1a2b22]/80 to-[#0a0f0d]"></div>

    <div class="w-full max-w-sm z-20 px-6">
      
      <div class="text-center mb-12 animate-fade-in">
        <div class="relative inline-block">
          <div class="absolute inset-0 bg-emerald-500/20 blur-[60px] rounded-full"></div>
          <img :src="logo" class="w-32 sm:w-40 mx-auto relative z-10 drop-shadow-[0_20px_20px_rgba(0,0,0,0.4)]" /> 
        </div>
      </div>

      <div class="p-8 rounded-[2.5rem] bg-white/10 backdrop-blur-3xl border border-white/20 shadow-2xl animate-pop-up">
        <div class="mb-8 text-left">
          <h2 class="text-xl font-bold text-white mb-1">Selamat Datang</h2>
          <p class="text-[12px] font-medium text-emerald-100/60">Silahkan Masukkan Akun Anda</p>
        </div>

        <form @submit.prevent="loginNow" class="space-y-6">
          
          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-white tracking-widest ml-4">Nip Pegawai</label>
            <div class="relative group">
              <input
                v-model="nip"
                type="text"
                class="input-login"
                placeholder="Masukkan Nomor Induk"
                required
              />
              <User class="absolute right-6 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40 group-focus-within:text-emerald-400 transition-colors" />
            </div>
          </div>

          <div class="space-y-2">
            <label class="block text-[10px] font-bold text-white tracking-widest ml-4">Kata Sandi</label>
            <div class="relative group">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                class="input-login"
                placeholder="Masukkan Kata Sandi"
                required
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-6 top-1/2 -translate-y-1/2 text-white/40 hover:text-white transition-colors"
              >
                <component :is="showPassword ? EyeOff : Eye" class="w-4 h-4" />
              </button>
            </div>
          </div>

          <button
            type="submit"
            :disabled="loading"
            class="w-full bg-emerald-600 hover:bg-emerald-500 text-white rounded-[1.5rem] py-4 text-[13px] font-bold shadow-xl shadow-emerald-900/40 active:scale-95 transition-all flex items-center justify-center gap-3 disabled:opacity-50 mt-4"
          >
            <div v-if="loading" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            <LogIn v-else class="w-4 h-4" />
            <span>{{ loading ? "Memproses Data..." : "Masuk Sekarang" }}</span>
          </button>
        </form>
      </div>
    </div>

    <Transition name="overlay-fade">
      <div v-if="showErrorModal" class="fixed inset-0 bg-black/60 backdrop-blur-md flex items-center justify-center z-[100] p-6">
        <div class="bg-[#1c2b22] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-white/10 animate-pop-up">
          <div class="p-8 text-center">
            <div class="w-16 h-16 bg-rose-500/10 rounded-2xl flex items-center justify-center mx-auto mb-6 text-rose-500">
              <AlertTriangle class="w-8 h-8" />
            </div>
            <h3 class="text-lg font-bold text-white mb-2">Akses Gagal</h3>
            <p class="text-sm text-white/60 font-medium leading-relaxed mb-8">
              {{ error || "Mohon Periksa Kembali Nip Dan Kata Sandi Anda." }}
            </p>
            <button 
              @click="showErrorModal = false" 
              class="w-full py-4 bg-white/10 hover:bg-white/20 text-white rounded-2xl font-bold text-[12px] active:scale-95 transition-all"
            >
              Coba Kembali
            </button>
          </div>
        </div>
      </div>
    </Transition>

  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore.js'; 
import logo from '@/assets/logo/logo.png'; 
import { 
  User, Eye, EyeOff, LogIn, 
  Sun, Moon, AlertTriangle 
} from 'lucide-vue-next';

const nip = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false); 
const showErrorModal = ref(false);
const showPassword = ref(false);
const router = useRouter();
const auth = useAuthStore();
const backgroundImage = "/images/background.jpg"; 

const loginNow = async () => {
  error.value = '';
  loading.value = true; 
  try {
    const success = await auth.login({ nip: nip.value, password: password.value });
    if (success) {
      if (auth.user?.role === 'admin') router.push('/admin/dashboard');
      else router.push('/karyawan/dashboard');
    } else {
      error.value = auth.errorMessage;
      showErrorModal.value = true;
    }
  } catch (err) {
    error.value = "Terjadi Kesalahan Pada Sistem.";
    showErrorModal.value = true;
  } finally {
    loading.value = false;
  }
};

onMounted(() => {
  // Memastikan tema selalu gelap/sesuai tema aplikasi anda agar teks putih tetap kontras
  document.documentElement.classList.add('dark');
});
</script>

<style scoped lang="postcss">
.input-login {
  @apply w-full bg-white/5 border border-white/10 rounded-[1.2rem] px-6 py-4 
         text-[14px] font-medium outline-none text-white placeholder:text-white/20
         focus:bg-white/10 focus:border-emerald-500/50 transition-all;
}

/* Animasi Pop-Up Kartu Login */
.animate-pop-up {
  animation: popUp 1s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes popUp {
  from { opacity: 0; transform: scale(0.92) translateY(40px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}

.animate-fade-in {
  animation: fadeIn 1.2s ease-out forwards;
}

@keyframes fadeIn {
  from { opacity: 0; }
  to { opacity: 1; }
}

.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from, .overlay-fade-leave-to { opacity: 0; }

* {
  -webkit-tap-highlight-color: transparent;
}
</style>