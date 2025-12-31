<template>
  <Transition name="overlay-fade">
    <div v-if="isOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="closeModal">
      <Transition name="modal-pop">
        <div v-if="isOpen" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          <div class="h-1.5 w-full bg-indigo-600"></div>
          <div class="p-8">
            <div class="flex flex-col items-center text-center">
              <div class="w-20 h-20 bg-indigo-50 dark:bg-indigo-500/10 rounded-[2rem] flex items-center justify-center mb-6 text-indigo-600 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-500/20 shadow-inner">
                <RefreshCw class="w-10 h-10" />
              </div>
              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight">Konfirmasi Selesai</h2>
              <p class="text-[9px] font-black uppercase tracking-[0.2em] text-indigo-600 mt-2 mb-6">Pengembalian Aset</p>
              
              <div class="bg-slate-50 dark:bg-white/5 p-5 rounded-3xl w-full border border-slate-100 dark:border-white/5 mb-6">
                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-2 text-left">Barang:</p>
                <p class="text-sm font-bold text-slate-800 dark:text-white text-left truncate">{{ peminjaman?.inventaris?.nama_barang }}</p>
                <div class="flex justify-between items-center mt-3 pt-3 border-t border-slate-200/50 dark:border-white/5">
                  <span class="text-[10px] font-bold text-slate-400 uppercase">Jumlah:</span>
                  <span class="text-xs font-black text-indigo-600">{{ peminjaman?.quantity }} Unit</span>
                </div>
              </div>

              <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed px-2 mb-8">
                Pastikan barang sudah diserahkan ke bagian gudang/admin sebelum menekan tombol konfirmasi.
              </p>

              <div class="grid grid-cols-1 gap-3 w-full">
                <button @click="handleReturn" :disabled="isProcessing" class="btn-return-modal">
                  <span v-if="!isProcessing" class="flex items-center justify-center gap-2">
                    <ArrowUpCircle class="w-4 h-4" /> Konfirmasi Pengembalian
                  </span>
                  <Loader2 v-else class="w-4 h-4 animate-spin" />
                </button>
                <button @click="closeModal" :disabled="isProcessing" class="btn-cancel-modal">Batal</button>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { RefreshCw, ArrowUpCircle, Loader2 } from 'lucide-vue-next';

const props = defineProps({ isOpen: Boolean, peminjaman: Object });
const emit = defineEmits(['close', 'success']);
const isProcessing = ref(false);

const closeModal = () => { if (!isProcessing.value) emit('close'); };

const handleReturn = async () => {
  isProcessing.value = true;
  try {
    await api.put(`/karyawan/peminjaman/${props.peminjaman.id}/kembalikan`);
    
    // BARIS INI YANG PENTING:
    emit('success'); // Memicu refresh data di halaman Detail
    emit('close');   // MEMERINTAHKAN MODAL UNTUK TUTUP
    
  } catch (error) {
    alert("Gagal memproses pengembalian.");
  } finally {
    isProcessing.value = false;
  }
};
</script>

<style scoped lang="postcss">
.btn-return-modal { @apply py-4 bg-indigo-600 text-white rounded-[1.2rem] font-bold uppercase text-[10px] tracking-widest shadow-xl shadow-indigo-900/20 active:scale-95 transition-all disabled:opacity-50; }
.btn-cancel-modal { @apply py-3 bg-white dark:bg-[#1a1d19] text-slate-500 rounded-[1.2rem] font-bold uppercase text-[10px] tracking-widest border border-slate-100 dark:border-slate-800 active:scale-95 transition-all; }
.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity 0.3s; }
.overlay-fade-enter-from, .overlay-fade-leave-to { opacity: 0; }
.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-enter-from { opacity: 0; transform: scale(0.9) translateY(20px); }
</style>