<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20">
                <FileEdit class="w-7 h-7 text-white" />
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 uppercase">
                    Koreksi Pengumuman
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
                    Otoritas Editor: Pembaruan Instruksi & Dokumen Terarsip
                </p>
            </div>
        </div>
        <button @click="router.push('/admin/pengumuman')" class="btn-back-eco">
            <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
        </button>
    </header>

    <div v-if="loading" class="flex flex-col items-center justify-center py-40">
        <div class="animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest animate-pulse">Menghubungkan Database...</p>
    </div>

    <form v-else @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
        
        <div class="lg:col-span-5 space-y-6">
            <div class="card-eco p-8 bg-white dark:bg-[#121512] shadow-xl border-none">
                <h3 class="kpi-label mb-6 flex items-center gap-2 !text-[#2d4a3e] dark:!text-emerald-500">
                    <Paperclip class="w-4 h-4" /> Manajemen Lampiran
                </h3>

                <div v-if="currentFile && !newFileSelected" class="mb-6 p-6 bg-slate-50 dark:bg-white/[0.03] rounded-3xl border border-dashed border-slate-200 dark:border-white/10 text-center">
                    <div class="flex flex-col items-center gap-3 mb-4">
                        <div class="p-3 bg-[#2d4a3e]/10 rounded-2xl">
                          <FileText class="w-8 h-8 text-[#2d4a3e]" />
                        </div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">Dokumen Terarsip</span>
                    </div>
                    <button type="button" @click="viewCurrentFile" class="text-[10px] font-bold text-blue-500 hover:text-blue-600 uppercase tracking-wider underline decoration-2 underline-offset-4">Buka Pratinjau Dokumen</button>
                </div>
                
                <div class="space-y-4">
                    <label class="flex flex-col items-center justify-center w-full h-44 border-2 border-[#2d4a3e]/20 border-dashed rounded-[2rem] cursor-pointer bg-slate-50 dark:bg-white/5 hover:bg-[#2d4a3e]/5 transition-all group">
                        <div v-if="!isAnalyzing" class="flex flex-col items-center px-6 text-center">
                            <UploadCloud class="w-8 h-8 text-[#2d4a3e] opacity-40 mb-3 group-hover:scale-110 transition-transform" />
                            <p class="text-[10px] text-slate-500 font-bold uppercase tracking-widest leading-tight">
                              {{ fileName || 'Ganti atau Perbarui Dokumen PDF' }}
                            </p>
                        </div>
                        <div v-else class="flex flex-col items-center">
                            <RefreshCw class="animate-spin h-8 w-8 text-[#2d4a3e] mb-2" />
                            <p class="text-[9px] font-black text-[#2d4a3e] uppercase animate-pulse">AI Re-Scanning...</p>
                        </div>
                        <input type="file" class="hidden" accept="application/pdf" @change="handleFileChange" :disabled="isAnalyzing" />
                    </label>
                    <p class="text-[9px] text-slate-400 italic text-center uppercase tracking-widest">Sistem AI akan mengekstrak ulang data jika file diganti.</p>
                </div>
            </div>
        </div>

        <div class="lg:col-span-7 space-y-6">
            <div class="card-eco p-8 md:p-10 bg-white dark:bg-[#121512] shadow-xl border-none space-y-6">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="kpi-label">Target Departemen</label>
                        <select v-model="form.target_departemen_id" class="input-field-eco">
                            <option :value="null">Global / Seluruh Personel</option>
                            <option v-for="dep in departemens" :key="dep.id" :value="dep.id">{{ dep.nama_departemen }}</option>
                        </select>
                    </div>
                    <div class="space-y-2">
                        <label class="kpi-label">Nomor Surat Resmi</label>
                        <input v-model="form.nomor_surat" type="text" class="input-field-eco font-mono font-bold uppercase" required />
                    </div>
                </div>

                <div class="space-y-2">
                    <label class="kpi-label">Judul Instruksi</label>
                    <input v-model="form.judul" type="text" class="input-field-eco font-bold text-lg" required />
                </div>

                <div class="space-y-2">
                    <label class="kpi-label">Narasi Pengumuman</label>
                    <textarea v-model="form.isi" class="input-field-eco min-h-[300px] text-sm leading-relaxed italic resize-none" required></textarea>
                </div>

                <div class="pt-4">
                    <button type="submit" class="btn-primary-eco w-full justify-center !py-5" :disabled="submitting || isAnalyzing">
                        <span v-if="!submitting" class="flex items-center gap-2 uppercase tracking-[0.2em] font-bold text-[10px]">
                            <Save class="w-5 h-5" /> Komit Perubahan Data
                        </span>
                        <span v-else class="flex items-center gap-2 animate-pulse text-[10px] font-bold uppercase tracking-[0.2em]">
                            <RefreshCw class="animate-spin w-5 h-5" /> Sinkronisasi Otoritas...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { useRoute, useRouter } from "vue-router";
import api from "@/services/api";
import { 
    FileEdit, ChevronLeft, UploadCloud, Save, 
    RefreshCw, Paperclip, FileText 
} from 'lucide-vue-next';
import * as pdfjsLib from "pdfjs-dist";

pdfjsLib.GlobalWorkerOptions.workerSrc = `https://unpkg.com/pdfjs-dist@${pdfjsLib.version}/build/pdf.worker.min.mjs`;

const route = useRoute();
const router = useRouter();
const loading = ref(true);
const submitting = ref(false);
const isAnalyzing = ref(false);

const departemens = ref([]);
const currentFile = ref(null);
const fileName = ref("");
const newFileSelected = ref(false);

const form = ref({
    nomor_surat: "",
    judul: "",
    isi: "",
    target_departemen_id: null,
    file: null
});

const loadInitialData = async () => {
    loading.value = true;
    try {
        const [depRes, annRes] = await Promise.all([
            api.get("/admin/departemens"),
            api.get(`/admin/pengumuman/${route.params.id}`)
        ]);
        departemens.value = depRes.data.data;
        const d = annRes.data.data;
        form.value = { 
            nomor_surat: d.nomor_surat, judul: d.judul, isi: d.isi, 
            target_departemen_id: d.target_departemen_id 
        };
        currentFile.value = d.file_path;
    } catch (e) {
        alert("Node data tidak ditemukan.");
        router.push("/admin/pengumuman");
    } finally { loading.value = false; }
};

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file || file.type !== "application/pdf") return;
    
    isAnalyzing.value = true;
    newFileSelected.value = true;
    fileName.value = file.name;
    form.value.file = file;

    try {
        const arrayBuffer = await file.arrayBuffer();
        const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
        const page = await pdf.getPage(1);
        const textContent = await page.getTextContent();
        const text = textContent.items.map(item => item.str).join(" ");

        const noMatch = text.match(/Nomor\s*[:]\s*([^\n\r]+)/i);
        if (noMatch) form.value.nomor_surat = noMatch[1].trim().split(" ")[0];

        const perihalMatch = text.match(/Perihal\s*[:]\s*([^\n\r]+)/i);
        if (perihalMatch) form.value.judul = perihalMatch[1].trim();

        const startIdx = text.search(/Sehubungan|Diberitahukan|Bersama/i);
        if (startIdx !== -1) form.value.isi = text.substring(startIdx, startIdx + 1200).trim();
    } catch (e) { console.error("AI Error:", e); }
    finally { isAnalyzing.value = false; }
};

const viewCurrentFile = () => {
    const url = `${import.meta.env.VITE_API_BASE_URL}/storage/${currentFile.value}`;
    window.open(url, '_blank');
};

const submitForm = async () => {
    submitting.value = true;
    try {
        const fd = new FormData();
        fd.append("nomor_surat", form.value.nomor_surat);
        fd.append("judul", form.value.judul);
        fd.append("isi", form.value.isi);
        
        if (form.value.target_departemen_id) {
            fd.append("target_departemen_id", form.value.target_departemen_id);
        }
        if (form.value.file) {
            fd.append("file", form.value.file);
        }

        // SPOOFING METHOD PUT
        await api.post(`/admin/pengumuman/${route.params.id}?_method=PUT`, fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        router.push("/admin/pengumuman");
    } catch (e) { alert("Gagal memperbarui data."); }
    finally { submitting.value = false; }
};

onMounted(loadInitialData);
</script>

<style scoped lang="postcss">
.card-eco { @apply rounded-[2rem] border border-slate-100 dark:border-gray-800 shadow-sm transition-all; }
.kpi-label { @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2 block ml-1; }

.input-field-eco { 
  @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-gray-800 
         rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none 
         transition-all dark:text-white font-poppins; 
}

.btn-primary-eco { 
  @apply inline-flex items-center px-6 bg-[#2d4a3e] text-white rounded-xl 
         shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 
         transition-all disabled:bg-slate-400 font-poppins; 
}

.btn-back-eco { 
  @apply inline-flex items-center px-6 py-3.5 bg-white dark:bg-[#1a1d19] border border-slate-100 
         dark:border-gray-800 rounded-xl text-[10px] font-bold uppercase tracking-widest 
         text-slate-500 hover:bg-slate-50 transition-all active:scale-95 font-poppins; 
}

.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>