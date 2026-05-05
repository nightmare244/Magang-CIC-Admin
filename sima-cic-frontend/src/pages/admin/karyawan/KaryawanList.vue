<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Users class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500">
            Daftar Karyawan
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
            Manajemen database personel dan akses operasional unit.
          </p>
        </div>
      </div>

      <div class="flex flex-wrap gap-3 w-full md:w-auto">
        <router-link to="/admin/karyawan/create" class="btn-refresh-eco group">
          <Plus class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" />
          Tambah Personel
        </router-link>
        
        <button @click="isImportModalOpen = true" class="btn-back-eco !text-blue-600 dark:!text-blue-400">
          <Upload class="w-4 h-4 mr-2" /> Impor Excel
        </button>

        <button @click="isExportModalOpen = true" class="btn-back-eco !text-emerald-600 dark:!text-emerald-500">
          <Download class="w-4 h-4 mr-2" /> Ekspor Data
        </button>
      </div>
    </header>

    <div class="card-eco p-6 bg-white/50 backdrop-blur-sm border-none shadow-sm font-poppins">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
        <div class="md:col-span-2 space-y-1">
          <label class="kpi-label ml-1">Pencarian Universal</label>
          <div class="relative">
            <input v-model="search" type="text" placeholder="Cari nama, email, atau NIP..." class="input-field-eco !pl-12" />
            <Search class="w-5 h-5 absolute left-4 top-1/2 -translate-y-1/2 text-slate-300" />
          </div>
        </div>

        <div class="space-y-1">
          <label class="kpi-label ml-1">Unit Departemen</label>
          <select v-model="filterDept" class="input-field-eco font-bold uppercase tracking-tighter">
            <option value="">Semua Unit</option>
            <option v-for="dept in departemens" :key="dept.id" :value="dept.id">
              {{ dept.nama_departemen }}
            </option>
          </select>
        </div>

        <div class="space-y-1">
          <label class="kpi-label ml-1">Status Kerja</label>
          <select v-model="filterStatus" class="input-field-eco font-bold uppercase tracking-tighter">
            <option value="">Semua Status</option>
            <option value="Aktif">Aktif</option>
            <option value="Permanent">Permanent</option>
            <option value="Kontrak">Kontrak</option>
            <option value="Harian">Harian</option>
            <option value="Non-Aktif">Non-Aktif</option>
          </select>
        </div>
      </div>
      
      <Transition name="slide-fade">
        <div v-if="error" class="mt-4 px-4 py-2 bg-rose-50 text-rose-600 rounded-xl text-[10px] font-bold border border-rose-100 uppercase tracking-widest flex items-center gap-2">
          <AlertTriangle class="w-3 h-3" /> {{ error }}
        </div>
      </Transition>
    </div>

    <div class="card-eco overflow-hidden border-none shadow-xl">
      <div v-if="loading" class="text-center py-40">
        <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-xs italic text-slate-400 animate-pulse font-poppins uppercase tracking-widest">Sinkronisasi Database...</p>
      </div>

      <div v-else-if="filteredKaryawan.length > 0" class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-50 dark:divide-gray-800">
          <thead class="bg-slate-50/50 dark:bg-[#1a1d19]">
            <tr>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-20">ID</th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left cursor-pointer hover:text-[#2d4a3e]" @click="sortBy('name')">
                  Nama Personel <SortIcon field="name" :sortKey="sortKey" :sortDir="sortDir" />
              </th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">NIP & Email</th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-left">Departemen</th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">Status Kerja</th>
              <th class="px-8 py-5 text-[10px] font-black text-slate-400 uppercase tracking-widest text-center w-40">Otoritas</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-gray-800 font-poppins text-sm">
            <tr v-for="(karyawan, index) in paginatedKaryawan" :key="karyawan.id" class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-all group">
              <td class="px-8 py-6 text-center font-mono text-xs text-slate-400">
                  {{ String(((currentPage - 1) * pageSize) + index + 1).padStart(2, '0') }}
              </td>
              <td class="px-8 py-6">
                <div class="flex items-center">
                    <div class="w-10 h-10 rounded-2xl bg-[#2d4a3e]/10 flex items-center justify-center text-[#2d4a3e] dark:text-emerald-500 font-bold mr-4 border border-[#2d4a3e]/10 group-hover:scale-110 shadow-sm uppercase overflow-hidden">
                        <img v-if="karyawan.foto_profil" :src="`${BACKEND_URL}/storage/${karyawan.foto_profil}`" class="w-full h-full object-cover" />
                        <span v-else>{{ karyawan.name.charAt(0) }}</span>
                    </div>
                    <span class="font-bold text-slate-800 dark:text-white">{{ karyawan.name }}</span>
                </div>
              </td>
              <td class="px-8 py-6">
                <div class="text-xs font-mono text-emerald-600 font-bold mb-0.5">{{ karyawan.nip }}</div>
                <div class="text-[10px] text-slate-400 font-medium lowercase italic">{{ karyawan.email }}</div>
              </td>
              <td class="px-8 py-6">
                  <span class="px-3 py-1 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 rounded-lg text-[10px] font-black uppercase tracking-tighter border dark:border-white/5">
                      {{ karyawan.departemen?.nama_departemen || 'N/A' }}
                  </span>
              </td>
              <td class="px-8 py-6 text-center">
                  <span :class="getStatusClass(karyawan.status_kerja)">
                      {{ karyawan.status_kerja || 'Aktif' }}
                  </span>
              </td>

              <td class="px-8 py-6 text-center">
                <div class="flex items-center justify-center gap-2">
                    <router-link :to="`/admin/karyawan/${karyawan.id}`" class="btn-action-eco text-sky-600 bg-sky-50 dark:bg-sky-900/10 hover:bg-sky-600 hover:text-white">
                        <Eye class="w-4 h-4" />
                    </router-link>
                    <router-link :to="`/admin/karyawan/${karyawan.id}/edit`" class="btn-action-eco text-amber-600 bg-amber-50 dark:bg-amber-900/10 hover:bg-amber-600 hover:text-white">
                        <Edit3 class="w-4 h-4" />
                    </router-link>
                    <button @click="openDeleteModal(karyawan.id)" class="btn-action-eco text-rose-600 bg-rose-50 dark:bg-rose-900/10 hover:bg-rose-600 hover:text-white">
                        <Trash2 class="w-4 h-4" />
                    </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <div v-if="!loading && filteredKaryawan.length === 0" class="py-40 text-center font-poppins">
          <div class="flex flex-col items-center opacity-20">
            <Users class="w-16 h-16 mb-4" />
            <p class="kpi-label uppercase tracking-[0.2em]">Data Personel Tidak Ditemukan</p>
          </div>
          <p class="text-slate-400 max-w-xs mx-auto text-[10px] mt-2 font-bold uppercase tracking-tight">Coba sesuaikan filter atau kata kunci pencarian Anda.</p>
      </div>
    </div>

    <div v-if="!loading && totalPages > 1" class="flex flex-col sm:flex-row justify-between items-center mt-10 gap-6 px-4 font-poppins">
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
            Menampilkan <span class="text-[#2d4a3e] dark:text-emerald-500 underline">{{ paginatedKaryawan.length }}</span> dari {{ filteredKaryawan.length }} Personel
        </p>
        <div class="flex items-center gap-3">
            <button @click="prevPage" :disabled="currentPage === 1" class="btn-pagination-eco">
                <ChevronLeft class="w-5 h-5" />
            </button>
            <div class="flex gap-2">
                <button v-for="p in totalPages" :key="p" @click="currentPage = p" :class="p === currentPage ? 'active-page' : 'inactive-page'" class="w-10 h-10 flex items-center justify-center rounded-xl text-xs font-bold transition-all">
                    {{ p }}
                </button>
            </div>
            <button @click="nextPage" :disabled="currentPage === totalPages" class="btn-pagination-eco">
                <ChevronRight class="w-5 h-5" />
            </button>
        </div>
    </div>

    <ModalExport :isOpen="isExportModalOpen" @close="isExportModalOpen = false" :filters="{ departemen_id: filterDept, status_kerja: filterStatus }" />
    <ModalImport :isOpen="isImportModalOpen" @close="isImportModalOpen = false" @success="fetchKaryawanList" />
    <DeleteModal :show="deleteId !== null" :id="deleteId" message="Hapus data personel secara permanen? Langkah ini tidak dapat dibatalkan." @close="deleteId = null" @confirm="deleteKaryawan" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed, watch } from "vue";
import api from "@/services/api";

import { 
  Plus, Upload, Download, Search, AlertTriangle, Users, 
  Eye, Edit3, Trash2, ChevronLeft, ChevronRight,
  ArrowUp, ArrowDown, ArrowUpDown
} from 'lucide-vue-next';

// Components & Modals
import DeleteModal from "@/pages/admin/departemen/components/DeleteModal.vue"; 
import ModalImport from "./components/ModalImport.vue";
import ModalExport from "./components/ModalExport.vue";

const search = ref("");
const filterDept = ref("");
const filterStatus = ref("");
const karyawanList = ref([]);
const departemens = ref([]);
const loading = ref(true);
const error = ref(null);
const deleteId = ref(null);
const isImportModalOpen = ref(false);
const isExportModalOpen = ref(false);

const currentPage = ref(1);
const pageSize = 10;
const sortKey = ref('name'); 
const sortDir = ref('asc');

const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';

const fetchKaryawanList = async () => {
  error.value = null;
  loading.value = true;
  try {
    const [kRes, dRes] = await Promise.all([
      api.get("/admin/karyawan"),
      api.get("/admin/departemens")
    ]);
    karyawanList.value = kRes.data.data || [];
    departemens.value = dRes.data.data || [];
  } catch (err) {
    error.value = "Gagal memproses sinkronisasi data personel.";
  } finally {
    setTimeout(() => { loading.value = false; }, 400);
  }
};

const getStatusClass = (status) => {
    const base = "px-3 py-1 rounded-lg text-[8px] font-black uppercase tracking-tighter border shadow-sm ";
    switch (status) {
        case 'Aktif':
        case 'Permanent':
            return base + "bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400 border-emerald-100 dark:border-emerald-500/20";
        case 'Kontrak':
        case 'Harian':
            return base + "bg-blue-50 text-blue-600 dark:bg-blue-500/10 dark:text-blue-400 border-blue-100 dark:border-blue-500/20";
        case 'Non-Aktif':
            return base + "bg-rose-50 text-rose-600 dark:bg-rose-500/10 dark:text-rose-400 border-rose-100 dark:border-rose-500/20";
        default:
            return base + "bg-slate-50 text-slate-600 border-slate-100";
    }
};

// --- FIX LOGIKA FILTER ---
const filteredKaryawan = computed(() => {
    let filtered = [...karyawanList.value];
    const term = search.value.toLowerCase();
    
    // 1. Filter Pencarian
    if (term) {
        filtered = filtered.filter(k =>
            (k.name && k.name.toLowerCase().includes(term)) ||
            (k.email && k.email.toLowerCase().includes(term)) ||
            (k.nip && k.nip.toLowerCase().includes(term))
        );
    }

    // 2. Filter Departemen (Menggunakan == untuk bypass perbedaan String/Number)
    if (filterDept.value !== "") {
        filtered = filtered.filter(k => k.departemen_id == filterDept.value);
    }

    // 3. Filter Status Kerja
    if (filterStatus.value !== "") {
        filtered = filtered.filter(k => k.status_kerja === filterStatus.value);
    }

    // 4. Sorting
    const key = sortKey.value;
    const dir = sortDir.value === 'asc' ? 1 : -1;
    filtered.sort((a, b) => {
        let aVal = a[key] || '';
        let bVal = b[key] || '';
        return (String(aVal).localeCompare(String(bVal))) * dir;
    });

    return filtered;
});

// --- WATCHER UNTUK RESET PAGINATION ---
watch([search, filterDept, filterStatus], () => { 
    currentPage.value = 1; 
});

const totalPages = computed(() => Math.ceil(filteredKaryawan.value.length / pageSize));
const paginatedKaryawan = computed(() => {
    const start = (currentPage.value - 1) * pageSize;
    return filteredKaryawan.value.slice(start, start + pageSize);
});

const sortBy = (key) => {
    if (sortKey.value === key) {
        sortDir.value = sortDir.value === 'asc' ? 'desc' : 'asc';
    } else {
        sortKey.value = key;
        sortDir.value = 'asc';
    }
    currentPage.value = 1;
};

const nextPage = () => { if (currentPage.value < totalPages.value) currentPage.value++; };
const prevPage = () => { if (currentPage.value > 1) currentPage.value--; };
const openDeleteModal = (id) => { deleteId.value = id; };

const deleteKaryawan = async () => {
    try {
        await api.delete(`/admin/karyawan/${deleteId.value}`);
        fetchKaryawanList();
    } catch (err) {
        error.value = "Sistem gagal melikuidasi data.";
    } finally {
        deleteId.value = null;
    }
};

const SortIcon = {
  props: ['field', 'sortKey', 'sortDir'],
  template: `
    <span class="ml-1 opacity-30">
      <template v-if="sortKey === field">
        <component :is="sortDir === 'asc' ? ArrowUp : ArrowDown" class="w-3 h-3 inline" />
      </template>
      <component v-else :is="ArrowUpDown" class="w-3 h-3 inline" />
    </span>
  `,
  setup() { return { ArrowUp, ArrowDown, ArrowUpDown }; }
};

onMounted(fetchKaryawanList);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }
.card-eco { @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all; }
.kpi-label { @apply text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1; }
.input-field-eco { @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-3.5 text-xs focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white; }
.btn-refresh-eco { @apply inline-flex items-center px-6 py-3.5 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all; }
.btn-back-eco { @apply inline-flex items-center px-6 py-3.5 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800 transition-all active:scale-95; }
.btn-action-eco { @apply p-2.5 rounded-xl transition-all duration-200 active:scale-90 flex items-center justify-center; }
.btn-pagination-eco { @apply p-3 bg-white dark:bg-[#121512] border border-gray-100 dark:border-gray-800 text-slate-400 rounded-xl hover:bg-[#2d4a3e] hover:text-white transition-all disabled:opacity-20; }
.active-page { @apply bg-[#2d4a3e] text-white shadow-lg shadow-[#2d4a3e]/20; }
.inactive-page { @apply bg-white dark:bg-white/5 text-slate-400 border border-gray-100 dark:border-gray-800 hover:bg-slate-50; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>