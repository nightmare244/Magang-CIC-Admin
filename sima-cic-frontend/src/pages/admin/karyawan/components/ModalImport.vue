<template>
  <Transition name="overlay-fade">
    <div v-if="isOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="closeModal">
      <Transition name="modal-pop">
        <div v-if="isOpen" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
          <div class="h-1.5 w-full bg-blue-500"></div>

          <div class="pt-8 pb-4 flex flex-col items-center border-b border-gray-50 dark:border-white/5">
            <img src="/logo.png" alt="Logo CIC" class="w-14 h-auto mb-2" />
            <p class="text-[8px] font-black text-blue-600 dark:text-blue-400 uppercase tracking-[0.4em]">Batch Registry System</p>
          </div>

          <div class="p-8 pt-6">
            <div class="flex flex-col items-center text-center">
              <div class="w-16 h-16 bg-blue-50 dark:bg-blue-500/10 rounded-[1.5rem] flex items-center justify-center mb-6 text-blue-600 border border-blue-100 dark:border-blue-500/20 shadow-inner">
                <UploadCloud class="w-8 h-8" />
              </div>

              <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight uppercase">Impor Personel</h2>
              <p class="kpi-label-small !text-blue-600 mt-2 mb-4">Input Data Massal</p>
              
              <div class="w-full mt-2">
                <label v-if="!selectedFile" class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed border-slate-200 dark:border-slate-800 rounded-[2rem] cursor-pointer hover:bg-slate-50 dark:hover:bg-white/5 transition-all">
                  <div class="flex flex-col items-center justify-center pt-5 pb-6 px-4">
                    <FileUp class="w-6 h-6 text-slate-300 mb-2" />
                    <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider text-center leading-relaxed">Klik untuk pilih file Excel <br> (5 Kolom Wajib)</p>
                  </div>
                  <input type="file" class="hidden" @change="onFileChange" accept=".xlsx, .xls" />
                </label>

                <div v-else class="flex items-center justify-between p-4 bg-blue-50 dark:bg-blue-500/10 rounded-2xl border border-blue-100 dark:border-blue-500/20">
                  <div class="flex items-center">
                    <FileText class="w-5 h-5 text-blue-600 mr-3" />
                    <div class="text-left">
                      <p class="text-[10px] font-black text-blue-800 dark:text-blue-400 uppercase truncate max-w-[150px]">{{ selectedFile.name }}</p>
                      <p class="text-[8px] text-blue-400 uppercase font-bold">{{ (selectedFile.size / 1024).toFixed(1) }} KB</p>
                    </div>
                  </div>
                  <button @click="selectedFile = null" class="text-blue-800 dark:text-blue-400 hover:bg-blue-200 dark:hover:bg-blue-800/30 p-1 rounded-lg">
                    <X class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <div class="mt-8 grid grid-cols-1 gap-3">
              <button @click="handleImport" :disabled="isProcessing || !selectedFile" class="btn-import-modal group">
                <span v-if="!isProcessing" class="flex items-center justify-center">
                  <ShieldCheck class="w-4 h-4 mr-2" /> Proses Sinkronisasi
                </span>
                <span v-else class="flex items-center justify-center">
                  <RefreshCw class="animate-spin h-4 w-4 mr-2" /> Menanamkan Data...
                </span>
              </button>

              <button 
                type="button"
                @click="downloadTemplate" 
                class="flex items-center justify-center gap-2 text-[10px] font-bold text-blue-600 dark:text-blue-400 hover:underline my-2 uppercase tracking-wider"
              >
                <FileDown class="w-3.5 h-3.5" /> Unduh Template Excel
              </button>
              
              <button @click="closeModal" :disabled="isProcessing" class="btn-cancel-modal">Batalkan</button>
            </div>
          </div>

          <div class="px-8 py-5 bg-slate-50 dark:bg-white/[0.02] border-t border-gray-100 dark:border-gray-800 flex flex-col items-center">
            <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">Sistem Registrasi Massal</p>
          </div>
        </div>
      </Transition>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { 
  UploadCloud, FileUp, FileText, X, 
  ShieldCheck, RefreshCw, FileDown 
} from 'lucide-vue-next';

const props = defineProps({ 
  isOpen: Boolean 
});

const emit = defineEmits(['close', 'success']);
const isProcessing = ref(false);
const selectedFile = ref(null);

const closeModal = () => { 
  if (!isProcessing.value) { 
    selectedFile.value = null; 
    emit('close'); 
  } 
};

const onFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    selectedFile.value = file;
  }
};

const downloadTemplate = async () => {
  try {
    const response = await api.get('/admin/karyawan/template', { responseType: 'blob' });
    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    
    // Pastikan ekstensi filenya .xlsx
    link.setAttribute('download', 'Template_Import_Karyawan_CIC.xlsx');
    
    document.body.appendChild(link);
    link.click();
    window.URL.revokeObjectURL(url);
  } catch (error) {
    console.error("Gagal mendownload template", error);
    alert("Gagal mengunduh template Excel.");
  }
};

const handleImport = async () => {
  if (!selectedFile.value) return;
  
  isProcessing.value = true;
  const formData = new FormData();
  formData.append('file', selectedFile.value);

  try {
    const res = await api.post('/admin/karyawan/import', formData);
    
    if (res.data.success) {
      emit('success'); 
      emit('close'); 
      selectedFile.value = null;
    }
  } catch (error) {
    console.error("Gagal Impor:", error);
    alert(error.response?.data?.message || "Terjadi kesalahan saat mengunggah file.");
  } finally {
    isProcessing.value = false;
  }
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }
.kpi-label-small { @apply text-[9px] font-black uppercase tracking-[0.2em]; }
.btn-import-modal {
  @apply py-4 px-6 bg-blue-600 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest
          shadow-xl shadow-blue-900/20 hover:bg-blue-500 transition-all active:scale-95 disabled:opacity-30;
}
.btn-cancel-modal {
  @apply py-3 px-6 bg-white dark:bg-[#1a1d19] text-slate-500 dark:text-slate-400 rounded-2xl font-bold uppercase text-[10px] tracking-widest
          border border-slate-100 dark:border-gray-800 hover:bg-slate-50 transition-all active:scale-95;
}

.overlay-fade-enter-active, .overlay-fade-leave-active { @apply transition-opacity duration-300 ease-out; }
.overlay-fade-enter-from, .overlay-fade-leave-to { @apply opacity-0; }
.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-leave-active { @apply transition-all duration-200 ease-in; }
.modal-pop-enter-from { @apply opacity-0 scale-90 translate-y-4; }
.modal-pop-leave-to { @apply opacity-0 scale-95; }
</style>