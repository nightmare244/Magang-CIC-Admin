<template>
  <Transition name="overlay-fade">
    <div 
      v-if="isOpen" 
      class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins"
      @click.self="closeModal"
    >
      <Transition name="modal-pop">
        <div 
          v-if="isOpen" 
          class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800"
        >
          <div class="h-1.5 w-full bg-[#2d4a3e]"></div>

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
            <div class="flex flex-col items-center text-center">
              <div class="w-16 h-16 bg-emerald-50 dark:bg-emerald-500/10 rounded-[1.5rem] flex items-center justify-center mb-6 text-[#2d4a3e] dark:text-emerald-500 border border-emerald-100 dark:border-emerald-500/20 shadow-inner">
                <CheckCircle2 class="w-8 h-8" />
              </div>

              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight uppercase">
                Konfirmasi Otoritas
              </h2>
              <p class="kpi-label-small !text-emerald-600 mt-2 mb-4">Persetujuan Administrasi</p>
              
              <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed font-medium">
                Anda akan memberikan persetujuan resmi untuk pengajuan izin karyawan:
                <span class="block font-bold text-[#2d4a3e] dark:text-emerald-500 mt-2 italic text-base">
                   {{ izin?.user?.name || 'Karyawan' }}
                </span>
              </p>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-3">
              <button
                @click="handleApprove"
                :disabled="isProcessing"
                class="btn-approve-modal-eco group"
              >
                <span v-if="!isProcessing" class="flex items-center justify-center">
                  <Check class="w-4 h-4 mr-2 group-hover:scale-125 transition-transform" /> Setujui Pengajuan
                </span>
                <span v-else class="flex items-center justify-center">
                  <RefreshCw class="animate-spin h-4 w-4 mr-2" />
                  Memproses...
                </span>
              </button>
              
              <button
                @click="closeModal"
                :disabled="isProcessing"
                class="btn-cancel-modal-eco"
              >
                Batalkan
              </button>
            </div>
          </div>

          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col items-center gap-1">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">Manajemen Izin Karyawan</p>
            <div class="w-12 h-0.5 bg-slate-200 dark:bg-slate-800 rounded-full mt-1"></div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { CheckCircle2, Check, RefreshCw } from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
  izin: Object,
});

const emit = defineEmits(['close', 'approve']);
const isProcessing = ref(false);

const closeModal = () => {
  if (!isProcessing.value) {
    emit('close');
  }
};

const handleApprove = async () => {
  isProcessing.value = true;
  try {
    await api.put(`/admin/persetujuan-izin/${props.izin.id}`, { status: 'disetujui' });
    emit('approve');
    emit('close');
  } catch (error) {
    console.error("Error approving izin:", error);
    // Disarankan menggunakan toast daripada alert untuk konsistensi UI
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

.btn-approve-modal-eco {
  @apply py-4 px-6 bg-[#2d4a3e] text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest
          shadow-xl shadow-[#2d4a3e]/20 hover:bg-[#385b4d] transition-all active:scale-95 disabled:opacity-50 font-poppins;
}

.btn-cancel-modal-eco {
  @apply py-3 px-6 bg-white dark:bg-[#1a1d19] text-slate-500 dark:text-slate-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest
          border border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-white/10 transition-all active:scale-95 font-poppins;
}

/* --- VUE TRANSITIONS --- */
.overlay-fade-enter-active, .overlay-fade-leave-active {
    @apply transition-opacity duration-300 ease-out;
}
.overlay-fade-enter-from, .overlay-fade-leave-to {
    @apply opacity-0;
}

.modal-pop-enter-active {
    transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-pop-leave-active {
    @apply transition-all duration-200 ease-in;
}
.modal-pop-enter-from {
    @apply opacity-0 scale-90 translate-y-4;
}
.modal-pop-leave-to {
    @apply opacity-0 scale-95;
}
</style>