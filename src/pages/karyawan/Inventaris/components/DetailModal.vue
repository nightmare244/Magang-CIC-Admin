<template>
  <div 
    v-if="item && isVisible" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/60 backdrop-blur-sm transition-all duration-300 animate-fade-in"
  >
    <div class="bg-white dark:bg-[#121512] w-full max-w-md rounded-[3rem] shadow-2xl border border-white dark:border-white/5 overflow-hidden animate-scale-up">
      
      <div class="relative h-48 w-full bg-slate-100 dark:bg-white/5">
        <img
          :src="item.foto_barang_url || '/img/default-inventaris.png'"
          alt="Foto Barang"
          class="w-full h-full object-cover"
          @error="(e) => (e.target.src = '/img/default-inventaris.png')"
        />
        <button 
          @click="closeModal" 
          class="absolute top-4 right-4 p-2 bg-black/20 hover:bg-black/40 backdrop-blur-md rounded-full text-white transition-all"
        >
          <X class="w-5 h-5" />
        </button>
        <div class="absolute bottom-4 left-4">
          <span :class="badgeClass(item.status_ketersediaan)" class="px-4 py-1.5 rounded-full text-[10px] font-bold uppercase tracking-widest border shadow-lg">
            {{ item.status_ketersediaan.replace('_', ' ') }}
          </span>
        </div>
      </div>

      <div class="p-8 font-poppins">
        <div class="mb-6 text-left">
          <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 uppercase tracking-[0.2em] mb-1">
            SKU: {{ item.kode_barang }}
          </p>
          <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight">
            {{ item.nama_barang }}
          </h2>
        </div>

        <div class="space-y-4">
          <div>
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-1.5">Deskripsi Barang</p>
            <p class="text-xs text-slate-600 dark:text-slate-400 leading-relaxed font-medium">
              {{ item.deskripsi || 'Tidak ada deskripsi tersedia untuk barang ini.' }}
            </p>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-2">
            <div class="bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-slate-100 dark:border-white/5 text-left">
              <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Stok Tersedia</p>
              <p class="text-sm font-bold text-slate-700 dark:text-white">{{ item.quantity }} Unit</p>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 p-3 rounded-2xl border border-slate-100 dark:border-white/5 text-left">
              <p class="text-[9px] font-bold text-slate-400 uppercase mb-1">Nilai Inventaris</p>
              <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(item.harga_satuan) }}</p>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <button 
            @click="closeModal" 
            class="w-full bg-[#2d4a3e] hover:bg-[#1e332a] text-white py-4 rounded-[2rem] text-xs font-bold uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20 active:scale-95 transition-all"
          >
            Selesai & Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { X } from 'lucide-vue-next';

const props = defineProps({
  item: {
    type: Object,
    required: true,
    default: () => ({})
  },
  isVisible: Boolean,
});

const emit = defineEmits(['close']);

const closeModal = () => emit('close');

const formatCurrency = (value) => {
  const num = Number(value);
  if (isNaN(num)) return 'Rp 0';
  return num.toLocaleString("id-ID", { 
    style: "currency", 
    currency: "IDR", 
    minimumFractionDigits: 0 
  });
}

function badgeClass(status) {
  const lowerStatus = status ? status.toLowerCase() : 'tidak_tersedia';
  return {
    'tersedia': 'bg-emerald-500 text-white border-emerald-400',
    'dipinjam': 'bg-amber-500 text-white border-amber-400',
    'tidak_tersedia': 'bg-rose-500 text-white border-rose-400',
  }[lowerStatus];
}
</script>

<style scoped lang="postcss">
@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.3s ease-out; }

@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-scale-up { animation: scaleUp 0.4s cubic-bezier(0.17, 0.67, 0.83, 0.67) forwards; }

.font-poppins { font-family: 'Poppins', sans-serif; }
</style>