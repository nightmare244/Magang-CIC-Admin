<template>
  <div class="card p-4 flex items-center gap-4">
    <div class="w-12 h-12 rounded-lg flex items-center justify-center bg-green-50">
      <span class="text-xl font-bold">{{ iconChar }}</span>
    </div>
    <div class="flex-1">
      <div class="text-sm text-gray-500">{{ title }}</div>
      <div class="text-2xl font-bold">{{ value ?? 0 }}</div>
    </div>
  </div>
</template>

<script setup>
const props = defineProps({
  title: { type: String, required: true },
  value: { type: [Number, String], default: 0 },
  icon: { type: String, default: 'users' }
});

const iconMap = {
  users: '👥',
  clipboard: '📋',
  package: '📦',
  box: '🗃️'
};

const iconChar = iconMap[props.icon] || 'ℹ️';
</script>

<style scoped>
</style>
