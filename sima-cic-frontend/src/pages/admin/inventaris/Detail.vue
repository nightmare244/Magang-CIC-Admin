<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <Archive class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 uppercase">
            Detail <span class="font-light text-slate-400 italic lowercase tracking-normal">Aset Strategis</span>
          </h1>
          <p class="text-[10px] md:text-xs font-semibold text-slate-400 uppercase tracking-[0.2em] mt-1 italic">
            Audit Trail & Logistik Node
          </p>
        </div>
      </div>

      <button @click="router.push({ name: 'admin.inventaris.index' })" class="btn-back-eco">
        <ChevronLeft class="w-4 h-4 mr-2" />
        Kembali ke Gudang
      </button>
    </header>

    <div v-if="isLoading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="kpi-label animate-pulse italic text-sm">Mensinkronkan Data Logistik...</p>
    </div>

    <div v-else-if="item.id" class="space-y-8 animate-fade-in">
        
        <div class="card-eco overflow-hidden shadow-2xl border-none">
            <div class="grid grid-cols-1 lg:grid-cols-3">
                <div class="p-8 bg-slate-50 dark:bg-[#1a1d19] flex items-center justify-center border-r border-gray-100 dark:border-gray-800 relative">
                    <img 
                      :src="getPhotoUrl(item.foto_barang)" 
                      class="w-full h-80 object-cover rounded-[2rem] shadow-2xl border-4 border-white dark:border-gray-800 transition-transform duration-500 hover:scale-105" 
                      @error="handleImageError"
                    />
                    <div class="absolute top-10 right-10">
                        <span :class="badgeClass(item.status_ketersediaan)" class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest shadow-lg border border-white/20 backdrop-blur-md">
                            {{ item.status_ketersediaan.replace('_', ' ') }}
                        </span>
                    </div>
                </div>

                <div class="lg:col-span-2 p-10 flex flex-col justify-center space-y-10 bg-white dark:bg-[#121512]">
                    <div>
                        <h2 class="text-3xl md:text-4xl font-black text-slate-800 dark:text-white tracking-tight uppercase">
                            {{ item.nama_barang }}
                        </h2>
                        <div class="mt-4 flex flex-wrap items-center gap-4">
                            <span class="px-4 py-1.5 bg-[#2d4a3e] text-white rounded-xl font-mono text-[10px] font-bold tracking-widest uppercase shadow-lg shadow-[#2d4a3e]/20">
                                ID: #{{ item.kode_barang }}
                            </span>
                            <span class="kpi-label !text-slate-400">Kapasitas Total: {{ item.quantity ?? '0' }} Unit</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 pt-8 border-t border-gray-100 dark:border-gray-800">
                        <div class="space-y-1">
                            <p class="kpi-label !text-slate-500">Nilai Satuan</p>
                            <p class="text-2xl font-black text-slate-700 dark:text-slate-300 tracking-tight">
                                <span class="text-xs mr-1 opacity-50 uppercase">rp</span>{{ formatCurrency(item.harga_satuan) }}
                            </p>
                        </div>
                        <div class="space-y-1">
                            <p class="kpi-label !text-emerald-600 dark:!text-emerald-400">Valuasi Inventaris</p>
                            <p class="text-3xl font-black text-[#2d4a3e] dark:text-emerald-500 tracking-tight leading-none">
                                <span class="text-xs mr-1 opacity-50 uppercase">rp</span>{{ formatCurrency(item.harga_satuan * item.quantity) }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div v-if="item.status_ketersediaan === 'dipinjam' && item.peminjam_aktif" class="card-eco bg-[#2d4a3e]/5 border-emerald-500/20 p-8 border-2 border-dashed">
            <div class="flex flex-col md:flex-row items-center gap-8">
                <div class="relative">
                    <img 
                      v-if="item.peminjam_aktif.foto_profil" 
                      :src="getPhotoUrl(item.peminjam_aktif.foto_profil)" 
                      class="w-20 h-20 rounded-2xl object-cover border-4 border-white dark:border-slate-800 shadow-xl"
                    />
                    <div v-else class="w-20 h-20 rounded-2xl bg-white dark:bg-slate-800 flex items-center justify-center font-black text-2xl text-[#2d4a3e] shadow-xl border-2 border-emerald-100 uppercase">
                        {{ item.peminjam_aktif.name.charAt(0) }}
                    </div>
                    <div class="absolute -top-2 -right-2 bg-rose-500 text-white px-2 py-1 rounded-lg text-[8px] font-black uppercase animate-pulse shadow-lg">In Use</div>
                </div>
                <div class="flex-grow text-center md:text-left">
                    <h3 class="kpi-label !text-rose-500 mb-1">Aset Sedang Dalam Otorisasi Personel</h3>
                    <p class="text-2xl font-black text-slate-800 dark:text-white uppercase tracking-tight">{{ item.peminjam_aktif.name }}</p>
                    <p class="text-[11px] text-slate-400 font-bold uppercase mt-1 tracking-tighter">
                      NIP: {{ item.peminjam_aktif.nip }} • Sejak: {{ formatDate(item.peminjam_aktif.tanggal_mulai) }}
                    </p>
                </div>
                <button @click="returnItem" :disabled="isReturning" class="btn-refresh-eco !bg-rose-600 hover:!bg-rose-700 !shadow-rose-600/20 min-w-[240px] !py-4">
                    <RefreshCw v-if="isReturning" class="animate-spin h-4 w-4 mr-2" />
                    <RotateCcw v-else class="w-4 h-4 mr-2" />
                    {{ isReturning ? 'MEMPROSES...' : 'CONFIRM RETURN' }}
                </button>
            </div>
        </div>

        <div class="card-eco overflow-hidden border-none shadow-2xl">
            <div class="p-8 border-b border-gray-100 dark:border-gray-800 bg-slate-50/50 dark:bg-[#1a1d19] flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h3 class="kpi-label !text-slate-800 dark:!text-white !mb-0 text-sm">Riwayat Penggunaan Personel</h3>
                    <p class="text-[10px] text-slate-400 font-medium mt-1">Audit trail seluruh penugasan dan pemakaian aset node</p>
                </div>
                <span class="px-4 py-1.5 bg-[#2d4a3e]/10 text-[#2d4a3e] dark:text-emerald-400 text-[10px] font-black rounded-xl border border-[#2d4a3e]/10 uppercase tracking-widest">Total Rekaman: {{ item.peminjamans?.length || 0 }}</span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead class="bg-slate-50 dark:bg-slate-800/50">
                        <tr>
                            <th class="px-8 py-5 kpi-label !mb-0">Informasi Personel</th>
                            <th class="px-8 py-5 kpi-label !mb-0 text-center">Detail Pinjaman</th>
                            <th class="px-8 py-5 kpi-label !mb-0">Periode Operasional</th>
                            <th class="px-8 py-5 kpi-label !mb-0 text-center">Status Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                        <tr v-for="log in item.peminjamans" :key="log.id" class="group hover:bg-emerald-50/30 dark:hover:bg-emerald-900/5 transition-colors">
                            <td class="px-8 py-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center font-black text-[#2d4a3e] dark:text-emerald-500 text-lg border-2 border-white dark:border-slate-700 group-hover:border-[#2d4a3e] transition-colors uppercase">
                                        {{ log.user?.name?.charAt(0) || 'U' }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 dark:text-white leading-tight uppercase">{{ log.user?.name || 'Unknown Unit' }}</p>
                                        <p class="text-[10px] text-slate-400 font-black mt-0.5 tracking-tighter uppercase">ID: {{ log.user?.nip || '-' }}</p>
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-6">
                                <div class="flex flex-col items-center gap-2">
                                    <span class="px-2.5 py-1 bg-[#2d4a3e] text-white text-[9px] font-black rounded-lg tracking-widest uppercase">{{ log.quantity }} UNIT</span>
                                    <p class="text-[10px] text-slate-400 italic text-center leading-tight max-w-[200px] line-clamp-2">
                                        "{{ log.keterangan || 'No additional Intel' }}"
                                    </p>
                                </div>
                            </td>

                            <td class="px-8 py-6 text-[10px] font-bold uppercase tracking-tighter">
                                <div class="flex flex-col gap-1.5 text-slate-500">
                                    <div class="flex items-center">
                                        <span class="w-16 opacity-40">Deployment:</span> {{ formatDate(log.tanggal_mulai) }}
                                    </div>
                                    <div class="flex items-center" :class="log.status === 'selesai' ? 'text-emerald-600' : 'text-slate-300 italic'">
                                        <span class="w-16 opacity-40">Returned:</span> {{ log.status === 'selesai' ? formatDate(log.updated_at) : 'Active Duty' }}
                                    </div>
                                </div>
                            </td>

                            <td class="px-8 py-6 text-center">
                                <span :class="statusBadgeClass(log.status)" class="text-[9px] font-black uppercase px-3 py-1.5 rounded-lg border shadow-sm inline-block tracking-widest">
                                    {{ log.status }}
                                </span>
                            </td>
                        </tr>
                        <tr v-if="!item.peminjamans?.length">
                            <td colspan="4" class="px-8 py-20 text-center">
                              <div class="flex flex-col items-center opacity-20">
                                <Database class="w-16 h-16 mb-4" />
                                <p class="kpi-label">Arsip Logistik Belum Tersedia</p>
                              </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";
import { 
  Archive, ChevronLeft, RefreshCw, RotateCcw, 
  Users, Database, Trash2, Edit3 
} from 'lucide-vue-next';

const route = useRoute();
const router = useRouter();
const isLoading = ref(true);
const item = ref({});
const isReturning = ref(false);

const BACKEND_URL = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const getPhotoUrl = (path) => {
    if (!path) return null;
    const cleanPath = path.replace(/^\/storage\//i, '');
    return `${BACKEND_URL}/storage/${cleanPath}`;
};

const handleImageError = (e) => {
    e.target.src = '/default-inventaris.png';
};

const formatDate = (dateStr) => {
    if (!dateStr) return '---';
    return new Date(dateStr).toLocaleDateString('id-ID', { 
        day: '2-digit', 
        month: 'short', 
        year: 'numeric'
    });
};

const formatCurrency = (val) => {
    const num = Number(val);
    return isNaN(num) ? "0" : num.toLocaleString("id-ID");
};

const load = async () => {
    isLoading.value = true;
    try {
        const res = await api.get(`/admin/inventaris/${route.params.id}`);
        item.value = res.data.data || {};
    } catch (err) {
        console.error("Error audit:", err);
    } finally { 
        isLoading.value = false; 
    }
};

const returnItem = async () => {
    const peminjamanId = item.value.peminjam_aktif?.id_peminjaman;
    if (!peminjamanId || !confirm("Konfirmasi pengembalian aset ini ke gudang pusat?")) return;
    
    isReturning.value = true;
    try {
        await api.put(`/admin/persetujuan-peminjaman/${peminjamanId}/return`);
        await load();
    } catch (err) { 
        alert("Otoritas pengembalian gagal."); 
    } finally { 
        isReturning.value = false; 
    }
};

const badgeClass = (status) => {
    const s = status?.toLowerCase();
    if (s === 'tersedia') return 'bg-emerald-500 text-white';
    if (s === 'dipinjam') return 'bg-amber-500 text-white';
    return 'bg-rose-600 text-white';
};

const statusBadgeClass = (status) => {
    const s = status?.toLowerCase();
    if (s === 'selesai') return 'text-emerald-600 bg-emerald-50 border-emerald-100';
    if (s === 'disetujui' || s === 'dipinjam') return 'text-amber-600 bg-amber-50 border-amber-100';
    return 'text-sky-600 bg-sky-50 border-sky-100';
};

onMounted(load);
</script>

<style scoped lang="postcss">
.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 
         dark:hover:bg-slate-800 transition-all active:scale-95;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all disabled:opacity-50 cursor-pointer;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>