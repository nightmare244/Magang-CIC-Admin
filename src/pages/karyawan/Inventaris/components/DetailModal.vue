<template>
  <div 
    v-if="item && isVisible" 
    class="fixed inset-0 z-[100] flex items-center justify-center p-6 bg-slate-900/80 backdrop-blur-md transition-all duration-300 animate-fade-in"
  >
    <div class="bg-white dark:bg-[#111311] w-full max-w-md rounded-[3rem] shadow-2xl border border-white dark:border-white/5 overflow-hidden animate-scale-up">
      
      <div class="relative h-56 w-full bg-slate-100 dark:bg-white/5">
        <img
          :src="item.foto_barang_url || '/img/default-inventaris.png'"
          alt="Foto Barang"
          class="w-full h-full object-cover"
          @error="(e) => (e.target.src = '/img/default-inventaris.png')"
        />
        <button 
          @click="closeModal" 
          class="absolute top-5 right-5 p-2.5 bg-black/20 hover:bg-black/40 backdrop-blur-md rounded-2xl text-white transition-all active:scale-90"
        >
          <X class="w-5 h-5" />
        </button>
        <div class="absolute bottom-5 left-5">
          <span :class="badgeClass(item.status_ketersediaan)" class="px-4 py-2 rounded-xl text-[9px] font-bold tracking-[0.2em] border shadow-xl capitalize backdrop-blur-md">
            {{ item.status_ketersediaan ? item.status_ketersediaan.replace('_', ' ') : 'Status' }}
          </span>
        </div>
      </div>

      <div class="p-8">
        <div class="mb-6 text-left">
          <p class="text-[10px] font-bold text-emerald-600 dark:text-emerald-500 tracking-[0.3em] mb-2 capitalize">
            Sku: {{ item.kode_barang }}
          </p>
          <h2 class="text-2xl font-bold text-slate-800 dark:text-white leading-tight capitalize">
            {{ item.nama_barang }}
          </h2>
        </div>

        <div class="space-y-5">
          <div>
            <p class="text-[10px] font-bold text-slate-400 tracking-[0.2em] mb-2 capitalize">Deskripsi Teknis</p>
            <p class="text-[12px] text-slate-500 dark:text-slate-400 leading-relaxed font-medium italic bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-50 dark:border-white/5">
              "{{ item.deskripsi || 'Tidak Ada Deskripsi Spesifik Untuk Aset Ini.' }}"
            </p>
          </div>

          <div class="grid grid-cols-2 gap-4 pt-2">
            <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-[1.5rem] border border-slate-100 dark:border-white/5 text-left">
              <p class="text-[9px] font-bold text-slate-400 tracking-widest mb-1 capitalize">Stok Aset</p>
              <p class="text-sm font-bold text-slate-800 dark:text-white capitalize">{{ item.quantity }} Unit</p>
            </div>
            <div class="bg-slate-50 dark:bg-white/5 p-4 rounded-[1.5rem] border border-slate-100 dark:border-white/5 text-left">
              <p class="text-[9px] font-bold text-slate-400 tracking-widest mb-1 capitalize">Nilai Administrasi</p>
              <p class="text-sm font-bold text-emerald-600 dark:text-emerald-400">{{ formatCurrency(item.harga_satuan) }}</p>
            </div>
          </div>
        </div>

        <div class="mt-8">
          <button 
            @click="closeModal" 
            class="btn-cic-modal w-full py-5 shadow-emerald-900/20 shadow-xl"
          >
            <span class="capitalize">Selesai & Tutup</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { X } from 'lucide-vue-next';

const props = defineProps({
  item: { type: Object, required: true, default: () => ({}) },
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
  if (lowerStatus === 'tersedia') return 'bg-emerald-500 text-white border-emerald-400';
  if (lowerStatus === 'dipinjam') return 'bg-amber-500 text-white border-amber-400';
  return 'bg-rose-500 text-white border-rose-400';
}
</script>

<style scoped lang="postcss">
.btn-cic-modal {
  @apply bg-[#1e332a] text-white rounded-[1.5rem] font-bold text-[10px] 
         tracking-[0.2em] active:scale-95 transition-all;
}

@keyframes fadeIn {
    from { opacity: 0; }
    to { opacity: 1; }
}
.animate-fade-in { animation: fadeIn 0.4s ease-out forwards; }

@keyframes scaleUp {
    from { transform: scale(0.95); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}
.animate-scale-up { animation: scaleUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }

* { -webkit-tap-highlight-color: transparent; }
</style>