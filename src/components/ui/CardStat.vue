<template>
  <div class="p-4 rounded-2xl shadow-sm border border-gray-100 dark:border-white/5 bg-white dark:bg-[#0f1410] transition-colors">
    <div class="flex items-center justify-between">
      <div>
        <div class="text-sm text-gray-500 dark:text-gray-300">{{ title }}</div>
        <div class="text-2xl font-bold mt-1 text-gray-900 dark:text-white">{{ value }}</div>
      </div>

      <div class="p-3 rounded-xl bg-army/10 text-army dark:bg-army/20">
        <slot name="icon">
          <!-- default icon -->
          <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 11V3m0 0L7 7m4-4 4 4M21 21v-6a2 2 0 00-2-2h-4" /></svg>
        </slot>
      </div>
    </div>

    <div class="text-xs text-gray-400 dark:text-gray-400 mt-2">{{ subtitle }}</div>
  </div>
</template>

<script setup>
defineProps({
  title: { type: String, default: "" },
  value: { type: [String, Number], default: 0 },
  subtitle: { type: String, default: "" },
});
</script>

<style scoped>
/* hover lift for a premium feel */
:root { --card-lift: 6px; }
div:hover { transform: none; }
</style>
