<template>
  <Transition name="overlay-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="$emit('close')">
      <Transition name="modal-pop">
        <div v-if="show" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          <div class="h-1.5 w-full bg-rose-500"></div>
          
          <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50 dark:border-white/5">
            <img src="/logo.png" alt="Logo CIC" class="w-16 h-auto drop-shadow-sm mb-2" />
            <p class="text-[8px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.4em]">Ciwangun Indah Camp</p>
          </div>

          <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center mb-6">
              <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-[1.5rem] flex items-center justify-center mb-4 text-rose-500 border border-rose-100 dark:border-rose-800/50 shadow-inner">
                <Trash2 class="w-8 h-8" />
              </div>
              <h2 class="text-xl font-bold text-slate-800 dark:text-white uppercase tracking-tight">Hapus Presensi?</h2>
              <p class="text-[9px] font-black uppercase tracking-[0.2em] text-rose-500 mt-2">Tindakan Penghapusan Permanen</p>
            </div>

            <p class="text-slate-500 dark:text-slate-400 text-sm leading-relaxed text-center px-2 mb-8">
              Data kehadiran <span class="font-bold text-slate-800 dark:text-white">{{ name }}</span> akan dihapus dari arsip digital. Tindakan ini tidak dapat dibatalkan.
            </p>

            <div class="grid grid-cols-1 gap-3">
              <button @click="$emit('confirm')" class="w-full py-4 bg-rose-600 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-xl shadow-rose-600/20 hover:bg-rose-700 active:scale-95 transition-all">
                Ya, Hapus Data
              </button>
              <button @click="$emit('close')" class="w-full py-4 bg-slate-100 dark:bg-white/5 text-slate-500 rounded-2xl font-bold uppercase text-[10px] tracking-widest hover:bg-slate-200 active:scale-95 transition-all">
                Batalkan
              </button>
            </div>
          </div>
          
          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex justify-center uppercase tracking-[0.3em] text-[9px] font-black text-slate-300">
            CIC Management Security
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { Trash2 } from 'lucide-vue-next';
defineProps({ show: Boolean, name: String });
defineEmits(['close', 'confirm']);
</script>

<style scoped>
.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-enter-from { opacity: 0; transform: scale(0.9) translateY(20px); }
.overlay-fade-enter-active { transition: opacity 0.3s ease; }
.overlay-fade-enter-from { opacity: 0; }
</style>