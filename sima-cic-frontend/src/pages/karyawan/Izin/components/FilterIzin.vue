<template>
  <div class="relative py-2 font-poppins">
    <div class="flex gap-2 p-1.5 overflow-x-auto no-scrollbar scroll-smooth bg-slate-100/40 dark:bg-white/5 rounded-[2rem] border border-slate-200/40 dark:border-white/5">
      <button
        v-for="s in statusList"
        :key="s.value"
        @click="apply(s.value)"
        :class="[
          'relative flex-shrink-0 px-5 py-2.5 rounded-[1.5rem] transition-all duration-500 ease-out',
          selected === s.value
            ? 'bg-white dark:bg-[#1e332a] shadow-sm dark:shadow-emerald-900/20 scale-[1.02] z-10'
            : 'hover:bg-white/50 dark:hover:bg-white/5'
        ]"
      >
        <div class="flex items-center gap-2.5 relative z-10">
          <div v-if="s.value" class="relative flex items-center justify-center">
            <span 
              class="w-1.5 h-1.5 rounded-full"
              :class="dotColor(s.value)"
            ></span>
            <span 
              v-if="selected === s.value"
              class="absolute w-3.5 h-3.5 rounded-full animate-ping opacity-25"
              :class="dotColor(s.value)"
            ></span>
          </div>

          <span 
            :class="[
              'text-[10px] font-bold capitalize tracking-wider transition-colors duration-300',
              selected === s.value 
                ? 'text-[#1e332a] dark:text-emerald-400' 
                : 'text-slate-400 dark:text-slate-500'
            ]"
          >
            {{ s.label }}
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

const statusList = [
  { label: "semua", value: "" },
  { label: "pending", value: "pending" },
  { label: "setuju", value: "disetujui" },
  { label: "tolak", value: "ditolak" },
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

<style scoped lang="postcss">
.no-scrollbar::-webkit-scrollbar {
  display: none;
}
.no-scrollbar {
  -ms-overflow-style: none;
  scrollbar-width: none;
}

button {
  white-space: nowrap;
  -webkit-tap-highlight-color: transparent;
  outline: none;
  /* animasi masuk yang lebih halus */
  animation: slideIn 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}

@keyframes slideIn {
  from { opacity: 0; transform: translateY(5px); }
  to { opacity: 1; transform: translateY(0); }
}
</style>