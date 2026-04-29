<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4">
          <button 
            @click="$router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>

          <div>
            <p class="text-[11px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-wide">portal pengajuan</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Ajukan Izin</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up">
        
        <form @submit.prevent="submitForm" class="space-y-6">
          
          <div class="space-y-3">
            <div class="flex items-center gap-2 px-1">
              <Layers class="w-4 h-4 text-emerald-500 opacity-60" />
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Tipe Pengajuan</label>
            </div>
            <div class="relative group">
              <select v-model="form.tipe_izin" class="input-cic appearance-none" required>
                <option value="sakit">Sakit / Medis</option>
                <option value="izin">Izin Keperluan</option>
                <option value="cuti">Cuti Tahunan</option>
              </select>
              <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                <ChevronDown class="w-4 h-4" />
              </div>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-3">
              <div class="flex items-center gap-2 px-1">
                <Calendar class="w-4 h-4 text-emerald-500 opacity-60" />
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Mulai</label>
              </div>
              <input v-model="form.tanggal_mulai" type="date" class="input-cic" required />
            </div>
            <div class="space-y-3">
              <div class="flex items-center gap-2 px-1">
                <CalendarCheck class="w-4 h-4 text-emerald-500 opacity-60" />
                <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Selesai</label>
              </div>
              <input v-model="form.tanggal_selesai" type="date" class="input-cic" required />
            </div>
          </div>

          <div class="space-y-3">
            <div class="flex items-center gap-2 px-1">
              <MessageSquareText class="w-4 h-4 text-emerald-500 opacity-60" />
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Alasan / Detail</label>
            </div>
            <textarea 
              v-model="form.keterangan" 
              class="input-cic min-h-[120px] py-4 resize-none leading-relaxed" 
              placeholder="Berikan alasan yang jelas..."
              required
            ></textarea>
          </div>

          <div class="space-y-3">
            <div class="flex items-center gap-2 px-1">
              <Paperclip class="w-4 h-4 text-emerald-500 opacity-60" />
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Lampiran Bukti</label>
            </div>
            
            <input type="file" ref="fileInput" @change="handleFileChange" class="hidden" accept="image/*,application/pdf" />
            
            <div v-if="previewUrl && isImage" class="relative rounded-[2rem] overflow-hidden mb-3 border border-slate-200 dark:border-white/10 aspect-video shadow-inner">
               <img :src="previewUrl" class="w-full h-full object-cover" />
               <button @click.stop="resetFile" class="absolute top-3 right-3 bg-rose-500 text-white p-2 rounded-xl shadow-lg active:scale-90 transition-transform">
                 <X class="w-4 h-4" />
               </button>
            </div>

            <div 
              @click="triggerFileInput"
              class="group relative overflow-hidden rounded-[2rem] border border-dashed transition-all duration-300 flex items-center gap-4 px-5 py-4 cursor-pointer"
              :class="fileName 
                ? 'border-emerald-500 bg-emerald-50/20 dark:bg-emerald-500/5' 
                : 'border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 hover:border-emerald-500/50'"
            >
              <div :class="fileName ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-white dark:bg-white/10 text-slate-400'" class="w-12 h-12 rounded-2xl flex items-center justify-center transition-all group-active:scale-90">
                <FileUp v-if="!fileName" class="w-5 h-5" />
                <CheckCircle2 v-else class="w-5 h-5" />
              </div>
              
              <div class="flex-1 overflow-hidden">
                <p class="text-[13px] font-bold truncate text-slate-700 dark:text-slate-200">
                  {{ fileName || 'Pilih Lampiran' }}
                </p>
                <p class="text-[9px] text-slate-400 font-medium uppercase tracking-wide mt-0.5">maksimal 2MB (JPG, PNG, PDF)</p>
              </div>

              <button v-if="fileName && !isImage" @click.stop="resetFile" class="p-2 text-rose-500">
                <X class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div class="bg-slate-50 dark:bg-white/5 rounded-[2rem] p-5 flex gap-4 items-start border border-slate-100 dark:border-white/5">
            <div class="w-10 h-10 bg-emerald-500/10 dark:bg-emerald-500/20 rounded-xl flex items-center justify-center text-emerald-600 shrink-0">
              <ShieldCheck class="w-5 h-5" />
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
              Pastikan data yang anda masukkan benar. penyalahgunaan izin dapat dikenakan sanksi sesuai aturan perusahaan.
            </p>
          </div>

          <div class="pt-4">
            <button 
              type="submit" 
              class="w-full bg-[#1e332a] text-white py-4.5 rounded-[2rem] font-bold text-[14px] shadow-xl shadow-emerald-900/20 active:scale-[0.98] transition-all flex items-center justify-center gap-3 disabled:opacity-70 h-16"
              :disabled="submitting"
            >
              <Loader2 v-if="submitting" class="w-5 h-5 animate-spin" />
              <template v-else>
                <span>Kirim Pengajuan</span>
                <Send class="w-4 h-4 text-emerald-400" />
              </template>
            </button>
          </div>
        </form>
      </div>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-medium tracking-widest capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { 
  ChevronLeft, ChevronDown, FileUp, Layers,
  Send, Loader2, CheckCircle2, X, ShieldCheck, 
  Calendar, CalendarCheck, MessageSquareText, Paperclip
} from 'lucide-vue-next';

const router = useRouter();
const submitting = ref(false);
const fileName = ref('');
const fileInput = ref(null);
const previewUrl = ref(null);

const form = ref({
  tipe_izin: 'sakit',
  tanggal_mulai: '',
  tanggal_selesai: '',
  keterangan: '',
  file_pendukung: null,
});

const isImage = computed(() => {
  if (!form.value.file_pendukung) return false;
  return form.value.file_pendukung.type.startsWith('image/');
});

const triggerFileInput = () => {
  if (fileInput.value) fileInput.value.click();
};

const handleFileChange = (event) => {
  const file = event.target.files[0];
  if (!file) return;
  
  const allowed = ['image/jpeg', 'image/png', 'application/pdf'];
  if (!allowed.includes(file.type)) {
    alert("format tidak didukung");
    resetFile();
    return;
  }

  if (file.size > 2 * 1024 * 1024) {
    alert("file terlalu besar (maks 2mb)");
    resetFile();
    return;
  }

  form.value.file_pendukung = file;
  fileName.value = file.name;

  if (file.type.startsWith('image/')) {
    previewUrl.value = URL.createObjectURL(file);
  } else {
    previewUrl.value = null;
  }
};

const resetFile = () => {
  form.value.file_pendukung = null;
  fileName.value = '';
  previewUrl.value = null;
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
    alert(err.response?.data?.message || 'gagal mengirim');
  } finally {
    submitting.value = false;
  }
};
</script>

<style scoped lang="postcss">
.input-cic {
  @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 
         rounded-2xl px-5 py-4 text-[13px] outline-none font-bold 
         focus:border-emerald-500 focus:bg-white dark:focus:bg-white/10 transition-all dark:text-white;
}

.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

input[type="date"]::-webkit-calendar-picker-indicator {
  @apply opacity-30 dark:invert;
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>