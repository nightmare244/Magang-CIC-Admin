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
          class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-md overflow-hidden border border-gray-100 dark:border-gray-800"
        >
          <div class="h-1.5 w-full bg-rose-600"></div>

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
              <div class="w-16 h-16 bg-rose-50 dark:bg-rose-900/20 rounded-[1.5rem] flex items-center justify-center mb-4 text-rose-600 border border-rose-100 dark:border-rose-800/50 shadow-inner">
                <XCircle class="w-8 h-8" />
              </div>

              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight uppercase">
                Tolak Pengajuan
              </h2>
              <p class="kpi-label-small !text-rose-600 mt-2">Otoritas Penolakan</p>
            </div>

            <div class="space-y-6">
              <p class="text-sm text-slate-500 dark:text-slate-400 text-center leading-relaxed font-medium">
                Anda akan menolak pengajuan izin milik karyawan: 
                <span class="font-bold text-[#2d4a3e] dark:text-emerald-500 block italic mt-1 text-base uppercase tracking-wide">
                  {{ izin?.user?.name || 'Karyawan' }}
                </span>
              </p>

              <div class="space-y-2">
                <label class="kpi-label-small !text-slate-400 ml-1">Alasan Penolakan Administrasi <span class="text-rose-500">*</span></label>
                <textarea
                  v-model="alasan"
                  rows="3"
                  class="input-field-eco-textarea"
                  placeholder="Sebutkan alasan mengapa pengajuan ini tidak disetujui..."
                  required
                ></textarea>
                
                <Transition name="slide-up">
                  <p v-if="errorMsg" class="text-[10px] text-rose-600 font-bold mt-1 ml-1 uppercase tracking-widest flex items-center gap-1">
                    <AlertTriangle class="w-3 h-3" /> {{ errorMsg }}
                  </p>
                </Transition>
              </div>
            </div>

            <div class="mt-8 grid grid-cols-1 sm:grid-cols-2 gap-3">
              <button
                @click="closeModal"
                :disabled="isProcessing"
                class="btn-cancel-modal-eco order-2 sm:order-1"
              >
                Batalkan
              </button>
              
              <button
                @click="handleReject"
                :disabled="isProcessing || !alasan.trim()"
                class="btn-reject-modal-eco order-1 sm:order-2 group"
              >
                <span v-if="!isProcessing" class="flex items-center justify-center">
                  <X class="w-4 h-4 mr-2 group-hover:scale-125 transition-transform" /> Konfirmasi Tolak
                </span>
                <span v-else class="flex items-center justify-center">
                  <RefreshCw class="animate-spin h-4 w-4 mr-2" />
                  Memproses...
                </span>
              </button>
            </div>
          </div>

          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col items-center gap-1">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">Pusat Kendali Administrasi</p>
            <div class="w-12 h-0.5 bg-slate-200 dark:bg-slate-800 rounded-full mt-1"></div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref, watch } from 'vue';
import api from '@/services/api';
import { XCircle, X, RefreshCw, AlertTriangle } from 'lucide-vue-next';

const props = defineProps({
  isOpen: Boolean,
  izin: Object,
});

const emit = defineEmits(['close', 'reject']);

const alasan = ref('');
const isProcessing = ref(false);
const errorMsg = ref('');

const closeModal = () => {
  if (!isProcessing.value) {
    alasan.value = '';
    errorMsg.value = '';
    emit('close');
  }
};

const handleReject = async () => {
  if (!alasan.value.trim()) {
    errorMsg.value = 'Wajib memberikan alasan penolakan.';
    return;
  }

  isProcessing.value = true;
  errorMsg.value = '';
  
  try {
    await api.put(`/admin/persetujuan-izin/${props.izin.id}`, { 
      status: 'ditolak', 
      alasan_penolakan: alasan.value 
    });
    emit('reject');
    closeModal();
  } catch (error) {
    console.error("Error rejecting izin:", error);
    errorMsg.value = error.response?.data?.message || "Gagal memproses penolakan.";
  } finally {
    isProcessing.value = false;
  }
};

watch(() => props.isOpen, (newVal) => {
    if (!newVal) {
        alasan.value = '';
        errorMsg.value = '';
    }
});
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-label-small {
  @apply text-[9px] font-black uppercase tracking-[0.2em];
}

.input-field-eco-textarea {
  @apply w-full bg-slate-50 dark:bg-[#1a1d19] border border-slate-200 dark:border-slate-800 rounded-3xl px-5 py-4 
          focus:ring-4 focus:ring-rose-500/10 focus:border-rose-500 outline-none transition-all dark:text-white font-medium text-sm resize-none font-poppins;
}

.btn-reject-modal-eco {
  @apply py-4 px-6 bg-rose-600 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest
          shadow-xl shadow-rose-600/20 hover:bg-rose-700 transition-all active:scale-95 disabled:bg-slate-300 disabled:shadow-none font-poppins;
}

.btn-cancel-modal-eco {
  @apply py-4 px-6 bg-white dark:bg-[#1a1d19] text-slate-500 dark:text-slate-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest
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

.slide-up-enter-active {
  transition: all 0.3s ease-out;
}
.slide-up-enter-from {
  transform: translateY(10px);
  opacity: 0;
}
</style>