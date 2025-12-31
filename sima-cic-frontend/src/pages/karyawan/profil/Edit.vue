<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { ChevronLeft, Save, Loader2, UserCircle } from "lucide-vue-next";

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

/**
 * Mengambil data profil terbaru untuk mengisi form
 * Sesuai rute GET /karyawan/profil
 */
const fetchProfil = async () => {
    try {
        const { data } = await api.get("/karyawan/profil");
        // Mengisi form dengan data dari backend
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
        fetching.value = false;
    }
};

onMounted(fetchProfil);

/**
 * Mengirimkan pembaruan profil ke backend
 * Sesuai rute PUT /karyawan/profil
 */
const submit = async () => {
    loading.value = true;
    try {
        await api.put("/karyawan/profil", form.value);
        // Kembali ke halaman profil setelah sukses
        router.push("/karyawan/profil");
    } catch (error) {
        alert(error.response?.data?.message || "Gagal memperbarui profil");
    } finally {
        loading.value = false;
    }
};
</script>

<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="p-2 bg-white/10 hover:bg-white/20 rounded-full transition-all">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Edit Profil</h1>
        <div class="w-10"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20">
      <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5 animate-fade-in-up">
        
        <div v-if="fetching" class="py-20 text-center">
            <Loader2 class="w-10 h-10 animate-spin text-emerald-500 mx-auto mb-4" />
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Menyiapkan Form...</p>
        </div>

        <form v-else @submit.prevent="submit" class="space-y-6">
          <div class="flex items-center gap-2 mb-2">
            <UserCircle class="w-5 h-5 text-emerald-600" />
            <h2 class="text-xs font-bold text-slate-400 uppercase tracking-widest">Informasi Personal</h2>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nama Lengkap</label>
            <input v-model="form.name" type="text" class="input-cic" placeholder="Masukkan nama..." required />
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Nomor WhatsApp</label>
            <input v-model="form.nomor_hp" type="tel" class="input-cic" placeholder="0812..." />
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tempat Lahir</label>
              <input v-model="form.tempat_lahir" type="text" class="input-cic" placeholder="Kota..." />
            </div>
            <div class="space-y-2">
              <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Tgl Lahir</label>
              <input v-model="form.tanggal_lahir" type="date" class="input-cic" />
            </div>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Jenis Kelamin</label>
            <select v-model="form.jenis_kelamin" class="input-cic appearance-none">
              <option value="">Pilih...</option>
              <option value="L">Laki-laki</option>
              <option value="P">Perempuan</option>
            </select>
          </div>

          <div class="space-y-2">
            <label class="text-[10px] font-bold text-slate-400 uppercase tracking-widest ml-1">Alamat Lengkap</label>
            <textarea v-model="form.alamat" class="input-cic min-h-[100px] py-4 resize-none" placeholder="Tulis alamat domisili..."></textarea>
          </div>

          <div class="pt-4">
            <button
              type="submit"
              :disabled="loading"
              class="btn-cic-primary w-full py-5 flex items-center justify-center gap-3"
            >
              <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
              <Save v-else class="w-4 h-4" />
              <span>{{ loading ? "Menyimpan..." : "Simpan Perubahan" }}</span>
            </button>
            
            <button 
              type="button"
              @click="$router.back()"
              class="w-full mt-4 text-[10px] font-bold text-slate-300 uppercase tracking-[0.3em] active:scale-95 transition-all"
            >
              Batalkan
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<style scoped lang="postcss">
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

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
</style>