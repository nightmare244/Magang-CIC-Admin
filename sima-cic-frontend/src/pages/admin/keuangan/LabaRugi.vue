<template>
  <div class="space-y-6 p-6 font-poppins">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Laporan Laba Rugi</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Income Statement — Pendapatan vs Beban</p>
      </div>
      <button
        @click="printReport"
        class="flex items-center gap-2 px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm"
      >
        <Printer class="w-4 h-4" />
        Cetak Laporan
      </button>
    </div>

    <!-- Filter Periode -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-4">
        <div class="flex rounded-xl overflow-hidden border border-gray-200 dark:border-gray-600">
          <button
            @click="periodeType = 'bulan'; loadData()"
            :class="periodeType === 'bulan'
              ? 'bg-[#2d4a3e] text-white'
              : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'"
            class="px-5 py-2 text-sm font-bold transition"
          >Per Bulan</button>
          <button
            @click="periodeType = 'tahun'; loadData()"
            :class="periodeType === 'tahun'
              ? 'bg-[#2d4a3e] text-white'
              : 'bg-white dark:bg-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-600'"
            class="px-5 py-2 text-sm font-bold transition"
          >Per Tahun</button>
        </div>

        <input
          v-if="periodeType === 'bulan'"
          v-model="selectedBulan"
          type="month"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        />
        <select
          v-else
          v-model="selectedTahun"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        >
          <option v-for="y in tahunOptions" :key="y" :value="y">{{ y }}</option>
        </select>

        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
          Periode: <span class="text-gray-800 dark:text-white">{{ periodeLabel }}</span>
        </span>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="flex justify-center items-center py-20">
      <div class="w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
    </div>

    <template v-else>
      <!-- KPI Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
              <TrendingUp class="w-5 h-5 text-emerald-600" />
            </div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pendapatan</p>
          </div>
          <p class="text-2xl font-bold text-emerald-600">Rp {{ formatCurrency(data.total_pemasukan) }}</p>
        </div>

        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
              <TrendingDown class="w-5 h-5 text-rose-500" />
            </div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Beban</p>
          </div>
          <p class="text-2xl font-bold text-rose-500">Rp {{ formatCurrency(data.total_pengeluaran) }}</p>
        </div>

        <div
          class="p-5 rounded-2xl border shadow-sm"
          :class="data.is_laba
            ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800'
            : 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800'"
        >
          <div class="flex items-center gap-3 mb-3">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center"
              :class="data.is_laba ? 'bg-emerald-100 dark:bg-emerald-800' : 'bg-rose-100 dark:bg-rose-800'"
            >
              <CheckCircle v-if="data.is_laba" class="w-5 h-5 text-emerald-600" />
              <XCircle v-else class="w-5 h-5 text-rose-500" />
            </div>
            <p class="text-xs font-bold uppercase tracking-wider" :class="data.is_laba ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
              {{ data.is_laba ? 'Laba Bersih' : 'Rugi Bersih' }}
            </p>
          </div>
          <p class="text-2xl font-bold" :class="data.is_laba ? 'text-emerald-600' : 'text-rose-500'">
            Rp {{ formatCurrency(Math.abs(data.laba_rugi_bersih)) }}
          </p>
          <p class="text-xs mt-1" :class="data.is_laba ? 'text-emerald-600/70' : 'text-rose-500/70'">
            {{ data.is_laba ? '↑ Surplus' : '↓ Defisit' }} periode ini
          </p>
        </div>
      </div>

      <!-- Tabel Pendapatan & Beban -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- PENDAPATAN -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div class="px-6 py-4 bg-emerald-50 dark:bg-emerald-900/20 border-b border-emerald-100 dark:border-emerald-800">
            <h2 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider flex items-center gap-2">
              <TrendingUp class="w-4 h-4" /> Pendapatan (Pemasukan)
            </h2>
          </div>
          <table class="w-full">
            <thead>
              <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/40">
                <th class="px-6 py-3 text-left">Sumber</th>
                <th class="px-6 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-right">%</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr
                v-for="item in data.breakdown_pemasukan"
                :key="item.tipe"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                :class="item.nominal === 0 ? 'opacity-40' : ''"
              >
                <td class="px-6 py-3">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" :class="getDotClass(item.tipe)"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ item.label }}</span>
                  </div>
                  <p v-if="item.jumlah > 0" class="text-xs text-gray-400 mt-0.5 ml-4">{{ item.jumlah }} unit</p>
                </td>
                <td class="px-6 py-3 text-right text-sm font-semibold text-emerald-600">
                  Rp {{ formatCurrency(item.nominal) }}
                </td>
                <td class="px-4 py-3 text-right">
                  <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ item.persentase }}%</span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-emerald-50 dark:bg-emerald-900/20 border-t-2 border-emerald-200 dark:border-emerald-800">
                <td class="px-6 py-4 text-sm font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Total Pendapatan</td>
                <td class="px-6 py-4 text-right text-base font-black text-emerald-600">Rp {{ formatCurrency(data.total_pemasukan) }}</td>
                <td class="px-4 py-4 text-right text-xs font-bold text-emerald-600">100%</td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- BEBAN -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div class="px-6 py-4 bg-rose-50 dark:bg-rose-900/20 border-b border-rose-100 dark:border-rose-800">
            <h2 class="text-sm font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider flex items-center gap-2">
              <TrendingDown class="w-4 h-4" /> Beban (Pengeluaran)
            </h2>
          </div>
          <table class="w-full">
            <thead>
              <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/40">
                <th class="px-6 py-3 text-left">Kategori</th>
                <th class="px-6 py-3 text-right">Nominal</th>
                <th class="px-4 py-3 text-right">%</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr
                v-for="item in data.breakdown_pengeluaran"
                :key="item.kategori"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
                :class="item.nominal === 0 ? 'opacity-40' : ''"
              >
                <td class="px-6 py-3">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400 flex-shrink-0"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ item.label }}</span>
                  </div>
                </td>
                <td class="px-6 py-3 text-right text-sm font-semibold text-rose-500">
                  Rp {{ formatCurrency(item.nominal) }}
                </td>
                <td class="px-4 py-3 text-right">
                  <span class="text-xs font-bold text-gray-500 dark:text-gray-400">{{ item.persentase }}%</span>
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-rose-50 dark:bg-rose-900/20 border-t-2 border-rose-200 dark:border-rose-800">
                <td class="px-6 py-4 text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider">Total Beban</td>
                <td class="px-6 py-4 text-right text-base font-black text-rose-500">Rp {{ formatCurrency(data.total_pengeluaran) }}</td>
                <td class="px-4 py-4 text-right text-xs font-bold text-rose-500">100%</td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Ringkasan Laba / Rugi Bersih -->
      <div
        class="rounded-2xl border-2 p-6 flex flex-col sm:flex-row items-center justify-between gap-4"
        :class="data.is_laba
          ? 'border-emerald-400 dark:border-emerald-700 bg-gradient-to-r from-emerald-50 to-teal-50 dark:from-emerald-900/20 dark:to-teal-900/10'
          : 'border-rose-400 dark:border-rose-700 bg-gradient-to-r from-rose-50 to-pink-50 dark:from-rose-900/20 dark:to-pink-900/10'"
      >
        <div>
          <p class="text-xs font-black uppercase tracking-widest mb-1"
            :class="data.is_laba ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
            ══ {{ data.is_laba ? 'LABA' : 'RUGI' }} BERSIH PERIODE {{ periodeLabel.toUpperCase() }} ══
          </p>
          <p class="text-3xl font-black" :class="data.is_laba ? 'text-emerald-600' : 'text-rose-500'">
            {{ data.is_laba ? '' : '- ' }}Rp {{ formatCurrency(Math.abs(data.laba_rugi_bersih)) }}
          </p>
          <p class="text-sm mt-1 text-gray-500 dark:text-gray-400">
            Pendapatan Rp {{ formatCurrency(data.total_pemasukan) }} &minus; Beban Rp {{ formatCurrency(data.total_pengeluaran) }}
          </p>
        </div>
        <div
          class="w-20 h-20 rounded-full flex items-center justify-center text-4xl flex-shrink-0"
          :class="data.is_laba ? 'bg-emerald-100 dark:bg-emerald-800' : 'bg-rose-100 dark:bg-rose-800'"
        >
          {{ data.is_laba ? '✅' : '❌' }}
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import { TrendingUp, TrendingDown, CheckCircle, XCircle, Printer } from 'lucide-vue-next'

const loading     = ref(false)
const periodeType = ref('bulan')
const selectedBulan  = ref(new Date().toISOString().slice(0, 7))
const selectedTahun  = ref(new Date().getFullYear().toString())
const tahunOptions   = ref([])

const data = ref({
  total_pemasukan: 0,
  total_pengeluaran: 0,
  laba_rugi_bersih: 0,
  is_laba: true,
  breakdown_pemasukan: [],
  breakdown_pengeluaran: [],
})

// Generate tahun dropdown 6 tahun ke belakang
for (let y = new Date().getFullYear(); y >= new Date().getFullYear() - 5; y--) {
  tahunOptions.value.push(y.toString())
}

const periodeLabel = computed(() => {
  if (periodeType.value === 'bulan') {
    const [y, m] = selectedBulan.value.split('-')
    return new Date(parseInt(y), parseInt(m) - 1, 1)
      .toLocaleDateString('id-ID', { year: 'numeric', month: 'long' })
  }
  return `Tahun ${selectedTahun.value}`
})

const formatCurrency = (v) => new Intl.NumberFormat('id-ID').format(v || 0)

const getDotClass = (tipe) => {
  const map = {
    tiket_masuk:      'bg-emerald-500',
    tiket_event:      'bg-teal-500',
    pendapatan_jasa:  'bg-cyan-500',
    penjualan_produk: 'bg-indigo-500',
    donasi:           'bg-blue-500',
    sponsor:          'bg-purple-500',
    grant:            'bg-amber-500',
    lainnya:          'bg-gray-400',
  }
  return map[tipe] || 'bg-gray-400'
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {
      periode_type: periodeType.value,
      periode: periodeType.value === 'bulan' ? selectedBulan.value : selectedTahun.value,
    }
    const res = await api.get('/admin/keuangan/laba-rugi', { params })
    data.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data laba rugi:', err)
  } finally {
    loading.value = false
  }
}

const printReport = () => window.print()

onMounted(() => loadData())
</script>

<style scoped>
@media print {
  button { display: none !important; }
}
</style>
