<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-24 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-all">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Ajukan Izin</h1>
        <div class="w-10"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20">
      <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-2xl border border-white dark:border-white/5 animate-fade-in-up">
        
        <form @submit.prevent="submitForm" class="space-y-6">
          <div class="space-y-2">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tipe Izin</label>
            <div class="relative">
              <select v-model="form.tipe_izin" class="input-cic appearance-none" required>
                <option value="sakit">Sakit</option>
                <option value="izin">Izin Keperluan</option>
                <option value="cuti">Cuti Tahunan</option>
              </select>
              <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                <ChevronDown class="w-4 h-4" />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
              <input v-model="form.tanggal_mulai" type="date" class="input-cic" required />
            </div>
            <div class="space-y-2">
              <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Selesai</label>
              <input v-model="form.tanggal_selesai" type="date" class="input-cic" required />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">Alasan / Keterangan</label>
            <textarea 
              v-model="form.keterangan" 
              class="input-cic min-h-[120px] py-4 resize-none" 
              placeholder="Berikan alasan yang jelas..."
              required
            ></textarea>
          </div>

          <div class="space-y-2">
            <label class="text-[11px] font-bold text-slate-400 uppercase tracking-widest ml-1">
              Lampiran Bukti (Opsional)
            </label>
            
            <input 
              type="file" 
              ref="fileInput"
              @change="handleFileChange" 
              class="hidden"
            />
            
            <div 
              @click="triggerFileInput"
              class="input-cic flex items-center gap-3 cursor-pointer transition-all active:scale-[0.98]"
              :class="fileName ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-500/10' : 'text-slate-400'"
            >
              <FileUp v-if="!fileName" class="w-4 h-4 text-emerald-600" />
              <CheckCircle2 v-else class="w-4 h-4 text-emerald-500" />
              
              <span class="truncate text-[11px] font-medium flex-1">
                {{ fileName || 'Pilih Gambar atau PDF' }}
              </span>

              <button 
                v-if="fileName" 
                @click.stop="resetFile" 
                class="p-1.5 hover:bg-rose-100 dark:hover:bg-rose-900/30 rounded-full text-rose-500"
              >
                <X class="w-4 h-4" />
              </button>
            </div>
            <p class="text-[9px] text-slate-400 italic ml-1 mt-1">*Maksimal 2MB (JPG/PNG/PDF)</p>
          </div>

          <div class="pt-4">
            <button 
              type="submit" 
              class="btn-cic-primary w-full py-5 flex items-center justify-center gap-3"
              :disabled="submitting"
            >
              <Loader2 v-if="submitting" class="w-5 h-5 animate-spin" />
              <Send v-else class="w-4 h-4" />
              <span>{{ submitting ? 'Mengirim...' : 'Kirim Pengajuan' }}</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, ChevronDown, FileUp, 
  Send, Loader2, CheckCircle2, X 
} from 'lucide-vue-next';

const router = useRouter();
const submitting = ref(false);
const fileName = ref('');
const fileInput = ref(null);

const form = ref({
  tipe_izin: 'sakit',
  tanggal_mulai: '',
  tanggal_selesai: '',
  keterangan: '',
  file_pendukung: null,
});

// FUNGSI PEMANGGIL EXPLORER YANG AMAN
const triggerFileInput = () => {
  // Gunakan requestAnimationFrame agar browser menyelesaikan render UI 
  // sebelum melakukan pemanggilan sistem operasi (Explorer)
  requestAnimationFrame(() => {
    if (fileInput.value) {
      fileInput.value.click();
    }
  });
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file) return;

  // Validasi format secara manual
  const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
  if (!allowed.includes(file.type)) {
    alert("Format tidak didukung (Gunakan JPG, PNG, atau PDF)");
    resetFile();
    return;
  }

  // Validasi ukuran (Max 2MB)
  if (file.size > 2 * 1024 * 1024) {
    alert("File terlalu besar (Maksimal 2MB)");
    resetFile();
    return;
  }

  form.value.file_pendukung = file;
  fileName.value = file.name;
};

const resetFile = () => {
  form.value.file_pendukung = null;
  fileName.value = '';
  if (fileInput.value) fileInput.value.value = "";
};

const submitForm = async () => {
  if (submitting.value) return;
  submitting.value = true;

  const formData = new FormData();
  formData.append('tipe_izin', form.value.tipe_izin);
  formData.append('tanggal_mulai', form.value.tanggal_mulai);
  formData.append('tanggal_selesai', form.value.tanggal_selesai);
  formData.append('keterangan', form.value.keterangan);
  if (form.value.file_pendukung) {
    formData.append('file_pendukung', form.value.file_pendukung);
  }

  try {
    await api.post('/karyawan/izin', formData, {
      headers: { 'Content-Type': 'multipart/form-data' },
    });
    router.push({ name: 'karyawan.izin.index' });
  } catch (err) {
    alert(err.response?.data?.message || 'Gagal mengirim');
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped lang="postcss">
/* Style tetap sama */
.input-cic {
    @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 
            rounded-2xl px-5 py-3.5 text-xs outline-none font-bold 
            focus:ring-2 focus:ring-emerald-500 transition-all dark:text-white;
}
.btn-cic-primary {
    @apply bg-[#2d4a3e] text-white rounded-[2rem] font-bold text-xs 
            uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20 
            active:scale-95 transition-all disabled:opacity-50;
}
.animate-fade-in-up { 
    animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; 
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>