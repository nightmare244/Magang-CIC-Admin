<template>
  <div class="p-6 max-w-5xl mx-auto font-poppins animate-fade-in">
    
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-10 gap-6 border-b dark:border-gray-800 pb-8">
        <div>
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 dark:text-white tracking-tight leading-none">
                Verifikasi Peminjaman
            </h1>
            <p class="text-sm text-gray-500 mt-2 font-medium opacity-70">
                Pusat otorisasi dan pengambilan keputusan aset inventaris.
            </p>
        </div>
        <button @click="router.push('/admin/peminjaman')" class="btn-back-army">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </button>
    </div>

    <Transition name="fade">
        <div v-if="apiError" class="p-4 mb-6 bg-rose-50 border-l-4 border-rose-500 rounded-r-2xl dark:bg-rose-900/10 text-rose-700 dark:text-rose-400 font-semibold text-sm animate-pulse">
            ⚠️ Kesalahan: {{ apiError }}
        </div>
    </Transition>
    <Transition name="fade">
        <div v-if="successMessage" class="p-4 mb-6 bg-emerald-50 border-l-4 border-emerald-500 rounded-r-2xl dark:bg-emerald-900/10 text-emerald-700 dark:text-emerald-400 font-semibold text-sm">
            ✅ Berhasil: {{ successMessage }}
        </div>
    </Transition>

    <div v-if="loading" class="text-center py-32 bg-white dark:bg-[#0f1410] rounded-[2rem] shadow-xl border dark:border-gray-800">
        <div class="inline-block animate-spin h-12 w-12 border-4 border-army border-t-transparent rounded-full mb-6"></div>
        <p class="text-gray-400 font-medium text-sm italic">Menyinkronkan dokumen intelijen...</p>
    </div>

    <div v-else-if="data.id" class="space-y-10">
        
        <div class="bg-gray-50 dark:bg-white/5 p-10 rounded-[2rem] border dark:border-gray-800 shadow-inner">
            <h3 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-10 text-center">Alur Kerja Pengajuan</h3>
            <div class="flex items-center justify-between max-w-2xl mx-auto relative px-4">
                <div class="absolute h-1 bg-gray-200 dark:bg-gray-800 w-full top-1/2 -translate-y-1/2 -z-10 left-0"></div>
                <WorkflowStep label="Menunggu" :active="data.status === 'pending'" icon="clock" />
                <WorkflowStep label="Disetujui" :active="data.status === 'disetujui'" icon="check" />
                <WorkflowStep label="Selesai" :active="data.status === 'selesai'" icon="flag" />
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <div class="bg-white dark:bg-[#1a1d19] p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-bold text-army uppercase tracking-widest mb-6 border-b dark:border-gray-800 pb-3">Logistik & Persediaan</h3>
                <div class="space-y-5">
                    <DetailRow label="Nama Aset">{{ data.inventaris.nama_barang }}</DetailRow>
                    <DetailRow label="ID Serial">{{ data.inventaris.kode_barang }}</DetailRow>
                    <div class="flex justify-between items-center p-6 bg-rose-50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 dark:border-rose-900/20">
                        <span class="text-xs font-bold text-rose-600 uppercase tracking-wider">Jumlah Unit Pinjam</span>
                        <span class="text-3xl font-bold text-rose-600">{{ data.quantity }}x</span>
                    </div>
                    <DetailRow label="Stok Tersedia">{{ data.inventaris.stok_saat_ini }} Unit</DetailRow>
                </div>
            </div>

            <div class="bg-white dark:bg-[#1a1d19] p-8 rounded-[2rem] shadow-xl border border-gray-100 dark:border-gray-800">
                <h3 class="text-xs font-bold text-army uppercase tracking-widest mb-6 border-b dark:border-gray-800 pb-3">Personel & Penjadwalan</h3>
                <div class="space-y-5">
                    <DetailRow label="Peminjam">{{ data.user.name }}</DetailRow>
                    <DetailRow label="NIP">{{ data.user.nip }}</DetailRow>
                    <DetailRow label="Masa Pakai">{{ formatDate(data.tanggal_mulai) }} — {{ formatDate(data.tanggal_selesai) }}</DetailRow>
                    <div class="pt-6 mt-4 border-t dark:border-gray-800">
                        <label class="text-[11px] font-bold text-gray-400 uppercase block mb-3 tracking-widest">Keterangan Penggunaan</label>
                        <div class="p-5 bg-gray-50 dark:bg-white/5 rounded-2xl border-l-4 border-army/30 text-sm text-gray-600 dark:text-gray-400 leading-relaxed italic">
                            "{{ data.keterangan || 'Tidak ada catatan tambahan yang diberikan.' }}"
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-[#0f1410] p-12 rounded-[2rem] border-2 border-dashed border-gray-200 dark:border-gray-800 text-center">
            
            <div v-if="data.status === 'pending'" class="space-y-8">
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white tracking-tight">Otorisasi Komando Diperlukan</h3>
                <div class="flex flex-wrap justify-center gap-6">
                    <button @click="processAction('approve')" :disabled="isProcessing" class="btn-approve-premium group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Konfirmasi Persetujuan
                    </button>
                    <button @click="openRejectModal" :disabled="isProcessing" class="btn-reject-premium group">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tolak Pengajuan
                    </button>
                </div>
                <p v-if="data.inventaris.stok_saat_ini < data.quantity" class="text-xs font-bold text-rose-500 uppercase animate-pulse tracking-widest">
                    ⚠️ Peringatan: Stok gudang saat ini tidak mencukupi untuk permintaan ini
                </p>
            </div>

            <div v-else-if="data.status === 'disetujui'" class="space-y-8">
                <h3 class="text-2xl font-bold text-army tracking-tight">Aset Dalam Penugasan Aktif</h3>
                <button @click="processAction('return')" :disabled="isProcessing" class="btn-return-premium group mx-auto">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                    Konfirmasi Pengembalian Aset
                </button>
            </div>

            <div v-else class="space-y-4">
                <p class="text-gray-400 font-bold uppercase tracking-widest text-sm italic">
                    Pengajuan Telah Diarsipkan (Status: {{ data.status }})
                </p>
                <div v-if="data.alasan_penolakan" class="p-6 bg-rose-50 dark:bg-rose-900/10 rounded-2xl border border-rose-100 max-w-2xl mx-auto">
                    <p class="text-[10px] font-bold text-rose-500 uppercase tracking-widest mb-1">Catatan Penolakan</p>
                    <p class="text-sm text-rose-700 dark:text-rose-400 font-medium">"{{ data.alasan_penolakan }}"</p>
                </div>
            </div>
        </div>
    </div>

    <Transition name="fade">
        <div v-if="showRejectModal" class="fixed inset-0 bg-black/80 backdrop-blur-md flex items-center justify-center z-50 p-6">
            <div class="bg-white dark:bg-[#1a1d19] p-10 rounded-[2.5rem] shadow-2xl w-full max-w-lg animate-pop">
                <h3 class="text-2xl font-bold text-rose-600 tracking-tight mb-6">Laporan Penolakan Teknis</h3>
                <textarea v-model="alasanPenolakan" class="input-field-army min-h-[160px] resize-none" placeholder="Tuliskan alasan spesifik penolakan pengajuan ini..."></textarea>
                <div class="flex flex-col sm:flex-row gap-4 mt-8">
                    <button @click="showRejectModal = false" class="btn-back-army flex-1 py-4">Batal</button>
                    <button @click="processAction('reject')" class="btn-reject-confirm flex-1 py-4">Proses Penolakan</button>
                </div>
            </div>
        </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";

const route = useRoute();
const router = useRouter();
const id = route.params.id;

const loading = ref(true);
const isProcessing = ref(false);
const apiError = ref(null);
const successMessage = ref(null);
const data = ref({});
const showRejectModal = ref(false);
const alasanPenolakan = ref('');

// SUB-COMPONENTS (FUNCTIONAL)
const DetailRow = {
    props: ['label'],
    template: `
        <div class="flex justify-between items-center py-3 border-b dark:border-gray-800 text-sm">
            <span class="font-semibold text-gray-400 uppercase text-[10px] tracking-widest">{{ label }}</span>
            <span class="font-bold text-gray-900 dark:text-white"><slot></slot></span>
        </div>
    `
};

const WorkflowStep = {
    props: ['label', 'active', 'icon'],
    template: `
        <div class="flex flex-col items-center gap-3 z-10">
            <div :class="['w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-500 border-4 border-white dark:border-[#1a1d19]', 
                active ? 'bg-army text-white shadow-xl scale-125' : 'bg-gray-100 dark:bg-gray-800 text-gray-300']">
                <i v-if="icon === 'clock'" class="fas fa-clock text-lg"></i>
                <i v-if="icon === 'check'" class="fas fa-check text-lg"></i>
                <i v-if="icon === 'flag'" class="fas fa-flag text-lg"></i>
            </div>
            <span :class="['text-[10px] font-bold uppercase tracking-widest', active ? 'text-army' : 'text-gray-400']">{{ label }}</span>
        </div>
    `
};

// LOGIC
const loadData = async () => {
    loading.value = true;
    try {
        const res = await api.get(`/admin/persetujuan-peminjaman/${id}`);
        data.value = res.data.data;
    } catch (e) {
        apiError.value = "Data pengajuan tidak ditemukan atau akses ditolak.";
    } finally {
        loading.value = false;
    }
};

const processAction = async (actionType) => {
    if (actionType === 'reject' && !alasanPenolakan.value) return;
    
    isProcessing.value = true;
    apiError.value = null;
    successMessage.value = null;

    try {
        const payload = actionType === 'reject' ? { alasan_penolakan: alasanPenolakan.value } : {};
        const res = await api.put(`/admin/persetujuan-peminjaman/${id}/${actionType}`, payload);
        successMessage.value = res.data.message;
        showRejectModal.value = false;
        setTimeout(loadData, 1500);
    } catch (e) {
        apiError.value = "Gagal memproses otorisasi pengajuan.";
    } finally {
        isProcessing.value = false;
    }
};

const formatDate = (dateStr) => {
    if(!dateStr) return '---';
    return new Date(dateStr).toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
};

const openRejectModal = () => {
    alasanPenolakan.value = '';
    showRejectModal.value = true;
};

onMounted(loadData);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.btn-back-army {
    @apply flex items-center px-6 py-3 bg-white dark:bg-gray-800 border-2 border-gray-100 dark:border-gray-700 
           rounded-2xl text-xs font-bold text-gray-500 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all active:scale-95;
}

.btn-approve-premium {
    @apply px-10 py-5 bg-army text-white rounded-[1.5rem] font-bold text-sm tracking-wide
           shadow-2xl shadow-army/30 hover:bg-army-dark transition-all active:scale-95 flex items-center;
}

.btn-reject-premium {
    @apply px-10 py-5 bg-white dark:bg-[#1a1d19] border-2 border-rose-500 text-rose-600 
           rounded-[1.5rem] font-bold text-sm tracking-wide hover:bg-rose-50 dark:hover:bg-rose-900/10 transition-all active:scale-95 flex items-center;
}

.btn-reject-confirm {
    @apply bg-rose-600 text-white rounded-2xl font-bold hover:bg-rose-700 transition-all active:scale-95;
}

.btn-return-premium {
    @apply px-12 py-5 bg-gray-900 text-white rounded-[1.5rem] font-bold text-sm tracking-wide
           hover:bg-black shadow-2xl transition-all active:scale-95 flex items-center;
}

.input-field-army {
    @apply w-full bg-gray-50 dark:bg-[#0f1410] border-2 border-gray-100 dark:border-gray-800 rounded-[1.5rem] px-6 py-4 
           focus:border-army dark:text-white outline-none transition-all font-semibold text-sm;
}

.animate-fade-in {
    animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>