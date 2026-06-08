<template>
  <aside
    :class="{ 
      'w-72': modelValue && !isCollapsed, 
      'w-24': modelValue && isCollapsed,
      'fixed inset-y-0 left-0 z-40 transform transition-all duration-500 ease-in-out': true,
      'translate-x-0 shadow-2xl': modelValue,
      '-translate-x-full': !modelValue,
      'md:translate-x-0 md:relative': true,
      'md:flex': true,
      'flex': modelValue || !isMobile,
      'hidden': !modelValue && isMobile
    }"
    class="flex flex-col bg-white dark:bg-[#0a0c0a] border-r border-gray-100 dark:border-white/5 h-screen sticky top-0 font-poppins overflow-hidden"
  >
    <button 
      @click="isCollapsed = !isCollapsed"
      class="hidden md:flex absolute -right-0 top-10 bg-[#2d4a3e] text-white p-1.5 rounded-l-xl shadow-lg z-50 hover:pr-4 transition-all duration-300"
    >
      <ChevronLeft v-if="!isCollapsed" class="w-4 h-4" />
      <ChevronRight v-else class="w-4 h-4" />
    </button>

    <div class="flex items-center justify-center py-8 transition-all duration-500">
      <div class="relative group">
        <img 
          :src="logo" 
          alt="logo" 
          :class="isCollapsed ? 'w-12 h-12' : 'w-40 h-auto'"
          class="object-contain transition-all duration-500 group-hover:scale-105" 
        />
      </div>
      <button @click="$emit('update:modelValue', false)" class="md:hidden absolute right-6 top-8 text-slate-400">
        <X class="w-6 h-6" />
      </button>
    </div>

    <Transition name="fade">
      <div v-if="!isCollapsed" class="px-4 mb-8">
        <div class="p-4 rounded-[1.8rem] bg-slate-50 dark:bg-white/[0.03] border border-slate-100 dark:border-white/5 shadow-inner flex items-center gap-3">
          <div class="w-11 h-11 rounded-2xl bg-[#2d4a3e] flex-shrink-0 flex items-center justify-center text-white font-black shadow-lg shadow-emerald-900/20">
              {{ auth.user?.name?.charAt(0) || 'A' }}
          </div>
          <div class="min-w-0">
            <p class="text-[11px] font-black text-slate-800 dark:text-white truncate uppercase tracking-tight">
              {{ auth.user?.name || 'Administrator' }}
            </p>
            <div class="flex items-center gap-1.5">
              <div class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></div>
              <p class="text-[9px] font-bold text-slate-400 tracking-widest uppercase truncate">ID: {{ auth.user?.nip || '-' }}</p>
            </div>
          </div>
        </div>
      </div>
      <div v-else class="flex justify-center mb-8">
         <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-[#2d4a3e] font-black">
            {{ auth.user?.name?.charAt(0) || 'A' }}
         </div>
      </div>
    </Transition>

    <nav class="flex-grow overflow-y-auto space-y-7 custom-scrollbar px-4">
      <div v-for="(group, gIdx) in navigationGroups" :key="gIdx" class="space-y-3">
        <div v-if="!isCollapsed" class="flex items-center gap-2 px-2">
          <span class="kpi-label-sidebar">{{ group.title }}</span>
          <div class="h-px flex-grow bg-slate-100 dark:bg-white/5"></div>
        </div>
        <div v-else class="h-px bg-slate-100 dark:bg-white/5 mx-2 my-4"></div>

        <div class="space-y-1.5">
          <template v-for="link in group.links" :key="link.name">
            <router-link 
              v-if="!link.subLinks"
              :to="link.to" 
              class="nav-link-eco group relative" 
              :class="[isActive(link.to), isCollapsed ? 'justify-center p-3' : 'p-3.5']"
            >
              <component :is="link.icon" :class="isCollapsed ? 'w-6 h-6' : 'w-5 h-5'" class="flex-shrink-0" />
              <span v-if="!isCollapsed" class="whitespace-nowrap font-bold text-[13px] tracking-tight ml-3">{{ link.name }}</span>
              
              <div v-if="isCollapsed" class="absolute left-16 px-3 py-1 bg-slate-800 text-white text-[10px] rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
                {{ link.name }}
              </div>
            </router-link>

            <div v-else class="space-y-1">
              <button 
                @click="isCollapsed ? (isCollapsed = false, isAbsensiOpen = true) : toggleAbsensi()" 
                class="nav-link-eco w-full group relative" 
                :class="[isActiveAbsensi(), isCollapsed ? 'justify-center p-3' : 'justify-between p-3.5']"
              >
                <div class="flex items-center">
                  <component :is="link.icon" :class="isCollapsed ? 'w-6 h-6' : 'w-5 h-5'" class="flex-shrink-0" />
                  <span v-if="!isCollapsed" class="whitespace-nowrap font-bold text-[13px] tracking-tight ml-3">{{ link.name }}</span>
                </div>
                <ChevronDown v-if="!isCollapsed" class="w-4 h-4 transition-transform duration-500" :class="{ 'rotate-180': isAbsensiOpen }" />
                
                <div v-if="isCollapsed" class="absolute left-16 px-3 py-1 bg-slate-800 text-white text-[10px] rounded-md opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity whitespace-nowrap z-50">
                  {{ link.name }}
                </div>
              </button>
              
              <Transition name="slide-fade">
                <div v-if="isAbsensiOpen && !isCollapsed" class="pl-4 space-y-1 mt-1 border-l-2 border-slate-100 dark:border-white/5 ml-6">
                  <router-link 
                    v-for="sub in link.subLinks" 
                    :key="sub.name" 
                    :to="sub.to" 
                    class="nav-link-eco sub-link" 
                    :class="isActive(sub.to)"
                  >
                    <span class="text-[11px] font-bold uppercase tracking-tight">{{ sub.name }}</span>
                  </router-link>
                </div>
              </Transition>
            </div>
          </template>
        </div>
      </div>
    </nav>

    <div class="mt-auto p-4 border-t border-slate-100 dark:border-white/5 space-y-2 bg-white/50 dark:bg-black/20 backdrop-blur-sm">
      <button @click="toggleDarkMode" class="control-link-eco group transition-all" :class="isCollapsed ? 'justify-center' : 'px-4'">
        <div class="relative w-5 h-5">
          <Sun v-if="isDarkMode" class="w-5 h-5 text-amber-500 animate-spin-slow" />
          <Moon v-else class="w-5 h-5 text-slate-400" />
        </div>
        <span v-if="!isCollapsed" class="text-[10px] font-black uppercase tracking-[0.2em] ml-3 text-slate-500 dark:text-slate-400">Interface</span>
      </button>

      <button @click="handleLogout" class="control-link-eco group text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20" :class="isCollapsed ? 'justify-center' : 'px-4'">
        <LogOut class="w-5 h-5 group-hover:-translate-x-1 transition-transform" />
        <span v-if="!isCollapsed" class="text-[10px] font-black uppercase tracking-[0.2em] ml-3">Terminate</span>
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
  ChevronLeft, ChevronRight, CircleDollarSign, BanknoteArrowUp, BanknoteArrowDown
} from "lucide-vue-next";

const props = defineProps({ modelValue: Boolean });
const emit = defineEmits(['update:modelValue']);
const auth = useAuthStore();
const router = useRouter();
const route = useRoute();
const logo = logoImg;

// STATE
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
      { name: "Laporan keuangan", icon: CircleDollarSign, to: "/admin/keuangan/grafik" },
    ]
  },

    {
    title: 'Keuangan & Operasional',
    links: [
      { name: "Pemasukan", icon: BanknoteArrowUp, to: "/admin/pemasukan" },
      { name: "Pengeluaran", icon: BanknoteArrowDown, to: "/admin/pengeluaran" },
      { name: "Grafik Keuangan", icon: CircleDollarSign, to: "/admin/keuangan/grafik" },
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

.transition-all { 
  transition-property: all; 
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1); 
  transition-duration: 500ms; 
}

.kpi-label-sidebar {
  @apply text-[9px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.2em] opacity-80 whitespace-nowrap;
}

.nav-link-eco {
  @apply flex items-center rounded-2xl transition-all duration-300 select-none;
}

.sub-link {
  @apply py-2.5 px-4 opacity-70 hover:opacity-100;
}

.active-link-eco {
  @apply bg-[#2d4a3e] text-white shadow-xl shadow-emerald-900/20 ring-1 ring-white/10 scale-[1.02];
}

.inactive-link-eco {
  @apply text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5 hover:translate-x-1;
}

/* Tooltip fix for collapsed */
.w-24 .inactive-link-eco:hover {
  @apply translate-x-0;
}

.control-link-eco {
  @apply w-full flex items-center py-3 rounded-2xl transition-all duration-300 font-bold;
}

.custom-scrollbar::-webkit-scrollbar { width: 3px; }
.custom-scrollbar::-webkit-scrollbar-thumb { @apply bg-slate-200 dark:bg-white/10 rounded-full; }

.fade-enter-active, .fade-leave-active { transition: opacity 0.3s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }

.slide-fade-enter-active { transition: all 0.4s ease-out; }
.slide-fade-leave-active { transition: all 0.3s ease-in; }
.slide-fade-enter-from, .slide-fade-leave-to { 
  max-height: 0; 
  opacity: 0; 
  transform: translateY(-10px); 
}
.slide-fade-enter-to, .slide-fade-leave-from { 
  max-height: 200px; 
  opacity: 1; 
  transform: translateY(0); 
}

.animate-spin-slow {
  animation: spin 6s linear infinite;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}
</style>