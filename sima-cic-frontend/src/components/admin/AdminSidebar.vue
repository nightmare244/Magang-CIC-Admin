<template>
  <aside
    :class="{ 
      'w-72': modelValue && !isCollapsed, 
      'w-20': modelValue && isCollapsed,
      'fixed inset-y-0 left-0 z-40 transform transition-all duration-500 ease-in-out': true,
      'translate-x-0 shadow-2xl': modelValue,
      '-translate-x-full': !modelValue,
      'md:translate-x-0 md:relative': true,
      'md:flex': true,
      'flex': modelValue || !isMobile,
      'hidden': !modelValue && isMobile
    }"
    class="flex flex-col bg-white dark:bg-[#0a0c0a] border-r border-gray-100 dark:border-white/5 p-4 h-screen sticky top-0 font-poppins overflow-hidden"
  >
    <button 
      @click="isCollapsed = !isCollapsed"
      class="hidden md:flex absolute -right-0 top-10 bg-[#2d4a3e] text-white p-1.5 rounded-l-xl shadow-lg z-50 hover:pr-4 transition-all duration-300"
    >
      <ChevronLeft v-if="!isCollapsed" class="w-4 h-4" />
      <ChevronRight v-else class="w-4 h-4" />
    </button>

    <div class="flex items-center justify-center mb-10 mt-4 transition-all duration-500">
      <img 
        :src="logo" 
        alt="logo" 
        :class="isCollapsed ? 'w-20 h-20' : 'w-45 h-45'"
        class="object-contain transition-all duration-500" 
      />
      <button @click="$emit('update:modelValue', false)" class="md:hidden absolute right-4 top-4 text-slate-400">
        <X class="w-6 h-6" />
      </button>
    </div>

    <Transition name="fade">
      <div v-if="!isCollapsed" class="p-4 mb-8 rounded-[1.5rem] bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/5 shadow-inner mx-2 overflow-hidden">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-[#2d4a3e]/10 flex-shrink-0 flex items-center justify-center text-[#2d4a3e] dark:text-emerald-500 font-bold uppercase">
              {{ auth.user?.name?.charAt(0) || 'A' }}
          </div>
          <div class="min-w-0">
            <p class="text-xs font-bold text-slate-800 dark:text-white truncate uppercase">{{ auth.user?.name || 'Administrator' }}</p>
            <p class="text-[9px] font-mono font-bold text-slate-400 tracking-widest uppercase truncate">ID: {{ auth.user?.nip || '-' }}</p>
          </div>
        </div>
      </div>
    </Transition>

    <nav class="flex-grow overflow-y-auto space-y-8 custom-scrollbar px-2">
      <div v-for="(group, gIdx) in navigationGroups" :key="gIdx" class="space-y-4">
        <p v-if="!isCollapsed" class="kpi-label-sidebar ml-2">{{ group.title }}</p>
        <div v-else class="border-t border-slate-100 dark:border-white/5 mx-2 my-4"></div>

        <div class="space-y-1">
          <template v-for="link in group.links" :key="link.name">
            <router-link 
              v-if="!link.subLinks"
              :to="link.to" 
              class="nav-link-eco" 
              :class="[isActive(link.to), isCollapsed ? 'justify-center p-3' : 'p-3.5']"
            >
              <component :is="link.icon" :class="isCollapsed ? 'w-6 h-6' : 'w-5 h-5'" class="flex-shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap transition-all duration-300 font-bold text-sm tracking-tight">{{ link.name }}</span>
            </router-link>

            <div v-else class="space-y-1">
              <button 
                @click="isCollapsed ? (isCollapsed = false, isAbsensiOpen = true) : toggleAbsensi()" 
                class="nav-link-eco w-full" 
                :class="[isActiveAbsensi(), isCollapsed ? 'justify-center p-3' : 'justify-between p-3.5']"
              >
                <div class="flex items-center gap-3.5">
                  <Clock4 :class="isCollapsed ? 'w-6 h-6' : 'w-5 h-5'" class="flex-shrink-0" />
                  <span v-if="!isCollapsed" class="whitespace-nowrap font-bold text-sm tracking-tight">{{ link.name }}</span>
                </div>
                <ChevronDown v-if="!isCollapsed" class="w-4 h-4 transition-transform duration-500" :class="{ 'rotate-180': isAbsensiOpen }" />
              </button>
              
              <Transition name="slide-fade">
                <div v-if="isAbsensiOpen && !isCollapsed" class="pl-4 space-y-1 mt-1 border-l border-slate-100 dark:border-white/5 ml-6">
                  <router-link 
                    v-for="sub in link.subLinks" 
                    :key="sub.name" 
                    :to="sub.to" 
                    class="nav-link-eco sub-link" 
                    :class="isActive(sub.to)"
                  >
                    <span class="text-xs font-medium">{{ sub.name }}</span>
                  </router-link>
                </div>
              </Transition>
            </div>
          </template>
        </div>
      </div>
    </nav>

    <div class="mt-auto pt-6 border-t border-slate-100 dark:border-white/5 space-y-2 px-2">
      <button @click="toggleDarkMode" class="control-link-eco group" :class="isCollapsed ? 'justify-center' : ''">
        <Sun v-if="isDarkMode" class="w-5 h-5 text-amber-500" />
        <Moon v-else class="w-5 h-5 text-slate-400" />
        <span v-if="!isCollapsed" class="text-[10px] font-bold uppercase tracking-widest ml-2">Interface</span>
      </button>

      <button @click="handleLogout" class="control-link-eco text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" :class="isCollapsed ? 'justify-center' : ''">
        <LogOut class="w-5 h-5" />
        <span v-if="!isCollapsed" class="text-[10px] font-bold uppercase tracking-widest ml-2">Logout</span>
      </button>
    </div>
  </aside>
</template>

<script setup>
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from "vue-router";
import { useAuthStore } from "../../stores/authStore";
import logoImg from "../../assets/logo/logo.png";
import { 
  X, LayoutDashboard, Megaphone, Users2, Layers, Clock4, 
  ChevronDown, CalendarCheck2, PackageSearch, Handshake, Sun, Moon, LogOut,
  ChevronLeft, ChevronRight 
} from "lucide-vue-next";

const props = defineProps({ modelValue: Boolean });
const emit = defineEmits(['update:modelValue']);
const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const logo = logoImg;

// STATE UTAMA
const isCollapsed = ref(false); 
const isAbsensiOpen = ref(false);
const isDarkMode = ref(false);
const isMobile = ref(false);

const navigationGroups = [
  {
    title: 'Strategic Overview',
    links: [
      { name: "Dashboard Central", icon: LayoutDashboard, to: "/admin/dashboard" },
      { name: "Broadcast Info", icon: Megaphone, to: "/admin/pengumuman" },
    ]
  },
  {
    title: 'Personnel Control',
    links: [
      { name: "Daftar Karyawan", icon: Users2, to: "/admin/karyawan" },
      { name: "Unit Departemen", icon: Layers, to: "/admin/departemen" },
      { 
        name: "Absensi", 
        icon: Clock4, 
        to: "/admin/absensi/laporan",
        subLinks: [
          { name: "Laporan Harian", to: "/admin/absensi/laporan" },
          { name: "Konfigurasi Jadwal", to: "/admin/absensi/settings" },
        ]
      },
      { name: "Verifikasi Izin", icon: CalendarCheck2, to: "/admin/izin" },
    ]
  },
  {
    title: 'Logistics & Asset',
    links: [
      { name: "Database Inventaris", icon: PackageSearch, to: "/admin/inventaris" },
      { name: "Alur Peminjaman", icon: Handshake, to: "/admin/peminjaman" },
    ]
  }
];

// LOGIKA LAYAR
const checkScreenWidth = () => { 
  isMobile.value = window.innerWidth < 768;
  if (isMobile.value) isCollapsed.value = false;
};

const isActive = (path) => route.path === path ? 'active-link-eco' : 'inactive-link-eco';
const isActiveAbsensi = () => route.path.startsWith('/admin/absensi/') ? 'active-link-eco' : 'inactive-link-eco';

const toggleAbsensi = () => { isAbsensiOpen.value = !isAbsensiOpen.value; };

const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  document.documentElement.classList.toggle("dark", isDarkMode.value);
  localStorage.setItem("darkMode", isDarkMode.value);
};

const handleLogout = async () => {
  await auth.logout();
  router.push({ name: "login" });
};

onMounted(() => {
  checkScreenWidth();
  window.addEventListener('resize', checkScreenWidth);
  const saved = localStorage.getItem("darkMode") === "true";
  isDarkMode.value = saved;
  document.documentElement.classList.toggle("dark", saved);
  if (route.path.startsWith('/admin/absensi/')) isAbsensiOpen.value = true;
});

watch(() => route.path, () => { if (isMobile.value) emit('update:modelValue', false); });
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

/* Transisi Halus */
.transition-all { transition-property: all; transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); transition-duration: 500ms; }

.kpi-label-sidebar {
  @apply text-[9px] font-black text-slate-400 uppercase tracking-[0.25em] ml-2 opacity-70 whitespace-nowrap;
}

.nav-link-eco {
  @apply flex items-center rounded-2xl transition-all duration-300 select-none;
}

.sub-link {
  @apply py-3 font-medium opacity-80;
}

.active-link-eco {
  @apply bg-[#2d4a3e] text-white shadow-xl shadow-[#2d4a3e]/20 ring-1 ring-[#2d4a3e]/20 scale-[1.02];
}

.inactive-link-eco {
  @apply text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5;
}

.control-link-eco {
  @apply w-full flex items-center p-3 rounded-2xl transition-all duration-300 font-bold;
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-white/10 rounded-full; }

/* Animasi Fade */
.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

/* Animasi Dropdown */
.slide-fade-enter-active, .slide-fade-leave-active { transition: all 0.4s cubic-bezier(0.16, 1, 0.3, 1); max-height: 200px; }
.slide-fade-enter-from, .slide-fade-leave-to { max-height: 0; opacity: 0; transform: translateY(-10px); }
</style>