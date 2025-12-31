<template>
  <div
    class="min-h-screen w-full flex flex-col items-center justify-center text-center relative overflow-hidden transition-colors duration-500"
    :class="{'bg-white': !isDark, 'bg-[#2A3B30]': isDark}"
    @mousemove="moveGradient"
    @mouseleave="resetGradient"
  >
    <!-- Overlay Loading -->
    <div v-if="loading" class="fixed inset-0 bg-[#2A3B30]/80 backdrop-blur-sm flex items-center justify-center z-50">
      <div class="w-20 h-20 border-4 border-[#5E815F]/40 border-t-white rounded-full animate-spin"></div>
    </div>

    <!-- 1. Layer Gambar Latar Belakang (Full Screen) -->
    <!-- MENAMBAH FALLBACK COLOR AGAR TIDAK PURE WHITE/BLANK JIKA GAMBAR GAGAL DIMUAT -->
    <div class="absolute inset-0 z-0 bg-cover bg-center transition-opacity duration-700"
         :style="{
             'background-image': 'url(' + backgroundImage + ')',
             'background-color': isDark ? '#1C2824' : '#F0F0F0' // Fallback visual theme color
         }">
    </div>

    <!-- 2. Layer Fade/Overlay Gradasi (Transparan ke Warna Solid) -->
    <!-- Ini menciptakan efek gambar hanya terlihat di atas dan memudar ke warna tema di bawah -->
    <div class="absolute inset-0 z-0"
         :class="{
             // Mode Terang: Fade dari Transparan ke Putih
             'bg-gradient-to-b from-white/0 via-white/50 to-white': !isDark, 
             // Mode Gelap: Fade dari Transparan ke Hijau Tua (#2A3B30)
             'bg-gradient-to-b from-transparent via-[#2A3B30]/50 to-[#2A3B30]': isDark
         }">
    </div>

    <!-- Dynamic Glow (Hanya terlihat jika tidak ada overlay gambar) -->
    <div class="absolute inset-0 pointer-events-none z-10" :style="gradientStyle" v-if="isDark"></div>

    <!-- Dekorasi blur (Z-10) -->
    <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-green-500/10 dark:bg-[#5E815F]/30 blur-[150px] rounded-full z-10"></div>
    <div class="absolute -bottom-60 -right-60 w-[500px] h-[500px] bg-teal-400/10 dark:bg-[#5E815F]/20 blur-[200px] rounded-full z-10"></div>

    <!-- Card content (Z-20, harus di atas semua background) -->
    <!-- PERUBAHAN: backdrop-blur-xl untuk efek glassmorphism yang lebih kuat -->
    <div class="w-full max-w-sm sm:max-w-md md:max-w-xl z-20 p-6 sm:p-8 rounded-3xl transition-all duration-700 ease-in-out bg-white/85 backdrop-blur-xl shadow-2xl shadow-gray-400/30 dark:bg-[#3B543D]/25 dark:shadow-2xl dark:shadow-black/70 dark:border dark:border-[#5E815F]/30">
      
      <img :src="logo" class="w-48 sm:w-56 md:w-60 mx-auto mb-6 sm:mb-8 animate-fade-in" /> 

      <p class="text-md sm:text-lg text-gray-600 dark:text-gray-300 max-w-xs sm:max-w-md mx-auto mb-8 sm:mb-10 font-light animate-fade-in delay-200">
        Sistem Informasi Manajemen <br> PT. Ciwangun Indah Camp.
      </p>

      <div class="flex flex-col gap-4 w-full max-w-sm mx-auto animate-slide-up delay-300">
        <router-link
          to="/login"
          class="py-3 sm:py-4 px-8 bg-[#5E815F] text-white rounded-xl font-bold text-base sm:text-lg shadow-xl shadow-[#5E815F]/50 hover:bg-[#6D956E] transition-all duration-300 transform hover:scale-[1.01] active:scale-[0.99] focus:outline-none focus:ring-4 focus:ring-[#5E815F]/50 tracking-wide gradient-button"
        >
          Masuk Sebagai Admin / Karyawan
        </router-link>

        <button
          @click="toggleDark"
          class="py-3 text-base flex items-center justify-center gap-2 rounded-xl
                 border border-gray-300 dark:border-[#5E815F]/50
                 text-gray-700 dark:text-gray-300 
                 hover:bg-gray-100 dark:hover:bg-[#5E815F]/20 transition-colors duration-300"
          :aria-label="isDark ? 'Ganti ke Mode Terang' : 'Ganti ke Mode Gelap'"
        >
          <span v-if="isDark" class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Mode Terang
          </span>
          <span v-else class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path></svg>
            Mode Gelap
          </span>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
// Ganti path ini ke lokasi file gambar latar belakang Anda
import logo from "@/assets/logo/logo.png";
// Path gambar diubah menjadi /images/background.jpg (asumsi disimpan di public/images/)
const backgroundImage = "/images/background.jpg"; 

const isDark = ref(localStorage.getItem("dark") === "true");
const gradientStyle = ref({
  backgroundPosition: "center",
  transition: "background-position 0.1s ease-out",
});
const loading = ref(false);

const toggleDark = () => {
  isDark.value = !isDark.value;
  localStorage.setItem("dark", isDark.value);
  document.documentElement.classList.toggle("dark", isDark.value);
};

const moveGradient = (event) => {
  const { clientX: x, clientY: y } = event;
  const offsetX = (x / window.innerWidth) * 100;
  const offsetY = (y / window.innerHeight) * 100;

  gradientStyle.value = {
    // Memastikan background radial gradient cocok dengan warna dasar tema
    background: `radial-gradient(at ${offsetX}% ${offsetY}%, ${isDark.value ? 'rgba(94, 129, 95, 0.2)' : 'rgba(100, 116, 139, 0.05)'}, transparent 80%)`,
    transition: "none",
  };
};

const resetGradient = () => {
  gradientStyle.value = {
    background: `radial-gradient(at center, ${isDark.value ? 'rgba(94, 129, 95, 0.2)' : 'rgba(100, 116, 139, 0.05)'}, transparent 80%)`,
    transition: "background 0.5s ease-out",
  };
};


document.documentElement.classList.toggle("dark", isDark.value);
</script>

<style scoped>
/* Animasi Fade-In */
@keyframes fade-in { from { opacity: 0; } to { opacity: 1; } }
.animate-fade-in { animation: fade-in 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }

/* Animasi Slide-Up */
@keyframes slide-up { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.animate-slide-up { animation: slide-up 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.animate-fade-in, .animate-slide-up { animation-fill-mode: both; }

/* Animasi Gradasi Tombol */
.gradient-button {
  position: relative;
  z-index: 1;
  overflow: hidden;
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

/* Responsiveness */
@media (max-width: 640px) {
  .w-60 { width: 80%; }
  .sm:w-48 { width: 70%; }
  .sm:mb-8 { margin-bottom: 4rem; }
  .max-w-sm { max-width: 90%; }
}
</style>