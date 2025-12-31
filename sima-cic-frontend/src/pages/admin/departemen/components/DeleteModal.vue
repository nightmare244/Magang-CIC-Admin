<template>
  <Transition name="overlay-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="$emit('close')">
      
      <Transition name="modal-pop">
        <div v-if="show" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          
          <div class="h-1.5 w-full bg-rose-500"></div>

          <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50 dark:border-white/5">
            <img 
              src="/logo.png" 
              alt="Logo CIC" 
              class="w-16 h-auto drop-shadow-sm mb-2"
            />
            <p class="text-[8px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.4em]">
              Ciwangun Indah Camp
            </p>
          </div>

          <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center mb-6">
              <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-[1.5rem] flex items-center justify-center mb-4 text-rose-500 border border-rose-100 dark:border-rose-800/50 shadow-inner">
                <AlertTriangle class="w-8 h-8" />
              </div>
              
              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight uppercase">
                Konfirmasi Otoritas
              </h2>
              <p class="kpi-label-small !text-rose-500 mt-2">Tindakan Penghapusan Data</p>
            </div>

            <div class="space-y-4 text-center">
              <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed font-medium px-2">
                {{ message || 'Apakah Anda yakin ingin menghapus data ini secara permanen dari basis data sistem?' }}
              </p>

              <div v-if="id" class="inline-block px-4 py-2 bg-slate-100 dark:bg-white/5 rounded-2xl border border-slate-200 dark:border-gray-700">
                <span class="text-[10px] font-mono text-slate-400 uppercase tracking-tighter">Node ID:</span>
                <span class="text-[10px] font-mono font-bold text-slate-600 dark:text-slate-300 ml-2">#{{ id }}</span>
              </div>
            </div>

            <div class="mt-8 grid grid-cols-2 gap-3">
              <button 
                class="btn-modal-secondary group"
                @click="$emit('close')" 
              >
                Batalkan
              </button>

              <button 
                class="btn-modal-danger group"
                @click="$emit('confirm')" 
              >
                Hapus Data
              </button>
            </div>
          </div>
          
          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col items-center gap-1">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">CIC Management Security</p>
            <div class="w-12 h-0.5 bg-slate-200 dark:bg-slate-800 rounded-full mt-1"></div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { AlertTriangle } from 'lucide-vue-next';

defineProps({
  show: Boolean,
  id: [Number, String],
  message: String
});

defineEmits(['close', 'confirm']);
</script>

<style scoped lang="postcss">
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.kpi-label-small {
  @apply text-[9px] font-black uppercase tracking-[0.2em];
}

.btn-modal-secondary {
  @apply px-4 py-4 bg-white dark:bg-[#1a1d19] text-slate-500 dark:text-slate-400 rounded-2xl font-bold text-[10px]
          border border-slate-200 dark:border-gray-700 hover:bg-slate-50 dark:hover:bg-gray-800 transition-all 
          active:scale-95 uppercase tracking-widest;
}

.btn-modal-danger {
  @apply px-4 py-4 bg-rose-600 text-white rounded-2xl font-bold text-[10px]
          shadow-xl shadow-rose-600/20 hover:bg-rose-700 transition-all 
          active:scale-95 uppercase tracking-widest;
}

/* Overlay Fade Animation */
.overlay-fade-enter-active,
.overlay-fade-leave-active {
  transition: opacity 0.3s ease;
}
.overlay-fade-enter-from,
.overlay-fade-leave-to {
  opacity: 0;
}

/* Modal Pop Animation */
.modal-pop-enter-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-pop-leave-active {
  transition: all 0.25s ease-in;
}
.modal-pop-enter-from {
  opacity: 0;
  transform: scale(0.9) translateY(20px);
}
.modal-pop-leave-to {
  opacity: 0;
  transform: scale(0.95);
}
</style>