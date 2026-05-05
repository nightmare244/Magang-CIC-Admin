<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#080908] font-poppins pb-20 transition-colors duration-300">
    
    <main class="max-w-md mx-auto pt-0 relative"> 
      <router-view v-slot="{ Component }">
        <transition name="page-swipe" mode="out-in">
          <component :is="Component" />
        </transition>
      </router-view>
    </main>

    <nav 
      class="fixed bottom-0 left-1/2 -translate-x-1/2 w-full max-w-md z-[50] transition-transform duration-500 ease-in-out border-t border-slate-100 dark:border-white/5 shadow-[0_-10px_30px_rgba(0,0,0,0.03)]"
      :class="isNavbarVisible ? 'translate-y-0' : 'translate-y-[110%]'"
    >
      <div class="bg-white/95 dark:bg-[#111311]/95 backdrop-blur-md flex items-center justify-around px-2 pt-3 pb-2">
        
        <router-link :to="{ name: 'karyawan.dashboard' }" class="nav-item">
          <Home class="w-5 h-5 nav-icon" />
          <span class="nav-text">Beranda</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.izin.index' }" class="nav-item">
          <CalendarDays class="w-5 h-5 nav-icon" />
          <span class="nav-text">Izin</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.absensi.index' }" class="nav-item">
          <div class="nav-icon-wrapper">
            <ScanLine class="w-5 h-5 nav-icon" />
          </div>
          <span class="nav-text">Presensi</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.absensi.history' }" class="nav-item">
          <History class="w-5 h-5 nav-icon" />
          <span class="nav-text">Riwayat</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.profil' }" class="nav-item">
          <User class="w-5 h-5 nav-icon" />
          <span class="nav-text">Profil</span>
        </router-link>
      </div>

      <div class="bg-white/95 dark:bg-[#111311]/95 h-[env(safe-area-inset-bottom,16px)]"></div>
    </nav>

    <Transition name="overlay-fade">
      <div v-if="isLogoutModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4" @click.self="isLogoutModalOpen = false">
        <Transition name="modal-pop">
          <div v-if="isLogoutModalOpen" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
            <div class="h-1.5 w-full bg-rose-500"></div>
            <div class="p-8 text-center">
              <div class="w-20 h-20 bg-rose-50 dark:bg-rose-500/10 rounded-[2rem] flex items-center justify-center mb-6 text-rose-500 mx-auto border border-rose-100 dark:border-rose-500/20 shadow-inner">
                <LogOut class="w-10 h-10" />
              </div>
              <h2 class="text-xl font-bold text-slate-800 dark:text-white leading-tight">Keluar Akun?</h2>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 mt-4 mb-8 font-medium px-4">
                Apakah Anda yakin ingin keluar? Anda perlu login kembali untuk mengakses data Anda.
              </p>
              <div class="grid grid-cols-1 gap-3">
                <button @click="confirmLogout" class="py-4 bg-rose-500 text-white rounded-2xl font-bold text-sm shadow-lg shadow-rose-500/20 active:scale-95 transition-all">
                  Ya, Keluar
                </button>
                <button @click="isLogoutModalOpen = false" class="py-3 text-slate-400 font-bold text-sm active:scale-95 transition-all">
                  Batalkan
                </button>
              </div>
            </div>
          </div>
        </Transition>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import {
  Home, ScanLine, CalendarDays, User, LogOut, History
} from 'lucide-vue-next';

const router = useRouter();
const auth = useAuthStore();
const isLogoutModalOpen = ref(false);

/**
 * LOGIKA NAVBAR HIDE ON SCROLL
 */
const isNavbarVisible = ref(true);
let lastScrollPosition = 0;
const SCROLL_THRESHOLD = 15; 

const handleScroll = () => {
  const currentScrollPosition = window.pageYOffset || document.documentElement.scrollTop;

  if (currentScrollPosition <= 50) {
    isNavbarVisible.value = true;
    lastScrollPosition = currentScrollPosition;
    return;
  }

  if (Math.abs(currentScrollPosition - lastScrollPosition) < SCROLL_THRESHOLD) {
    return;
  }

  if (currentScrollPosition > lastScrollPosition && currentScrollPosition > 100) {
    isNavbarVisible.value = false;
  } else {
    isNavbarVisible.value = true;
  }

  lastScrollPosition = currentScrollPosition;
};

onMounted(() => {
  window.addEventListener('scroll', handleScroll, { passive: true });
});

onUnmounted(() => {
  window.removeEventListener('scroll', handleScroll);
});

const confirmLogout = async () => {
  try {
    await auth.logout();
    isLogoutModalOpen.value = false;
    router.replace({ name: 'login' });
  } catch (error) {
    router.replace({ name: 'login' });
  }
};
</script>

<style scoped lang="postcss">
/* Nav Item Base */
.nav-item { 
  @apply flex-1 flex flex-col items-center justify-center transition-all duration-300;
  -webkit-tap-highlight-color: transparent;
  height: 60px;
}

.nav-icon {
  @apply text-slate-400 dark:text-slate-500 mb-1 transition-all duration-300;
}

.nav-text { 
  @apply text-[10px] font-semibold text-slate-400 dark:text-slate-500 transition-colors duration-300; 
}

/* State Aktif untuk Semua Item */
.router-link-active.nav-item .nav-icon { 
  @apply text-emerald-600 dark:text-emerald-400 scale-110; 
}
.router-link-active.nav-item .nav-text { 
  @apply text-emerald-600 dark:text-emerald-400; 
}

/* Khusus Presensi - Memberikan sedikit efek angkat saat aktif */
.router-link-active.nav-item .nav-icon-wrapper {
  @apply -translate-y-1 transition-transform duration-300;
}

/* Transitions Halaman */
.page-swipe-enter-active, .page-swipe-leave-active { transition: all 0.25s ease; }
.page-swipe-enter-from { opacity: 0; transform: translateY(10px); }
.page-swipe-leave-to { opacity: 0; transform: translateY(-10px); }

/* Modal Transitions */
.overlay-fade-enter-active, .overlay-fade-leave-active { transition: opacity 0.3s; }
.overlay-fade-enter-from, .overlay-fade-leave-to { opacity: 0; }
.modal-pop-enter-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.modal-pop-enter-from { opacity: 0; transform: scale(0.9) translateY(20px); }
</style>