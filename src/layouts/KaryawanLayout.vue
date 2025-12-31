<template>
  <div class="min-h-screen bg-[#FDFDFD] dark:bg-[#0a0c0a] font-poppins pb-36 overflow-x-hidden transition-colors duration-300">
    
    <div class="fixed top-0 left-0 right-0 z-[40] p-6 pointer-events-none">
      <div class="max-w-md mx-auto flex justify-end items-center">
        <router-link 
          :to="{ name: 'karyawan.pengumuman.index' }"
          class="p-3 bg-white/80 dark:bg-[#1a1d19]/80 backdrop-blur-xl rounded-2xl text-slate-600 dark:text-slate-400 border border-slate-100 dark:border-white/5 shadow-sm pointer-events-auto active:scale-75 transition-all relative"
        >
          <Bell class="w-5 h-5" />
          <span class="absolute top-3 right-3 w-2 h-2 bg-rose-500 rounded-full border-2 border-white dark:border-[#1a1d19]"></span>
        </router-link>
      </div>
    </div>

<main class="max-w-md mx-auto pt-0 relative"> 
  <router-view v-slot="{ Component }">
    <transition name="page-swipe" mode="out-in">
      <component :is="Component" />
    </transition>
  </router-view>
</main>

    <div class="fixed bottom-32 right-6 flex flex-col items-end gap-3 z-[45]">
        <TransitionGroup name="fab-expand">
          <div v-if="isFabOpen" class="flex flex-col items-end gap-3 mb-2" key="fab-items">
            <router-link @click="isFabOpen = false" :to="{ name: 'karyawan.pengumuman.index' }" class="fab-sub-item">
              <div class="fab-label-box"><span>Pengumuman</span></div>
              <div class="fab-icon-box bg-emerald-50 text-emerald-600"><Megaphone class="w-5 h-5" /></div>
            </router-link>
            <router-link @click="isFabOpen = false" :to="{ name: 'karyawan.inventaris.index' }" class="fab-sub-item">
              <div class="fab-label-box"><span>Gudang</span></div>
              <div class="fab-icon-box bg-blue-50 text-blue-600"><Archive class="w-5 h-5" /></div>
            </router-link>
            <router-link @click="isFabOpen = false" :to="{ name: 'karyawan.peminjaman.index' }" class="fab-sub-item">
              <div class="fab-label-box"><span>Pinjam Alat</span></div>
              <div class="fab-icon-box bg-amber-50 text-amber-600"><Handshake class="w-5 h-5" /></div>
            </router-link>
            <button @click="handleLogout" class="fab-sub-item">
              <div class="fab-label-box !border-rose-100"><span class="text-rose-500">Keluar Akun</span></div>
              <div class="fab-icon-box bg-rose-50 text-rose-500 border-rose-100"><LogOut class="w-5 h-5" /></div>
            </button>
          </div>
        </TransitionGroup>

        <button @click="isFabOpen = !isFabOpen" class="w-14 h-14 bg-[#2d4a3e] dark:bg-emerald-600 rounded-[1.5rem] flex items-center justify-center text-white shadow-xl transition-all duration-300 active:scale-75" :class="{'rotate-[225deg] bg-rose-500': isFabOpen}">
          <Plus class="w-6 h-6" />
        </button>
    </div>

    <nav class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[50] w-[92%] max-w-[400px]">
      <div class="bg-white/90 dark:bg-[#1a1d19]/90 backdrop-blur-xl rounded-[2.2rem] shadow-[0_20px_40px_rgba(0,0,0,0.15)] border border-white/40 dark:border-white/5 p-1.5 flex items-center justify-between relative">
        <router-link :to="{ name: 'karyawan.dashboard' }" class="nav-item">
          <div class="nav-icon-wrapper"><Home class="w-5 h-5" /></div>
          <span class="nav-text">Home</span>
        </router-link>
        <router-link :to="{ name: 'karyawan.izin.index' }" class="nav-item">
          <div class="nav-icon-wrapper"><CalendarDays class="w-5 h-5" /></div>
          <span class="nav-text">Izin</span>
        </router-link>
        <div class="center-action-anchor">
          <router-link :to="{ name: 'karyawan.absensi.index' }" class="absensi-fab">
            <div class="fab-inner shadow-lg shadow-emerald-500/40">
              <ScanLine class="w-7 h-7" /><div class="scan-line-overlay"></div>
            </div>
          </router-link>
          <span class="fab-label">Presensi</span>
        </div>
        <router-link :to="{ name: 'karyawan.absensi.history' }" class="nav-item">
          <div class="nav-icon-wrapper"><History class="w-5 h-5" /></div>
          <span class="nav-text">Riwayat</span>
        </router-link>
        <router-link :to="{ name: 'karyawan.profil' }" class="nav-item">
          <div class="nav-icon-wrapper"><User class="w-5 h-5" /></div>
          <span class="nav-text">Profil</span>
        </router-link>
      </div>
    </nav>

<Transition name="overlay-fade">
  <div v-if="isLogoutModalOpen" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center z-[100] p-4 font-poppins" @click.self="isLogoutModalOpen = false">
    <Transition name="modal-pop">
      <div v-if="isLogoutModalOpen" class="bg-white dark:bg-[#121512] rounded-[2.5rem] shadow-2xl w-full max-w-sm overflow-hidden border border-gray-100 dark:border-gray-800">
        <div class="h-1.5 w-full bg-rose-500"></div>
        <div class="p-8 text-center">
          <div class="w-20 h-20 bg-rose-50 dark:bg-rose-500/10 rounded-[2rem] flex items-center justify-center mb-6 text-rose-500 mx-auto border border-rose-100 dark:border-rose-500/20 shadow-inner">
            <LogOut class="w-10 h-10" />
          </div>
          <h2 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight leading-tight">Akhiri Sesi?</h2>
          <p class="text-[10px] font-black uppercase tracking-[0.2em] text-rose-500 mt-2 mb-6">Konfirmasi Keluar</p>
          <p class="text-[11px] text-slate-500 dark:text-slate-400 leading-relaxed px-2 mb-8 font-medium">
            Apakah Anda yakin ingin keluar dari aplikasi CIC Mobile? Anda perlu login kembali untuk mengakses data Anda.
          </p>
          <div class="grid grid-cols-1 gap-3">
            <button @click="confirmLogout" class="py-4 bg-rose-500 text-white rounded-2xl font-bold uppercase text-[10px] tracking-widest shadow-lg shadow-rose-500/20 active:scale-95 transition-all">
              Ya, Keluar Sekarang
            </button>
            <button @click="isLogoutModalOpen = false" class="py-3 bg-white dark:bg-[#1a1d19] text-slate-500 rounded-2xl font-bold uppercase text-[10px] tracking-widest border border-slate-100 dark:border-slate-800 active:scale-95 transition-all">
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
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { useAuthStore } from '../stores/authStore';
import {
  Home, ScanLine, CalendarDays, Archive,
  Handshake, User, LogOut, History, Megaphone,
  Bell, Plus
} from 'lucide-vue-next';

const router = useRouter();
const auth = useAuthStore();
const isFabOpen = ref(false);
const isLogoutModalOpen = ref(false); // State untuk modal logout

const handleLogout = () => {
  isFabOpen.value = false; // Tutup FAB
  isLogoutModalOpen.value = true; // Buka Modal Konfirmasi
};

const confirmLogout = async () => {
  try {
    await auth.logout();
    isLogoutModalOpen.value = false;
    router.replace({ name: 'login' });
  } catch (error) {
    console.error("Logout error:", error);
    // Jika gagal connect API, tetap paksa logout ke login page
    router.replace({ name: 'login' });
  }
};
</script>

<style scoped lang="postcss">
/* --- MODAL ANIMATIONS --- */
.overlay-fade-enter-active, .overlay-fade-leave-active {
  @apply transition-opacity duration-300 ease-out;
}
.overlay-fade-enter-from, .overlay-fade-leave-to {
  @apply opacity-0;
}

.modal-pop-enter-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.modal-pop-leave-active {
  @apply transition-all duration-200 ease-in;
}
.modal-pop-enter-from {
  @apply opacity-0 scale-90 translate-y-4;
}
.modal-pop-leave-to {
  @apply opacity-0 scale-95;
}
/* FAB Elements */
.fab-sub-item { @apply flex items-center justify-end mb-2 transition-all; }
.fab-label-box { @apply bg-white dark:bg-[#1a1d19] px-3 py-1.5 rounded-lg shadow-sm border border-slate-100 dark:border-white/5 mr-3; }
.fab-label-box span { @apply text-[10px] font-black uppercase tracking-widest text-slate-500; }
.fab-icon-box { @apply p-3.5 rounded-2xl shadow-lg border border-white/10 transition-transform active:scale-90; }

/* Navbar Items */
.nav-item { @apply flex-1 flex flex-col items-center justify-center py-2 relative transition-all duration-300; -webkit-tap-highlight-color: transparent; }
.nav-icon-wrapper { @apply p-2 rounded-2xl transition-all duration-300 text-slate-400 dark:text-slate-500; }
.nav-text { @apply text-[9px] font-black uppercase tracking-widest mt-0.5 text-slate-400 opacity-0 scale-75 transition-all duration-500; }

.router-link-active.nav-item .nav-icon-wrapper { @apply text-[#2d4a3e] dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-500/10 scale-110 -translate-y-1; }
.router-link-active.nav-item .nav-text { @apply opacity-100 scale-100 text-[#2d4a3e] dark:text-emerald-400; }

/* FAB Center Presensi */
.center-action-anchor { @apply flex flex-col items-center relative -top-6 transition-all duration-300; width: 70px; }
.fab-inner { @apply w-16 h-16 bg-[#2d4a3e] dark:bg-emerald-600 rounded-[1.8rem] flex items-center justify-center text-white border-[6px] border-[#FDFDFD] dark:border-[#0a0c0a] relative overflow-hidden transition-all duration-500; }
.fab-label { @apply text-[9px] font-black uppercase tracking-[0.15em] text-[#2d4a3e] dark:text-emerald-500 mt-1; }
.router-link-active.absensi-fab .fab-inner { @apply bg-emerald-500 shadow-emerald-500/50 scale-110; }

/* Scan Animation */
.scan-line-overlay { @apply absolute top-0 left-0 w-full h-1 bg-white/40 opacity-0 transition-opacity; }
.router-link-active .scan-line-overlay { @apply opacity-100 animate-[scanning_2s_linear_infinite]; }
@keyframes scanning { 0% { transform: translateY(0); } 100% { transform: translateY(64px); } }

/* Transitions */
.fab-expand-enter-active { transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275); }
.fab-expand-leave-active { transition: all 0.2s ease-in; }
.fab-expand-enter-from, .fab-expand-leave-to { opacity: 0; transform: translateY(30px) scale(0.3); }

.page-swipe-enter-active, .page-swipe-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); }
.page-swipe-enter-from { opacity: 0; transform: translateY(20px); filter: blur(5px); }
.page-swipe-leave-to { opacity: 0; transform: translateY(-20px); filter: blur(5px); }
</style>