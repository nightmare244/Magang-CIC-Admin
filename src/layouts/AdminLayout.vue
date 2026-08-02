<template>
  <div class="flex h-screen bg-gray-50 dark:bg-[#1a1d19] transition-colors duration-500 overflow-hidden">
    
    <!-- Sidebar -->
    <AdminSidebar v-model="isSidebarOpen" />

    <!-- Main Content Area -->
    <div class="flex flex-col flex-1 overflow-x-hidden overflow-y-auto">
      
      <!-- Header / Topbar -->
      <AdminTopbar @toggleSidebar="isSidebarOpen = !isSidebarOpen" />
      
      <!-- Content (Router View) -->
      <main class="p-4 md:p-6 flex-1">
        <RouterView /> 
      </main>

      <!-- Footer -->
      <footer class="p-4 text-center text-sm text-gray-500 dark:text-gray-400 border-t dark:border-white/5 flex-shrink-0">
          &copy; {{ currentYear }} SIMA CIC. All rights reserved.
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import AdminTopbar from '../components/admin/AdminTopbar.vue'; 
import AdminSidebar from '../components/admin/AdminSidebar.vue'; 
import { RouterView } from 'vue-router';

// State untuk Sidebar
const isSidebarOpen = ref(window.innerWidth >= 768);
const currentYear = ref(new Date().getFullYear());

// Logika Responsif untuk Sidebar
const checkScreenSize = () => {
    if (window.innerWidth >= 768) {
        isSidebarOpen.value = true;
    } 
    else if (window.innerWidth < 768) {
        if (isSidebarOpen.value) {
            isSidebarOpen.value = false;
        }
    }
};

onMounted(() => {
    checkScreenSize();
    window.addEventListener('resize', checkScreenSize);
});

onUnmounted(() => {
    window.removeEventListener('resize', checkScreenSize);
});
</script>