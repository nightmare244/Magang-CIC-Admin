<template>
  <div 
    class="relative overflow-hidden bg-white dark:bg-[#121512] rounded-[2.5rem] p-6 border transition-all duration-300 group font-poppins"
    :class="pengumuman.telah_dibaca ? 'border-slate-100 opacity-75' : 'border-emerald-100 shadow-xl shadow-emerald-900/5'"
  >
    <div 
      v-if="!pengumuman.telah_dibaca" 
      class="absolute top-0 right-0 bg-emerald-500 text-white text-[8px] font-black uppercase px-4 py-1.5 rounded-bl-2xl tracking-[0.2em] shadow-sm"
    >
      Pesan Baru
    </div>

    <div class="flex flex-col gap-4">
      <div class="flex items-start justify-between gap-4">
        <div class="flex-1 min-w-0">
          <div class="flex items-center gap-2 mb-1">
             <User class="w-3 h-3 text-emerald-600" />
             <p class="text-[9px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-widest truncate">
               {{ pengumuman.user?.name || 'Administrator' }} • {{ formatDate(pengumuman.created_at) }}
             </p>
          </div>
          <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight mb-1 truncate">
            {{ pengumuman.judul }}
          </h3>
          <p class="text-[10px] text-slate-400 font-medium">No: {{ pengumuman.nomor_surat || '-' }}</p>
        </div>
        
        <div class="flex-shrink-0 p-3 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5">
          <component 
            :is="pengumuman.telah_dibaca ? CheckCircle2 : Megaphone" 
            class="w-5 h-5 transition-colors duration-500"
            :class="pengumuman.telah_dibaca ? 'text-emerald-500' : 'text-slate-400 animate-pulse'"
          />
        </div>
      </div>

      <div class="relative">
        <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed line-clamp-2 font-medium italic">
          "{{ pengumuman.isi }}"
        </p>
      </div>

      <div class="flex items-center justify-between pt-2 border-t border-slate-50 dark:border-white/5 mt-2">
        <router-link 
          :to="`/karyawan/pengumuman/${pengumuman.id}`"
          class="text-[10px] font-bold text-slate-400 hover:text-emerald-600 transition-colors uppercase tracking-widest flex items-center gap-1 group"
        >
          Lihat Detail
          <ArrowRight class="w-3 h-3 transform group-hover:translate-x-1 transition-transform" />
        </router-link>

        <button 
          v-if="!pengumuman.telah_dibaca"
          @click="$emit('tandaiDibaca')"
          class="flex items-center gap-2 bg-[#2d4a3e] text-white px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-[#1e332a] shadow-lg shadow-emerald-900/20 active:scale-95 transition-all"
        >
          <Check class="w-3 h-3" />
          Konfirmasi Paham
        </button>
        
        <div 
          v-else 
          class="flex items-center gap-1.5 text-[9px] font-bold text-emerald-500 bg-emerald-50 dark:bg-emerald-500/10 px-4 py-2 rounded-xl"
        >
          <CheckCircle2 class="w-3 h-3" />
          Selesai Dibaca
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { 
  Megaphone, CheckCircle2, ArrowRight, 
  User, Check 
} from 'lucide-vue-next';

defineProps({
  pengumuman: {
    type: Object,
    required: true
  }
});

defineEmits(['tandaiDibaca']);

const formatDate = (dateString) => {
  if (!dateString) return '-';
  return new Date(dateString).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};
</script>

<style scoped lang="postcss">
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;  
  overflow: hidden;
}
</style>