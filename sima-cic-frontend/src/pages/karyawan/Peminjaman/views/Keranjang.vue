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
            <p class="text-[10px] font-medium text-emerald-400/90 leading-none mb-1 capitalize tracking-[0.2em]">Inventaris Sistem</p>
            <h1 class="text-xl font-bold tracking-tight text-white capitalize">Keranjang Pinjam</h1>
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">

      <div
        v-if="cart.items.length === 0"
        class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-16 text-center shadow-sm border border-slate-100 dark:border-white/5 animate-fade-in-up"
      >
        <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-[2.5rem] flex items-center justify-center mx-auto mb-6">
          <ShoppingBag class="w-10 h-10 text-slate-200 dark:text-slate-700" />
        </div>
        <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-8">Keranjang Anda Kosong</p>
        <router-link
          to="/karyawan/inventaris"
          class="w-full py-4 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 rounded-2xl text-[10px] font-black uppercase tracking-widest block border border-slate-200/50 dark:border-white/5 active:scale-95 transition-all"
        >
          Kembali Ke Katalog
        </router-link>
      </div>

      <div v-else class="space-y-4 animate-fade-in-up">
        <div
          v-for="item in normalizedItems"
          :key="item.id"
          class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-4 shadow-sm border border-slate-100 dark:border-white/5 flex items-center gap-4"
        >
          <div class="w-20 h-20 bg-slate-50 dark:bg-white/5 rounded-3xl overflow-hidden flex-shrink-0 border border-slate-100 dark:border-white/5">
            <img
              :src="getPhotoUrl(item.foto_barang)"
              class="w-full h-full object-cover"
              @error="(e) => (e.target.src = '/img/default-inventaris.png')"
            />
          </div>

          <div class="flex-grow min-w-0">
            <p class="text-[9px] font-black text-emerald-500 uppercase tracking-widest mb-1">
              {{ item.kode_barang }}
            </p>
            <h2 class="text-[14px] font-bold text-slate-800 dark:text-white truncate capitalize leading-tight">
              {{ item.nama_barang }}
            </h2>

            <div class="flex items-center gap-2 mt-2">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Dipilih:</span>
              <span class="px-3 py-1 bg-emerald-500/10 text-emerald-500 rounded-lg text-[10px] font-black border border-emerald-500/20 uppercase">
                {{ item.quantity_pinjam }} Unit
              </span>
            </div>
          </div>

          <button
            @click="cart.removeItem(item.id)"
            class="w-11 h-11 flex items-center justify-center bg-rose-500/10 text-rose-500 rounded-2xl active:scale-90 transition-all border border-rose-500/10"
          >
            <Trash2 class="w-5 h-5" />
          </button>
        </div>

        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-sm border border-slate-100 dark:border-white/5 space-y-6">
          <div class="flex justify-between items-center px-2">
            <div>
              <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1">Total Aset</p>
              <div class="w-8 h-1 bg-emerald-500 rounded-full"></div>
            </div>
            <p class="text-xl font-black text-slate-800 dark:text-white capitalize">
              {{ normalizedItems.length }} Item
            </p>
          </div>

          <div class="space-y-3">
            <router-link
              to="/karyawan/peminjaman/checkout"
              class="w-full py-5 bg-[#1e332a] text-white rounded-[2rem] font-black text-[11px] uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20 flex items-center justify-center gap-3 active:scale-95 transition-all border border-white/10"
            >
              <CheckCircle2 class="w-5 h-5 text-emerald-400" />
              <span>Lanjutkan Konfirmasi</span>
            </router-link>

            <button
              @click="cart.clearCart()"
              class="w-full py-4 text-[10px] font-black text-rose-400 uppercase tracking-[0.3em] active:opacity-60 transition-all"
            >
              Bersihkan Keranjang
            </button>
          </div>
        </div>
      </div>
    </div>

    <footer class="pt-10 pb-6 text-center">
      <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-widest capitalize">Ciwangun Indah Camp</p>
    </footer>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useKeranjangStore } from '@/stores/keranjangStore';
import { ChevronLeft, ShoppingBag, Trash2, CheckCircle2 } from 'lucide-vue-next';

const cart = useKeranjangStore();
const baseUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';

const normalizedItems = computed(() =>
  cart.items.map(item => ({
    ...item,
    quantity_pinjam: item.quantity_pinjam ?? 1,
  }))
);

function getPhotoUrl(path) {
  if (!path) return '/img/default-inventaris.png';
  const cleanPath = path.replace(/^\/?storage\//i, '').replace(/^\/?public\//i, '');
  return `${baseUrl.replace(/\/$/, '')}/storage/${cleanPath}`;
}
</script>

<style scoped>
.animate-fade-in-up {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}
* { -webkit-tap-highlight-color: transparent; }
</style>