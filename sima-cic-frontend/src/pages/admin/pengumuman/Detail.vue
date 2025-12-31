<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
                <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
                <BookOpen class="w-7 h-7 text-white relative z-10" />
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
                    Detail Pengumuman
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1">
                    Verifikasi informasi resmi dan log aktivitas pembaca karyawan.
                </p>
            </div>
        </div>
        <button @click="$router.push('/admin/pengumuman')" class="btn-refresh-eco group">
            <ChevronLeft class="w-4 h-4 mr-2 transition-transform group-hover:-translate-x-1" /> Kembali ke Daftar
        </button>
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40 card-eco bg-white dark:bg-[#121512]">
        <div class="inline-block animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-xs italic text-slate-400 animate-pulse font-poppins">Menyinkronkan data pengumuman...</p>
    </div>

    <div v-else-if="pengumuman.id" class="max-w-5xl mx-auto space-y-8 animate-fade-in">
        
        <div class="card-eco p-8 md:p-12 bg-white dark:bg-[#121512] shadow-xl border-none relative overflow-hidden font-poppins">
            <div class="absolute top-0 right-0 w-40 h-40 bg-[#2d4a3e]/5 rounded-bl-[5rem] -mr-16 -mt-16"></div>

            <div class="flex flex-wrap items-center gap-4 mb-10">
                <span class="px-3 py-1 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 text-[10px] font-bold rounded-lg uppercase tracking-tighter">
                    {{ pengumuman.target_departemen?.nama_departemen || 'Seluruh Departemen' }}
                </span>
                <span class="text-[10px] text-slate-400 font-bold uppercase tracking-widest border-l-2 border-slate-100 dark:border-slate-800 pl-4 flex items-center gap-2">
                    <Calendar class="w-3.5 h-3.5" />
                    Terbit: {{ formatDate(pengumuman.created_at) }}
                </span>
            </div>

            <div class="mb-10">
                <p class="kpi-label text-slate-400">Nomor Referensi Surat</p>
                <div class="mt-2 inline-flex items-center gap-3 px-5 py-2.5 bg-slate-50 dark:bg-white/5 rounded-xl border border-slate-100 dark:border-slate-800">
                    <Hash class="w-4 h-4 text-[#2d4a3e] opacity-50" />
                    <span class="text-sm font-mono font-bold text-slate-600 dark:text-slate-300">{{ pengumuman.nomor_surat }}</span>
                </div>
            </div>

            <h2 class="text-3xl md:text-4xl font-bold text-slate-800 dark:text-white mb-8 leading-tight tracking-tight uppercase">
                {{ pengumuman.judul }}
            </h2>
            
            <div class="bg-slate-50/50 dark:bg-white/[0.02] p-8 md:p-10 rounded-[1.8rem] border border-slate-100 dark:border-slate-800 mb-12 shadow-inner">
                <p class="kpi-label text-[#2d4a3e] dark:text-emerald-500 mb-6">Isi Informasi</p>
                <div class="text-slate-600 dark:text-slate-300 leading-relaxed text-base md:text-lg whitespace-pre-line font-medium italic">
                    "{{ pengumuman.isi }}"
                </div>
            </div>

            <div v-if="pengumuman.file_path" class="bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-slate-800 p-8 rounded-[1.8rem] flex flex-col sm:flex-row items-center justify-between gap-8">
                <div class="flex items-center gap-6">
                    <div class="w-14 h-14 bg-[#2d4a3e] text-white rounded-2xl flex items-center justify-center shadow-lg shadow-[#2d4a3e]/20">
                        <FileText class="w-7 h-7" />
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-800 dark:text-white uppercase tracking-wider">Dokumen Lampiran</h4>
                        <p class="text-[10px] text-slate-400 uppercase font-medium tracking-widest mt-1">Format: Digital PDF</p>
                    </div>
                </div>
                <button @click="downloadManual" class="btn-refresh-eco min-w-[200px] justify-center">
                    <Download class="w-4 h-4 mr-2" />
                    Unduh Dokumen
                </button>
            </div>

            <div class="mt-16 pt-12 border-t border-slate-100 dark:border-slate-800">
                <div class="flex items-center justify-between mb-10">
                    <h3 class="kpi-label text-slate-800 dark:text-white">Log Aktivitas Karyawan</h3>
                    <div class="flex items-center gap-2 px-4 py-2 bg-[#2d4a3e]/10 text-[#2d4a3e] dark:text-emerald-400 text-[10px] font-bold rounded-lg uppercase tracking-widest border border-[#2d4a3e]/10">
                        <Users class="w-3.5 h-3.5" />
                        {{ pengumuman.reads?.length || 0 }} Pembaca Terverifikasi
                    </div>
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 max-h-80 overflow-y-auto custom-scrollbar pr-4">
                    <div v-for="read in pengumuman.reads" :key="read.id" class="flex items-center gap-4 group p-4 rounded-2xl bg-slate-50 dark:bg-white/5 hover:bg-white dark:hover:bg-white/10 border border-transparent hover:border-slate-200 transition-all duration-300">
                        <div class="w-10 h-10 rounded-xl bg-[#2d4a3e]/10 flex items-center justify-center text-[#2d4a3e] font-bold text-xs">
                            {{ read.user?.name?.substring(0,2).toUpperCase() }}
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-[11px] font-bold text-slate-800 dark:text-slate-200 truncate uppercase tracking-widest">{{ read.user?.name }}</p>
                            <div class="flex items-center gap-2 mt-1 opacity-60">
                                <Clock class="w-3 h-3" />
                                <p class="text-[9px] font-mono font-medium">{{ formatTime(read.read_at) }} • {{ formatDateShort(read.read_at) }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div v-if="!pengumuman.reads?.length" class="col-span-full text-center py-16 opacity-30">
                        <div class="flex flex-col items-center gap-3">
                            <History class="w-12 h-12" />
                            <p class="text-[10px] font-bold uppercase tracking-widest">Belum ada log pembaca</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute } from "vue-router";
import api from "@/services/api";
import { 
    BookOpen, ChevronLeft, Calendar, Hash, FileText, 
    Download, Users, Clock, History 
} from 'lucide-vue-next';

const route = useRoute();
const pengumuman = ref({});
const loading = ref(true);
const downloadLoading = ref(false);

const loadData = async () => {
    loading.value = true;
    try {
        const res = await api.get(`/admin/pengumuman/${route.params.id}`);
        pengumuman.value = res.data.data || res.data;
    } catch (e) {
        console.error("Gagal mengambil data pengumuman:", e);
    } finally {
        setTimeout(() => { loading.value = false; }, 400);
    }
};

const downloadManual = async () => {
    if (!pengumuman.value.id) return;
    downloadLoading.value = true;
    try {
        const response = await api.get(`/admin/pengumuman/${pengumuman.value.id}/file-stream`, {
            responseType: 'blob' 
        });
        const blob = new Blob([response.data], { type: 'application/pdf' });
        const url = URL.createObjectURL(blob);
        const link = document.createElement('a');
        link.href = url;
        link.setAttribute('download', `DOKUMEN_PENGUMUMAN_${pengumuman.value.nomor_surat.replace(/[/\\?%*:|"<>]/g, '-')}.pdf`);
        document.body.appendChild(link);
        link.click();
        setTimeout(() => { URL.revokeObjectURL(url); link.remove(); }, 100);
    } catch (e) {
        alert("Gagal mengunduh file dari server.");
    } finally {
        downloadLoading.value = false;
    }
};

const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric'}) : '---';
const formatDateShort = (d) => d ? new Date(d).toLocaleDateString('id-ID', {day:'2-digit', month:'2-digit', year:'2-digit'}) : '---';
const formatTime = (d) => d ? new Date(d).toLocaleTimeString('id-ID', {hour:'2-digit', minute:'2-digit'}) : '---';

onMounted(loadData);
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

/* STYLE PATOKAN: INVENTARIS (Eco-Modern) */
.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm;
}

.kpi-label {
  @apply text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer font-poppins;
}

/* Custom reader log scrollbar */
.custom-scrollbar::-webkit-scrollbar { width: 5px; }
.custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-slate-800 rounded-full; }

.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>