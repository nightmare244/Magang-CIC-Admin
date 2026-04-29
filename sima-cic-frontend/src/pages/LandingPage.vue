<template>
  <div
    class="min-h-screen w-full flex flex-col items-center justify-center text-center relative overflow-hidden transition-colors duration-500"
    :class="{'bg-white': !isDark, 'bg-[#2A3B30]': isDark}"
  >
    <div class="absolute inset-0 z-0 bg-cover bg-center transition-opacity duration-700"
         :style="{
             'background-image': 'url(' + backgroundImage + ')',
             'background-color': isDark ? '#1C2824' : '#F0F0F0'
         }">
    </div>

    <div class="absolute inset-0 z-0"
         :class="{
             'bg-gradient-to-b from-white/0 via-white/50 to-white': !isDark, 
             'bg-gradient-to-b from-transparent via-[#2A3B30]/50 to-[#2A3B30]': isDark
         }">
    </div>

    <div class="absolute -top-40 -left-40 w-[400px] h-[400px] bg-green-500/10 dark:bg-[#5E815F]/30 blur-[150px] rounded-full z-10"></div>
    <div class="absolute -bottom-60 -right-60 w-[500px] h-[500px] bg-teal-400/10 dark:bg-[#5E815F]/20 blur-[200px] rounded-full z-10"></div>

    <div class="z-20 flex flex-col items-center justify-center">
      <div class="relative animate-float">
        <div class="absolute inset-0 bg-emerald-500/20 blur-[60px] rounded-full animate-pulse-slow"></div>
        
        <img 
          :src="logo" 
          class="w-52 sm:w-72 relative z-10 animate-pop-in"
          alt="Logo Ciwangun Indah Camp"
        />
      </div>
    </div>

  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRouter } from "vue-router";
import logo from "@/assets/logo/logo.png";

const router = useRouter();
const backgroundImage = "/images/background.jpg"; 
const isDark = ref(localStorage.getItem("dark") === "true");

onMounted(() => {
  document.documentElement.classList.toggle("dark", isDark.value);

  // Durasi diperlama menjadi 4 detik sebelum pindah ke login
  setTimeout(() => {
    router.push("/login");
  }, 4000);
});
</script>

<style scoped>
/* Animasi Muncul Pertama Kali */
@keyframes pop-in {
  0% {
    opacity: 0;
    transform: scale(0.8);
    filter: blur(15px);
  }
  100% {
    opacity: 1;
    transform: scale(1);
    filter: blur(0px);
  }
}

/* Animasi Mengambang Halus */
@keyframes float {
  0%, 100% { transform: translateY(0); }
  50% { transform: translateY(-15px); }
}

/* Animasi Glow */
@keyframes pulse-slow {
  0%, 100% { opacity: 0.2; transform: scale(1); }
  50% { opacity: 0.5; transform: scale(1.3); }
}

.animate-pop-in {
  animation: pop-in 2s cubic-bezier(0.2, 0.8, 0.2, 1) forwards;
}

.animate-float {
  animation: float 4s ease-in-out infinite;
}

.animate-pulse-slow {
  animation: pulse-slow 4s infinite ease-in-out;
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>