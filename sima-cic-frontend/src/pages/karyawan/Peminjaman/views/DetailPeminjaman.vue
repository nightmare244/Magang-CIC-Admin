<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-28 px-8 rounded-b-[4rem] shadow-2xl text-white relative overflow-hidden">
      <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-500/20 rounded-full blur-[80px]"></div>
      <div class="relative z-10 flex items-center justify-between">
        <button @click="$router.back()" class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-md hover:bg-white/20 rounded-2xl transition-all border border-white/20 shadow-inner">
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold tracking-tight">Konfirmasi Pinjam</h1>
        <div class="w-12"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6">
      
      <div v-if="cart.items.length === 0" class="bg-white dark:bg-[#121512] rounded-[3rem] p-12 text-center shadow-xl border border-dashed border-slate-200">
        <AlertTriangle class="w-16 h-16 text-amber-500 mx-auto mb-4" />
        <p class="text-sm font-bold text-slate-700 dark:text-white mb-6">Keranjang Anda Kosong!</p>
        <router-link to="/karyawan/inventaris" class="btn-cic-secondary block w-full text-center">Kembali ke Katalog</router-link>
      </div>

      <template v-else>
        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5 animate-fade-in-up">
          <div class="flex items-center gap-2 mb-6">
            <Package class="w-5 h-5 text-emerald-600" />
            <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Daftar Aset Dipilih</h2>
          </div>
          
          <ul class="space-y-4">
            <li v-for="item in cart.items" :key="item.id" class="flex items-center gap-4 bg-slate-50 dark:bg-white/5 p-3 rounded-3xl border border-slate-100 dark:border-white/5">
              <div class="w-14 h-14 bg-white dark:bg-white/10 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-200 dark:border-white/5 shadow-sm">
                <img 
                  :src="getPhotoUrl(item.foto_barang)" 
                  class="w-full h-full object-cover"
                  @error="(e) => (e.target.src = '/img/default-inventaris.png')"
                />
              </div>

              <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-slate-800 dark:text-white truncate">{{ item.nama_barang }}</p>
                <p class="text-[9px] text-slate-400 font-black uppercase tracking-tighter">SKU: {{ item.kode_barang }}</p>
              </div>

              <div class="flex flex-col items-end">
                <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-xl border border-emerald-100 dark:border-emerald-500/20">
                  {{ item.quantity_pinjam || 1 }} Unit
                </span>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-xl border border-white dark:border-white/5 animate-fade-in-up" style="animation-delay: 100ms">
          <form @submit.prevent="submit" class="space-y-6">
            
            <div class="flex items-center gap-2 mb-2">
              <CalendarRange class="w-5 h-5 text-emerald-600" />
              <h2 class="text-xs font-black text-slate-400 uppercase tracking-widest">Periode Peminjaman</h2>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Mulai</label>
                <input type="date" v-model="tanggalMulai" :min="todayDate" class="input-cic" required />
              </div>
              <div class="space-y-2">
                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Selesai</label>
                <input type="date" v-model="tanggalSelesai" :min="tanggalMulai || todayDate" class="input-cic" required />
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest ml-1">Keperluan / Alasan</label>
              <textarea v-model="keterangan" class="input-cic min-h-[120px] py-4 resize-none" placeholder="Contoh: Digunakan untuk dokumentasi event outbound di Area A..." required></textarea>
            </div>

            <div v-if="apiError" class="p-4 bg-rose-50 dark:bg-rose-500/10 rounded-2xl border border-rose-100 dark:border-rose-500/20 text-[11px] text-rose-600 font-bold leading-relaxed">
              <AlertCircle class="w-4 h-4 inline mr-1 mb-0.5" /> {{ apiError }}
            </div>

            <div class="pt-4 space-y-4">
              <button
                type="submit"
                :disabled="loading || !isDateValid"
                class="btn-cic-primary w-full py-5 flex items-center justify-center gap-3 shadow-emerald-900/30"
              >
                <Loader2 v-if="loading" class="w-5 h-5 animate-spin" />
                <Send v-else class="w-4 h-4" />
                <span class="tracking-widest">{{ loading ? "MEMPROSES DATA..." : "KIRIM PENGAJUAN" }}</span>
              </button>

              <button 
                type="button"
                @click="$router.push('/karyawan/peminjaman/keranjang')" 
                class="w-full text-[10px] font-black text-slate-400 uppercase tracking-[0.3em] active:scale-95 transition-all"
              >
                ← Edit Daftar Aset
              </button>
            </div>
          </form>
        </div>
      </template>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import { useKeranjangStore } from '@/stores/keranjangStore';
import { 
  ChevronLeft, Package, CalendarRange, 
  Send, Loader2, AlertTriangle, AlertCircle 
} from 'lucide-vue-next';

const router = useRouter();
const cart = useKeranjangStore();

const tanggalMulai = ref(new Date().toISOString().split('T')[0]);
const tanggalSelesai = ref('');
const keterangan = ref('');
const loading = ref(false);
const apiError = ref(null);
const todayDate = new Date().toISOString().split('T')[0];

const baseUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";

/**
 * FUNGSI FOTO: Sinkron dengan Detail dan Keranjang
 */
function getPhotoUrl(path) {
    if (!path) return '/img/default-inventaris.png';
    const cleanPath = path.replace(/^(public\/|storage\/)/i, '').replace(/^\//, '');
    return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
}

const isDateValid = computed(() => {
    return tanggalMulai.value && tanggalSelesai.value && (tanggalSelesai.value >= tanggalMulai.value);
});

const submit = async () => {
    loading.value = true;
    apiError.value = null;
    let failedItems = [];

    try {
        // Loop pengiriman pengajuan tiap aset
        for (const item of cart.items) {
            try {
                await api.post('/karyawan/peminjaman', {
                    inventaris_id: item.id,
                    quantity: item.quantity_pinjam || 1, // Pastikan mengambil jumlah yang dipilih
                    tanggal_mulai: tanggalMulai.value,
                    tanggal_selesai: tanggalSelesai.value,
                    keterangan: keterangan.value
                });
            } catch (itemErr) {
                failedItems.push(item.nama_barang);
            }
        }
        
        if (failedItems.length === 0) {
            cart.clearCart();
            router.push('/karyawan/peminjaman/riwayat');
        } else {
            apiError.value = `Gagal untuk: ${failedItems.join(', ')}. Harap hubungi admin logistik.`;
        }
    } catch (err) {
        apiError.value = err.response?.data?.message || "Gagal memproses pengajuan aset.";
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped lang="postcss">
.input-cic {
    @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
           rounded-[1.5rem] px-5 py-4 text-xs outline-none font-bold 
           focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 transition-all dark:text-white;
}

.btn-cic-primary {
    @apply bg-[#2d4a3e] text-white rounded-[1.5rem] font-black text-[11px] 
           uppercase tracking-[0.2em] shadow-2xl active:scale-[0.97] transition-all duration-300;
}

.btn-cic-secondary {
    @apply bg-slate-100 dark:bg-white/5 text-slate-500 rounded-[1.5rem] py-4 text-[10px] font-black uppercase tracking-widest;
}

@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(40px); }
    to { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
</style>