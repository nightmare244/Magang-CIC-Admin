<template>
    <div v-if="show" class="fixed inset-0 bg-black/40 flex items-center justify-center z-50 transition-opacity duration-300">
        <div class="bg-white dark:bg-[#1a1d19] p-6 rounded-xl shadow-2xl w-full max-w-lg transition-transform border dark:border-gray-700">
            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Impor Data Karyawan</h2>
            
            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                Unggah file CSV atau Excel (.xlsx) untuk menambahkan banyak karyawan sekaligus.
                Pastikan format file Anda sesuai dengan template yang disediakan.
            </p>

            <input
                type="file"
                @change="handleFileChange"
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-200 file:text-gray-700 hover:file:bg-gray-300 dark:file:bg-gray-700 dark:file:text-gray-200 dark:text-gray-400"
                accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel"
            />
            
            <div v-if="fileError" class="text-red-500 text-sm mt-2">{{ fileError }}</div>

            <div class="mt-6 flex justify-end gap-3">
                <button @click="$emit('close')" class="btn-secondary">
                    Batal
                </button>
                <button 
                    @click="confirmImport" 
                    :disabled="!selectedFile"
                    class="btn-primary"
                >
                    Mulai Impor
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, defineProps, defineEmits } from 'vue';

defineProps({
    show: Boolean,
});
const emit = defineEmits(['close', 'confirm']);

const selectedFile = ref(null);
const fileError = ref(null);

const handleFileChange = (event) => {
    const file = event.target.files[0];
    if (file) {
        if (file.size > 5 * 1024 * 1024) { // Max 5MB
            fileError.value = "Ukuran file maksimal 5MB.";
            selectedFile.value = null;
        } else {
            selectedFile.value = file;
            fileError.value = null;
        }
    } else {
        selectedFile.value = null;
        fileError.value = null;
    }
};

const confirmImport = () => {
    if (selectedFile.value) {
        emit('confirm', selectedFile.value);
    }
};
</script>

<style scoped lang="postcss">
.btn-primary {
  @apply px-4 py-2 bg-[#5E815F] text-white rounded-xl font-semibold 
         hover:bg-[#6D956E] transition shadow-md disabled:bg-gray-400;
}
.btn-secondary {
    @apply px-4 py-2 bg-gray-200 text-gray-800 rounded-xl font-semibold 
           hover:bg-gray-300 transition dark:bg-gray-600 dark:text-gray-100 dark:hover:bg-gray-500;
}
</style>