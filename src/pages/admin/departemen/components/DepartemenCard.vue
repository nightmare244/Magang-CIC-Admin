<template>
  <div class="kpi-card-inner group" @click="$emit('detail')">
    
    <div class="absolute top-0 left-0 w-full h-1 bg-[#2d4a3e] opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>

    <div class="flex items-start justify-between p-6 pb-0">
      <div class="flex-grow">
        <h2 class="font-bold text-lg text-slate-800 dark:text-white group-hover:text-[#2d4a3e] dark:group-hover:text-emerald-500 transition-colors duration-200 leading-tight">
          {{ departemen.nama_departemen }}
        </h2>
        <p class="kpi-label-small">Divisi Operasional CIC</p>
      </div>
      <div class="p-2.5 bg-slate-50 dark:bg-[#0a0c0a] rounded-xl border border-slate-100 dark:border-slate-800 group-hover:bg-[#2d4a3e]/10 transition-colors">
        <Building2 class="w-5 h-5 text-[#2d4a3e] dark:text-emerald-500" />
      </div>
    </div>

    <div class="px-6 mt-4 min-h-[48px]">
      <p class="text-slate-500 dark:text-slate-400 text-xs leading-relaxed line-clamp-2 italic font-medium">
        "{{ departemen.deskripsi || 'Sistem belum mencatat deskripsi khusus untuk unit departemen ini.' }}"
      </p>
    </div>

    <div class="px-6 flex items-center justify-between mt-6">
      <div v-if="departemen.users_count !== undefined" class="flex items-center">
        <div class="flex -space-x-2 mr-3">
          <div v-for="i in 2" :key="i" class="w-7 h-7 rounded-full border-2 border-white dark:border-[#121512] bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
            <User class="w-3 h-3 text-slate-400" />
          </div>
          <div class="w-7 h-7 rounded-full border-2 border-white dark:border-[#121512] bg-[#2d4a3e] flex items-center justify-center text-[9px] font-bold text-white shadow-sm">
            +
          </div>
        </div>
        <span class="text-[10px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
          {{ departemen.users_count }} Personel
        </span>
      </div>
    </div>

    <div class="flex justify-end gap-2 mt-6 p-4 bg-slate-50/50 dark:bg-white/[0.02] border-t border-slate-100 dark:border-gray-800">
      <button 
        class="action-btn-eco group/edit"
        @click.stop="$emit('edit', departemen.id)"
        title="Edit Departemen"
      >
        <Edit3 class="w-4 h-4 text-sky-600 group-hover/edit:scale-110 transition-transform" />
      </button>

      <button 
        class="action-btn-eco group/del"
        @click.stop="$emit('delete', departemen.id)"
        title="Hapus Departemen"
      >
        <Trash2 class="w-4 h-4 text-rose-500 group-hover/del:scale-110 transition-transform" />
      </button>
    </div>
  </div>
</template>

<script setup>
import { Building2, User, Edit3, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  departemen: {
    type: Object,
    required: true
  }
});

defineEmits(['detail', 'edit', 'delete']);
</script>

<style scoped lang="postcss">
/* Font Poppins Integration */
.font-poppins {
  font-family: 'Poppins', sans-serif;
}

.kpi-card-inner {
  @apply relative flex flex-col justify-between bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 
         dark:border-gray-800 shadow-sm transition-all duration-500 cursor-pointer overflow-hidden font-poppins;
}

.kpi-label-small {
  @apply text-[9px] font-bold text-slate-400 uppercase tracking-[0.2em] mt-1;
}

.action-btn-eco {
  @apply p-2.5 rounded-xl bg-white dark:bg-[#1a1d19] border border-slate-200 dark:border-slate-800 
         hover:shadow-md transition-all duration-200 active:scale-90 flex items-center justify-center;
}

/* Custom Line Clamp Logic */
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>