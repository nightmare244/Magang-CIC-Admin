<template>
  <div
    class="min-h-screen w-full flex flex-col items-center justify-center text-center relative overflow-hidden 
           bg-gray-50 dark:bg-[#2A3B30] transition-colors duration-700 ease-in-out py-0"
  >
    <div class="absolute inset-0 bg-gradient-to-br from-gray-100/30 via-transparent to-transparent dark:from-white/5 pointer-events-none"></div>
    <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-green-500/10 dark:bg-[#5E815F]/30 blur-[150px] rounded-full"></div>
    <div class="absolute -bottom-60 -right-60 w-[500px] h-[500px] bg-teal-400/10 dark:bg-[#5E815F]/20 blur-[200px] rounded-full"></div>
    <button
      @click="toggleDark"
      class="absolute top-6 right-6 p-3 rounded-full shadow-lg transition-all duration-300 ease-in-out flex items-center justify-center text-sm font-medium z-20 
             text-gray-700 dark:text-gray-200 bg-white dark:bg-[#3B543D]/50 border border-gray-200 dark:border-[#5E815F]/50
             hover:shadow-md"
    >
      <div class="w-6 h-6 flex items-center justify-center relative overflow-hidden">
        <svg v-if="isDark" key="moon" class="w-5 h-5 transition-transform duration-500 transform scale-100 opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
        </svg>
        <svg v-else key="sun" class="w-5 h-5 transition-transform duration-500 transform scale-100 opacity-100" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
        </svg>
      </div>
      <span class="ml-2">{{ isDark ? 'Mode Terang' : 'Mode Gelap' }}</span>
    </button>

    <div class="w-full max-w-sm z-30 mt-0 mb-0 px-4 sm:px-6 lg:px-8">
      <div class="text-center mb-8 sm:mb-10">
        <img :src="logo" class="w-32 sm:w-48 lg:w-58 mx-auto mb-6 sm:mb-8 animate-fade-in" /> 
        <h2 class="text-md sm:text-lg font-normal text-gray-700 dark:text-gray-300">Silahkan Masukkan NIP (Nomor Induk Pegawai) dan Password Anda</h2>
      </div>

      <div class="p-6 rounded-3xl shadow-2xl shadow-gray-400/30 bg-white/85 backdrop-blur-lg dark:bg-[#3B543D]/25 dark:border dark:border-[#5E815F]/30 dark:shadow-black/70 z-30">
        <form @submit.prevent="loginNow" class="space-y-6">
          <div>
            <label class="block text-gray-700 text-sm mb-1 dark:text-gray-300 text-left">NIP (Nomor Induk Pegawai)</label>
            <input
              v-model="nip"
              type="text"
              placeholder=""
              class="w-full p-3 rounded-xl bg-gray-100 border border-gray-200 focus:ring-2 focus:ring-[#5E815F] transition dark:bg-gray-700 dark:border-gray-600 dark:text-white"
              required
            />
          </div>

          <div>
            <label class="block text-gray-700 text-sm mb-1 dark:text-gray-300 text-left">Password</label>
            <div class="relative">
              <input
                v-model="password"
                :type="showPassword ? 'text' : 'password'"
                placeholder=""
                class="w-full p-3 pr-12 rounded-xl bg-gray-100 border border-gray-200 focus:ring-2 focus:ring-[#5E815F] transition dark:bg-gray-700 dark:border-gray-600 dark:text-white"
                required
              />
              <button
                type="button"
                @click="togglePasswordVisibility"
                class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition"
                aria-label="Toggle password visibility"
              >
                <svg v-if="showPassword" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.617-5.617A9.954 9.954 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.257m-6.83-6.83a3 3 0 10-4.244 4.243m4.243-4.243l10.58 10.58" />
                </svg>
                <svg v-else class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
              </button>
            </div>
          </div>
          <p v-if="error" class="text-red-600 bg-red-100 py-2 rounded-xl text-center text-sm dark:bg-red-900/50 dark:text-red-300">
            {{ error }}
          </p>

          <button
            type="submit"
            class="w-full py-3 sm:py-4 bg-[#5E815F] text-white rounded-xl font-semibold text-lg hover:bg-[#6D956E] transition duration-200 mt-4 relative overflow-hidden gradient-button shadow-lg shadow-[#5E815F]/50"
          >
            Masuk
          </button>
        </form>
      </div>

      <div class="text-center mt-6">
        <p class="text-sm text-gray-600 dark:text-gray-400">Belum Punya Akun? <a href="#" class="text-gray-900 font-medium hover:underline dark:text-white">Daftar</a></p>
      </div>
    </div>

    <div v-if="loading" class="fixed inset-0 bg-[#2A3B30]/80 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="relative flex items-center justify-center">
        <div class="w-20 h-20 border-4 border-[#5E815F]/40 border-t-white rounded-full animate-spin"></div>
        <img :src="logo" class="absolute w-10 h-10 object-contain drop-shadow-lg" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '@/stores/authStore.js'; 
import logo from '@/assets/logo/logo.png'; 

const nip = ref('');
const password = ref('');
const error = ref('');
const loading = ref(false); 
const router = useRouter();
const auth = useAuthStore();

// --- PENAMBAHAN UNTUK VISIBILITAS PASSWORD ---
const showPassword = ref(false); // State baru untuk mengontrol visibilitas

const togglePasswordVisibility = () => {
    showPassword.value = !showPassword.value;
};
// ---------------------------------------------

// --- DARK MODE LOGIC (Tidak Berubah) ---
const isDark = ref(localStorage.getItem('dark') === 'true');

const applyDarkModeClass = (isDark) => {
  if (isDark) {
    document.documentElement.classList.add('dark');
  } else {
    document.documentElement.classList.remove('dark');
  }
};

applyDarkModeClass(isDark.value);

const toggleDark = () => {
  isDark.value = !isDark.value;
  localStorage.setItem('dark', isDark.value.toString());
  applyDarkModeClass(isDark.value); 
};

// --- LOGIN FUNCTION (Simulasi - Tidak Berubah) ---
const loginNow = async () => {
  error.value = '';
  loading.value = true; 
  await new Promise(resolve => setTimeout(resolve, 1500)); 
  const success = await auth.login({ nip: nip.value, password: password.value });
  loading.value = false; 

  if (!success) {
    error.value = auth.errorMessage || "NIP atau Password salah.";
    return;
  }

  if (auth.user?.role === 'admin') {
    router.push('/admin/dashboard');
  } else {
    router.push('/karyawan/dashboard');
  }
};
</script>

<style scoped>
/* --- ANIMASI GRADASI TOMBOL HIJAU ARMY (Tidak Berubah) --- */
.gradient-button {
  position: relative;
  z-index: 1; 
  transition: color 0.3s ease; 
}

.gradient-button::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background: linear-gradient(90deg, #4A654C 0%, #8DAB8E 50%, #4A654C 100%); 
  background-size: 200% 100%; 
  opacity: 0; 
  transition: all 0.5s ease;
  z-index: -1; 
  border-radius: 0.75rem;
}

.gradient-button:hover::before {
  opacity: 1; 
  background-position: -100% 0; 
}

.gradient-button:hover {
  color: white; 
}
</style>