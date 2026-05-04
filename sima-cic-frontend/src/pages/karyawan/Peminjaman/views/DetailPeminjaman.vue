<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 transition-colors duration-500 overflow-x-hidden">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(1px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex items-center gap-4">
          <button 
            @click="$router.back()" 
            class="p-3 bg-white/10 backdrop-blur-md rounded-2xl border border-white/20 shadow-xl text-white active:scale-90 transition-all"
          >
            <ChevronLeft class="w-6 h-6" />
          </button>

          <div>
            <p class="text-[10px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-[0.2em]">Otorisasi Aset</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Konfirmasi Pinjam</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <div v-if="cart.items.length === 0" class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-16 text-center shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up">
        <div class="w-20 h-20 bg-amber-500/10 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
          <AlertTriangle class="w-10 h-10 text-amber-500" />
        </div>
        <p class="text-[10px] font-black text-slate-400 capitalize tracking-[0.2em] mb-8">Keranjang Anda Masih Kosong</p>
        <router-link to="/karyawan/inventaris" class="w-full py-4 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 rounded-2xl text-[10px] font-black capitalize tracking-widest block border border-slate-200/50 dark:border-white/5 active:scale-95 transition-all">
          Kembali Ke Katalog
        </router-link>
      </div>

      <template v-else>
        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up">
          <div class="flex items-center gap-3 mb-6 px-2">
            <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
            <h2 class="text-[10px] font-medium text-slate-400 capitalize tracking-widest">Daftar Aset Dipilih</h2>
          </div>
          
          <ul class="space-y-3">
            <li v-for="item in cart.items" :key="item.id" class="flex items-center gap-4 bg-slate-50 dark:bg-white/5 p-3 rounded-[2rem] border border-slate-100 dark:border-white/5">
              <div class="w-14 h-14 bg-white dark:bg-white/10 rounded-2xl overflow-hidden flex-shrink-0 border border-slate-100 dark:border-white/5 shadow-sm">
                <img 
                  :src="getPhotoUrl(item.foto_barang)" 
                  class="w-full h-full object-cover"
                  @error="(e) => (e.target.src = '/img/default-inventaris.png')"
                />
              </div>

              <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-slate-800 dark:text-white truncate capitalize">{{ item.nama_barang }}</p>
                <p class="text-[9px] text-slate-400 font-black capitalize tracking-tighter mt-0.5">S K U : {{ item.kode_barang }}</p>
              </div>

              <div class="pr-2">
                <span class="text-[10px] font-black text-emerald-500 bg-emerald-500/10 px-3 py-1.5 rounded-xl border border-emerald-500/20 capitalize">
                  {{ item.quantity_pinjam || 1 }} Unit
                </span>
              </div>
            </li>
          </ul>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[3rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up" style="animation-delay: 100ms">
          <form @submit.prevent="submit" class="space-y-6">
            
            <div class="flex items-center gap-3 mb-2 px-1">
              <CalendarRange class="w-5 h-5 text-emerald-500" />
              <h2 class="text-[10px] font-medium text-slate-400 capitalize tracking-widest">Periode Peminjaman</h2>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div class="space-y-2">
                <label class="text-[9px] font-black text-slate-400 capitalize tracking-widest ml-2">Mulai</label>
                <input type="date" v-model="tanggalMulai" :min="todayDate" class="input-cic font-bold" required />
              </div>
              <div class="space-y-2">
                <label class="text-[9px] font-black text-slate-400 capitalize tracking-widest ml-2">Selesai</label>
                <input type="date" v-model="tanggalSelesai" :min="tanggalMulai || todayDate" class="input-cic font-bold" required />
              </div>
            </div>

            <div class="space-y-2">
              <label class="text-[9px] font-black text-slate-400 capitalize tracking-widest ml-2">Keperluan / Alasan</label>
              <textarea v-model="keterangan" class="input-cic min-h-[140px] py-4 resize-none leading-relaxed font-medium" placeholder="Tuliskan alasan peminjaman anda secara detail di sini..." required></textarea>
            </div>

            <div v-if="apiError" class="p-4 bg-rose-500/10 rounded-2xl border border-rose-500/20 text-[10px] text-rose-500 font-black tracking-wide leading-relaxed capitalize">
              <AlertCircle class="w-4 h-4 inline mr-2 mb-0.5" /> {{ apiError }}
            </div>

            <div class="pt-4 space-y-4">
              <button
                type="submit"
                :disabled="loading || !isDateValid"
                class="w-full py-5 bg-[#1e332a] text-white rounded-[2rem] font-black text-[11px] capitalize tracking-[0.2em] shadow-xl shadow-emerald-900/20 flex items-center justify-center gap-4 active:scale-95 transition-all border border-white/10 disabled:opacity-50"
              >
                <Loader2 v-if="loading" class="w-5 h-5 animate-spin text-emerald-400" />
                <Send v-else class="w-4 h-4 text-emerald-400" />
                <span>{{ loading ? "Sedang Memproses..." : "Kirim Pengajuan" }}</span>
              </button>

              <button 
                type="button"
                @click="$router.push('/karyawan/peminjaman/keranjang')" 
                class="w-full text-[10px] font-black text-slate-400 capitalize tracking-[0.3em] active:opacity-60 transition-all text-center"
              >
                ← Edit Daftar Aset
              </button>
            </div>
          </form>
        </div>
      </template>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-widest capitalize">Ciwangun Indah Camp</p>
    </footer>
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
        for (const item of cart.items) {
            try {
                await api.post('/karyawan/peminjaman', {
                    inventaris_id: item.id,
                    quantity: item.quantity_pinjam || 1,
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
            apiError.value = `Gagal Untuk: ${failedItems.join(', ')}. Harap Hubungi Admin.`;
        }
    } catch (err) {
        apiError.value = err.response?.data?.message || "Gagal Memproses Pengajuan.";
    } finally {
        loading.value = false;
    }
};
</script>

<style scoped>
.input-cic {
    @apply w-full bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
           rounded-[2rem] px-6 py-5 text-[12px] outline-none transition-all dark:text-white
           focus:ring-4 focus:ring-emerald-500/5 focus:border-emerald-500/50;
}

.animate-fade-in-up {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

* { -webkit-tap-highlight-color: transparent; }
</style>