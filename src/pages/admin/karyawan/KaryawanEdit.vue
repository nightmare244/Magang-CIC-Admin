<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <UserCog class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Edit Data Karyawan
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            ID Database: {{ route.params.id }} | Personel: {{ form.nama }}
          </p>
        </div>
      </div>

      <Transition name="slide-fade">
        <div v-if="apiError" class="flex items-center gap-3 px-4 py-2 bg-rose-50 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 rounded-r-xl font-bold text-sm uppercase tracking-widest shadow-sm">
            <AlertTriangle class="w-4 h-4" /> {{ apiError }}
        </div>
      </Transition>
    </header>

    <div v-if="initialLoading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse font-poppins text-center">Menghubungkan ke basis data personel...<br>Memastikan sinkronisasi tanggal aman.</p>
    </div>

    <form v-else @submit.prevent="updateKaryawan" class="max-w-5xl mx-auto space-y-8 font-poppins">
      
      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm shadow-xl border-none">
        <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 mb-6 flex items-center gap-2 font-bold uppercase tracking-widest">
          <Camera class="w-4 h-4" /> Identitas Visual
        </h3>
        <div class="flex flex-col sm:flex-row items-center gap-8">
          <div class="relative group">
            <div class="w-36 h-36 rounded-[2.5rem] overflow-hidden border-4 border-white dark:border-slate-800 shadow-2xl transition-transform group-hover:scale-105 duration-500">
              <img :src="currentFotoUrl" class="w-full h-full object-cover" @error="handleImageError" />
            </div>
            <div v-if="newFotoFile" class="absolute -bottom-2 -right-2 bg-emerald-500 text-white p-2 rounded-2xl shadow-lg animate-bounce">
              <Check class="w-5 h-5 font-bold" />
            </div>
          </div>
          <div class="flex-1 space-y-4">
            <div>
              <label class="kpi-label !text-slate-500">Unggah Foto Baru</label>
              <input type="file" @change="handleFileUpload" accept="image/*" class="block w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-6 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:tracking-widest file:bg-[#2d4a3e] file:text-white hover:file:bg-[#385b4d] transition-all cursor-pointer" />
            </div>
            <p v-if="errors.foto" class="text-rose-500 text-[10px] font-bold mt-1 italic uppercase tracking-tighter">{{ errors.foto[0] }}</p>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <div class="card-eco p-8 bg-white/50 backdrop-blur-sm space-y-6 shadow-sm border-none">
          <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 font-bold uppercase tracking-widest">Informasi Dasar</h3>
          <div class="space-y-4">
            <div class="space-y-1">
              <label class="kpi-label !text-slate-500">Nama Lengkap <span class="text-rose-500">*</span></label>
              <input type="text" v-model="form.nama" class="input-field-eco" required />
            </div>
            <div class="space-y-1">
              <label class="kpi-label !text-slate-500">Email Korespondensi <span class="text-rose-500">*</span></label>
              <input type="email" v-model="form.email" class="input-field-eco" required />
            </div>
            <div class="grid grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">NIP <span class="text-rose-500">*</span></label>
                <input type="text" v-model="form.nip" class="input-field-eco font-mono !bg-slate-50 dark:!bg-slate-900/50" required />
              </div>
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Unit Departemen <span class="text-rose-500">*</span></label>
                <select v-model="form.departemen_id" class="input-field-eco" required>
                  <option v-for="d in departemens" :key="d.id" :value="d.id">{{ d.nama_departemen }}</option>
                </select>
              </div>
            </div>
          </div>
        </div>

        <div class="card-eco p-8 bg-white/50 backdrop-blur-sm space-y-6 shadow-sm border-none">
          <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 font-bold uppercase tracking-widest">Detail Personal</h3>
          <div class="space-y-4">
            <div class="grid grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Tempat Lahir</label>
                <input type="text" v-model="form.tempat_lahir" class="input-field-eco" />
              </div>
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Tanggal Lahir <span class="text-rose-500">*</span></label>
                <input type="date" v-model="form.tanggal_lahir" class="input-field-eco focus:ring-emerald-500" required />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Jenis Kelamin</label>
                <select v-model="form.jenis_kelamin" class="input-field-eco">
                  <option value="L">Laki-laki</option>
                  <option value="P">Perempuan</option>
                </select>
              </div>
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Kontak (HP/WhatsApp)</label>
                <input type="text" v-model="form.nomor_hp" class="input-field-eco font-mono" />
              </div>
            </div>
            <div class="grid grid-cols-2 gap-6">
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Role Akses</label>
                <select v-model="form.role" class="input-field-eco font-bold uppercase tracking-tighter" required>
                  <option value="karyawan">Karyawan</option>
                  <option value="admin">Administrator</option>
                </select>
              </div>
              <div class="space-y-1">
                <label class="kpi-label !text-slate-500">Status Akun</label>
                <select v-model="form.is_active" class="input-field-eco font-bold uppercase tracking-tighter" required>
                  <option :value="1">AKTIF</option>
                  <option :value="0">NON-AKTIF</option>
                </select>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 pt-4 border-t border-gray-100 dark:border-gray-800">
        <div class="space-y-1">
          <label class="kpi-label !text-slate-500 ml-1">Domisili Lengkap (KTP)</label>
          <textarea v-model="form.alamat" rows="4" class="input-field-eco min-h-[120px] resize-none" placeholder="Masukkan alamat lengkap sesuai domisili aktif..."></textarea>
        </div>
        
        <div class="card-eco p-6 bg-amber-50/50 dark:bg-amber-900/5 border-amber-100 dark:border-amber-900/20 shadow-none relative overflow-hidden group">
          <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div class="space-y-1">
              <div class="flex items-center gap-2">
                <Lock class="w-4 h-4 text-amber-600" />
                <label class="kpi-label !text-amber-800 dark:!text-amber-500 uppercase !mb-0 font-bold tracking-widest">Keamanan Akun</label>
              </div>
              <p class="text-[10px] text-amber-600/70 italic font-medium">Password default: TTTTBBHH (Format tgl lahir)</p>
            </div>
            <button type="button" @click="resetPasswordToDefault" class="flex items-center gap-2 px-5 py-2.5 bg-amber-500 hover:bg-amber-600 text-white rounded-xl text-[10px] font-black uppercase tracking-widest transition-all active:scale-95 shadow-lg shadow-amber-500/20">
              <RefreshCw class="w-3.5 h-3.5" :class="{'animate-spin': isResetting}" /> Reset Password
            </button>
          </div>
          <Transition name="slide-fade">
            <div v-if="form.password" class="mt-4 p-3 bg-emerald-500/10 border border-emerald-500/20 rounded-lg flex items-center gap-3">
              <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
              <p class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold uppercase tracking-tight">
                Password siap dikirim: <span class="font-mono bg-white dark:bg-black/20 px-2 py-0.5 rounded">{{ form.password }}</span>
              </p>
            </div>
          </Transition>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row justify-end gap-4 pt-10">
        <button type="button" @click="$router.back()" class="btn-back-eco min-w-[140px] justify-center">Batalkan</button>
        <button type="submit" :disabled="loading" class="btn-refresh-eco min-w-[240px] justify-center !py-4 shadow-lg shadow-[#2d4a3e]/20">
          <span v-if="loading" class="flex items-center gap-2"><RefreshCw class="animate-spin w-5 h-5" /> MENGIRIM DATA...</span>
          <span v-else class="flex items-center gap-2 uppercase font-bold tracking-widest"><Save class="w-5 h-5" /> Komit Perubahan</span>
        </button>
      </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";
import { UserCog, Camera, Check, Info, Lock, Save, RefreshCw, AlertTriangle } from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();

const initialLoading = ref(true);
const loading = ref(false);
const isResetting = ref(false);
const departemens = ref([]);
const errors = ref({});
const apiError = ref(null);
const newFotoFile = ref(null);

const form = ref({
    nama: "", email: "", nip: "", departemen_id: "", role: "karyawan", nomor_hp: "",
    tempat_lahir: "", tanggal_lahir: "", jenis_kelamin: "", alamat: "",
    is_active: 1, password: null, foto_profil: null,
});

const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const currentFotoUrl = computed(() => {
    if (newFotoFile.value) return URL.createObjectURL(newFotoFile.value);
    return form.value.foto_profil ? `${BACKEND_URL}/storage/${form.value.foto_profil}` : '/default-user-avatar.png';
});

const handleFileUpload = (e) => {
    const file = e.target.files[0];
    if (file) newFotoFile.value = file;
};

const handleImageError = (e) => { e.target.src = '/default-user-avatar.png'; };

// RESET PASSWORD LOGIC
const resetPasswordToDefault = () => {
    if (!form.value.tanggal_lahir) {
        apiError.value = "Isi Tanggal Lahir terlebih dahulu untuk reset password.";
        return;
    }
    isResetting.value = true;
    const defaultPassword = form.value.tanggal_lahir.replace(/-/g, "");
    form.value.password = defaultPassword;
    setTimeout(() => { isResetting.value = false; }, 600);
};

const fetchData = async () => {
    try {
        const [karyawanRes, deptRes] = await Promise.all([
            api.get(`/admin/karyawan/${route.params.id}`),
            api.get("/admin/departemens")
        ]);

        const d = karyawanRes.data.data;
        
        // PENTING: Paksa ambil string YYYY-MM-DD murni dari database
        const cleanDateFromDB = d.tanggal_lahir ? d.tanggal_lahir.substring(0, 10) : "";

        form.value = {
            nama: d.name, 
            email: d.email,
            nip: d.nip,
            departemen_id: d.departemen_id,
            role: d.role,
            nomor_hp: d.nomor_hp || "",
            tempat_lahir: d.tempat_lahir || "",
            tanggal_lahir: cleanDateFromDB, // Sinkronisasi aman
            jenis_kelamin: d.jenis_kelamin || "L",
            alamat: d.alamat || "",
            is_active: d.is_active ? 1 : 0,
            foto_profil: d.foto_profil,
            password: null
        };
        departemens.value = deptRes.data.data;
    } catch (err) {
        apiError.value = "Data gagal ditarik. Cek koneksi backend.";
    } finally {
        setTimeout(() => { initialLoading.value = false; }, 400);
    }
};

const updateKaryawan = async () => {
    loading.value = true;
    errors.value = {};
    apiError.value = null;
    
    const formData = new FormData();
    formData.append('_method', 'POST'); 
    
    formData.append('nama', form.value.nama); 
    formData.append('email', form.value.email);
    formData.append('nip', form.value.nip);
    formData.append('departemen_id', form.value.departemen_id);
    formData.append('role', form.value.role);
    formData.append('is_active', form.value.is_active);
    
    // Pastikan No HP & Alamat terkirim dengan string murni
    formData.append('nomor_hp', form.value.nomor_hp || '');
    formData.append('alamat', form.value.alamat || '');
    
    formData.append('tempat_lahir', form.value.tempat_lahir || '');
    formData.append('tanggal_lahir', form.value.tanggal_lahir); // Kirim string murni YYYY-MM-DD
    
    if (form.value.jenis_kelamin) formData.append('jenis_kelamin', form.value.jenis_kelamin);
    if (form.value.password) formData.append('password', form.value.password);
    if (newFotoFile.value) formData.append('foto', newFotoFile.value);

    try {
        await api.post(`/admin/karyawan/${route.params.id}`, formData);
        router.push(`/admin/karyawan/${route.params.id}`);
    } catch (err) {
        if (err.response?.status === 422) {
            errors.value = err.response.data.errors;
        } else {
            apiError.value = "Gagal memperbarui data pusat.";
        }
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);
</script>

<style scoped lang="postcss">
.card-eco { @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all; }
.kpi-label { @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1; }
.input-field-eco { @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3.5 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white; }
.btn-refresh-eco { @apply inline-flex items-center px-6 py-3.5 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg hover:bg-[#385b4d] active:scale-95 disabled:bg-slate-400 transition-all; }
.btn-back-eco { @apply inline-flex items-center px-6 py-3.5 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-all; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>