<template>
  <Transition name="overlay-fade">
    <div v-if="show" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[110] p-4 font-poppins">
      <Transition name="modal-pop">
        <div v-if="show" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-5xl h-[90vh] overflow-hidden border border-gray-100 dark:border-gray-800 flex flex-col">
          
          <div class="px-8 py-6 border-b border-gray-100 dark:border-white/5 flex flex-col md:flex-row justify-between items-center gap-4 bg-slate-50/50 dark:bg-white/[0.02]">
            <div class="flex items-center gap-4">
              <div class="w-12 h-12 bg-[#2d4a3e] rounded-2xl flex items-center justify-center text-white shadow-lg shadow-[#2d4a3e]/20">
                <FileText v-if="type === 'pdf'" class="w-6 h-6" />
                <FileSpreadsheet v-else class="w-6 h-6" />
              </div>
              <div>
                <h3 class="text-lg font-bold text-slate-800 dark:text-white leading-tight">Pratinjau Laporan</h3>
                <p class="text-[10px] font-black text-emerald-600 uppercase tracking-[0.2em]">Ekstensi: {{ type.toUpperCase() }}</p>
              </div>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
              <button 
                @click="handlePrint" 
                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3 bg-sky-600 text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-sky-700 transition-all active:scale-95 shadow-lg shadow-sky-600/20"
              >
                <Printer class="w-4 h-4" /> Cetak
              </button>
              
              <button 
                @click="handleDownload" 
                class="flex-1 md:flex-none flex items-center justify-center gap-2 px-6 py-3 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest hover:bg-[#385b4d] transition-all active:scale-95 shadow-lg shadow-emerald-600/20"
              >
                <Download class="w-4 h-4" /> Unduh
              </button>
              
              <button 
                @click="$emit('close')" 
                class="p-3 text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-500/10 rounded-xl transition-all"
              >
                <X class="w-6 h-6" />
              </button>
            </div>
          </div>

          <div class="flex-1 bg-slate-100 dark:bg-[#0a0c0a] relative overflow-hidden flex items-center justify-center">
            <template v-if="type === 'pdf'">
              <iframe 
                v-if="blobUrl" 
                :src="blobUrl" 
                class="w-full h-full border-none shadow-inner"
              ></iframe>
            </template>

            <template v-else-if="type === 'excel'">
              <div class="text-center p-12 max-w-md bg-white dark:bg-[#121512] rounded-[3rem] shadow-xl border border-gray-100 dark:border-white/5 mx-4">
                <div class="w-24 h-24 bg-emerald-50 dark:bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-emerald-600 border border-emerald-100 dark:border-emerald-500/20">
                  <FileSpreadsheet class="w-12 h-12" />
                </div>
                <h4 class="text-xl font-bold text-slate-800 dark:text-white mb-2 uppercase tracking-tight">Berkas Excel Siap</h4>
                <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed mb-8 uppercase tracking-widest font-medium">
                  Browser tidak mendukung pratinjau langsung untuk format spreadsheet. Silakan unduh untuk membuka di MS Excel atau Google Sheets.
                </p>
                <button 
                  @click="handleDownload" 
                  class="w-full py-4 bg-[#2d4a3e] text-white rounded-2xl text-[11px] font-black uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20"
                >
                  Mulai Unduhan
                </button>
              </div>
            </template>
          </div>

          <div class="px-8 py-4 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex justify-center text-[9px] font-black text-slate-400 uppercase tracking-[0.3em]">
            Pratinjau Dokumen Operasional CIC • Ciwangun Indah Camp
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { FileText, Printer, Download, X, FileSpreadsheet } from 'lucide-vue-next';
import Swal from 'sweetalert2';

const props = defineProps({
  show: Boolean,
  blobUrl: String,
  type: String, // 'pdf' atau 'excel'
  fileName: String
});

const emit = defineEmits(['close']);

const handlePrint = () => {
  if (props.type === 'pdf' && props.blobUrl) {
    // Membuka jendela print browser standar
    const printWindow = window.open(props.blobUrl, '_blank');
    if (printWindow) {
      printWindow.onload = () => {
        printWindow.print();
      };
    } else {
      Swal.fire('Pop-up Terblokir', 'Harap izinkan pop-up untuk mencetak dokumen.', 'warning');
    }
  } else {
    Swal.fire({
      title: 'Info Cetak',
      text: 'Dokumen Excel harus diunduh terlebih dahulu untuk dicetak melalui aplikasi spreadsheet Anda.',
      icon: 'info',
      confirmButtonColor: '#2d4a3e'
    });
  }
};

const handleDownload = () => {
  if (!props.blobUrl) return;
  const link = document.createElement('a');
  link.href = props.blobUrl;
  link.setAttribute('download', props.fileName);
  document.body.appendChild(link);
  link.click();
  link.remove();
};
</script>

<style scoped>
.modal-pop-enter-active { transition: all 0.5s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-enter-from { opacity: 0; transform: scale(0.9) translateY(40px); }

.overlay-fade-enter-active { transition: opacity 0.4s ease; }
.overlay-fade-enter-from { opacity: 0; }

.font-poppins { font-family: 'Poppins', sans-serif; }

/* Styling scrollbar untuk iframe jika diperlukan */
iframe::-webkit-scrollbar {
  width: 8px;
}
iframe::-webkit-scrollbar-thumb {
  background: #2d4a3e;
  border-radius: 10px;
}
</style>