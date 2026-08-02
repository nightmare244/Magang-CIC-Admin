<template>
  <header
    class="sticky top-0 z-30 flex h-20 flex-shrink-0 items-center justify-between border-b
           border-gray-100 bg-white/70 px-4 shadow-sm backdrop-blur-md
           dark:border-white/5 dark:bg-[#0a0c0a]/80 md:px-8 font-poppins"
  >
    <div class="flex items-center gap-6">
      <button
        @click="$emit('toggleSidebar')"
        class="p-2.5 rounded-xl text-slate-500 hover:bg-slate-50 dark:hover:bg-white/5 md:hidden transition-all active:scale-90"
        aria-label="Toggle Command Sidebar"
      >
        <Menu class="w-6 h-6" />
      </button>

      <div class="relative hidden sm:block group">
        <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
          <Search class="w-4 h-4 text-slate-400 group-focus-within:text-[#2d4a3e] transition-colors" />
        </span>

        <input
          v-model="query"
          @keyup.enter="onSearch"
          placeholder="Cari data operasional..."
          class="nav-search-input w-64 pl-11"
        />
      </div>
    </div>

    <div class="flex items-center gap-3 md:gap-6">
      <button 
        @click="toggleDarkMode" 
        class="icon-control-btn shadow-sm border border-gray-100 dark:border-white/5"
        :aria-label="isDarkMode ? 'Mode Terang' : 'Mode Gelap'"
      >
        <Sun v-if="isDarkMode" class="w-5 h-5 text-amber-500" />
        <Moon v-else class="w-5 h-5 text-slate-400" />
      </button>

      <div class="relative">
        <button 
          @click="toggleDropdown" 
          class="flex items-center gap-3 p-1.5 rounded-2xl hover:bg-slate-50 dark:hover:bg-white/5 transition-all"
          aria-expanded="true" 
          id="user-menu-button"
        >
          <div class="relative">
            <img
              :src="avatarUrl"
              alt="Admin Profile"
              class="w-10 h-10 rounded-xl object-cover bg-slate-200 border-2 border-white dark:border-[#1a1d19] shadow-md transition-all duration-300 group-hover:scale-105"
              @error="handleAvatarError"
            />
            <div class="absolute -bottom-1 -right-1 w-3.5 h-3.5 bg-emerald-500 border-2 border-white dark:border-[#0a0c0a] rounded-full shadow-sm animate-pulse"></div>
          </div>

          <div class="text-left hidden md:block">
            <div class="text-[9px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.2em] leading-none">
              {{ user?.role || "Administrator" }}
            </div>
            <div class="text-sm font-bold text-slate-800 dark:text-white leading-none mt-1">
              {{ user?.name || "User Node" }}
            </div>
          </div>

          <ChevronDown class="w-4 h-4 text-slate-400 hidden md:block transition-transform duration-300" 
                       :class="{ 'rotate-180': isDropdownOpen }" />
        </button>

        <Transition name="dropdown-pop">
          <div
            v-if="isDropdownOpen"
            id="user-dropdown-menu"
            class="dropdown-card-eco"
            @click.stop
          >
            <div class="px-5 py-4 border-b border-slate-100 dark:border-white/5 md:hidden bg-slate-50/50 dark:bg-white/5">
              <div class="text-[9px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-widest">{{ user?.role }}</div>
              <div class="text-sm font-bold text-slate-800 dark:text-white mt-1">{{ user?.name }}</div>
            </div>

            <div class="p-1.5">
              <button
                @click="handleLogout"
                class="dropdown-item-eco group text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20"
              >
                <div class="p-2 bg-rose-50 dark:bg-rose-900/20 rounded-lg group-hover:bg-rose-100 transition-colors">
                  <LogOut class="w-4 h-4" />
                </div>
                <span class="font-bold uppercase tracking-widest text-[10px]">Terminate Session</span>
              </button>
            </div>
          </div>
        </Transition>
      </div>
    </div>
  </header>
</template>

<script setup>
import { ref, computed, onMounted, onBeforeUnmount } from "vue";
import { useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore"; 
import { Menu, Search, Sun, Moon, LogOut, ChevronDown } from "lucide-vue-next";

defineEmits(["toggleSidebar"]);

const auth = useAuthStore();
const router = useRouter();

const user = computed(() => auth.user);
const query = ref("");
const isDropdownOpen = ref(false);

// PATH KONFIGURASI BACKEND & ASSETS
const BACKEND_URL = import.meta.env.VITE_API_URL || 'http://localhost:8000';
const defaultAvatar = new URL('@/assets/avatar-placeholder.png', import.meta.url).href;

// LOGIKA SINKRONISASI FOTO PROFIL USER NODE
const avatarUrl = computed(() => {
  if (user.value?.foto_profil) {
    // 1. Pastikan tidak ada double slash //
    const baseUrl = BACKEND_URL.endsWith('/') ? BACKEND_URL.slice(0, -1) : BACKEND_URL;
    
    // 2. Ambil path foto. Jika di database tersimpan 'profil/namafile.jpg'
    const path = user.value.foto_profil;
    
    // 3. Gabungkan. Hasilnya: http://localhost:8000/storage/profil/namafile.jpg
    return `${baseUrl}/storage/${path}`;
  }
  return defaultAvatar;
});

const isDarkMode = ref(false);

function updateDarkMode(value) {
  isDarkMode.value = value;
  document.documentElement.classList.toggle("dark", value);
  localStorage.setItem("darkMode", value ? "true" : "false");
}

function toggleDarkMode() {
  updateDarkMode(!isDarkMode.value);
}

function toggleDropdown() {
  isDropdownOpen.value = !isDropdownOpen.value;
}

function handleAvatarError(event) {
  const target = event.target;

  if (target.dataset.avatarFallback === "true") return;

  target.src = defaultAvatar;
  target.dataset.avatarFallback = "true";
}

// Menutup dropdown jika klik di luar elemen
function handleClickOutside(event) {
    const dropdown = document.getElementById('user-dropdown-menu');
    const button = document.getElementById('user-menu-button');

    if (isDropdownOpen.value && dropdown && !dropdown.contains(event.target) && !button?.contains(event.target)) {
        isDropdownOpen.value = false;
    }
}

onMounted(() => {
  document.addEventListener("click", handleClickOutside);
  const saved = localStorage.getItem("darkMode") === "true";
  updateDarkMode(saved);
});

onBeforeUnmount(() => {
  document.removeEventListener("click", handleClickOutside);
});

async function handleLogout() {
  isDropdownOpen.value = false;
  await auth.logout();
  router.push({ name: "login" });
}

function onSearch() {
  console.log("Mencari Data Operasional:", query.value);
}
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.nav-search-input {
  @apply block w-full rounded-2xl border-gray-100 bg-white/80 py-2.5 text-sm shadow-inner transition-all
         focus:border-[#2d4a3e] focus:ring-[#2d4a3e]/20
         dark:border-white/5 dark:bg-white/5 dark:text-white dark:focus:border-emerald-500 font-medium;
}

.icon-control-btn {
  @apply flex items-center justify-center w-10 h-10 rounded-2xl bg-white dark:bg-white/5
         transition-all active:scale-90 hover:shadow-md;
}

.dropdown-card-eco {
  @apply absolute right-0 top-16 z-50 w-64 rounded-[1.8rem] border border-gray-100 bg-white shadow-2xl shadow-slate-200/50
         dark:border-white/10 dark:bg-[#121512] dark:shadow-black/50 overflow-hidden font-poppins;
}

.dropdown-item-eco {
  @apply flex w-full items-center gap-4 rounded-2xl px-3 py-2 text-sm transition-all duration-300;
}

/* Logic Pop Animation */
.dropdown-pop-enter-active {
  transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1);
}
.dropdown-pop-leave-active {
  transition: all 0.2s ease-in;
}
.dropdown-pop-enter-from {
  opacity: 0;
  transform: translateY(15px) scale(0.95);
}
.dropdown-pop-leave-to {
  opacity: 0;
  transform: scale(0.98);
}
</style>