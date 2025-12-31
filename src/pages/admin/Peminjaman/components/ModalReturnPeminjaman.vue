<template>
  <Transition name="modal-fade">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeModal"></div>
      
      <div class="bg-white dark:bg-[#121512] w-full max-w-sm rounded-[2.5rem] overflow-hidden relative z-10 shadow-2xl border border-gray-100 dark:border-gray-800 text-center animate-modal-pop">
        
        <div class="h-1.5 w-full bg-[#2d4a3e]"></div>

        <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50 dark:border-white/5">
            <img src="/logo.png" alt="Logo CIC" class="w-16 h-auto drop-shadow-sm mb-2" />
            <p class="text-[8px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.4em]">
              Ciwangun Indah Camp
            </p>
        </div>

        <div class="p-8 pt-6">
          <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-[#2d4a3e] dark:text-emerald-500 border border-slate-100 dark:border-white/5 shadow-inner">
            <PackageSearch class="w-10 h-10" />
          </div>
          
          <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2 font-poppins uppercase tracking-tight">
            Konfirmasi Kembali
          </h3>
          <p class="kpi-label-small !text-[#2d4a3e] dark:!text-emerald-500 mb-4">Verifikasi Logistik</p>
          
          <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-poppins px-2">
            Apakah Anda memvalidasi bahwa aset <span class="font-bold text-[#2d4a3e] dark:text-emerald-500 italic">"{{ peminjaman?.inventaris?.nama_barang }}"</span> telah diterima kembali dalam kondisi baik?
          </p>
          
          <div class="flex flex-col gap-3">
            <button 
              @click="handleReturn" 
              :disabled="isProcessing"
              class="btn-confirm-eco group"
            >
              <RefreshCw v-if="isProcessing" class="w-4 h-4 mr-2 animate-spin" />
              <Check v-else class="w-4 h-4 mr-2 group-hover:scale-125 transition-transform" />
              {{ isProcessing ? 'Memproses...' : 'Ya, Konfirmasi Kembali' }}
            </button>
            
            <button 
              @click="closeModal" 
              :disabled="isProcessing"
              class="btn-cancel-eco"
            >
              Batalkan
            </button>
          </div>
        </div>

        <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col items-center gap-1">
            <p class="text-[9px] font-black text-slate-400 dark:text-slate-600 uppercase tracking-[0.3em]">
              Logistik CIC • Asset Management
            </p>
            <div class="w-12 h-0.5 bg-slate-200 dark:bg-slate-800 rounded-full mt-1"></div>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { PackageSearch, RefreshCw, Check } from 'lucide-vue-next';

const props = defineProps({
    isOpen: Boolean,
    peminjaman: Object
});

const emit = defineEmits(['close', 'success']);
const isProcessing = ref(false);

const closeModal = () => {
    if (!isProcessing.value) emit('close');
};

const handleReturn = async () => {
    isProcessing.value = true;
    try {
        await api.put(`/admin/persetujuan-peminjaman/${props.peminjaman.id}/return`);
        emit('success');
        emit('close');
    } catch (e) {
        console.error(e);
        // Bisa diganti dengan library toast jika ada
    } finally {
        isProcessing.value = false;
    }
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-label-small {
  @apply text-[9px] font-black uppercase tracking-[0.2em];
}

.btn-confirm-eco {
  @apply py-4 px-6 bg-[#2d4a3e] text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest
         shadow-xl shadow-[#2d4a3e]/20 hover:bg-[#385b4d] transition-all active:scale-95 disabled:opacity-50 flex items-center justify-center;
}

.btn-cancel-eco {
  @apply py-3 px-6 bg-white dark:bg-[#1a1d19] text-slate-500 dark:text-slate-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest
         border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-white/10 transition-all active:scale-95;
}

/* --- ANIMATIONS --- */
.modal-fade-enter-active, .modal-fade-leave-active { 
    @apply transition-opacity duration-300 ease-out; 
}
.modal-fade-enter-from, .modal-fade-leave-to { 
    @apply opacity-0; 
}

.animate-modal-pop { 
    animation: modalPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.9) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>