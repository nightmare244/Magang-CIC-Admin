<template>
  <div class="relative w-full">
    <div class="flex gap-3 overflow-x-auto pb-4 no-scrollbar scroll-smooth px-1">
      <button
        v-for="status in statusOptions"
        :key="status.value"
        @click="selectStatus(status.value)"
        :class="[
          'filter-pill-btn group transition-all duration-500',
          selected === status.value
            ? 'filter-active'
            : 'filter-inactive'
        ]"
      >
        <div class="flex items-center gap-2.5">
          <span 
            v-if="status.value !== ''" 
            class="w-1.5 h-1.5 rounded-full transition-transform duration-300 group-hover:scale-125"
            :class="dotColor(status.value)"
          ></span>
          
          <span class="text-[10px] font-black capitalize tracking-[0.1em] whitespace-nowrap">
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
  { label: "Semua Aset", value: "" },
  { label: "Pending", value: "pending" },
  { label: "Disetujui", value: "disetujui" },
  { label: "Ditolak", value: "ditolak" },
  { label: "Selesai", value: "selesai" },
];

const dotColor = (status) => {
  switch (status) {
    case 'disetujui': return 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.6)]';
    case 'ditolak': return 'bg-rose-500 shadow-[0_0_8_rgba(244,63,94,0.6)]';
    case 'pending': return 'bg-amber-500 shadow-[0_0_8px_rgba(245,158,11,0.6)]';
    case 'selesai': return 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.6)]';
    default: return 'bg-slate-300';
  }
};

const selectStatus = (val) => {
  selected.value = val;
  emit("filter", val);
};
</script>

<style scoped>
/* button base style */
.filter-pill-btn {
  @apply flex-shrink-0 px-6 py-3.5 rounded-2xl border transition-all duration-300;
}

/* active state: mengikuti gaya portal izin (emerald gelap) */
.filter-active {
  @apply bg-[#1e332a] text-white border-[#1e332a] shadow-lg shadow-emerald-900/30 scale-[1.05] z-10;
}

/* inactive state: bersih dan minimalis */
.filter-inactive {
  @apply bg-white dark:bg-[#111311] text-slate-400 border-slate-100 dark:border-white/5 
         hover:border-emerald-500/30 hover:text-emerald-500;
}

/* utilitas capitalize */
.capitalize {
  text-transform: capitalize;
}

/* sembunyikan scrollbar */
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

* {
  -webkit-tap-highlight-color: transparent;
}
</style>