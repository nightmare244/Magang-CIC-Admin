<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20">
                <FilePlus2 class="w-7 h-7 text-white" />
            </div>
            <div>
                <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 uppercase">
                    Tambah Pengumuman
                </h1>
                <p class="text-xs font-medium text-slate-400 mt-1 italic uppercase tracking-widest">
                    AI Integration: Automated Document Extraction
                </p>
            </div>
        </div>
        <button @click="router.push('/admin/pengumuman')" class="btn-back-eco">
            <ChevronLeft class="w-4 h-4 mr-1" /> Kembali
        </button>
    </header>

    <form @submit.prevent="submitForm" class="grid grid-cols-1 lg:grid-cols-12 gap-8 max-w-7xl mx-auto">
        
        <div class="lg:col-span-5 space-y-6">
            <div class="card-eco p-8 bg-white dark:bg-[#121512] shadow-xl border-none text-center relative overflow-hidden">
                <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 mb-6 flex items-center justify-center gap-2">
                    <ScanLine class="w-4 h-4" /> Langkah 1: Scan Dokumen (PDF)
                </h3>
                
                <div class="mt-4">
                    <label class="flex flex-col items-center justify-center w-full h-52 border-2 border-[#2d4a3e]/20 border-dashed rounded-[2rem] cursor-pointer bg-slate-50 dark:bg-[#1a1d19] hover:bg-[#2d4a3e]/5 transition-all group">
                        <div v-if="!isAnalyzing" class="flex flex-col items-center px-6">
                            <div class="p-4 bg-white dark:bg-[#121512] rounded-2xl shadow-sm mb-4 group-hover:scale-110 transition-transform">
                                <UploadCloud class="w-8 h-8 text-[#2d4a3e] opacity-60" />
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-400 font-bold">Klik untuk Analisis AI</p>
                            <p class="text-[10px] text-slate-400 mt-1 uppercase tracking-widest text-center">Dokumen harus berupa teks digital (bukan hasil foto)</p>
                        </div>
                        <div v-else class="flex flex-col items-center">
                            <div class="animate-spin h-10 w-10 border-[3px] border-[#2d4a3e] border-t-transparent rounded-full mb-4"></div>
                            <p class="text-[10px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.2em] animate-pulse">Menghubungkan Neural Engine...</p>
                        </div>
                        <input type="file" class="hidden" accept="application/pdf" @change="handleFileChange" :disabled="isAnalyzing" />
                    </label>
                </div>

                <Transition name="slide-fade">
                    <div v-if="pdfPreviewUrl" class="mt-8 relative animate-fade-in">
                        <div class="absolute -top-3 -right-3 z-10 p-2 bg-emerald-500 text-white rounded-xl shadow-lg">
                            <CheckCircle2 class="w-4 h-4" />
                        </div>
                        <iframe :src="pdfPreviewUrl" class="w-full h-[450px] rounded-[1.5rem] bg-slate-900 shadow-inner border dark:border-white/5"></iframe>
                    </div>
                </Transition>
            </div>
        </div>

        <div class="lg:col-span-7 space-y-6">
            <div class="card-eco p-8 md:p-10 bg-white dark:bg-[#121512] shadow-xl border-none space-y-6">
                
                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Target Departemen (Opsional)</label>
                    <select v-model="form.target_departemen_id" class="input-field-eco">
                      <option :value="null">Global / Seluruh Personel</option>
                      <option v-for="dep in departemens" :key="dep.id" :value="dep.id">{{ dep.nama_departemen }}</option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Nomor Surat (AI Detected)</label>
                    <input v-model="form.nomor_surat" type="text" class="input-field-eco font-mono font-bold uppercase" placeholder="Contoh: 001/CIC/2025" required />
                </div>

                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Judul / Perihal</label>
                    <input v-model="form.judul" type="text" class="input-field-eco font-bold" placeholder="Judul Instruksi" required />
                </div>

                <div class="space-y-2">
                    <label class="kpi-label !text-slate-500">Ringkasan Narasi</label>
                    <textarea v-model="form.isi" class="input-field-eco min-h-[250px] text-sm leading-relaxed" placeholder="Isi pesan pengumuman..." required></textarea>
                </div>

                <button type="submit" class="btn-refresh-eco w-full justify-center !py-5 shadow-lg shadow-[#2d4a3e]/20" :disabled="submitting || isAnalyzing">
                    <span v-if="!submitting" class="font-bold tracking-widest uppercase">Publikasikan Sekarang</span>
                    <span v-else class="flex items-center gap-2">
                        <RefreshCw class="animate-spin w-4 h-4" /> Sinkronisasi Otoritas...
                    </span>
                </button>
            </div>
        </div>
    </form>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  FilePlus2, ChevronLeft, UploadCloud, ScanLine, 
  CheckCircle2, RefreshCw 
} from 'lucide-vue-next';
import * as pdfjsLib from "pdfjs-dist";

// SANGAT PENTING: Menggunakan Worker dari UNPKG yang stabil
pdfjsLib.GlobalWorkerOptions.workerSrc = `https://unpkg.com/pdfjs-dist@${pdfjsLib.version}/build/pdf.worker.min.mjs`;

const router = useRouter();
const isAnalyzing = ref(false);
const submitting = ref(false);
const pdfPreviewUrl = ref(null);
const departemens = ref([]);
const form = ref({ nomor_surat: "", judul: "", isi: "", target_departemen_id: null, file: null });

// LOAD DEPARTEMENS UNTUK DROPDOWN
onMounted(async () => {
  try {
    const res = await api.get('/admin/departemens');
    departemens.value = res.data.data;
  } catch (e) {
    console.error("Gagal memuat daftar departemen.");
  }
});

// LOGIKA AI EKSTRAKSI TEKS
const extractInfoFromText = (text) => {
    // 1. Ekstrak Nomor Surat (Pencarian Pola Nomor: ...)
    const noReg = /Nomor\s*[:]\s*([^\n\r]+)/i;
    const noMatch = text.match(noReg);
    if (noMatch) {
        form.value.nomor_surat = noMatch[1].trim().split(" ")[0].replace(/[,.;]$/, "");
    }

    // 2. Ekstrak Judul (Pencarian Pola Perihal: ...)
    const perihalReg = /Perihal\s*[:]\s*([^\n\r]+)/i;
    const perihalMatch = text.match(perihalReg);
    if (perihalMatch) {
        form.value.judul = perihalMatch[1].trim();
    } else {
        // Fallback: Ambil 5 kata pertama sebagai judul
        form.value.judul = text.split(" ").slice(0, 5).join(" ") + "...";
    }

    // 3. Ekstrak Isi (Mencari blok teks utama)
    const keywords = ["Sehubungan", "Diberitahukan", "Bersama ini", "Menimbang"];
    let body = text;
    for (let kw of keywords) {
        let idx = text.toLowerCase().indexOf(kw.toLowerCase());
        if (idx !== -1) {
            body = text.substring(idx);
            break;
        }
    }
    form.value.isi = body.substring(0, 1500).replace(/\s+/g, ' ').trim();
};

const handleFileChange = async (event) => {
    const file = event.target.files[0];
    if (!file || file.type !== "application/pdf") {
        alert("Hanya format PDF yang didukung sistem.");
        return;
    }
    
    isAnalyzing.value = true;
    try {
        form.value.file = file;
        if (pdfPreviewUrl.value) URL.revokeObjectURL(pdfPreviewUrl.value);
        pdfPreviewUrl.value = URL.createObjectURL(file);
        
        const arrayBuffer = await file.arrayBuffer();
        const loadingTask = pdfjsLib.getDocument({ data: arrayBuffer });
        const pdf = await loadingTask.promise;
        
        let fullText = "";
        // Ambil teks dari maksimal 2 halaman pertama untuk efisiensi
        const pagesToScan = Math.min(pdf.numPages, 2);
        
        for (let i = 1; i <= pagesToScan; i++) {
            const page = await pdf.getPage(i);
            const textContent = await page.getTextContent();
            fullText += textContent.items.map(item => item.str).join(" ") + " ";
        }

        if (fullText.trim().length < 10) {
            throw new Error("Teks tidak terdeteksi. Gunakan PDF versi digital, bukan hasil scan foto.");
        }

        extractInfoFromText(fullText);
    } catch (e) {
        console.error("AI Error:", e);
        alert("AI Gagal: " + (e.message || "Gagal memproses dokumen."));
    } finally { 
        isAnalyzing.value = false; 
    }
};

const submitForm = async () => {
    submitting.value = true;
    try {
        const fd = new FormData();
        fd.append("nomor_surat", form.value.nomor_surat);
        fd.append("judul", form.value.judul);
        fd.append("isi", form.value.isi);
        
        // SANGAT PENTING: Jangan kirim string "null" ke Backend
        if (form.value.target_departemen_id && form.value.target_departemen_id !== 'null') {
            fd.append("target_departemen_id", form.value.target_departemen_id);
        }

        if (form.value.file) {
            fd.append("file", form.value.file);
        }

        await api.post("/admin/pengumuman", fd, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        
        router.push("/admin/pengumuman");
    } catch (e) {
        const errorData = e.response?.data?.errors;
        if (errorData) {
            const firstError = Object.values(errorData)[0][0];
            alert("VALIDASI: " + firstError);
        } else {
            alert("GAGAL: Masalah pada otoritas server.");
        }
    } finally {
        submitting.value = false;
    }
};
</script>

<style scoped lang="postcss">
.card-eco { @apply rounded-[2rem] border border-slate-100 dark:border-gray-800 transition-all; }
.kpi-label { @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1; }
.input-field-eco { @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-gray-800 rounded-2xl px-6 py-4 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none dark:text-white transition-all; }
.btn-refresh-eco { @apply bg-[#2d4a3e] text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest hover:bg-[#385b4d] active:scale-95 transition-all disabled:bg-slate-400; }
.btn-back-eco { @apply flex items-center px-6 py-3 bg-white dark:bg-[#1a1d19] border border-slate-100 dark:border-gray-800 rounded-2xl text-[10px] font-bold uppercase tracking-widest text-slate-500 hover:bg-slate-50 transition-all; }
.animate-fade-in { animation: fadeIn 0.6s ease-out forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
</style>