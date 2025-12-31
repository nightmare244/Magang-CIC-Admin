<template>
  <Transition name="overlay-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="$emit('close')">
      <Transition name="modal-pop">
        <div v-if="show" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          
          <div class="h-1.5 w-full bg-[#2d4a3e]"></div>

          <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50 dark:border-white/5">
            <img src="/logo.png" alt="Logo CIC" class="w-16 h-auto drop-shadow-sm mb-2" />
            <p class="text-[8px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.4em]">Ciwangun Indah Camp</p>
          </div>

          <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center mb-8">
              <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center mb-4 text-[#2d4a3e] dark:text-emerald-500 border border-emerald-100 dark:border-emerald-500/20 shadow-inner">
                <Download class="w-8 h-8" />
              </div>
              <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Format Laporan</h2>
              <p class="text-[9px] font-black uppercase tracking-[0.2em] text-emerald-600 mt-2">Pilih ekspor dokumen operasional</p>
            </div>

            <div class="grid grid-cols-1 gap-3">
              <button 
                @click="$emit('confirm', 'excel')" 
                class="flex items-center justify-between px-6 py-4 bg-emerald-50 dark:bg-emerald-500/5 border border-emerald-100 dark:border-emerald-500/20 rounded-2xl group hover:bg-[#2d4a3e] hover:text-white transition-all duration-300"
              >
                <div class="flex items-center">
                  <div class="p-2 bg-white dark:bg-emerald-500/20 rounded-lg mr-3 shadow-sm group-hover:bg-white/20">
                    <FileSpreadsheet class="w-5 h-5 text-emerald-600 group-hover:text-white" />
                  </div>
                  <span class="text-xs font-bold uppercase tracking-widest text-emerald-700 dark:text-emerald-400 group-hover:text-white">MS Excel</span>
                </div>
                <ChevronRight class="w-4 h-4 opacity-50 group-hover:translate-x-1 transition-transform" />
              </button>

              <button 
                @click="$emit('confirm', 'pdf')" 
                class="flex items-center justify-between px-6 py-4 bg-rose-50 dark:bg-rose-500/5 border border-rose-100 dark:border-rose-500/20 rounded-2xl group hover:bg-rose-600 hover:text-white transition-all duration-300"
              >
                <div class="flex items-center">
                  <div class="p-2 bg-white dark:bg-rose-500/20 rounded-lg mr-3 shadow-sm group-hover:bg-white/20">
                    <FileText class="w-5 h-5 text-rose-600 group-hover:text-white" />
                  </div>
                  <span class="text-xs font-bold uppercase tracking-widest text-rose-700 dark:text-rose-400 group-hover:text-white">Dokumen PDF</span>
                </div>
                <ChevronRight class="w-4 h-4 opacity-50 group-hover:translate-x-1 transition-transform" />
              </button>

              <button @click="$emit('close')" class="mt-4 text-[10px] font-bold text-slate-400 uppercase tracking-widest hover:text-slate-600 dark:hover:text-white transition-colors">
                Batalkan
              </button>
            </div>
          </div>
          
          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex justify-center uppercase tracking-[0.3em] text-[9px] font-black text-slate-300">
            CIC Cloud Storage Service
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { Download, FileSpreadsheet, FileText, ChevronRight } from 'lucide-vue-next';
defineProps({ show: Boolean });
defineEmits(['close', 'confirm']);
</script>

<style scoped>
.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-enter-from { opacity: 0; transform: scale(0.9) translateY(20px); }
.overlay-fade-enter-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from { opacity: 0; }
</style>