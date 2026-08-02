<template>
  <Transition name="overlay-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[120] p-4 font-poppins">
      <Transition name="modal-pop">
        <div v-if="show" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          <div class="h-1.5 w-full bg-rose-600"></div>

          <div class="p-8">
            <div class="flex flex-col items-center text-center">
              <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 rounded-[2rem] flex items-center justify-center mb-6 text-rose-600 border border-rose-100 dark:border-rose-800/50 shadow-inner">
                <FileWarning class="w-10 h-10" />
              </div>

              <h3 class="text-xl font-bold text-slate-800 dark:text-white leading-tight uppercase tracking-tight">
                Ekspor Gagal
              </h3>
              <p class="text-[9px] font-black text-rose-500 uppercase tracking-[0.2em] mt-2 mb-6">
                System Processing Error
              </p>

              <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-2xl w-full border border-slate-100 dark:border-white/5 mb-6">
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                  {{ message || 'Terjadi kesalahan internal saat mencoba mengolah data laporan. Pastikan data tersedia untuk filter ini.' }}
                </p>
              </div>

              <div class="w-full space-y-3">
                <button 
                  @click="$emit('retry')" 
                  class="w-full py-4 bg-slate-800 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-xl hover:bg-slate-900 active:scale-95 transition-all flex items-center justify-center gap-2"
                >
                  <RefreshCw class="w-4 h-4" /> Coba Lagi
                </button>
                
                <button 
                  @click="$emit('close')" 
                  class="w-full py-3 text-slate-400 font-bold uppercase text-[10px] tracking-widest hover:text-slate-600 dark:hover:text-white transition-colors"
                >
                  Tutup
                </button>
              </div>
            </div>
          </div>

          <div class="px-8 py-4 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex justify-center">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">
              Error Code: 500 / Network Failure
            </p>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { FileWarning, RefreshCw } from 'lucide-vue-next';
defineProps({
  show: Boolean,
  message: String
});
defineEmits(['close', 'retry']);
</script>