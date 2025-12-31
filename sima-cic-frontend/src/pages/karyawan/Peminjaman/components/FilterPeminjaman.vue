<template>
  <div class="relative w-full">
    <div class="flex gap-3 overflow-x-auto pb-2 no-scrollbar scroll-smooth">
      <button
        v-for="status in statusOptions"
        :key="status.value"
        @click="selectStatus(status.value)"
        :class="[
          'filter-pill-btn group',
          selected === status.value
            ? 'filter-active'
            : 'filter-inactive'
        ]"
      >
        <div class="flex items-center gap-2">
          <span 
            v-if="status.value !== ''" 
            class="w-1.5 h-1.5 rounded-full transition-transform group-hover:scale-125"
            :class="dotColor(status.value)"
          ></span>
          
          <span class="text-[10px] font-bold uppercase tracking-[0.15em] whitespace-nowrap">
            {{ status.label }}
          </span>
        </div>
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref } from "vue";

const emit = defineEmits(["filter"]);

const selected = ref("");

const statusOptions = [
  { label: "Semua", value: "" },
  { label: "Pending", value: "pending" },
  { label: "Disetujui", value: "disetujui" },
  { label: "Ditolak", value: "ditolak" },
  { label: "Selesai", value: "selesai" },
];

const dotColor = (status) => {
  switch (status) {
    case 'disetujui': return 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]';
    case 'ditolak': return 'bg-rose-500 shadow-[0_0_8px_rgba(244,63,94,0.5)]';
    case 'pending': return 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.5)]';
    case 'selesai': return 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.5)]';
    default: return 'bg-slate-300';
  }
};

const selectStatus = (val) => {
  selected.value = val;
  emit("filter", val);
};
</script>

<style scoped lang="postcss">
.filter-pill-btn {
  @apply flex-shrink-0 px-6 py-3 rounded-2xl transition-all duration-300 border;
}

.filter-active {
  @apply bg-[#2d4a3e] text-white border-[#2d4a3e] shadow-lg shadow-emerald-900/20 scale-[1.02];
}

.filter-inactive {
  @apply bg-white dark:bg-[#121512] text-slate-400 border-slate-100 dark:border-white/5 
         hover:border-emerald-200 hover:text-emerald-600;
}

/* Sembunyikan Scrollbar untuk Chrome, Safari dan Opera */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}

/* Sembunyikan Scrollbar untuk IE, Edge dan Firefox */
.no-scrollbar {
  -ms-overflow-style: none;  /* IE and Edge */
  scrollbar-width: none;  /* Firefox */
}
</style>