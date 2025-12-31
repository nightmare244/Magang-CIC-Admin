<template>
  <div
    v-if="user"
    class="relative group animate-fade-in z-30"
  >
    <!-- Background Glass Card -->
    <div class="flex items-center gap-5 bg-white/10 backdrop-blur-xl rounded-[2.5rem] p-5 border border-white/20 shadow-[0_20px_50px_rgba(0,0,0,0.2)]">
      
      <!-- Profile Picture Container (Square with Smooth Radius) -->
      <div class="relative flex-shrink-0">
        <!-- Main Image Frame -->
        <div class="w-20 h-20 rounded-[1.75rem] bg-gradient-to-br from-emerald-400/30 to-teal-500/30 p-[2px] shadow-2xl rotate-3 group-hover:rotate-0 transition-transform duration-500">
          <div class="w-full h-full rounded-[1.65rem] overflow-hidden bg-[#1b3329] border border-white/10">
            <img
              :src="user.foto_profil_url || '/img/default-user.png'" 
              class="w-full h-full object-cover scale-110 group-hover:scale-100 transition-transform duration-700"
              alt="Foto Profil"
              @error="(e) => (e.target.src = '/img/default-user.png')"
            />
          </div>
        </div>
        
        <!-- Online Status Badge -->
        <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-[#1b3329]"></span>
        </div>
      </div>

      <!-- User Text Info -->
      <div class="flex-1 overflow-hidden">
        <!-- ID Badge -->
        <div class="inline-flex items-center px-2.5 py-1 rounded-full bg-emerald-500/20 border border-emerald-400/20 mb-2">
          <span class="text-[9px] font-black uppercase tracking-[0.15em] text-emerald-300">
            ID: {{ user.nip || '------' }}
          </span>
        </div>
        
        <!-- Name -->
        <h1 class="text-xl font-extrabold text-white truncate leading-tight drop-shadow-lg tracking-tight">
          {{ user.name || 'Karyawan CIC' }}
        </h1>
        
        <!-- Job Title / Department -->
        <div class="flex items-center gap-2 mt-1.5 bg-black/10 w-fit px-2 py-0.5 rounded-md">
          <Trees class="w-3.5 h-3.5 text-emerald-400" />
          <p class="text-[11px] font-semibold text-emerald-100/90 truncate uppercase tracking-wider">
            {{ user.departemen?.nama_departemen || 'Staf CIC' }}
          </p>
        </div>
      </div>
    </div>

    <!-- Decorative Particle -->
    <div class="absolute -top-2 -right-2 w-10 h-10 bg-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
  </div>
</template>

<script setup>
import { Trees } from 'lucide-vue-next';
defineProps({
  user: { type: [Object, null], required: true }
});
</script>

<style scoped>
/* Keyframes untuk animasi halus saat muncul */
.animate-fade-in { 
  animation: headerAppear 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes headerAppear { 
  from { 
    opacity: 0; 
    transform: translateY(-20px) scale(0.95);
    filter: blur(10px);
  } 
  to { 
    opacity: 1; 
    transform: translateY(0) scale(1);
    filter: blur(0);
  } 
}

/* Custom easing untuk transisi hover */
.group:hover .group-hover\:scale-100 {
  transform: scale(1);
}
</style>