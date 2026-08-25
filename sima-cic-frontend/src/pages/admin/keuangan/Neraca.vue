<template>
  <div class="space-y-6 p-6 font-poppins">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Neraca Keuangan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Balance Sheet — Aktiva vs Pasiva</p>
      </div>
      <button
        @click="printReport"
        class="flex items-center gap-2 px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm"
      >
        <Printer class="w-4 h-4" />
        Cetak Neraca
      </button>
    </div>

    <!-- Filter Tahun -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap items-center gap-4">
        <label class="text-sm font-bold text-gray-600 dark:text-gray-300">Filter Tahun:</label>

        <select
          v-model="selectedTahun"
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        >
          <option value="">Semua Periode</option>
          <option v-for="y in data.tahun_tersedia" :key="y" :value="y.toString()">{{ y }}</option>
        </select>

        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">
          Menampilkan: <span class="text-gray-800 dark:text-white font-bold">
            {{ selectedTahun ? `Tahun ${selectedTahun}` : 'Seluruh Periode' }}
          </span>
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
        <!-- Total Aktiva -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
              <Building class="w-5 h-5 text-emerald-600" />
            </div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Aktiva</p>
          </div>
          <p class="text-2xl font-bold text-emerald-600">Rp {{ formatCurrency(data.total_aktiva) }}</p>
          <p class="text-xs text-gray-400 mt-1">Total pemasukan kumulatif</p>
        </div>

        <!-- Total Pasiva -->
        <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
              <Wallet class="w-5 h-5 text-rose-500" />
            </div>
            <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pasiva</p>
          </div>
          <p class="text-2xl font-bold text-rose-500">Rp {{ formatCurrency(data.total_pasiva) }}</p>
          <p class="text-xs text-gray-400 mt-1">Total pengeluaran kumulatif</p>
        </div>

        <!-- Saldo Bersih -->
        <div
          class="p-5 rounded-2xl border shadow-sm"
          :class="data.is_surplus
            ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-800'
            : 'bg-rose-50 dark:bg-rose-900/20 border-rose-200 dark:border-rose-800'"
        >
          <div class="flex items-center gap-3 mb-3">
            <div
              class="w-10 h-10 rounded-xl flex items-center justify-center"
              :class="data.is_surplus ? 'bg-emerald-100 dark:bg-emerald-800' : 'bg-rose-100 dark:bg-rose-800'"
            >
              <Scale class="w-5 h-5" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'" />
            </div>
            <p class="text-xs font-bold uppercase tracking-wider"
              :class="data.is_surplus ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'">
              Saldo / Ekuitas
            </p>
          </div>
          <p class="text-2xl font-bold" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'">
            {{ data.is_surplus ? '' : '- ' }}Rp {{ formatCurrency(Math.abs(data.saldo_bersih)) }}
          </p>
          <p class="text-xs mt-1" :class="data.is_surplus ? 'text-emerald-600/70' : 'text-rose-500/70'">
            {{ data.is_surplus ? '↑ Surplus' : '↓ Defisit' }}
          </p>
        </div>
      </div>

      <!-- Tabel Neraca Dua Kolom -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- AKTIVA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-emerald-50 dark:bg-emerald-900/20">
            <h2 class="text-sm font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-wider flex items-center gap-2">
              <Building class="w-4 h-4" />
              AKTIVA — Sumber Pendapatan
            </h2>
            <p class="text-xs text-emerald-600/70 dark:text-emerald-400/70 mt-0.5">Kumulatif pemasukan per sumber</p>
          </div>

          <div v-if="data.aktiva.length === 0" class="px-6 py-10 text-center text-gray-400 text-sm">
            Belum ada data aktiva untuk periode ini
          </div>

          <table v-else class="w-full">
            <thead>
              <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/40">
                <th class="px-6 py-3 text-left">Sumber Pendapatan</th>
                <th class="px-6 py-3 text-right">Nominal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr
                v-for="item in data.aktiva"
                :key="item.tipe"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
              >
                <td class="px-6 py-3.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full flex-shrink-0" :class="getDotClass(item.tipe)"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ item.label }}</span>
                  </div>
                </td>
                <td class="px-6 py-3.5 text-right text-sm font-semibold text-emerald-600">
                  Rp {{ formatCurrency(item.nominal) }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-emerald-50 dark:bg-emerald-900/20 border-t-2 border-emerald-300 dark:border-emerald-700">
                <td class="px-6 py-4 text-sm font-black text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">
                  Total Aktiva
                </td>
                <td class="px-6 py-4 text-right text-base font-black text-emerald-600">
                  Rp {{ formatCurrency(data.total_aktiva) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>

        <!-- PASIVA -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
          <div class="px-6 py-4 border-b border-gray-100 dark:border-gray-700 bg-rose-50 dark:bg-rose-900/20">
            <h2 class="text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider flex items-center gap-2">
              <Wallet class="w-4 h-4" />
              PASIVA — Beban & Ekuitas
            </h2>
            <p class="text-xs text-rose-600/70 dark:text-rose-400/70 mt-0.5">Kumulatif pengeluaran per kategori</p>
          </div>

          <div v-if="data.pasiva.length === 0" class="px-6 py-10 text-center text-gray-400 text-sm">
            Belum ada data pasiva untuk periode ini
          </div>

          <table v-else class="w-full">
            <thead>
              <tr class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider bg-gray-50 dark:bg-gray-700/40">
                <th class="px-6 py-3 text-left">Kategori Beban</th>
                <th class="px-6 py-3 text-right">Nominal</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
              <tr
                v-for="item in data.pasiva"
                :key="item.kategori"
                class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
              >
                <td class="px-6 py-3.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-rose-400 flex-shrink-0"></span>
                    <span class="text-sm font-medium text-gray-800 dark:text-white">{{ item.label }}</span>
                  </div>
                </td>
                <td class="px-6 py-3.5 text-right text-sm font-semibold text-rose-500">
                  Rp {{ formatCurrency(item.nominal) }}
                </td>
              </tr>

              <!-- Baris Ekuitas / Saldo -->
              <tr class="bg-gray-50 dark:bg-gray-700/30">
                <td class="px-6 py-3.5">
                  <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full flex-shrink-0"
                      :class="data.is_surplus ? 'bg-emerald-500' : 'bg-rose-500'"></span>
                    <span class="text-sm font-bold" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'">
                      Ekuitas / Saldo Bersih
                    </span>
                  </div>
                </td>
                <td class="px-6 py-3.5 text-right text-sm font-bold" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'">
                  {{ data.is_surplus ? '' : '- ' }}Rp {{ formatCurrency(Math.abs(data.saldo_bersih)) }}
                </td>
              </tr>
            </tbody>
            <tfoot>
              <tr class="bg-rose-50 dark:bg-rose-900/20 border-t-2 border-rose-300 dark:border-rose-700">
                <td class="px-6 py-4 text-sm font-black text-rose-700 dark:text-rose-400 uppercase tracking-wider">
                  Total Pasiva + Ekuitas
                </td>
                <td class="px-6 py-4 text-right text-base font-black text-rose-500">
                  Rp {{ formatCurrency(data.total_aktiva) }}
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>

      <!-- Footer Neraca Balance -->
      <div class="rounded-2xl border-2 border-gray-300 dark:border-gray-600 bg-gradient-to-r from-gray-50 to-slate-50 dark:from-gray-800 dark:to-gray-700 p-6">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-6">
          <div class="text-center sm:text-left">
            <p class="text-xs font-black uppercase tracking-widest text-gray-500 dark:text-gray-400 mb-2">
              ══ POSISI KEUANGAN ══
            </p>
            <div class="flex items-center gap-4 flex-wrap justify-center sm:justify-start">
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Aktiva</p>
                <p class="text-lg font-black text-emerald-600">Rp {{ formatCurrency(data.total_aktiva) }}</p>
              </div>
              <div class="text-2xl text-gray-400 font-light">=</div>
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Pasiva</p>
                <p class="text-lg font-black text-rose-500">Rp {{ formatCurrency(data.total_pasiva) }}</p>
              </div>
              <div class="text-2xl text-gray-400 font-light">+</div>
              <div>
                <p class="text-xs text-gray-400 uppercase tracking-wider">Ekuitas</p>
                <p class="text-lg font-black" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'">
                  Rp {{ formatCurrency(Math.abs(data.saldo_bersih)) }}
                </p>
              </div>
            </div>
          </div>
          <div class="flex-shrink-0">
            <div
              class="w-20 h-20 rounded-full flex items-center justify-center"
              :class="data.is_surplus ? 'bg-emerald-100 dark:bg-emerald-900' : 'bg-rose-100 dark:bg-rose-900'"
            >
              <Scale class="w-10 h-10" :class="data.is_surplus ? 'text-emerald-600' : 'text-rose-500'" />
            </div>
          </div>
        </div>
      </div>
    </template>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { Building, Wallet, Scale, Printer } from 'lucide-vue-next'

const loading      = ref(false)
const selectedTahun = ref('')

const data = ref({
  tahun: null,
  tahun_tersedia: [],
  total_aktiva:  0,
  total_pasiva:  0,
  saldo_bersih:  0,
  is_surplus:    true,
  aktiva:        [],
  pasiva:        [],
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
    const params = {}
    if (selectedTahun.value) params.tahun = selectedTahun.value

    const res = await api.get('/admin/keuangan/neraca', { params })
    data.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data neraca:', err)
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
