<template>
  <Transition name="fade">
    <div v-if="isOpen" class="fixed inset-0 z-[100] flex items-center justify-center px-6">
      <div @click="closeModal" class="absolute inset-0 bg-[#2d4a3e]/40 backdrop-blur-sm transition-opacity"></div>

      <div class="relative bg-white dark:bg-[#121512] w-full max-w-sm rounded-[3rem] p-8 shadow-2xl border border-white dark:border-white/5 animate-scale-up">
        
        <div class="w-20 h-20 bg-emerald-50 dark:bg-emerald-500/10 rounded-[2rem] flex items-center justify-center mx-auto mb-6 text-emerald-600 shadow-inner">
          <HelpCircle class="w-10 h-10" />
        </div>

        <div class="text-center space-y-2 mb-8">
          <h3 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">
            Konfirmasi Paham
          </h3>
          <p class="text-[11px] text-slate-400 font-medium leading-relaxed uppercase tracking-wider">
            Apakah Anda yakin telah membaca dan memahami informasi ini?
          </p>
        </div>

        <div class="flex flex-col gap-3">
          <button 
            @click="confirmMarkAsRead"
            :disabled="loading"
            class="w-full py-5 bg-[#2d4a3e] text-white rounded-[2rem] font-bold text-xs uppercase tracking-[0.2em] shadow-xl shadow-emerald-900/20 active:scale-95 transition-all flex items-center justify-center gap-3"
          >
            <Loader2 v-if="loading" class="w-4 h-4 animate-spin" />
            <span v-else>Ya, Saya Paham</span>
          </button>
          
          <button 
            @click="closeModal"
            class="w-full py-4 text-[10px] font-bold text-slate-300 uppercase tracking-[0.3em] active:scale-95 transition-all"
          >
            Nanti Saja
          </button>
        </div>
      </div>
    </div>
  </Transition>
</template>

<script setup>
import { ref } from 'vue';
import api from '@/services/api';
import { HelpCircle, Loader2 } from 'lucide-vue-next';

const isOpen = ref(false);
const loading = ref(false);
const pengumumanId = ref(null);

// Definisikan emit agar parent tahu ketika data berhasil diupdate
const emit = defineEmits(['success']);

const openModal = (id) => {
  pengumumanId.value = id;
  isOpen.value = true;
};

const closeModal = () => {
  if (loading.value) return;
  isOpen.value = false;
};

const confirmMarkAsRead = async () => {
  loading.value = true;
  try {
    // Backend menggunakan firstOrCreate untuk mencatat baca
    await api.post(`/karyawan/pengumuman/${pengumumanId.value}/baca`);
    emit('success', pengumumanId.value);
    closeModal();
  } catch (err) {
    console.error("Gagal menandai pengumuman sebagai dibaca:", err);
  } finally {
    loading.value = false;
  }
};

// Ekspos fungsi agar bisa dipanggil oleh parent (index.vue) melalui template ref
defineExpose({ openModal });
</script>

<style scoped lang="postcss">
.fade-enter-active, .fade-leave-active {
  transition: opacity 0.4s ease;
}
.fade-enter-from, .fade-leave-to {
  opacity: 0;
}

@keyframes scaleUp {
  from { opacity: 0; transform: scale(0.9) translateY(20px); }
  to { opacity: 1; transform: scale(1) translateY(0); }
}
.animate-scale-up {
  animation: scaleUp 0.5s cubic-bezier(0.22, 1, 0.36, 1) forwards;
}
</style>