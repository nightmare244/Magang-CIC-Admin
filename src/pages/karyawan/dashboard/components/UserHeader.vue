<template>
  <div
    v-if="user"
    class="relative group animate-fade-in z-30 font-poppins"
  >
    <div class="flex items-center gap-5 bg-white/5 backdrop-blur-md rounded-[2.5rem] p-5 border border-white/10 shadow-[0_20px_50px_rgba(0,0,0,0.1)]">
      
      <div class="relative flex-shrink-0">
        <div class="w-20 h-20 rounded-[1.75rem] bg-gradient-to-br from-emerald-400/20 to-teal-500/20 p-[1px] rotate-3 group-hover:rotate-0 transition-all duration-500">
          <div class="w-full h-full rounded-[1.70rem] overflow-hidden bg-[#1b3329]/60 border border-white/5">
            <img
    :src="photoUrl" 
    class="w-full h-full object-cover scale-110 group-hover:scale-100 transition-transform duration-700"
    alt="Foto Profil"
    @error="handleImageError"
  />
          </div>
        </div>
        
        <div class="absolute -bottom-1 -right-1 flex h-6 w-6 items-center justify-center">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400/50 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-4 w-4 bg-emerald-500 border-2 border-[#1b3329]"></span>
        </div>
      </div>

      <div class="flex-1 overflow-hidden">
        <div class="flex flex-wrap items-center gap-2 mb-2">
          <div class="inline-flex items-center px-3 py-0.5 rounded-full bg-emerald-500/10 border border-emerald-400/20">
            <span class="text-[9px] font-bold tracking-tight text-emerald-300">
              ID: {{ user.nip || '------' }}
            </span>
          </div>

          <div 
            class="inline-flex items-center px-3 py-0.5 rounded-full border transition-colors duration-300"
            :class="user.status_kerja === 'Tetap' 
              ? 'bg-blue-500/10 border-blue-400/20 text-blue-300' 
              : 'bg-amber-500/10 border-amber-400/20 text-amber-300'"
          >
            <Briefcase class="w-2.5 h-2.5 mr-1.5" />
            <span class="text-[9px] font-bold">
              {{ user.status_kerja || 'Kontrak' }}
            </span>
          </div>
        </div>
        
        <h1 class="text-xl font-bold text-white truncate leading-tight tracking-tight group-hover:text-emerald-300 transition-colors">
          {{ user.name || 'Karyawan CIC' }}
        </h1>
        
        <div class="flex items-center gap-2 mt-1.5 bg-white/5 border border-white/5 w-fit px-2.5 py-1 rounded-lg">
          <Trees class="w-3.5 h-3.5 text-emerald-400" />
          <p class="text-[10px] font-medium text-emerald-100/80 truncate">
            {{ user.departemen?.nama_departemen || 'Staf CIC' }}
          </p>
        </div>
      </div>
    </div>

    <div class="absolute -top-2 -right-2 w-10 h-10 bg-emerald-500/10 rounded-full blur-xl animate-pulse"></div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { Trees, Briefcase } from 'lucide-vue-next';

const props = defineProps({
  user: { type: [Object, null], required: true }
});

// Pengecekan otomatis: coba semua kemungkinan key yang biasanya datang dari API
const photoUrl = computed(() => {
  if (!props.user) return '/img/default-user.png';
  
  return props.user.foto_profil_url || 
         props.user.foto_url || 
         props.user.avatar || 
         props.user.foto || 
         '/img/default-user.png';
});

// Jika link-nya ada tapi gambarnya rusak (404), gunakan avatar inisial nama
const handleImageError = (e) => {
  const name = props.user?.name || 'User';
  e.target.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&background=1e332a&color=ffffff&bold=true`;
};
</script>