<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <ClipboardCheck class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Otorisasi Pengajuan Izin
          </h1>
          <p class="text-[10px] font-bold text-slate-400 mt-1 uppercase tracking-widest">
            Identifikasi Karyawan & Verifikasi Dokumen
          </p>
        </div>
      </div>

      <button @click="$router.back()" class="btn-back-eco">
        <ChevronLeft class="w-4 h-4 mr-1" /> Kembali ke Daftar
      </button>
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco">
      <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
      <p class="text-xs italic text-slate-400 animate-pulse">Sinkronisasi Data Karyawan...</p>
    </div>

    <div v-else-if="izin" class="grid grid-cols-1 lg:grid-cols-12 gap-8 animate-fade-in">
      
      <div class="lg:col-span-4 space-y-6">
        <div class="card-eco p-8 text-center bg-white dark:bg-[#121512] shadow-sm border border-slate-100 dark:border-white/5 relative overflow-hidden">
          <div class="relative inline-block mb-6">
            <div class="w-32 h-32 rounded-[2.5rem] bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center overflow-hidden border-2 border-emerald-100 dark:border-emerald-500/20 shadow-inner">
              <img v-if="izin.user?.foto_profil" :src="getImageUrl(izin.user.foto_profil)" class="w-full h-full object-cover" />
              <span v-else class="text-4xl font-bold text-emerald-600">{{ getInitials(izin.user?.name) }}</span>
            </div>
            <div :class="statusBadgeClass" class="absolute -bottom-2 -right-2 px-4 py-1.5 rounded-xl text-[9px] font-black uppercase shadow-lg border-4 border-white dark:border-[#121512]">
              {{ formatStatus(izin.status) }}
            </div>
          </div>
          
          <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">{{ izin.user?.name }}</h2>
          <p class="text-[10px] font-bold text-emerald-600 uppercase tracking-widest mt-1">{{ izin.user?.departemen?.nama_departemen || 'Staf Operasional' }}</p>

          <div class="mt-8 pt-8 border-t border-slate-50 dark:border-white/5 space-y-4 text-left font-poppins">
            <div class="flex flex-col">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">NIP / ID KARYAWAN</span>
              <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ izin.user?.nip || '---' }}</span>
            </div>
            <div class="flex flex-col">
              <span class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Email Resmi</span>
              <span class="text-sm font-bold text-slate-700 dark:text-slate-200">{{ izin.user?.email || '---' }}</span>
            </div>
          </div>
        </div>

        <div class="card-eco p-6 bg-white dark:bg-[#121512] shadow-sm border border-slate-100 dark:border-white/5">
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-4">Lampiran Bukti</p>
          <div v-if="izin.file_pendukung">
            <img v-if="isImage(izin.file_pendukung)" :src="getImageUrl(izin.file_pendukung)" class="w-full rounded-2xl mb-4 border border-slate-100 dark:border-white/5 shadow-sm" />
            <a :href="getImageUrl(izin.file_pendukung)" target="_blank" class="flex items-center justify-center gap-2 py-3 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 rounded-xl text-[10px] font-bold uppercase tracking-widest transition-colors hover:bg-emerald-100">
              <ExternalLink class="w-4 h-4" /> Lihat Berkas Penuh
            </a>
          </div>
          <div v-else class="py-10 text-center border-2 border-dashed border-slate-100 dark:border-white/5 rounded-2xl">
            <p class="text-[9px] font-bold text-slate-300 uppercase tracking-widest">Tidak Ada Lampiran</p>
          </div>
        </div>
      </div>

      <div class="lg:col-span-8 space-y-6">
        <div class="card-eco p-10 bg-white dark:bg-[#121512] shadow-sm border border-slate-100 dark:border-white/5">
          <div class="flex justify-between items-center border-b border-slate-50 dark:border-white/5 pb-6 mb-8">
            <span class="text-xs font-bold text-slate-400 uppercase tracking-[0.2em]">Kategori Pengajuan</span>
            <span class="px-4 py-1.5 bg-emerald-500 text-white text-[10px] font-black rounded-lg uppercase tracking-widest">
              {{ izin.tipe_izin }}
            </span>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <div class="space-y-1">
              <p class="text-[9px] font-bold text-slate-400 uppercase">Mulai</p>
              <p class="text-base font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 font-poppins">
                <Calendar class="w-4 h-4 text-emerald-500" /> {{ formatDate(izin.tanggal_mulai) }}
              </p>
            </div>
            <div class="space-y-1">
              <p class="text-[9px] font-bold text-slate-400 uppercase">Selesai</p>
              <p class="text-base font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 font-poppins">
                <CalendarCheck class="w-4 h-4 text-rose-500" /> {{ formatDate(izin.tanggal_selesai) }}
              </p>
            </div>
            <div class="space-y-1">
              <p class="text-[9px] font-bold text-slate-400 uppercase">Durasi Izin</p>
              <p class="text-base font-bold text-emerald-600 flex items-center gap-2">
                <Clock class="w-4 h-4" /> {{ calculateDuration(izin.tanggal_mulai, izin.tanggal_selesai) }} Hari
              </p>
            </div>
          </div>

          <div class="space-y-3">
            <p class="text-[9px] font-bold text-slate-400 uppercase tracking-widest">Keterangan Alasan</p>
            <div class="p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border-l-4 border-emerald-500 italic text-slate-600 dark:text-slate-300 text-sm leading-relaxed">
              "{{ izin.keterangan || 'Tidak ada catatan.' }}"
            </div>
          </div>
        </div>

        <div v-if="izin.status?.toLowerCase() === 'pending'" class="flex gap-4">
          <button @click="isApproveModalOpen = true" class="flex-1 py-5 bg-[#2d4a3e] text-white rounded-3xl text-[11px] font-bold uppercase tracking-[0.2em] shadow-xl shadow-[#2d4a3e]/20 hover:scale-[1.02] transition-all flex items-center justify-center gap-2 font-poppins">
            <CheckCircle2 class="w-5 h-5" /> SETUJUI PENGAJUAN
          </button>
          <button @click="isRejectModalOpen = true" class="flex-1 py-5 bg-white border-2 border-rose-500 text-rose-500 rounded-3xl text-[11px] font-bold uppercase tracking-[0.2em] hover:bg-rose-50 transition-all flex items-center justify-center gap-2 font-poppins">
            <XCircle class="w-5 h-5" /> TOLAK PENGAJUAN
          </button>
        </div>

        <div v-else class="p-8 card-eco bg-slate-50 dark:bg-white/5 border-dashed border-2 flex items-center justify-center gap-3 font-poppins">
          <div :class="statusBadgeClass" class="w-3 h-3 rounded-full animate-pulse shadow-sm"></div>
          <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">
            Status Akhir Administrasi: {{ formatStatus(izin.status) }}
          </p>
        </div>
      </div>
    </div>

    <div v-else class="py-40 text-center card-eco bg-transparent border-dashed border-2 flex flex-col items-center">
      <Database class="w-16 h-16 opacity-10 mb-4" />
      <p class="kpi-label !text-slate-400 uppercase tracking-widest">Data Karyawan Tidak Ditemukan</p>
    </div>

    <ModalApprove 
      :isOpen="isApproveModalOpen" 
      :izin="izin" 
      @close="isApproveModalOpen = false" 
      @approve="handleActionSuccess" 
    />

    <ModalReject 
      :isOpen="isRejectModalOpen" 
      :izin="izin" 
      @close="isRejectModalOpen = false" 
      @reject="handleActionSuccess" 
    />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRoute } from 'vue-router';
import api from '@/services/api';
import { 
  ClipboardCheck, ChevronLeft, Calendar, CalendarCheck, 
  Clock, CheckCircle2, XCircle, ExternalLink, Database 
} from 'lucide-vue-next';

import ModalApprove from './components/ModalApprove.vue';
import ModalReject from './components/ModalReject.vue';

const route = useRoute();
const izin = ref(null);
const loading = ref(true);

const isApproveModalOpen = ref(false);
const isRejectModalOpen = ref(false);

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

const getImageUrl = (path) => {
  if (!path) return '';
  const cleanPath = path.replace(/^(public\/|storage\/)/i, '');
  return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};

const isImage = (path) => /\.(jpg|jpeg|png|webp|gif)$/i.test(path);

const getInitials = (n) => {
  if (!n) return '??';
  const parts = n.split(' ');
  return parts.length > 1 
    ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() 
    : parts[0][0].toUpperCase();
};

const formatDate = (d) => {
  if (!d) return '---';
  return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }).format(new Date(d));
};

const calculateDuration = (s, e) => {
  if (!s || !e) return 0;
  const start = new Date(s);
  const end = new Date(e);
  const diffTime = Math.abs(end - start);
  return Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
};

const formatStatus = (s) => {
  const st = s?.toLowerCase();
  if (st === 'disetujui' || st === 'approved') return 'Disetujui';
  if (st === 'ditolak' || st === 'rejected') return 'Ditolak';
  return 'Menunggu Antrean';
};

const fetchDetail = async () => {
  loading.value = true;
  try {
    const response = await api.get(`/admin/persetujuan-izin/${route.params.id}`);
    if (response.data && response.data.data) {
      izin.value = response.data.data;
    } else {
      izin.value = response.data;
    }
  } catch (err) {
    console.error('Gagal memuat detail karyawan:', err);
  } finally {
    loading.value = false;
  }
};

const handleActionSuccess = () => {
  fetchDetail();
};

const statusBadgeClass = computed(() => {
  const s = izin.value?.status?.toLowerCase();
  if (s === 'disetujui' || s === 'approved') return 'bg-emerald-500 text-white shadow-emerald-500/30';
  if (s === 'ditolak' || s === 'rejected') return 'bg-rose-600 text-white shadow-rose-600/30';
  return 'bg-amber-500 text-white shadow-amber-500/30';
});

onMounted(fetchDetail);
</script>

<style scoped lang="postcss">
.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[2.5rem] border border-gray-100 dark:border-white/5 shadow-sm transition-all;
}
.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}
.btn-back-eco {
  @apply inline-flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-white/5 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-all active:scale-95 font-poppins;
}
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>