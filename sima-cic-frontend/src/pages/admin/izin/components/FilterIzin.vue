<template>
  <div class="flex flex-wrap items-center gap-3 bg-white dark:bg-[#121512] p-3 rounded-2xl border border-gray-100 dark:border-gray-800 shadow-sm font-poppins">
    <div class="flex items-center gap-2 mr-2">
      <Filter class="w-4 h-4 text-[#2d4a3e] dark:text-emerald-500" />
      <span class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Status:</span>
    </div>

    <div class="flex flex-wrap gap-2">
      <button 
        v-for="opt in options" :key="opt.value"
        @click="selectStatus(opt.value)"
        :class="['chip', active === opt.value ? 'chip-active' : 'chip-inactive']"
      >
        <span v-if="active === opt.value" class="dot"></span>
        {{ opt.label }}
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";
import { Filter } from "lucide-vue-next";

const active = ref("");
const emit = defineEmits(["filter"]);

const options = [
  { label: 'Semua', value: '' },
  { label: 'Pending', value: 'pending' },
  { label: 'Setuju', value: 'disetujui' },
  { label: 'Tolak', value: 'ditolak' },
];

const selectStatus = (v) => {
  active.value = v;
  emit("filter", v);
};
</script>

<style scoped lang="postcss">
.chip {
  @apply px-4 py-1.5 rounded-xl text-[9px] font-bold uppercase tracking-widest transition-all flex items-center border;
}
.chip-inactive {
  @apply bg-slate-50 dark:bg-[#1a1d19] text-slate-400 border-slate-100 dark:border-slate-800 hover:text-[#2d4a3e];
}
.chip-active {
  @apply bg-[#2d4a3e] text-white border-[#2d4a3e] shadow-md scale-105;
}
.dot {
  @apply w-1 h-1 rounded-full bg-white mr-2 animate-pulse;
}
</style>