<template>
  <div class="min-h-screen bg-[#F9FBFC] dark:bg-[#0a0c0a] font-poppins pb-32 overflow-x-hidden">
    <header class="bg-[#2d4a3e] pt-16 pb-28 px-8 rounded-b-[4rem] shadow-2xl text-white relative">
      <div class="relative z-10 flex items-center justify-between">
        <button
          @click="$router.back()"
          class="w-12 h-12 flex items-center justify-center bg-white/10 backdrop-blur-md rounded-2xl border border-white/20"
        >
          <ChevronLeft class="w-6 h-6" />
        </button>
        <h1 class="text-xl font-bold">Keranjang Pinjam</h1>
        <div class="w-12"></div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-16 relative z-20 space-y-6">

      <!-- KERANJANG KOSONG -->
      <div
        v-if="cart.items.length === 0"
        class="bg-white dark:bg-[#121512] rounded-[3rem] p-12 text-center shadow-xl border border-dashed border-slate-200"
      >
        <ShoppingBag class="w-16 h-16 text-slate-200 mx-auto mb-4" />
        <p class="text-sm font-bold text-slate-700">Keranjang Kosong</p>
        <router-link
          to="/karyawan/inventaris"
          class="btn-cic-secondary mt-6 block"
        >
          KEMBALI KE KATALOG
        </router-link>
      </div>

      <!-- LIST ITEM -->
      <div v-else class="space-y-4 animate-fade-in-up">
        <div
          v-for="item in normalizedItems"
          :key="item.id"
          class="bg-white dark:bg-[#121512] rounded-[2.5rem] p-4 shadow-xl flex items-center gap-4"
        >
          <div class="w-20 h-20 bg-slate-100 rounded-3xl overflow-hidden flex-shrink-0">
            <img
              :src="getPhotoUrl(item.foto_barang)"
              class="w-full h-full object-cover"
              @error="(e) => (e.target.src = '/img/default-inventaris.png')"
            />
          </div>

          <div class="flex-grow min-w-0">
            <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mb-1">
              {{ item.kode_barang }}
            </p>
            <h2 class="text-[14px] font-bold text-slate-800 dark:text-white truncate">
              {{ item.nama_barang }}
            </h2>

            <div class="flex items-center gap-2 mt-2">
              <span class="text-[10px] font-black text-slate-400 uppercase tracking-widest">
                Dipilih:
              </span>
              <span
                class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-lg text-xs font-black border border-emerald-100"
              >
                {{ item.quantity_pinjam }} Unit
              </span>
            </div>
          </div>

          <button
            @click="cart.removeItem(item.id)"
            class="w-10 h-10 flex items-center justify-center bg-rose-50 text-rose-500 rounded-2xl"
          >
            <Trash2 class="w-4 h-4" />
          </button>
        </div>

        <!-- FOOTER KERANJANG -->
        <div class="bg-white dark:bg-[#121512] rounded-[3rem] p-8 shadow-2xl mt-8 space-y-6 text-center">
          <div class="flex justify-between items-center px-2">
            <p class="text-[10px] font-black text-slate-400 uppercase tracking-[0.3em]">
              Total Aset
            </p>
            <p class="text-lg font-black text-slate-800 dark:text-white">
              {{ normalizedItems.length }} Item
            </p>
          </div>

          <router-link
            to="/karyawan/peminjaman/checkout"
            class="btn-cic-primary w-full py-5 block"
          >
            LANJUTKAN KONFIRMASI
          </router-link>

          <button
            @click="cart.clearCart()"
            class="w-full text-[10px] font-black text-rose-400 uppercase tracking-[0.3em]"
          >
            Bersihkan Keranjang
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { useKeranjangStore } from '@/stores/keranjangStore';
import { ChevronLeft, ShoppingBag, Trash2 } from 'lucide-vue-next';

const cart = useKeranjangStore();
const baseUrl = import.meta.env.VITE_API_URL || 'http://127.0.0.1:8000';

/**
 * 🔐 NORMALISASI DATA
 * Pastikan quantity_pinjam TIDAK PERNAH undefined di UI
 */
const normalizedItems = computed(() =>
  cart.items.map(item => ({
    ...item,
    quantity_pinjam: item.quantity_pinjam ?? 1,
  }))
);

function getPhotoUrl(path) {
  if (!path) return '/img/default-inventaris.png';
  const cleanPath = path
    .replace(/^\/?storage\//i, '')
    .replace(/^\/?public\//i, '');
  return `${baseUrl.replace(/\/$/, '')}/storage/${cleanPath}`;
}
</script>

<style scoped lang="postcss">
.btn-cic-primary {
  @apply bg-[#2d4a3e] text-white rounded-[1.5rem] font-black text-[11px]
         uppercase tracking-[0.2em] shadow-2xl active:scale-95 transition-all;
}
.btn-cic-secondary {
  @apply bg-slate-50 text-slate-500 rounded-[1.5rem] py-4 text-[10px]
         font-black uppercase tracking-widest border border-slate-100;
}
.animate-fade-in-up {
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
@keyframes fadeInUp {
  from {
    opacity: 0;
    transform: translateY(40px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}
</style>
