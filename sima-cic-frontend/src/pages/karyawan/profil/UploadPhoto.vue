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
            @click="router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>

          <div>
            <p class="text-[10px] font-black text-emerald-400/90 leading-none mb-1 capitalize tracking-[0.2em]">pengaturan profil</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">ganti foto profil</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6 animate-fade-in-up">
      
      <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 flex flex-col items-center">
        
        <div class="relative group mb-8">
          <div class="absolute -inset-1 bg-gradient-to-r from-emerald-500 to-teal-500 rounded-full blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
          
          <div class="relative w-44 h-44 rounded-full overflow-hidden bg-slate-50 dark:bg-white/5 border-4 border-white dark:border-[#1a1c1a] shadow-2xl flex items-center justify-center">
            <img 
              v-if="preview" 
              :src="preview" 
              class="w-full h-full object-cover" 
            />
            <div v-else class="flex flex-col items-center gap-2">
              <ImageIcon class="w-10 h-10 text-slate-300" />
              <p class="text-[10px] font-black text-slate-400 tracking-widest capitalize">pratinjau</p>
            </div>
          </div>

          <label class="absolute bottom-2 right-2 p-3 bg-emerald-500 rounded-2xl text-white shadow-lg border-4 border-white dark:border-[#111311] cursor-pointer active:scale-90 transition-all">
            <Camera class="w-5 h-5" />
            <input 
              type="file" 
              accept="image/*" 
              @change="onFileChange" 
              class="hidden"
            />
          </label>
        </div>

        <div class="w-full space-y-2 text-center mb-8">
          <p class="text-[11px] font-bold text-slate-700 dark:text-slate-300 capitalize tracking-wide">pilih berkas terbaik</p>
          <p class="text-[10px] font-black text-slate-400 tracking-widest uppercase">jpg, png atau webp • maks 2mb</p>
        </div>

        <div class="grid grid-cols-2 gap-4 w-full">
          <button 
            @click="router.back()" 
            class="px-6 py-4 rounded-2xl bg-slate-50 dark:bg-white/5 text-slate-400 text-[10px] font-black tracking-widest uppercase active:scale-95 transition-all border border-slate-100 dark:border-white/5"
          >
            batal
          </button>
          
          <button
            @click="upload"
            :disabled="loading || !file"
            class="px-6 py-4 rounded-2xl bg-emerald-500 text-white text-[10px] font-black tracking-widest uppercase shadow-lg shadow-emerald-500/20 active:scale-95 transition-all disabled:opacity-50 disabled:grayscale flex items-center justify-center gap-2"
          >
            <div v-if="loading" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
            <span v-if="loading">proses...</span>
            <span v-else>simpan</span>
          </button>
        </div>
      </div>

      <div class="bg-amber-50/50 dark:bg-amber-500/5 border border-amber-100 dark:border-amber-500/10 rounded-[2rem] p-5 flex items-start gap-4">
        <div class="w-10 h-10 bg-amber-500 rounded-xl flex-shrink-0 flex items-center justify-center text-white">
          <AlertCircle class="w-5 h-5" />
        </div>
        <div class="flex flex-col gap-1">
          <span class="text-[11px] font-bold text-amber-800 dark:text-amber-400 capitalize">informasi</span>
          <p class="text-[10px] text-amber-700/70 dark:text-amber-400/60 font-medium leading-relaxed">pastikan wajah terlihat jelas untuk mempermudah verifikasi identitas oleh sistem administrasi.</p>
        </div>
      </div>

    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-[0.3em] uppercase">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import Swal from "sweetalert2";
import { 
  ChevronLeft, Camera, Image as ImageIcon, 
  AlertCircle, UploadCloud 
} from 'lucide-vue-next';

const router = useRouter();
const file = ref(null);
const preview = ref(null);
const loading = ref(false);

const onFileChange = (e) => {
    const selectedFile = e.target.files[0];
    if (selectedFile) {
        if (selectedFile.size > 2 * 1024 * 1024) {
            Swal.fire({
                title: "Gagal",
                text: "Ukuran file terlalu besar. Maksimal 2MB.",
                icon: "error",
                confirmButtonColor: "#10b981",
                customClass: {
                    popup: 'rounded-[2rem] font-poppins',
                    title: 'text-[15px] font-bold',
                    htmlContainer: 'text-[12px]'
                }
            });
            return;
        }
        
        file.value = selectedFile;
        preview.value = URL.createObjectURL(selectedFile);
    }
};

const upload = async () => {
    if (!file.value) return;

    loading.value = true;

    try {
        const formData = new FormData();
        formData.append("foto_profil", file.value);

        const response = await api.post("/karyawan/profil/upload-photo", formData, {
            headers: { 
                "Content-Type": "multipart/form-data" 
            },
        });

        if (response.data.success) {
            Swal.fire({
                title: "Berhasil!",
                text: "Foto profil Anda telah diperbarui.",
                icon: "success",
                timer: 2000,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-[2rem] font-poppins'
                }
            });
            
            setTimeout(() => {
                router.push("/karyawan/profil");
            }, 1500);
        }
    } catch (error) {
        console.error("Upload error:", error);
        const errorMessage = error.response?.data?.message || "Gagal mengunggah foto.";
        Swal.fire({
            title: "Error",
            text: errorMessage,
            icon: "error",
            confirmButtonColor: "#10b981"
        });
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped lang="postcss">
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>