<template>
  <div class="card-container">
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center">
      
      <!-- Tanggal & Status Hari -->
      <div>
        <h2 class="font-bold text-lg text-gray-900 dark:text-white">
            {{ formatDate(item.tanggal) }}
        </h2>
        
        <div class="mt-1">
            <span :class="['badge-status', badgeClass(item.status_hari)]" class="capitalize">
                {{ item.status_hari || 'Belum Absen' }}
            </span>
        </div>
      </div>

      <!-- Waktu Masuk/Pulang -->
      <div class="text-left sm:text-right mt-3 sm:mt-0 space-y-1">
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Masuk: 
          <b :class="item.status_masuk === 'terlambat' ? 'text-red-600 dark:text-red-400' : 'text-green-600 dark:text-green-400'">
            {{ item.jam_masuk ?? "-" }}
          </b>
        </p>
        <p class="text-sm text-gray-700 dark:text-gray-300">
          Pulang: <b>{{ item.jam_pulang ?? "-" }}</b>
        </p>
      </div>
    </div>

    <!-- Foto Check-in/out -->
    <div v-if="item.foto_checkin || item.foto_checkout" class="mt-4 pt-3 border-t dark:border-gray-700 flex gap-4">
      
      <!-- Foto Check-in -->
      <div v-if="item.foto_checkin" class="flex flex-col items-center">
        <img :src="imageUrl(item.foto_checkin)" class="img-thumb" alt="Check-in Photo" />
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Check-in</p>
      </div>

      <!-- Foto Check-out -->
      <div v-if="item.foto_checkout" class="flex flex-col items-center">
        <img :src="imageUrl(item.foto_checkout)" class="img-thumb" alt="Check-out Photo" />
        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Check-out</p>
      </div>
    </div>

    <!-- ACTION BUTTONS -->
    <div class="mt-4 pt-3 border-t dark:border-gray-700 flex justify-between items-center">
        <!-- Tombol Detail -->
        <router-link
          :to="`/karyawan/absensi/${item.id}`"
          class="btn-detail"
        >
          Lihat Detail
        </router-link>
        
        <!-- Check-out Button (Hanya tampil jika sudah check-in dan belum check-out) -->
        <div v-if="item.jam_masuk && !item.jam_pulang">
            <button
                @click="checkOut"
                class="btn-checkout"
            >
                Check-out
            </button>
        </div>
    </div>

  </div>
</template>

<script setup>
import { defineProps, computed } from 'vue';
import api from '@/services/api'; // Menggunakan API service yang benar

const props = defineProps({
  item: { type: Object, required: true },
});

const emit = defineEmits(["checked-out"]);

// Computed property untuk Base URL
const baseUrl = computed(() => {
    const url = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
    return url.replace(/\/$/, ""); 
});

/**
 * Membangun URL Foto Selfie
 */
const imageUrl = (path) => {
    if (!path) return '/default-user-avatar.png';
    const cleanPath = path.replace(/^\/storage\//i, '');
    return `${baseUrl.value}/storage/${cleanPath}`;
};

/**
 * Format Tanggal
 */
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    if (isNaN(date)) return dateString; 
    return new Intl.DateTimeFormat('id-ID', { dateStyle: 'medium' }).format(date);
}

/**
 * Badge Class untuk Status Hari
 */
function badgeClass(status) {
    const lowerStatus = status ? status.toLowerCase() : '';
    switch (lowerStatus) {
        case 'hadir':
            return 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300';
        case 'terlambat':
            return 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300';
        case 'cuti':
        case 'izin':
        case 'sakit':
            return 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300';
        default:
            return 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300';
    }
}

// =======================================================
// LOGIC CHECKOUT (INTEGRASI DARI QUERY USER)
// =======================================================
const checkOut = async () => {
    // Tombol Check-out Langsung dari Riwayat TIDAK BISA TANPA GPS/Selfie.
    // Kita arahkan ke halaman utama absensi dengan pesan.
    
    if (!confirm("Anda akan diarahkan ke halaman Absensi untuk Check-out. Lanjutkan?")) {
        return;
    }
    
    // Asumsi rute utama absensi adalah /karyawan/absensi
    // Karyawan harus mengisi QR, GPS, dan Selfie di halaman utama.
    window.location.href = '/karyawan/absensi';
};
</script>

<style scoped lang="postcss">
.card-container {
    @apply border border-gray-200 dark:border-gray-700 p-4 rounded-xl shadow-md bg-white dark:bg-[#1a1d19];
}

.badge-status {
    @apply px-3 py-1 rounded-full text-xs font-semibold inline-block;
}

.img-thumb {
    @apply w-20 h-20 rounded-lg object-cover shadow-sm border border-gray-300 dark:border-gray-700;
}

.btn-detail {
    @apply inline-block bg-blue-600 text-white px-3 py-1 rounded-lg text-sm font-medium
           hover:bg-blue-700 transition shadow-sm;
}
.btn-checkout {
    @apply px-4 py-2 bg-red-600 text-white rounded-xl text-sm font-medium hover:bg-red-700 transition shadow-md;
}
</style>