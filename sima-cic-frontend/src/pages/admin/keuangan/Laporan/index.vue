<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-[#2d4a3e]/10 dark:bg-emerald-500/10 text-[#2d4a3e] dark:text-emerald-400">
          <FileSpreadsheet class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Laporan Keuangan</h1>
          <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Laporan Arus Kas, Laba Rugi, dan Neraca tersinkronisasi otomatis</p>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2.5 w-full sm:w-auto">
        <!-- Filter Bulan -->
        <input 
          v-model="filterBulan" 
          type="month" 
          class="px-3.5 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadReport"
        />

        <button 
          @click="printReport" 
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 transition font-medium text-xs shadow-sm"
        >
          <Printer class="w-4 h-4" />
          <span>Cetak Laporan</span>
        </button>
      </div>
    </div>

    <!-- Navigation Tabs: Arus Kas / Laba Rugi / Neraca -->
    <div class="flex items-center gap-2 p-1.5 bg-gray-100 dark:bg-gray-800/80 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-x-auto">
      <button 
        @click="activeTab = 'arus_kas'; loadReport()"
        class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2"
        :class="activeTab === 'arus_kas' 
          ? 'bg-white dark:bg-gray-700 text-emerald-800 dark:text-emerald-300 shadow-sm border border-gray-200/60 dark:border-gray-600' 
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
      >
        <ArrowDownUp class="w-4 h-4" />
        <span>1. Laporan Arus Kas</span>
      </button>

      <button 
        @click="activeTab = 'laba_rugi'; loadReport()"
        class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2"
        :class="activeTab === 'laba_rugi' 
          ? 'bg-white dark:bg-gray-700 text-blue-800 dark:text-blue-300 shadow-sm border border-gray-200/60 dark:border-gray-600' 
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
      >
        <TrendingUp class="w-4 h-4" />
        <span>2. Laporan Laba Rugi</span>
      </button>

      <button 
        @click="activeTab = 'neraca'; loadReport()"
        class="flex-1 min-w-[140px] py-2.5 px-4 rounded-xl text-xs sm:text-sm font-bold transition-all flex items-center justify-center gap-2"
        :class="activeTab === 'neraca' 
          ? 'bg-white dark:bg-gray-700 text-purple-800 dark:text-purple-300 shadow-sm border border-gray-200/60 dark:border-gray-600' 
          : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
      >
        <Scale class="w-4 h-4" />
        <span>3. Laporan Neraca</span>
      </button>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-20 bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700">
      <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
      <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Menghasilkan laporan keuangan realtime...</p>
    </div>

    <!-- Active Report Component -->
    <div v-else>
      <ArusKas v-if="activeTab === 'arus_kas'" :report="reportData" />
      <LabaRugi v-else-if="activeTab === 'laba_rugi'" :report="reportData" />
      <Neraca v-else-if="activeTab === 'neraca'" :report="reportData" />
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import { FileSpreadsheet, ArrowDownUp, TrendingUp, Scale, Printer } from 'lucide-vue-next'
import ArusKas from './ArusKas.vue'
import LabaRugi from './LabaRugi.vue'
import Neraca from './Neraca.vue'

const route = useRoute()
const router = useRouter()

const activeTab = ref(route.query.tab || 'arus_kas')
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const loading = ref(false)
const reportData = ref({})

const loadReport = async () => {
  loading.value = true
  try {
    const params = { bulan: filterBulan.value }
    let endpoint = '/admin/keuangan/laporan/arus-kas'

    if (activeTab.value === 'laba_rugi') {
      endpoint = '/admin/keuangan/laporan/laba-rugi'
    } else if (activeTab.value === 'neraca') {
      endpoint = '/admin/keuangan/laporan/neraca'
    }

    const res = await api.get(endpoint, { params })
    reportData.value = res.data.data
  } catch (err) {
    console.error('Gagal memuat laporan keuangan:', err)
  } finally {
    loading.value = false
  }
}

const printReport = () => {
  window.print()
}

onMounted(() => {
  if (route.path.includes('laba-rugi')) activeTab.value = 'laba_rugi'
  else if (route.path.includes('neraca')) activeTab.value = 'neraca'
  else if (route.path.includes('arus-kas')) activeTab.value = 'arus_kas'

  loadReport()
})
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  .printable-area, .printable-area * {
    visibility: visible;
  }
  .printable-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>
