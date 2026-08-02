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
            <p class="text-[10px] font-medium text-emerald-500/90 leading-none mb-1 capitalize tracking-[0.3em]">pengaturan akun</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">perbarui profil</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="fetching" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-16 text-center shadow-sm border border-slate-100 dark:border-white/5">
        <div class="w-10 h-10 border-4 border-emerald-500/10 border-t-emerald-500 rounded-full animate-spin mx-auto mb-4"></div>
        <p class="text-[10px] font-bold text-slate-400 capitalize tracking-[0.2em]">sinkronisasi data...</p>
      </div>

      <div v-else class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up">
        
        <div class="flex items-center gap-3 mb-8 ml-2">
          <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
          <h2 class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em]">informasi personal</h2>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          
          <div class="space-y-2 px-2">
            <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">nama lengkap</label>
            <input v-model="form.name" type="text" class="input-cic" placeholder="masukkan nama..." required />
          </div>

          <div class="space-y-2 px-2">
            <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">nomor whatsapp</label>
            <input v-model="form.nomor_hp" type="tel" class="input-cic" placeholder="0812..." />
          </div>

          <div class="grid grid-cols-2 gap-4 px-2">
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">tempat lahir</label>
              <input v-model="form.tempat_lahir" type="text" class="input-cic" placeholder="kota..." />
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">tgl lahir</label>
              <input v-model="form.tanggal_lahir" type="date" class="input-cic" />
            </div>
          </div>

          <div class="space-y-2 px-2">
            <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">jenis kelamin</label>
            <div class="relative">
              <select v-model="form.jenis_kelamin" class="input-cic appearance-none">
                <option value="">pilih...</option>
                <option value="L">laki-laki</option>
                <option value="P">perempuan</option>
              </select>
              <div class="absolute right-6 top-1/2 -translate-y-1/2 pointer-events-none text-slate-400">
                <UserCircle class="w-4 h-4 opacity-40" />
              </div>
            </div>
          </div>

          <div class="space-y-2 px-2">
            <label class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] ml-1">alamat lengkap</label>
            <textarea 
              v-model="form.alamat" 
              class="input-cic min-h-[120px] py-4 resize-none leading-relaxed font-medium" 
              placeholder="tulis alamat domisili..."
            ></textarea>
          </div>

          <div class="pt-6 space-y-4">
            <button
              type="submit"
              :disabled="loading"
              class="btn-submit-cic w-full shadow-lg shadow-emerald-500/20 active:scale-95 transition-all flex items-center justify-center gap-3"
            >
              <div v-if="loading" class="w-4 h-4 border-2 border-white/20 border-t-white rounded-full animate-spin"></div>
              <Save v-else class="w-4 h-4" />
              <span class="capitalize tracking-[0.2em] font-black">{{ loading ? "proses..." : "simpan perubahan" }}</span>
            </button>
            
            <button 
              type="button"
              @click="router.back()"
              class="w-full text-[10px] font-bold text-slate-300 capitalize tracking-[0.4em] active:scale-95 transition-all py-2"
            >
              batalkan
            </button>
          </div>
        </form>
      </div>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-bold tracking-[0.5em] capitalize">ciwangun indah camp</p>
    </footer>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { ChevronLeft, Save, UserCircle } from "lucide-vue-next";

const router = useRouter();
const form = ref({
    name: "",
    nomor_hp: "",
    tempat_lahir: "",
    tanggal_lahir: "",
    jenis_kelamin: "",
    alamat: "",
});
const loading = ref(false);
const fetching = ref(true);

const fetchProfil = async () => {
    try {
        const { data } = await api.get("/karyawan/profil");
        Object.assign(form.value, {
            name: data.data.name,
            nomor_hp: data.data.nomor_hp || "",
            tempat_lahir: data.data.tempat_lahir || "",
            tanggal_lahir: data.data.tanggal_lahir || "",
            jenis_kelamin: data.data.jenis_kelamin || "",
            alamat: data.data.alamat || "",
        });
    } catch (error) {
        console.error("Gagal mengambil data profil:", error);
    } finally {
        setTimeout(() => { fetching.value = false; }, 600);
    }
};

onMounted(fetchProfil);

const submit = async () => {
    loading.value = true;
    try {
        await api.put("/karyawan/profil", form.value);
        router.push("/karyawan/profil");
    } catch (error) {
        alert(error.response?.data?.message || "Gagal memperbarui profil");
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped lang="postcss">
.input-cic {
    /* Font menggunakan bold (bukan black) agar isi input tidak terlalu berat dibanding label */
    @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
           rounded-[1.5rem] px-6 py-4 text-[10px] outline-none font-bold tracking-[0.1em]
           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500/50 
           transition-all duration-300 dark:text-white placeholder:text-slate-400 placeholder:font-medium;
}

.btn-submit-cic {
    @apply bg-emerald-500 text-white rounded-[2rem] py-5 text-[10px];
}

.animate-fade-in-up { 
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
    opacity: 0;
}

@keyframes fadeInUp { 
    from { transform: translateY(30px); opacity: 0; } 
    to { transform: translateY(0); opacity: 1; } 
}

/* Mematikan highlight biru pada mobile */
* {
    -webkit-tap-highlight-color: transparent;
}
</style>