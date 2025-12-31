<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Package class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Edit Inventaris
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Perbarui informasi aset dan manajemen logistik gudang pusat.
          </p>
        </div>
      </div>

      <div v-if="apiError" class="flex items-center gap-3 px-4 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl animate-bounce">
        <AlertTriangle class="w-4 h-4 text-rose-600" />
        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ apiError }}</span>
      </div>
    </header>

    <div v-if="initialLoading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse">Sinkronisasi basis data aset...</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="space-y-8 animate-fade-in max-w-5xl mx-auto">

      <div class="card-eco p-8 bg-white/50 backdrop-blur-sm relative overflow-hidden">
        <h2 class="kpi-label mb-6 text-emerald-600 dark:text-emerald-400 flex items-center gap-2">
          <ImageIcon class="w-4 h-4" /> Media & Visual Aset
        </h2>
        <div class="flex flex-col md:flex-row items-center space-y-6 md:space-y-0 md:space-x-10 relative z-10">
            <div class="relative group">
                <img
                    :src="previewImage || getPhotoUrl(form.foto_barang) || '/default-inventaris.png'"
                    alt="Preview"
                    class="w-44 h-44 object-cover rounded-[2rem] border-4 border-white dark:border-gray-800 shadow-2xl transition-transform group-hover:scale-105"
                    @error="handleImageError"
                />
                <div v-if="newImageFile" class="absolute -top-2 -right-2 bg-emerald-500 text-white p-2 rounded-full shadow-lg border-2 border-white">
                    <Check class="w-4 h-4" />
                </div>
            </div>

            <div class="flex-grow space-y-4">
                <label class="kpi-label !text-slate-500">Unggah foto profil barang</label>
                <input
                    type="file"
                    accept="image/*"
                    @change="handleImageUpload"
                    class="input-file-eco w-full"
                />
                <p class="text-[10px] text-slate-400 italic">Format yang didukung: JPG, PNG. Maksimal 10MB.</p>
                <p v-if="errors.foto" class="text-[10px] font-bold text-rose-500 uppercase mt-2 italic">{{ errors.foto[0] }}</p> 
            </div>
        </div>
        <Package class="absolute -right-10 -bottom-10 w-48 h-48 opacity-[0.03] dark:opacity-[0.05]" />
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <div class="card-eco p-8 space-y-6">
            <h3 class="kpi-label border-b border-slate-100 dark:border-slate-800 pb-3 mb-2 flex items-center gap-2">
              <Info class="w-4 h-4" /> Informasi dasar
            </h3>
            
            <div class="space-y-2">
                <label class="kpi-label !text-slate-500">Nama barang <span class="text-rose-500">*</span></label>
                <input v-model="form.nama_barang" type="text" class="input-field-eco font-semibold" placeholder="Masukkan nama aset" required />
                <p v-if="errors.nama_barang" class="text-[10px] font-bold text-rose-500 uppercase mt-1 italic">{{ errors.nama_barang[0] }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Kode aset</label>
                    <input v-model="form.kode_barang" type="text" class="input-field-eco bg-slate-50 dark:bg-white/5 opacity-60 font-mono" readonly />
                </div>
                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Status ketersediaan</label>
                    <select v-model="form.status_ketersediaan" class="input-field-eco font-bold text-emerald-600 dark:text-emerald-400" required>
                        <option value="tersedia">Tersedia</option>
                        <option value="dipinjam">Dipinjam</option>
                        <option value="tidak_tersedia">Rusak / Hilang</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="card-eco p-8 space-y-6">
            <h3 class="kpi-label border-b border-slate-100 dark:border-slate-800 pb-3 mb-2 flex items-center gap-2">
              <DollarSign class="w-4 h-4" /> Catatan finansial
            </h3>
            
            <div class="grid grid-cols-2 gap-4">
                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Harga satuan (IDR)</label>
                    <input v-model="form.harga_satuan" type="number" class="input-field-eco font-bold" required />
                    <p v-if="errors.harga_satuan" class="text-[10px] font-bold text-rose-500 uppercase mt-1 italic">{{ errors.harga_satuan[0] }}</p>
                </div>
                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Jumlah stok (Unit)</label>
                    <input v-model="form.quantity" type="number" class="input-field-eco font-bold" required />
                    <p v-if="errors.quantity" class="text-[10px] font-bold text-rose-500 uppercase mt-1 italic">{{ errors.quantity[0] }}</p>
                </div>
            </div>

            <div class="mt-6 p-6 bg-[#2d4a3e]/5 dark:bg-emerald-500/5 rounded-3xl border-2 border-dashed border-[#2d4a3e]/20 dark:border-emerald-500/20">
                <label class="kpi-label !text-slate-400">Estimasi total valuasi aset</label>
                <div class="text-3xl font-bold text-[#2d4a3e] dark:text-emerald-400 tracking-tight leading-none mt-1">
                    <span class="text-sm mr-1 opacity-50 uppercase">Rp</span>{{ formatRupiah(totalNilaiBarang) }}
                </div>
            </div>
        </div>
      </div>

      <div class="card-eco p-8 space-y-4">
        <label class="kpi-label !text-slate-500 flex items-center gap-2">
          <FileText class="w-4 h-4" /> Deskripsi & spesifikasi teknis
        </label>
        <textarea v-model="form.deskripsi" class="input-field-eco min-h-[160px] resize-none py-4 text-sm font-medium" placeholder="Masukkan detail spesifikasi teknis barang..."></textarea>
      </div>

      <div class="pt-6 flex flex-col md:flex-row justify-end gap-4 border-t border-gray-100 dark:border-gray-800">
        <button type="button" @click="$router.back()" class="btn-back-eco min-w-[160px]">
          Batal
        </button>
        <button type="submit" :disabled="submitting" class="btn-refresh-eco min-w-[240px] !py-4">
          <RefreshCw v-if="submitting" class="animate-spin h-4 w-4 mr-2" />
          <Save v-else class="h-4 w-4 mr-2" />
          {{ submitting ? 'Memproses Perubahan...' : 'Simpan Perubahan Aset' }}
        </button>
      </div>

    </form>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";
import { 
  Package, ImageIcon, Check, Info, 
  DollarSign, FileText, AlertTriangle, 
  RefreshCw, Save 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();

const initialLoading = ref(true);
const submitting = ref(false);
const apiError = ref(null);
const errors = ref({});

const form = ref({
  nama_barang: "",
  kode_barang: "",
  deskripsi: "",
  harga_satuan: 0,
  quantity: 1,
  status_ketersediaan: "tersedia",
  foto_barang: null, 
});

const previewImage = ref(null);
const newImageFile = ref(null); 

const BACKEND_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const totalNilaiBarang = computed(() => (parseFloat(form.value.harga_satuan) || 0) * (parseInt(form.value.quantity) || 0));

function formatRupiah(num) {
  const number = parseFloat(num);
  return isNaN(number) ? '0' : number.toLocaleString("id-ID");
}

function getPhotoUrl(path) {
    if (!path) return null;
    return `${BACKEND_URL}/storage/${path.replace(/^\/storage\//i, '')}`;
}

function handleImageError(e) {
    e.target.src = '/default-inventaris.png';
}

function handleImageUpload(e) {
  const file = e.target.files[0];
  if (file) {
    if (file.size > 10 * 1024 * 1024) {
      errors.value.foto = ["Ukuran file melebihi batas 10MB."];
      return;
    }
    errors.value.foto = null;
    newImageFile.value = file;
    previewImage.value = URL.createObjectURL(file);
  }
}

onMounted(async () => {
  try {
    const res = await api.get(`/admin/inventaris/${route.params.id}`);
    const data = res.data.data;
    Object.assign(form.value, data);
    form.value.harga_satuan = parseFloat(data.harga_satuan);
    form.value.quantity = parseInt(data.quantity);
  } catch (err) {
    apiError.value = "Data tidak ditemukan atau akses server gagal.";
  } finally {
    initialLoading.value = false;
  }
});

async function submitForm() {
  submitting.value = true;
  errors.value = {};

  const fd = new FormData();
  fd.append("nama_barang", form.value.nama_barang);
  fd.append("deskripsi", form.value.deskripsi || "");
  fd.append("harga_satuan", form.value.harga_satuan);
  fd.append("quantity", form.value.quantity);
  fd.append("status_ketersediaan", form.value.status_ketersediaan);
  if (newImageFile.value) fd.append("foto", newImageFile.value);
  fd.append("_method", "PUT");

  try {
    await api.post(`/admin/inventaris/${route.params.id}`, fd, {
      headers: { "Content-Type": "multipart/form-data" },
    });
    router.push({ name: "admin.inventaris.index" });
  } catch (err) {
    if (err.response?.status === 422) {
        errors.value = err.response.data.errors;
    } else {
        apiError.value = "Terjadi kesalahan pada sistem server.";
    }
  } finally {
    submitting.value = false;
  }
}
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.input-field-eco {
  @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl px-5 py-3.5 
         text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white font-poppins;
}

.input-file-eco {
  @apply block text-xs text-slate-500 file:mr-6 file:py-3 file:px-8 file:rounded-xl file:border-0 
         file:text-[10px] file:font-bold file:uppercase file:bg-[#2d4a3e] file:text-white 
         hover:file:bg-[#385b4d] transition-all cursor-pointer;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all disabled:opacity-50;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 
         dark:hover:bg-slate-800 transition-all active:scale-95;
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>