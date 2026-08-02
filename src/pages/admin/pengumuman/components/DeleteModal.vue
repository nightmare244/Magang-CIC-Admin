<template>
  <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 overflow-hidden outline-none font-poppins">
    <div 
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-[2px] transition-opacity duration-500" 
      @click="close"
    ></div>

    <div class="relative w-full max-w-md bg-white dark:bg-[#0a0c0a] rounded-[2.5rem] shadow-2xl border border-gray-100 dark:border-white/5 transform transition-all animate-pop overflow-hidden">
      
      <div class="h-1.5 w-full bg-gradient-to-r from-rose-600 to-rose-400"></div>

      <div class="p-10 text-center">
        <div class="mx-auto flex items-center justify-center h-24 w-24 rounded-[2rem] bg-rose-50 dark:bg-rose-950/20 mb-8 border border-rose-100 dark:border-rose-900/30 shadow-inner relative group">
          <div class="absolute inset-0 bg-rose-500/10 rounded-[2rem] animate-pulse"></div>
          <Trash2 class="h-10 w-10 text-rose-600 relative z-10 group-hover:scale-110 transition-transform duration-500" />
        </div>

        <h3 class="text-2xl font-black text-slate-800 dark:text-white tracking-tight mb-2 uppercase">
          Konfirmasi Likuidasi
        </h3>
        <p class="text-[10px] font-black text-rose-600 uppercase tracking-[0.3em] mb-6">
          Security Authorization Required
        </p>
        
        <div class="bg-slate-50 dark:bg-white/[0.02] rounded-3xl p-6 border border-slate-100 dark:border-white/5">
          <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
            Apakah Anda yakin ingin menghapus permanen pengumuman:
            <span class="font-bold text-slate-900 dark:text-emerald-500 block italic mt-2 uppercase tracking-wide break-words">
              "{{ pengumuman.judul }}"
            </span>
          </p>
        </div>
        
        <p class="text-[10px] text-slate-400 mt-4 italic font-medium">
          *Prosedur ini akan melikuidasi data secara permanen dari basis data utama.
        </p>
      </div>

      <div class="flex flex-col sm:flex-row gap-4 p-10 pt-0">
        <button 
          @click="close" 
          class="btn-back-eco flex-1 justify-center py-5"
        >
          Batalkan
        </button>
        <button 
          @click="deletePengumuman" 
          class="btn-danger-eco flex-1 justify-center py-5 shadow-lg shadow-rose-600/20"
        >
          Ya, Likuidasi
        </button>
      </div>

      <div class="px-8 py-4 bg-slate-50 dark:bg-white/[0.03] border-t border-gray-100 dark:border-white/5 flex justify-center">
        <p class="text-[9px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.4em]">
          Command Center Protocol v1.0
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Trash2 } from 'lucide-vue-next';
import api from '@/services/api';

const props = defineProps({
  pengumuman: {
    type: Object,
    required: true
  }
});

const emit = defineEmits(['close', 'confirm']);

const close = () => {
  emit('close');
};

const deletePengumuman = async () => {
  try {
    await api.delete(`/admin/pengumuman/${props.pengumuman.id}`);
    emit('confirm'); 
  } catch (error) {
    console.error('Gagal melikuidasi data pengumuman:', error);
    alert('KRITIKAL: Otoritas server menolak permintaan likuidasi data.');
  }
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.btn-back-eco {
  @apply inline-flex items-center px-6 bg-white dark:bg-[#1a1d19] border border-slate-200 dark:border-white/10 
         rounded-2xl text-[10px] font-bold uppercase tracking-widest text-slate-500 dark:text-slate-400
         hover:bg-slate-50 dark:hover:bg-white/5 transition-all active:scale-95;
}

.btn-danger-eco {
  @apply inline-flex items-center px-6 rounded-2xl bg-rose-600 text-white text-[10px] font-bold uppercase tracking-widest
         hover:bg-rose-700 transition-all active:scale-95;
}

.animate-pop {
    animation: modalPop 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes modalPop {
    from { opacity: 0; transform: scale(0.95) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}

/* Custom Scrollbar for dark mode compatibility */
::-webkit-scrollbar {
  width: 4px;
}
::-webkit-scrollbar-thumb {
  @apply bg-slate-200 dark:bg-white/10 rounded-full;
}
</style>