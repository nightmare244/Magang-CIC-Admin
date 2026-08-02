<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
                <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
                <ShieldCheck class="w-7 h-7 text-white relative z-10" />
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
                    Verifikasi Peminjaman
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
                    Pusat Otorisasi Keputusan Aset Inventaris Kantor
                </p>
            </div>
        </div>
        <button @click="router.push('/admin/peminjaman')" class="btn-back-eco">
            <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
        </button>
    </header>

    <Transition name="slide-fade">
        <div v-if="apiError" class="p-4 bg-rose-50 border-l-4 border-rose-500 text-rose-700 dark:bg-rose-900/20 dark:text-rose-400 rounded-r-xl font-bold text-sm uppercase tracking-widest shadow-sm">
            <AlertTriangle class="w-5 h-5 inline mr-2" /> {{ apiError }}
        </div>
    </Transition>
    <Transition name="slide-fade">
        <div v-if="successMessage" class="p-4 bg-emerald-50 border-l-4 border-emerald-500 text-emerald-700 dark:bg-emerald-900/20 dark:text-emerald-400 rounded-r-xl font-bold text-sm uppercase tracking-widest shadow-sm">
            <CheckCircle2 class="w-5 h-5 inline mr-2" /> {{ successMessage }}
        </div>
    </Transition>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco">
        <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyinkronkan Dokumen Intelijen...</p>
    </div>

    <div v-else-if="data" class="max-w-5xl mx-auto space-y-10">
        
        <div class="card-eco p-10 bg-white/50 backdrop-blur-sm shadow-sm border-none">
            <h3 class="kpi-label !text-slate-400 text-center mb-10">Alur Kerja Pengajuan</h3>
            <div class="flex items-center justify-between max-w-2xl mx-auto relative px-4">
                <div class="absolute h-0.5 bg-slate-100 dark:bg-slate-800 w-full top-1/2 -translate-y-1/2 -z-10 left-0"></div>
                <WorkflowStep label="Menunggu" :active="data.status === 'pending'" type="clock" />
                <WorkflowStep label="Disetujui" :active="data.status === 'disetujui'" type="check" />
                <WorkflowStep label="Selesai" :active="data.status === 'selesai'" type="flag" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 font-poppins">
            <div class="card-eco p-8 bg-white dark:bg-[#121512] shadow-xl border-none">
                <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <Package class="w-4 h-4" /> Logistik & Persediaan
                </h3>
                <div class="space-y-4 mt-6">
                    <DetailRow label="Nama Aset">{{ data.inventaris?.nama_barang }}</DetailRow>
                    <DetailRow label="ID Serial">{{ data.inventaris?.kode_barang }}</DetailRow>
                    <div class="flex justify-between items-center p-6 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-100 dark:border-white/5 mt-4">
                        <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Kuantitas Pinjam</span>
                        <span class="text-3xl font-black text-[#2d4a3e] dark:text-emerald-500">{{ data.quantity }}x</span>
                    </div>
                    <DetailRow label="Stok Gudang">{{ data.inventaris?.stok_saat_ini }} Unit Tersedia</DetailRow>
                </div>
            </div>

            <div class="card-eco p-8 bg-white dark:bg-[#121512] shadow-xl border-none">
                <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 flex items-center gap-2">
                    <Users class="w-4 h-4" /> Personel & Penjadwalan
                </h3>
                <div class="space-y-4 mt-6">
                    <DetailRow label="Identitas">{{ data.user?.name }}</DetailRow>
                    <DetailRow label="ID Node (NIP)">{{ data.user?.nip }}</DetailRow>
                    <DetailRow label="Masa Pakai">{{ data.tanggal_mulai }} — {{ data.tanggal_selesai }}</DetailRow>
                    <div class="pt-4">
                        <label class="kpi-label !text-slate-400 lowercase tracking-normal mb-2">Catatan Operasional</label>
                        <div class="p-5 bg-slate-50 dark:bg-white/5 rounded-3xl border-l-[6px] border-[#2d4a3e] text-sm text-slate-600 dark:text-slate-300 leading-relaxed italic font-medium">
                            "{{ data.keterangan || 'Tidak ada catatan tambahan.' }}"
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card-eco p-12 bg-white/50 dark:bg-[#121512]/50 backdrop-blur-md border-2 border-dashed border-slate-200 dark:border-slate-800 text-center shadow-none">
            
            <div v-if="data.status === 'pending'" class="space-y-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">Otorisasi Komando Diperlukan</h3>
                <div class="flex flex-wrap justify-center gap-6">
                    <button 
                        @click="openApproveModal" 
                        :disabled="submitting || data.inventaris?.stok_saat_ini < data.quantity" 
                        class="btn-refresh-eco min-w-[220px] justify-center !py-4 shadow-emerald-500/20"
                    >
                        <Check class="w-5 h-5 mr-2" /> Setujui Pinjaman
                    </button>
                    <button 
                        @click="openRejectModal" 
                        :disabled="submitting" 
                        class="btn-back-eco min-w-[220px] justify-center !py-4 border-rose-500 text-rose-600 hover:bg-rose-50 font-bold"
                    >
                        <X class="w-5 h-5 mr-2" /> Tolak Pengajuan
                    </button>
                </div>
                <p v-if="data.inventaris?.stok_saat_ini < data.quantity" class="text-[10px] font-black text-rose-500 uppercase animate-pulse tracking-[0.2em] flex items-center justify-center gap-2">
                    <AlertTriangle class="w-4 h-4" /> Stok gudang tidak mencukupi untuk permintaan ini
                </p>
            </div>

            <div v-else-if="data.status === 'disetujui'" class="space-y-8 animate-fade-in">
                <h3 class="text-2xl font-bold text-[#2d4a3e] dark:text-emerald-500 tracking-tight">Aset Dalam Penugasan Aktif</h3>
                <button 
                    @click="openReturnModal" 
                    :disabled="submitting" 
                    class="btn-refresh-eco mx-auto min-w-[280px] justify-center !py-5 shadow-slate-900/20 bg-slate-800"
                >
                    <PackageSearch class="w-5 h-5 mr-2" /> Konfirmasi Pengembalian Aset
                </button>
            </div>

            <div v-else class="space-y-4">
                <p class="text-slate-400 font-black uppercase tracking-[0.3em] text-[10px] italic">
                    Berkas Diarsipkan (Status: {{ data.status }})
                </p>
                <div v-if="data.alasan_penolakan" class="p-6 bg-rose-50 dark:bg-rose-900/10 rounded-3xl border border-rose-100 dark:border-rose-900/20 max-w-2xl mx-auto">
                    <p class="kpi-label !text-rose-600 !mb-1">Catatan Penolakan Pusat</p>
                    <p class="text-sm text-rose-700 dark:text-rose-400 font-bold italic">"{{ data.alasan_penolakan }}"</p>
                </div>
            </div>
        </div>
    </div>

    <ModalApprove 
        :isOpen="isApproveModalOpen" 
        :peminjaman="data" 
        @close="isApproveModalOpen = false" 
        @success="handleActionSuccess" 
    />

    <ModalReject 
        :isOpen="isRejectModalOpen" 
        :peminjaman="data" 
        @close="isRejectModalOpen = false" 
        @success="handleActionSuccess" 
    />

    <ModalReturn
        :isOpen="isReturnModalOpen"
        :peminjaman="data"
        @close="isReturnModalOpen = false"
        @success="handleActionSuccess"
    />
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import api from '@/services/api';
import { 
    ShieldCheck, ChevronLeft, AlertTriangle, CheckCircle2, 
    Package, Users, Check, X, PackageSearch,
    Clock, Flag
} from 'lucide-vue-next';

// IMPORT MODALS
import ModalApprove from './components/ModalApprovePeminjaman.vue';
import ModalReject from './components/ModalRejectPeminjaman.vue';
import ModalReturn from './components/ModalReturnPeminjaman.vue';

const route = useRoute();
const router = useRouter();
const data = ref(null);
const loading = ref(true);
const apiError = ref(null);
const successMessage = ref(null);
const submitting = ref(false);

// MODAL STATES
const isApproveModalOpen = ref(false);
const isRejectModalOpen = ref(false);
const isReturnModalOpen = ref(false);

const openApproveModal = () => { isApproveModalOpen.value = true; };
const openRejectModal = () => { isRejectModalOpen.value = true; };
const openReturnModal = () => { isReturnModalOpen.value = true; };

const handleActionSuccess = () => {
    successMessage.value = "Otorisasi Berhasil Diperbarui.";
    loadDetail();
    setTimeout(() => { successMessage.value = null; }, 3000);
};

const DetailRow = {
    props: ['label'],
    template: `
        <div class="flex justify-between items-center py-3 border-b border-slate-50 dark:border-white/5 text-sm">
            <span class="text-[10px] font-black text-slate-400 uppercase tracking-[0.15em]">{{ label }}</span>
            <span class="font-bold text-slate-700 dark:text-slate-200"><slot></slot></span>
        </div>
    `
};

const WorkflowStep = {
    props: ['label', 'active', 'type'],
    components: { Clock, Check, Flag },
    template: `
        <div class="flex flex-col items-center gap-3 z-10">
            <div :class="['w-12 h-12 rounded-[1rem] flex items-center justify-center transition-all duration-500 border-4 border-white dark:border-[#1a1d19] shadow-lg', 
                active ? 'bg-[#2d4a3e] text-white scale-125' : 'bg-slate-100 dark:bg-slate-800 text-slate-300']">
                <Clock v-if="type === 'clock'" class="w-5 h-5" />
                <Check v-if="type === 'check'" class="w-5 h-5" />
                <Flag v-if="type === 'flag'" class="w-5 h-5" />
            </div>
            <span :class="['text-[9px] font-black uppercase tracking-[0.2em]', active ? 'text-[#2d4a3e] dark:text-emerald-500' : 'text-slate-400']">{{ label }}</span>
        </div>
    `
};

const loadDetail = async () => {
    loading.value = true;
    apiError.value = null;
    try {
        const res = await api.get(`/admin/persetujuan-peminjaman/${route.params.id}`);
        data.value = res.data.data;
    } catch (e) {
        apiError.value = "Akses Ditolak: Data intelijen tidak ditemukan.";
    } finally {
        setTimeout(() => { loading.value = false; }, 400);
    }
};

onMounted(loadDetail);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-6 py-3.5 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins;
}

.btn-back-eco {
  @apply inline-flex items-center px-6 py-3.5 bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 
         rounded-xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 
         dark:hover:bg-slate-800 transition-all active:scale-95 font-poppins;
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>