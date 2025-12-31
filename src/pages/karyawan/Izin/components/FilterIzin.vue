<template>
  <div class="relative">
    <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
      <button
        v-for="s in statusList"
        :key="s.value"
        @click="apply(s.value)"
        :class="[
          'flex-shrink-0 px-6 py-2.5 rounded-2xl text-[11px] font-bold uppercase tracking-widest transition-all duration-300 border',
          selected === s.value
            ? 'bg-[#2d4a3e] text-white border-[#2d4a3e] shadow-md shadow-emerald-900/20'
            : 'bg-white dark:bg-[#121512] text-slate-400 border-slate-100 dark:border-white/5 hover:border-emerald-200'
        ]"
      >
        <div class="flex items-center gap-2">
          <span 
            v-if="s.value" 
            class="w-1.5 h-1.5 rounded-full"
            :class="dotColor(s.value)"
          ></span>
          {{ s.label }}
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(["filter"]);

const selected = ref("");

const statusList = [
  { label: "Semua", value: "" },
  { label: "Pending", value: "pending" },
  { label: "Disetujui", value: "disetujui" },
  { label: "Ditolak", value: "ditolak" },
];

const dotColor = (status) => {
  switch (status) {
    case 'disetujui': return 'bg-emerald-500';
    case 'ditolak': return 'bg-rose-500';
    case 'pending': return 'bg-amber-500';
    default: return 'bg-slate-300';
  }
};

const apply = (status) => {
  selected.value = status;
  emit("filter", status);
};
</script>

<style scoped>
/* Menghilangkan scrollbar tapi fungsi scroll tetap aktif */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

/* Memastikan tombol tidak gepeng saat scroll */
button {
  white-space: nowrap;
}
</style>