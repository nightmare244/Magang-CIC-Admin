<template>
  <div class="p-10 text-center bg-white dark:bg-[#0f1410] rounded-xl shadow-lg dark:shadow-black/20 max-w-lg mx-auto mt-20">
    <h1 class="text-6xl font-extrabold text-red-600 dark:text-red-400">404</h1>
    <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mt-4">Halaman Tidak Ditemukan</h2>
    <p class="text-gray-600 dark:text-gray-400 mt-2">
        Maaf, kami tidak dapat menemukan halaman yang Anda cari.
    </p>
    <router-link to="/admin/peminjaman" class="btn-primary mt-6 inline-block">
        Kembali ke Daftar Peminjaman
    </router-link>
  </div>
</template>

<script setup>
// Script minimal untuk halaman 404
// Pastikan komponen ini memiliki tag <template> dan <script>
</script>

<style scoped lang="postcss">
.btn-primary {
  @apply px-4 py-2 rounded-xl bg-[#5E815F] text-white font-semibold
         hover:bg-[#6D956E] shadow-md transition-all;
}
</style>
