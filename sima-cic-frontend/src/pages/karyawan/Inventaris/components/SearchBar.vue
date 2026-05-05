<template>
  <div class="relative w-full group">
    <div class="absolute left-5 top-1/2 transform -translate-y-1/2 transition-colors duration-300">
      <svg 
        class="w-5 h-5 text-slate-400 group-focus-within:text-emerald-500 opacity-60" 
        fill="none" 
        stroke="currentColor" 
        viewBox="0 0 24 24"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>
    
    <input
      :value="modelValue"
      @input="$emit('update:modelValue', $event.target.value)"
      type="text"
      :placeholder="formattedPlaceholder"
      class="input-cic-search w-full pl-14 pr-6"
    />
  </div>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
  modelValue: {
    type: String,
    default: ''
  },
  placeholder: {
    type: String,
    default: 'Cari Data...'
  }
});

defineEmits(['update:modelValue']);

// Memastikan Placeholder Selalu Capitalize Each Word
const formattedPlaceholder = computed(() => {
  return props.placeholder.replace(/\b\w/g, l => l.toUpperCase());
});
</script>

<style scoped lang="postcss">
.input-cic-search {
  /* Layout Rounded & Border Sesuai Style Acuan */
  @apply bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/10 
         rounded-[1.5rem] py-4 text-[10px] font-bold outline-none shadow-sm
         placeholder:text-slate-300 placeholder:font-medium tracking-wide
         focus:ring-4 focus:ring-emerald-500/10 focus:border-emerald-500 focus:bg-white 
         dark:focus:bg-[#080908] transition-all duration-500 dark:text-white;
}

/* Mematikan Highlight Biru Di Mobile */
* {
  -webkit-tap-highlight-color: transparent;
}
</style>