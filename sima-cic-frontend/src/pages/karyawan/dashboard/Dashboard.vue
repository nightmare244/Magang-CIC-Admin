<template>
  <div class="min-h-screen bg-white dark:bg-[#080908] font-poppins pb-32 overflow-x-hidden transition-colors duration-500">
    
    <!-- Premium Moving Wave Header -->
    <header class="relative bg-gradient-to-br from-[#1b3329] via-[#2d4a3e] to-[#1e332a] dark:from-[#0a0f0d] dark:to-[#050505] pt-16 pb-40 px-6 overflow-hidden">
      <!-- Decorative Background Elements -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -right-10 -top-10 w-64 h-64 bg-emerald-400/10 rounded-full blur-[80px] animate-pulse"></div>
        <div class="absolute left-1/4 top-1/2 w-32 h-32 bg-teal-500/5 rounded-full blur-[40px]"></div>
      </div>

      <!-- User Info Area -->
      <div class="relative z-20 max-w-md mx-auto animate-header-slide">
        <UserHeader :user="auth.user" />
      </div>

      <!-- Enhanced Multi-Layer Animated Wave -->
      <div class="absolute bottom-0 left-0 w-full leading-[0]">
        <svg class="waves h-[100px] min-h-[100px] max-h-[150px] w-full" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 24 150 28" preserveAspectRatio="none" shape-rendering="auto">
          <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
          </defs>
          <g class="parallax-waves">
            <!-- Layer 1: Slowest & Most Transparent -->
            <use xlink:href="#gentle-wave" x="48" y="0" fill="currentColor" class="text-white/20 dark:text-[#080908]/20 animate-wave-1" />
            <!-- Layer 2: Medium Slow -->
            <use xlink:href="#gentle-wave" x="48" y="3" fill="currentColor" class="text-white/40 dark:text-[#080908]/40 animate-wave-2" />
            <!-- Layer 3: Faster -->
            <use xlink:href="#gentle-wave" x="48" y="5" fill="currentColor" class="text-white/60 dark:text-[#080908]/60 animate-wave-3" />
            <!-- Layer 4: Front Layer (Solid) -->
            <use xlink:href="#gentle-wave" x="48" y="7" fill="currentColor" class="text-white dark:text-[#080908] animate-wave-4" />
          </g>
        </svg>
      </div>
    </header>

    <!-- Main Content Area -->
    <div class="max-w-md mx-auto px-6 -mt-16 relative z-30 space-y-8">
      
      <!-- Skeleton Loading -->
      <div v-if="loading && !summary.absensi_today" class="space-y-6 animate-pulse">
        <div class="h-44 bg-slate-50 dark:bg-white/5 rounded-3xl border border-slate-100"></div>
        <div class="grid grid-cols-2 gap-4">
          <div class="h-32 bg-slate-50 dark:bg-white/5 rounded-3xl"></div>
          <div class="h-32 bg-slate-50 dark:bg-white/5 rounded-3xl"></div>
        </div>
      </div>

      <template v-else>
        <!-- Daily Attendance Section -->
        <section class="animate-fade-in-up">
          <AbsensiTodayCard 
            v-if="summary.absensi_today" 
            :data="summary.absensi_today" 
            class="shadow-2xl shadow-slate-200/60 dark:shadow-none border border-slate-100 dark:border-white/5 transition-transform active:scale-[0.98]" 
          />
        </section>

        <!-- KPI & Stats Grid -->
        <section class="animate-fade-in-up" style="animation-delay: 150ms">
          <div class="grid grid-cols-2 gap-4">
            
            <!-- Attendance Summary -->
            <div class="col-span-2 bg-slate-50/50 dark:bg-[#111311] rounded-[2rem] p-6 border border-slate-100 dark:border-white/5 flex items-center justify-between group">
              <div class="flex items-center gap-4">
                <div class="w-12 h-12 bg-white dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-700 dark:text-emerald-400 shadow-sm transition-transform group-hover:rotate-6">
                  <CalendarCheck class="w-6 h-6" />
                </div>
                <div>
                  <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Total Kehadiran</p>
                  <p class="text-xl font-black text-slate-800 dark:text-slate-100">
                    {{ summary.kpi?.total_hadir || 0 }} 
                    <span class="text-xs text-slate-400 font-bold ml-1">/ {{ summary.kpi?.target_hari || 26 }} Hari</span>
                  </p>
                </div>
              </div>
              
              <div class="w-12 h-12 relative">
                <svg class="w-full h-full transform -rotate-90">
                  <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4.5" fill="transparent" class="text-slate-200 dark:text-white/5" />
                  <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4.5" fill="transparent" 
                    :stroke-dasharray="125.6" 
                    :stroke-dashoffset="125.6 - (125.6 * ((summary.kpi?.total_hadir || 0) / (summary.kpi?.target_hari || 26)))" 
                    stroke-linecap="round" class="text-emerald-600 dark:text-emerald-500 transition-all duration-1000 ease-out" />
                </svg>
              </div>
            </div>

            <!-- Discipline Score -->
            <div class="bg-white dark:bg-[#111311] rounded-[2rem] p-5 shadow-lg shadow-slate-100 dark:shadow-none border border-slate-100 dark:border-white/5 flex flex-col items-center text-center transition-all hover:scale-[1.02]">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Skor Disiplin</p>
              
              <div 
                class="text-3xl font-black mb-4 tracking-tighter"
                :class="summary.kpi?.skor_disiplin < 70 ? 'text-amber-500' : 'text-emerald-600'"
              >
                {{ summary.kpi?.skor_disiplin || 0 }}%
              </div>

              <div class="w-full h-1.5 bg-slate-100 dark:bg-white/5 rounded-full overflow-hidden">
                <div 
                  class="h-full transition-all duration-1000 rounded-full shadow-[0_0_8px_rgba(16,185,129,0.3)]" 
                  :class="summary.kpi?.skor_disiplin < 70 ? 'bg-amber-500' : 'bg-emerald-500'"
                  :style="{ width: (summary.kpi?.skor_disiplin || 0) + '%' }"
                ></div>
              </div>
            </div>

            <!-- Inventory Info -->
            <div class="bg-white dark:bg-[#111311] rounded-[2rem] p-5 shadow-lg shadow-slate-100 dark:shadow-none border border-slate-100 dark:border-white/5 flex flex-col items-center text-center relative overflow-hidden group">
              <p class="text-[9px] font-black text-slate-400 uppercase tracking-[0.2em] mb-4">Inventaris</p>
              <div class="text-3xl font-black text-slate-800 dark:text-white group-hover:scale-110 transition-transform">
                {{ summary.kpi?.barang_dipinjam || 0 }}
              </div>
              <p class="text-[10px] font-bold text-slate-300 dark:text-slate-500 mt-2 uppercase">Barang Dipinjam</p>
            </div>
          </div>
          
          <!-- Warning Alert -->
          <div v-if="summary.kpi?.total_alpa >= 1" class="mt-4 p-4 bg-red-50 dark:bg-red-500/5 border-l-4 border-red-500 rounded-r-2xl">
              <p class="text-[10px] text-red-600 dark:text-red-400 font-black uppercase tracking-widest text-left">
                Perhatian: Alpa {{ summary.kpi.total_alpa }} Hari
              </p>
              <p class="text-[9px] text-red-400 mt-1 uppercase font-medium">Batas maksimal ketidakhadiran tanpa keterangan adalah 3 hari.</p>
          </div>
        </section>

        <!-- Active Permits -->
        <section v-if="summary.izin_aktif" class="animate-fade-in-up" style="animation-delay: 300ms">
          <div class="flex items-center gap-2 mb-4 px-1">
            <div class="w-1 h-4 bg-sky-500 rounded-full"></div>
            <h3 class="text-[12px] font-black text-slate-800 dark:text-sky-400 uppercase tracking-widest">Izin Berjalan</h3>
          </div>
          <IzinAktifCard :izin="summary.izin_aktif" class="shadow-sm" />
        </section>

        <!-- Weekly Chart -->
        <section class="animate-fade-in-up" style="animation-delay: 450ms">
          <div class="flex items-center justify-between mb-4 px-1">
            <h3 class="text-[12px] font-black text-slate-800 dark:text-slate-400 uppercase tracking-widest">Laporan Mingguan</h3>
            <div class="text-[10px] font-bold text-slate-300 uppercase">7 Hari Terakhir</div>
          </div>
          <div class="bg-slate-50/50 dark:bg-[#111311] rounded-[2.5rem] p-6 border border-slate-100 dark:border-white/5">
              <AbsensiChart7Hari v-if="summary.chart_7_hari?.length" :chart-data="summary.chart_7_hari" />
              <div v-else class="py-12 text-center">
                  <BarChart3 class="w-10 h-10 text-slate-200 dark:text-white/5 mx-auto mb-3" />
                  <p class="text-[10px] font-bold text-slate-300 uppercase tracking-widest">Sinkronisasi Data...</p>
              </div>
          </div>
        </section>
      </template>

      <!-- Footer -->
      <footer class="pt-16 pb-8 text-center">
        <div class="inline-flex items-center gap-2 mb-6">
          <div class="w-8 h-[1px] bg-slate-100"></div>
          <div class="w-2 h-2 bg-emerald-500 rounded-full"></div>
          <div class="w-8 h-[1px] bg-slate-100"></div>
        </div>
        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black uppercase tracking-[0.5em]">
          Ciwangun Indah Camp
        </p>
        <p class="text-[8px] text-slate-300 dark:text-slate-700 font-medium uppercase tracking-[0.2em] mt-2">
          Asset & Attendance Mobile v1.0.4
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useAuthStore } from "@/stores/authStore";
import { Trees, CalendarCheck, BarChart3 } from "lucide-vue-next";

// Components
import UserHeader from "./components/UserHeader.vue";
import AbsensiTodayCard from "./components/AbsensiTodayCard.vue";
import IzinAktifCard from "./components/IzinAktifCard.vue";
import AbsensiChart7Hari from "./components/AbsensiChart7Hari.vue";

const auth = useAuthStore();
const loading = ref(false);
const summary = ref({
  absensi_today: null,
  izin_aktif: null,
  chart_7_hari: [],
  kpi: { 
    skor_disiplin: 0, 
    total_hadir: 0, 
    target_hari: 26, 
    barang_dipinjam: 0, 
    total_alpa: 0 
  }
});

const loadDashboard = async () => {
  loading.value = true;
  try {
    const res = await api.get("/karyawan/dashboard-stats");
    if (res.data && res.data.success) {
      summary.value = res.data.summary;
      if (res.data.user) auth.user = res.data.user;
    }
  } catch (e) {
    console.error("Dashboard error:", e);
  } finally {
    setTimeout(() => { loading.value = false; }, 500);
  }
};

onMounted(loadDashboard);
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700;800;900&display=swap');

.font-poppins { font-family: 'Poppins', sans-serif; }

/* ANIMATIONS */
.animate-header-slide { 
  animation: headerSlide 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes headerSlide { 
  from { transform: translateY(-20px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

.animate-fade-in-up { 
  opacity: 0; 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

/* SEAMLESS WAVE ANIMATION
  Menggunakan durasi berbeda untuk setiap layer agar tidak terlihat kaku
*/
.animate-wave-1 { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-2 { animation: move-forever 18s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-3 { animation: move-forever 13s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-4 { animation: move-forever 10s cubic-bezier(.55,.5,.45,.5) infinite; }

@keyframes move-forever {
  0% { transform: translate3d(-90px, 0, 0); }
  100% { transform: translate3d(85px, 0, 0); }
}

/* Custom Progress Transitions */
circle {
  transition: stroke-dashoffset 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
}

/* Smooth Background Color Transitions */
.transition-colors {
  transition-property: background-color, border-color, color, fill, stroke;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 500ms;
}
</style>