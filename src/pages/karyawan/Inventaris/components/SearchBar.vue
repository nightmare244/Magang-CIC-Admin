<template>
  <div class="relative w-full group">
    <div class="absolute left-4 top-1/2 transform -translate-y-1/2 transition-colors duration-300">
      <svg 
        class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-500" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>
    
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="text"
      :placeholder="placeholder"
      class="input-cic-search w-full pl-12 pr-4"
    />
  </div>
</template>

<script setup>
// defineProps & defineEmits tidak perlu diimport di Vue 3 script setup (sudah compiler macro)
defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Cari data...'
  }
});

defineEmits(['update:modelValue']);
</script>

<style scoped lang="postcss">
.input-cic-search {
  /* Font Poppins dari global.css */
  @apply bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5 
         rounded-2xl py-4 text-xs font-bold outline-none shadow-sm
         placeholder:text-slate-400 placeholder:font-medium
         focus:ring-2 focus:ring-emerald-500 focus:bg-white 
         dark:focus:bg-black transition-all duration-300 dark:text-white;
}
</style>