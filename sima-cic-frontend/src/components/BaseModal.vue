<template>
  <Transition name="modal-fade">
    <div v-if="show" class="modal-overlay" @click.self="close">
      <div class="modal-card" @click.stop>
        <div class="modal-header">
          <slot name="header">
            <h3 class="text-xl font-semibold text-gray-900 dark:text-white">
              Judul Modal
            </h3>
          </slot>
          <button @click="close" class="modal-close-button">
            <X class="h-5 w-5" />
          </button>
        </div>
        <div class="modal-body">
          <slot />
        </div>
        <div v-if="$slots.footer" class="modal-footer">
          <slot name="footer" />
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { X } from 'lucide-vue-next';
defineProps({ show: { type: Boolean, default: false } });
const emit = defineEmits(['close']);
const close = () => { emit('close'); };
</script>

<style scoped>
.modal-overlay { @apply fixed inset-0 z-40 flex items-center justify-center bg-black/60 backdrop-blur-sm; }
.modal-card { @apply relative z-50 m-4 w-full max-w-2xl rounded-xl bg-white shadow-lg dark:bg-gray-800; }
.modal-header { @apply flex items-start justify-between border-b border-gray-200 p-5 dark:border-gray-700; }
.modal-close-button { @apply rounded-full p-1 text-gray-400 transition hover:bg-gray-100 hover:text-gray-600 dark:hover:bg-gray-700 dark:hover:text-gray-300; }
.modal-body { @apply p-6 max-h-[70vh] overflow-y-auto; }
.modal-footer { @apply border-t border-gray-200 p-6 dark:border-gray-700; }
.modal-fade-enter-active, .modal-fade-leave-active { transition: opacity 0.3s ease; }
.modal-fade-enter-from, .modal-fade-leave-to { opacity: 0; }
</style>