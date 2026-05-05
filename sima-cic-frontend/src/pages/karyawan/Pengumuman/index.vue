<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-12 pb-24 px-8 rounded-b-[4rem] shadow-xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-48 h-48 bg-emerald-500/10 rounded-full blur-3xl"></div>
      <div class="absolute left-4 top-12 opacity-10">
        <BellRing class="w-24 h-24" />
      </div>
      
      <div class="relative z-10 flex flex-col items-center text-center">
        <p class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-300 mb-2">Pusat Informasi</p>
        <h1 class="text-3xl font-bold tracking-tight">Pengumuman</h1>
        <p class="text-[11px] opacity-70 mt-1 font-medium italic text-emerald-50">Ciwangun Indah Camp - Stay Updated</p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-12 relative z-20 space-y-6">
      
      <div v-if="loading" class="space-y-4">
        <div v-for="i in 3" :key="i" class="h-40 bg-white dark:bg-[#121512] rounded-[2.5rem] animate-pulse border border-white dark:border-white/5"></div>
      </div>

      <div v-else-if="error" class="bg-rose-50 dark:bg-rose-500/10 p-8 rounded-[2.5rem] text-center border border-rose-100">
        <AlertCircle class="w-12 h-12 text-rose-500 mx-auto mb-3" />
        <p class="text-sm font-bold text-rose-700 dark:text-rose-400">{{ error }}</p>
        <button @click="getPengumumans" class="mt-4 text-xs font-bold uppercase tracking-widest text-rose-600 underline">Coba Lagi</button>
      </div>

      <div v-else class="space-y-5 animate-fade-in-up">
        <div v-if="!pengumumans.data || pengumumans.data.length === 0" class="bg-white dark:bg-[#121512] rounded-[3rem] p-12 text-center shadow-md border border-dashed border-slate-200 dark:border-white/10">
          <div class="w-16 h-16 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-4 text-slate-200">
            <MegaphoneOff class="w-8 h-8" />
          </div>
          <p class="text-[11px] font-bold text-slate-400 uppercase tracking-widest">Tidak ada pengumuman baru</p>
        </div>

        <div
          v-for="pengumuman in pengumumans.data"
          :key="pengumuman.id"
          class="transition-all duration-300 active:scale-[0.98]"
        >
          <PengumumanCard 
            :pengumuman="pengumuman" 
            @tandaiDibaca="markAsRead(pengumuman)" 
            class="shadow-xl shadow-emerald-900/5"
          />
        </div>

        <div v-if="pengumumans.prev_page_url || pengumumans.next_page_url" class="flex justify-center gap-4 mt-8">
          <button 
            @click="getPengumumansByUrl(pengumumans.prev_page_url)"
            :disabled="!pengumumans.prev_page_url"
            class="px-6 py-3 bg-white dark:bg-[#121512] rounded-2xl text-[10px] font-bold uppercase tracking-widest disabled:opacity-30 shadow-sm border border-slate-100 dark:border-white/5 transition-all active:scale-90"
          >
            ← Prev
          </button>
          <button 
            @click="getPengumumansByUrl(pengumumans.next_page_url)"
            :disabled="!pengumumans.next_page_url"
            class="px-6 py-3 bg-[#2d4a3e] text-white rounded-2xl text-[10px] font-bold uppercase tracking-widest disabled:opacity-30 shadow-md transition-all active:scale-90"
          >
            Next →
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';
import { BellRing, MegaphoneOff, AlertCircle, Loader2 } from 'lucide-vue-next';
import PengumumanCard from './components/PengumumanCard.vue';

const pengumumans = ref({ data: [], prev_page_url: null, next_page_url: null });
const loading = ref(true);
const error = ref(null);

/**
 * Mengambil daftar pengumuman dari API berdasarkan halaman.
 * Terintegrasi dengan PengumumanKaryawanController@index
 */
const getPengumumans = async (page = 1) => {
  loading.value = true;
  error.value = null;
  try {
    const response = await api.get(`/karyawan/pengumuman?page=${page}`);
    // Backend menggunakan simplePaginate sehingga struktur langsung tersedia
    pengumumans.value = response.data;
  } catch (err) {
    error.value = "Gagal memuat pengumuman. Periksa koneksi Anda.";
  } finally {
    // Delay halus untuk UX
    setTimeout(() => { loading.value = false; }, 400);
  }
};

/**
 * Navigasi paginasi menggunakan URL yang diberikan oleh Laravel
 */
const getPengumumansByUrl = (url) => {
  if (!url) return;
  const urlObj = new URL(url);
  const page = urlObj.searchParams.get('page');
  getPengumumans(page);
};

/**
 * Menandai pengumuman sebagai dibaca.
 * Terintegrasi dengan PengumumanKaryawanController@tandaiDibaca
 */
const markAsRead = async (pengumuman) => {
  try {
    // Memanggil endpoint backend untuk mencatat di tabel pengumuman_reads
    await api.post(`/karyawan/pengumuman/${pengumuman.id}/baca`);
    // Update UI secara reaktif
    pengumuman.telah_dibaca = true;
  } catch (err) {
    console.error("Gagal menandai pengumuman sebagai dibaca:", err);
  }
};

onMounted(() => {
  getPengumumans();
});
</script>

<style scoped lang="postcss">
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.22, 1, 0.36, 1) forwards; }
</style>