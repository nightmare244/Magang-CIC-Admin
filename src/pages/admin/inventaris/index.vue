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
            Data Inventaris
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Manajemen aset strategis dan pemantauan stok real-time.
          </p>
        </div>
      </div>

      <router-link
        :to="{ name: 'admin.inventaris.create' }"
        class="btn-refresh-eco group"
      >
        <Plus class="w-4 h-4 mr-2 transition-transform group-hover:rotate-90" />
        Tambah Aset Baru
      </router-link>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="card in summaryStats" :key="card.label" class="kpi-card-new group">
        <div class="relative z-10">
          <p class="kpi-label" :class="card.textColor">{{ card.label }}</p>
          <h3 class="kpi-value">{{ card.value }}</h3>
          <p class="kpi-sub">{{ card.sub }}</p>
        </div>
        <div class="kpi-icon-wrapper" :class="card.bgColor">
          <component :is="card.icon" class="w-8 h-8 opacity-80" :class="card.textColor" />
        </div>
      </div>
    </div>

    <div class="flex flex-col md:flex-row gap-6 items-center">
      <div class="relative w-full md:w-96 group">
        <div class="absolute inset-y-0 left-4 flex items-center pointer-events-none">
          <Search class="w-5 h-5 text-slate-400 group-focus-within:text-[#2d4a3e] transition-colors" />
        </div>
        <input
          v-model="search"
          @input="onSearch"
          type="text"
          placeholder="Cari nama atau kode aset..."
          class="input-field-eco !pl-12"
        />
      </div>

      <div v-if="apiError" class="flex items-center gap-3 px-4 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl">
        <AlertTriangle class="w-4 h-4 text-rose-600" />
        <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ apiError }}</span>
      </div>
    </div>

    <div class="card-eco overflow-hidden border-none shadow-xl">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-slate-50/50 dark:bg-[#1a1d19] border-b border-gray-100 dark:border-gray-800">
            <tr>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center w-24">Visual</th>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Identitas Aset</th>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-right">Harga</th>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Stok</th>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-8 py-5 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center w-40">Otoritas</th>
            </tr>
          </thead>

          <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
            <tr v-if="loading">
              <td colspan="6" class="px-8 py-32 text-center">
                <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
                <p class="text-xs italic text-slate-400 animate-pulse">Menghubungkan ke basis data gudang...</p>
              </td>
            </tr>
            
            <tr v-for="item in items" :key="item.id" class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
              <td class="px-8 py-5 text-center">
                <img :src="getPhotoUrl(item.foto_barang)" class="w-12 h-12 rounded-xl object-cover border-2 border-white dark:border-gray-800 shadow-sm mx-auto group-hover:scale-110 transition-transform" @error="handleImageError" />
              </td>

              <td class="px-8 py-5">
                <div class="font-semibold text-slate-800 dark:text-slate-100">{{ item.nama_barang }}</div>
                <div class="text-[10px] font-mono text-[#2d4a3e] dark:text-emerald-500 mt-1 uppercase tracking-tighter">Code: {{ item.kode_barang }}</div>
              </td>
              
              <td class="px-8 py-5 text-right font-medium text-slate-600 dark:text-slate-400">
                <span class="text-[10px] mr-1 opacity-50 uppercase">rp</span>{{ formatRupiah(item.harga_satuan) }}
              </td>
              
              <td class="px-8 py-5 text-center">
                <span class="text-sm font-bold text-slate-800 dark:text-slate-200">{{ item.quantity }}</span>
                <span class="text-[10px] ml-1 text-slate-400 uppercase">Unit</span>
              </td>

              <td class="px-8 py-5 text-center">
                <span :class="badgeClass(item.status_ketersediaan)" class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-tighter">
                  {{ item.status_ketersediaan ? item.status_ketersediaan.replace('_', ' ') : '-' }}
                </span>
              </td>

              <td class="px-8 py-5">
                <div class="flex justify-center gap-2">
                  <button @click="router.push({ name: 'admin.inventaris.detail', params: { id: item.id } })" class="btn-action bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white" title="Detail">
                    <FileText class="w-3.5 h-3.5" />
                  </button>
                  <button @click="router.push({ name: 'admin.inventaris.edit', params: { id: item.id } })" class="btn-action bg-amber-50 text-amber-600 hover:bg-amber-600 hover:text-white" title="Edit">
                    <Edit3 class="w-3.5 h-3.5" />
                  </button>
                  <button @click="openConfirmDelete(item)" class="btn-action bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white" title="Hapus">
                    <Trash2 class="w-3.5 h-3.5" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <Transition name="modal-fade">
      <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeDeleteModal"></div>
        
        <div class="bg-white dark:bg-[#121512] w-full max-w-sm rounded-[2.5rem] p-8 relative z-10 shadow-2xl border border-gray-100 dark:border-gray-800 text-center animate-modal-pop">
          <div class="w-20 h-20 bg-rose-50 dark:bg-rose-900/20 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-rose-600">
            <Trash2 class="w-10 h-10" />
          </div>
          
          <h3 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Hapus Aset?</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mb-8 leading-relaxed font-medium">
            Anda akan menghapus <span class="font-bold text-rose-600 italic">"{{ itemToDelete?.nama_barang }}"</span> secara permanen dari sistem gudang. Tindakan ini tidak dapat dibatalkan.
          </p>
          
          <div class="flex flex-col gap-3">
            <button 
              @click="handleDelete" 
              :disabled="isDeleting"
              class="w-full py-4 bg-rose-600 text-white rounded-2xl font-bold uppercase text-[10px] tracking-[0.2em] shadow-lg shadow-rose-600/20 hover:bg-rose-700 active:scale-95 transition-all flex items-center justify-center"
            >
              <RotateCcw v-if="isDeleting" class="w-4 h-4 mr-2 animate-spin" />
              {{ isDeleting ? 'Menghapus...' : 'Ya, Hapus Permanen' }}
            </button>
            
            <button 
              @click="closeDeleteModal" 
              :disabled="isDeleting"
              class="w-full py-4 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 rounded-2xl font-bold uppercase text-[10px] tracking-[0.2em] hover:bg-slate-200 dark:hover:bg-white/10 active:scale-95 transition-all"
            >
              Batalkan
            </button>
          </div>

          <div class="mt-8 pt-6 border-t border-gray-50 dark:border-gray-800">
             <p class="text-[9px] font-black text-slate-300 dark:text-slate-600 uppercase tracking-[0.3em]">Otoritas Inventaris CIC</p>
          </div>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  Package, Plus, Search, AlertTriangle, 
  FileText, Edit3, Trash2, 
  Archive, RotateCcw, CheckCircle2, AlertOctagon 
} from 'lucide-vue-next';

const router = useRouter();
const items = ref([]);
const search = ref("");
const apiError = ref(null);
const loading = ref(true);

// State Modal Hapus
const showDeleteModal = ref(false);
const itemToDelete = ref(null);
const isDeleting = ref(false);

const BACKEND_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

// Computed Stats
const totalStok = computed(() => items.value.reduce((acc, curr) => acc + (parseInt(curr.quantity) || 0), 0));
const totalDipinjam = computed(() => items.value.reduce((acc, curr) => {
    const borrowedQty = curr.peminjamans 
        ? curr.peminjamans.filter(p => p.status === 'disetujui' || p.status === 'dipinjam').reduce((a, c) => a + c.quantity, 0)
        : 0;
    return acc + borrowedQty;
}, 0));

const stokTersedia = computed(() => totalStok.value - totalDipinjam.value);
const countBarangRusak = computed(() => items.value.filter(i => i.status_ketersediaan === 'tidak_tersedia').length);

const summaryStats = computed(() => [
  { label: 'Total Stok', value: totalStok.value, icon: Archive, textColor: 'text-blue-600', bgColor: 'bg-blue-50 dark:bg-blue-900/20', sub: 'Unit Terdaftar' },
  { label: 'Sedang Dipinjam', value: totalDipinjam.value, icon: RotateCcw, textColor: 'text-amber-600', bgColor: 'bg-amber-50 dark:bg-amber-900/20', sub: 'Aset Aktif' },
  { label: 'Siap Digunakan', value: stokTersedia.value, icon: CheckCircle2, textColor: 'text-emerald-600', bgColor: 'bg-emerald-50 dark:bg-emerald-900/20', sub: 'Kondisi Ready' },
  { label: 'Kondisi Rusak', value: countBarangRusak.value, icon: AlertOctagon, textColor: 'text-rose-600', bgColor: 'bg-rose-50 dark:bg-rose-900/20', sub: 'Perlu Perbaikan' },
]);

// Fetch & Logic
async function fetchData() {
  loading.value = true;
  apiError.value = null;
  try {
    const res = await api.get("/admin/inventaris", { params: { search: search.value } });
    items.value = res.data.data || []; 
  } catch (error) {
    apiError.value = "Koneksi ke server terputus.";
  } finally {
    loading.value = false;
  }
}

// Logic Modal Hapus
function openConfirmDelete(item) {
  itemToDelete.value = item;
  showDeleteModal.value = true;
}

function closeDeleteModal() {
  showDeleteModal.value = false;
  itemToDelete.value = null;
}

async function handleDelete() {
  if (!itemToDelete.value) return;
  isDeleting.value = true;
  try {
    await api.delete(`/admin/inventaris/${itemToDelete.value.id}`);
    fetchData(); // Refresh table
    closeDeleteModal();
  } catch (error) {
    apiError.value = "Gagal menghapus data aset.";
  } finally {
    isDeleting.value = false;
  }
}

// Helpers
function getPhotoUrl(path) {
    if (!path) return '/default-inventaris.png';
    const cleanPath = path.replace(/^\/storage\//i, '');
    return `${BACKEND_URL}/storage/${cleanPath}`;
}

function handleImageError(e) { e.target.src = '/default-inventaris.png'; }

function formatRupiah(num) {
  const number = parseFloat(num); 
  return isNaN(number) ? '0' : number.toLocaleString("id-ID");
}

let debounceTimer = null;
function onSearch() {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchData(), 400);
}

function badgeClass(status) {
    const s = status ? status.toLowerCase() : '';
    if (s === 'tersedia') return "bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400";
    if (s === 'dipinjam') return "bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400";
    return "bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400";
}

onMounted(fetchData);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.kpi-card-new {
  @apply bg-white dark:bg-[#121512] p-6 rounded-[1.8rem] border border-gray-100 
         dark:border-gray-800 shadow-sm relative overflow-hidden transition-all 
         duration-500 hover:shadow-xl hover:-translate-y-1 flex items-center justify-between;
}

.kpi-label {
  @apply text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60;
}

.kpi-value {
  @apply text-3xl font-bold text-slate-800 dark:text-white;
}

.kpi-sub {
  @apply text-[10px] text-slate-400 mt-1 font-medium;
}

.kpi-icon-wrapper {
  @apply w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110;
}

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm;
}

.input-field-eco {
  @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all;
}

.btn-action {
  @apply p-2.5 rounded-xl transition-all active:scale-90 flex items-center justify-center;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}

/* --- MODAL TRANSITIONS --- */
.modal-fade-enter-active, .modal-fade-leave-active {
  @apply transition-opacity duration-300 ease-out;
}

.modal-fade-enter-from, .modal-fade-leave-to {
  @apply opacity-0;
}

.animate-modal-pop {
  animation: modalPop 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes modalPop {
  from { opacity: 0; transform: scale(0.9) translateY(10px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
</style>