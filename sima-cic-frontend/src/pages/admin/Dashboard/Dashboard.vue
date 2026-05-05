<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <LayoutDashboard class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight text-[#2d4a3e] dark:text-emerald-500 uppercase">
            Dashboard <span class="font-light text-slate-400 italic lowercase tracking-normal">Super Admin</span>
          </h1>
          <p class="text-[10px] md:text-xs font-semibold text-slate-400 uppercase tracking-[0.2em] mt-1 italic">
            PT. Ciwangun Indah Camp 
          </p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-end sm:items-center gap-6 w-full lg:w-auto">
        <div class="text-right hidden sm:block border-r border-gray-200 dark:border-gray-700 pr-6">
          <p class="text-[10px] font-bold text-[#2d4a3e] uppercase tracking-widest mb-1">{{ currentTime }}</p>
          <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">{{ currentDate }}</p>
        </div>
        <button @click="fetchData" :disabled="loading" class="btn-refresh-eco group">
          <RefreshCw :class="{'animate-spin': loading}" class="w-4 h-4 mr-2 transition-transform" />
          {{ loading ? 'Synchronizing...' : 'Segarkan Data' }}
        </button>
      </div>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <template v-if="loading">
        <div v-for="i in 8" :key="i" class="h-32 bg-slate-200 dark:bg-slate-800/50 rounded-[1.8rem] animate-pulse"></div>
      </template>
      <template v-else>
        <div v-for="item in kpiHR" :key="item.title" class="kpi-card-new group">
          <div class="relative z-10">
            <p class="kpi-label">{{ item.title }}</p>
            <h3 class="kpi-value">{{ item.value }}</h3>
            <p class="kpi-sub">{{ item.sub }}</p>
          </div>
          <div class="kpi-icon-wrapper" :class="item.colorClass">
            <component :is="item.icon" class="w-12 h-12 opacity-20 group-hover:opacity-40 transition-all duration-500 group-hover:scale-110" />
          </div>
        </div>
        <div v-for="item in kpiLogistik" :key="item.title" class="kpi-card-new group">
          <div class="relative z-10">
            <p class="kpi-label">{{ item.title }}</p>
            <h3 class="kpi-value">{{ item.value }}</h3>
            <p class="kpi-sub">{{ item.sub }}</p>
          </div>
          <div class="kpi-icon-wrapper" :class="item.colorClass">
            <component :is="item.icon" class="w-12 h-12 opacity-20 group-hover:opacity-40 transition-all duration-500 group-hover:scale-110" />
          </div>
        </div>
      </template>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
      <div class="lg:col-span-8 space-y-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div class="card-eco p-6 h-[380px]">
            <div class="flex items-center justify-between mb-6">
              <h3 class="card-title-eco text-xs">Statistik Absensi Mingguan</h3>
              <TrendingUp class="w-4 h-4 text-emerald-500" />
            </div>
            <ChartAbsensi7Hari v-if="!loading" :series="charts.absensi_7_hari" :key="chartKey" />
            <div v-else class="w-full h-full bg-slate-100 dark:bg-slate-800/30 rounded-2xl animate-pulse"></div>
          </div>

          <div class="card-eco p-6 h-[380px]">
            <h3 class="card-title-eco text-xs mb-6">Rasio Kehadiran Harian</h3>
            <div v-if="loading" class="w-full h-full flex items-center justify-center">
              <div class="w-32 h-32 rounded-full border-8 border-slate-100 dark:border-slate-800 border-t-emerald-500 animate-spin"></div>
            </div>
            <ChartAbsensiHariIni v-else :dataObj="charts.absensi_hari_ini" :key="chartKey" />
          </div>
        </div>

        <div class="card-eco overflow-hidden">
          <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-[10px] font-bold text-slate-700 dark:text-white uppercase tracking-[0.2em]">Antrian Izin Karyawan</h3>
            <router-link to="/admin/izin" class="btn-detail-eco">Lihat Detail</router-link>
          </div>
          <div class="p-4">
            <div v-if="loading" class="space-y-3">
              <div v-for="i in 3" :key="i" class="h-20 bg-slate-100 dark:bg-slate-800/40 rounded-xl animate-pulse"></div>
            </div>
            <PendingIzinTable v-else :rows="pending_izin" />
          </div>
        </div>
      </div>

      <div class="lg:col-span-4 space-y-8">
        <div class="card-eco overflow-hidden">
          <div class="p-5 border-b border-gray-100 dark:border-gray-800 flex justify-between items-center bg-slate-50/30">
            <h3 class="text-[10px] font-bold text-slate-700 dark:text-white uppercase tracking-[0.2em]">Logistik Pending</h3>
            <router-link to="/admin/peminjaman" class="btn-detail-eco">Lihat Detail</router-link>
          </div>
          <div class="p-4">
            <div v-if="loading" class="space-y-3">
              <div v-for="i in 4" :key="i" class="h-16 bg-slate-100 dark:bg-slate-800/40 rounded-xl animate-pulse"></div>
            </div>
            <PendingPeminjamanTable v-else :rows="pending_peminjaman" />
          </div>
        </div>

        <div class="bg-[#1a2e26] rounded-[2rem] p-8 text-white relative overflow-hidden shadow-2xl">
          <div class="relative z-10 space-y-6">
            <div class="flex items-center gap-3">
              <div class="w-2 h-2 bg-emerald-400 rounded-full animate-ping"></div>
              <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-emerald-400">System Live</span>
            </div>
            <div>
              <p class="text-xs opacity-60 mb-1 font-light">Database Integrity</p>
              <p class="text-sm font-bold tracking-widest uppercase">Verified & Encrypted</p>
            </div>
            <div class="h-[1px] bg-white/10 w-full"></div>
            <div class="flex justify-between items-center">
              <p class="text-[10px] opacity-40 italic uppercase tracking-tighter">Sync: {{ lastUpdate }}</p>
              <p class="text-[10px] opacity-40 italic font-mono">cic-hq-01</p>
            </div>
          </div>
          <ShieldCheck class="absolute -right-10 -bottom-10 w-48 h-48 opacity-5 text-white" />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed, onUnmounted, nextTick } from 'vue';
import api from '@/services/api'; 
import { 
  Users, UserCheck, Timer, CalendarCheck, Package, 
  ClipboardList, ArrowLeftRight, Activity, LayoutDashboard,
  RefreshCw, TrendingUp, ShieldCheck 
} from 'lucide-vue-next';

// Components
import PendingIzinTable from './components/PendingIzinTable.vue';
import PendingPeminjamanTable from './components/PendingPeminjamanTable.vue';
import ChartAbsensi7Hari from './components/ChartAbsensi7Hari.vue';
import ChartAbsensiHariIni from './components/ChartAbsensiHariIni.vue';

const loading = ref(false);
const lastUpdate = ref('--:--');
const chartKey = ref(0); 

const kpi = ref({
  total_karyawan: { total: 0, laki: 0, perempuan: 0 }, 
  hadir_hari_ini: 0, 
  total_izin_pending: 0, 
  izin_approved_month: 0,
  total_inventaris: { total_unit: 0, total_jenis: 0, total_nilai: 0 }, 
  total_peminjaman_pending: 0, 
  peminjaman_aktif: 0
});

const pending_izin = ref([]);
const pending_peminjaman = ref([]);
const charts = ref({ 
  absensi_7_hari: { labels: [], datasets: [] }, 
  absensi_hari_ini: { labels: [], data: [] } 
});

// Formatting Helpers
const formatNum = (n) => new Intl.NumberFormat('id-ID').format(n || 0);
const formatIDR = (n) => {
  return new Intl.NumberFormat('id-ID', {
    style: 'currency',
    currency: 'IDR',
    minimumFractionDigits: 0
  }).format(n || 0);
};

const kpiHR = computed(() => [
 { 
  title: 'Total Personil', 
  value: `${formatNum(kpi.value.total_karyawan.total)} Orang`, 
  icon: Users, 
  colorClass: 'text-emerald-600', 
  sub: `${kpi.value.total_karyawan.laki} Laki-laki, ${kpi.value.total_karyawan.perempuan} Perempuan` 
},
  { title: 'Hadir Hari Ini', value: formatNum(kpi.value.hadir_hari_ini), icon: UserCheck, colorClass: 'text-[#2d4a3e]', sub: 'Absensi Masuk' },
  { title: 'Izin Pending', value: formatNum(kpi.value.total_izin_pending), icon: Timer, colorClass: 'text-amber-600', sub: 'Perlu Verifikasi' },
  { title: 'Izin Disetujui', value: formatNum(kpi.value.izin_approved_month), icon: CalendarCheck, colorClass: 'text-sky-600', sub: 'Bulan Ini' }
]);

const kpiLogistik = computed(() => [
 { 
  title: 'Unit Inventaris', 
  value: `${formatNum(kpi.value.total_inventaris.total_unit)} Unit`, 
  icon: Package, 
  colorClass: 'text-emerald-700', 
  sub: `${kpi.value.total_inventaris.total_jenis} Kategori Barang` 
},
 { 
  title: 'Nilai Aset', 
  value: formatIDR(kpi.value.total_inventaris.total_nilai), 
  icon: ShieldCheck, 
  colorClass: 'text-blue-700', 
  sub: 'Total Valuasi Barang' 
},
  { title: 'Peminjaman Pending', value: formatNum(kpi.value.total_peminjaman_pending), icon: ClipboardList, colorClass: 'text-amber-700', sub: 'Antrian Logistik' },
  { title: 'Aset Terpinjam', value: formatNum(kpi.value.peminjaman_aktif), icon: ArrowLeftRight, colorClass: 'text-blue-600', sub: 'Sedang Digunakan' }
]);

const currentDate = computed(() => new Date().toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }));
const currentTime = ref('');
const timer = setInterval(() => {
  currentTime.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false });
}, 1000);

async function fetchData() {
  loading.value = true;
  try {
    const response = await api.get('/admin/dashboard-summary');
    
    if (response.data.success) {
      // 1. Update KPI
      kpi.value = response.data.kpi;
      pending_izin.value = response.data.pending_izin || [];
      pending_peminjaman.value = response.data.pending_peminjaman || [];

      // 2. Update Charts
      const chartData = response.data.charts;
      charts.value.absensi_7_hari = {
        labels: chartData.absensi_7_hari?.labels || [],
        datasets: chartData.absensi_7_hari?.datasets || []
      };
      charts.value.absensi_hari_ini = chartData.absensi_hari_ini || { labels: [], data: [] };

      // 3. Trigger re-render total
      await nextTick();
      chartKey.value++; 
      
      lastUpdate.value = new Date().toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
    }
  } catch (err) {
    console.error('Fetch Error:', err);
  } finally {
    setTimeout(() => { loading.value = false; }, 500);
  }
}

onMounted(fetchData);
onUnmounted(() => clearInterval(timer));
</script>

<style scoped lang="postcss">
.kpi-card-new {
  @apply bg-white dark:bg-[#121512] p-6 rounded-[1.8rem] border border-gray-100 
          dark:border-gray-800 shadow-sm relative overflow-hidden transition-all 
          duration-500 hover:shadow-xl hover:-translate-y-1 hover:border-emerald-500/30;
}

.kpi-label {
  @apply text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.kpi-value {
  @apply text-3xl font-black text-slate-800 dark:text-white tracking-tight;
}

.kpi-sub {
  @apply text-[10px] text-slate-400 mt-2 font-medium italic;
}

.kpi-icon-wrapper {
  @apply absolute -right-2 -bottom-2 transition-transform duration-700;
}

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.card-title-eco {
  @apply font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest;
}

.btn-refresh-eco {
  @apply inline-flex items-center px-6 py-3 bg-[#2d4a3e] text-white rounded-xl text-xs font-bold 
          shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all disabled:opacity-50 cursor-pointer;
}

.btn-detail-eco {
  @apply px-3 py-1.5 bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 
          text-[10px] font-bold uppercase tracking-wider rounded-lg hover:bg-[#2d4a3e] 
          hover:text-white transition-all duration-300;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>