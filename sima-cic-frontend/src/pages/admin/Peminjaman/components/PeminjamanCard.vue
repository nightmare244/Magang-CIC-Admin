<template>
  <div class="card-eco-premium group font-poppins" @click="navigateToDetail">
    <div class="absolute left-0 top-0 h-full w-1.5 bg-[#2d4a3e] opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
    
    <div class="flex flex-col md:flex-row items-center justify-between gap-6 p-6">
      
      <div class="flex items-center space-x-5 flex-1 w-full md:w-auto">
        <div class="relative flex-shrink-0">
          <div class="w-14 h-14 rounded-2xl bg-[#2d4a3e]/5 dark:bg-emerald-500/5 border-2 border-slate-50 dark:border-slate-800 flex items-center justify-center font-bold text-[#2d4a3e] dark:text-emerald-500 text-xl shadow-inner group-hover:scale-110 transition-transform duration-500">
            {{ getInitials(item.user_name) }}
          </div>
          <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-[#2d4a3e] rounded-lg border-2 border-white dark:border-[#121512] flex items-center justify-center shadow-lg">
              <Box class="w-3 h-3 text-white" />
          </div>
        </div>

        <div class="min-w-0">
          <div class="flex items-center gap-2">
              <span class="font-mono text-[9px] font-black text-emerald-600 dark:text-emerald-400 uppercase tracking-[0.2em] bg-emerald-50 dark:bg-emerald-500/10 px-2 py-0.5 rounded-md">
                  #{{ item.kode_peminjaman || `TRX-${item.id}` }}
              </span>
          </div>
          <h3 class="font-bold text-slate-800 dark:text-white tracking-tight text-lg truncate leading-tight mt-1">
              {{ item.user_name || 'Personal Unknown' }}
          </h3>
          <p class="text-[10px] text-slate-400 font-medium uppercase tracking-widest mt-1 flex items-center gap-1.5">
              <Calendar class="w-3 h-3 opacity-60" />
              Diajukan: {{ formatDate(item.tanggal_pinjam || item.tanggal_mulai) }}
          </p>
        </div>
      </div>

      <div class="hidden md:block flex-1 px-8 border-x border-slate-50 dark:border-white/5">
          <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-2">Target Alokasi Aset</p>
          <div class="flex items-center gap-3">
            <div class="p-2 bg-slate-50 dark:bg-white/5 rounded-xl">
              <Package class="w-4 h-4 text-[#2d4a3e] dark:text-emerald-500" />
            </div>
            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 truncate max-w-[200px]">
                {{ item.inventaris_name || 'Asset Item' }} 
            </p>
            <span class="text-[10px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-2 py-1 rounded-lg">
              {{ item.quantity || 1 }} Unit
            </span>
          </div>
      </div>

      <div class="flex items-center justify-between md:justify-end w-full md:w-auto space-x-6 md:pl-4 border-t md:border-t-0 pt-4 md:pt-0 border-slate-50 dark:border-white/5">
        <StatusBadge :status="item.status" />
        
        <router-link
          :to="`/admin/persetujuan-peminjaman/${item.id}`"
          class="btn-action-nav-eco shadow-lg shadow-[#2d4a3e]/10"
        >
          <ChevronRight class="w-5 h-5" />
        </router-link>
      </div>
    </div>
  </div>
</template>

<script setup>
import { useRouter } from 'vue-router';
import { Box, Calendar, Package, ChevronRight } from 'lucide-vue-next';
import StatusBadge from "./StatusBadge.vue";

const router = useRouter();
const props = defineProps({
  item: {
      type: Object,
      required: true,
      default: () => ({})
  }
});

const getInitials = (name) => {
    if (!name) return '??';
    const parts = name.trim().split(' ');
    return parts.length > 1 
        ? (parts[0][0] + parts[parts.length - 1][0]).toUpperCase() 
        : parts[0][0].toUpperCase();
};

const navigateToDetail = () => {
    router.push(`/admin/persetujuan-peminjaman/${props.item.id}`);
};

const formatDate = (d) => {
    if (!d) return '-';
    const date = new Date(d);
    return date.toLocaleDateString("id-ID", {
        day: "2-digit",
        month: "short",
        year: "numeric"
    });
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco-premium {
    @apply relative bg-white dark:bg-[#121512] shadow-sm rounded-[1.8rem] border border-gray-100 dark:border-gray-800
           transition-all duration-500 cursor-pointer mb-4
           hover:shadow-2xl hover:shadow-[#2d4a3e]/10 hover:-translate-y-1 overflow-hidden;
}

.btn-action-nav-eco {
    @apply p-3 bg-slate-50 dark:bg-[#1a1d19] text-slate-400 rounded-2xl 
           group-hover:bg-[#2d4a3e] group-hover:text-white transition-all duration-500 active:scale-90;
}

/* Custom truncated line clamp for asset name if needed */
.truncate {
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
</style>