<template>
  <div 
    class="card-cic-inventaris group" 
    @click="$router.push(`/karyawan/inventaris/${item.kode_barang}`)"
  >
    <div class="flex items-center gap-4">
      <div class="relative flex-shrink-0">
        <img 
          :src="imageUrl(item.foto_barang)" 
          alt="Foto Barang"
          class="w-16 h-16 rounded-[1.2rem] object-cover border border-slate-100 dark:border-white/5 shadow-sm"
          @error="(e) => (e.target.src = '/img/default-inventaris.png')"
        />
        <div 
          class="absolute -top-1 -right-1 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-[#111311]"
          :class="item.status_ketersediaan === 'tersedia' ? 'bg-emerald-500' : 'bg-amber-500'"
        ></div>
      </div>

      <div class="flex-1 overflow-hidden">
        <div class="flex flex-col">
          <p class="text-[9px] font-bold text-emerald-600/70 dark:text-emerald-500 tracking-[0.2em] mb-1 capitalize">
            Sku: {{ item.kode_barang }}
          </p>
          <h2 class="font-bold text-[14px] text-slate-800 dark:text-white truncate leading-tight capitalize">
            {{ item.nama_barang }}
          </h2>
          
          <div class="flex items-center gap-2 mt-2">
            <span 
              :class="badgeClass(item.status_ketersediaan)" 
              class="text-[9px] px-3 py-1 rounded-xl font-bold border tracking-widest capitalize transition-colors duration-300"
            >
              {{ item.status_ketersediaan.replace('_', ' ') }}
            </span>
          </div>
        </div>
      </div>

      <div class="text-slate-300 group-hover:text-emerald-500 transition-all duration-300 pr-2 group-hover:translate-x-1">
        <ChevronRight class="w-5 h-5" />
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue';
import { ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  item: { type: Object, required: true },
});

const baseUrl = computed(() => {
    const url = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
    return url.replace(/\/$/, ""); 
});

const imageUrl = (path) => {
    if (!path) return '/img/default-inventaris.png';
    const cleanPath = path.replace(/^\/?storage\//i, '');
    return `${baseUrl.value}/storage/${cleanPath}`;
};

function badgeClass(status) {
    const lowerStatus = status ? status.toLowerCase() : 'tidak_tersedia';
    return {
      'tersedia': 'bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20',
      'dipinjam': 'bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20',
      'tidak_tersedia': 'bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20',
    }[lowerStatus];
}
</script>

<style scoped lang="postcss">
.card-cic-inventaris {
    /* tata letak kartu menggunakan rounded besar sesuai acuan */
    @apply bg-white dark:bg-[#111311] p-4 rounded-[2rem] shadow-sm border border-slate-100 
           dark:border-white/5 transition-all duration-500 cursor-pointer
           active:scale-[0.97] hover:shadow-md hover:border-emerald-100 dark:hover:border-emerald-500/20;
}

.truncate {
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>