<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-[#2d4a3e]/10 dark:bg-emerald-500/10 text-[#2d4a3e] dark:text-emerald-400">
          <BookMarked class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Jurnal Kas</h1>
          <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Buku kas masuk & keluar kronologis dengan saldo berjalan otomatis</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button 
          @click="printJurnal" 
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 transition font-medium text-xs shadow-sm"
        >
          <Printer class="w-4 h-4" />
          <span>Cetak Jurnal</span>
        </button>
      </div>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Saldo Awal -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Saldo Awal Kas</p>
        <p class="text-xl font-black text-gray-700 dark:text-gray-200 mt-1">Rp {{ formatCurrency(jurnalData.saldo_awal_periode) }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">Posisi kas awal periode</p>
      </div>

      <!-- Total Debit / Kas Masuk -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Total Debit (Masuk)</p>
        <p class="text-xl font-black text-emerald-600 dark:text-emerald-400 mt-1">Rp {{ formatCurrency(jurnalData.total_debit) }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">Penerimaan kas masuk</p>
      </div>

      <!-- Total Kredit / Kas Keluar -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Total Kredit (Keluar)</p>
        <p class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1">Rp {{ formatCurrency(jurnalData.total_kredit) }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">Pengeluaran kas keluar</p>
      </div>

      <!-- Saldo Akhir Kas -->
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Saldo Akhir Kas</p>
        <p class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">Rp {{ formatCurrency(jurnalData.saldo_akhir_periode) }}</p>
        <p class="text-[11px] text-gray-400 mt-0.5">Kas akhir periode ini</p>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-3 w-full sm:w-auto">
        <div>
          <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1">Periode Bulan</label>
          <input 
            v-model="filterBulan" 
            type="month" 
            class="px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500 outline-none"
            @change="loadJurnal"
          />
        </div>
        <div class="self-end">
          <p class="text-xs font-bold text-gray-700 dark:text-gray-300 py-2">
            Periode: <span class="text-emerald-600 dark:text-emerald-400">{{ jurnalData.periode || '-' }}</span>
          </p>
        </div>
      </div>

      <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">
        Menampilkan <span class="text-gray-900 dark:text-white font-bold">{{ jurnalData.entries ? jurnalData.entries.length : 0 }}</span> entri pembukuan
      </div>
    </div>

    <!-- Tabel Jurnal Kas -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm printable-area">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Tanggal</th>
              <th class="px-5 py-3.5">No. Bukti</th>
              <th class="px-5 py-3.5">Uraian / Keterangan</th>
              <th class="px-5 py-3.5">Akun Lawan (CoA)</th>
              <th class="px-5 py-3.5 text-right">Debit (Kas Masuk)</th>
              <th class="px-5 py-3.5 text-right">Kredit (Kas Keluar)</th>
              <th class="px-5 py-3.5 text-right bg-emerald-50/40 dark:bg-emerald-950/20">Saldo Berjalan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60 text-xs sm:text-sm">
            <!-- Row Saldo Awal -->
            <tr class="bg-gray-50/70 dark:bg-gray-750 font-bold text-gray-600 dark:text-gray-300 italic">
              <td class="px-5 py-3">-</td>
              <td class="px-5 py-3 font-mono">-</td>
              <td class="px-5 py-3" colspan="2">SALDO AWAL PERIODE</td>
              <td class="px-5 py-3 text-right">-</td>
              <td class="px-5 py-3 text-right">-</td>
              <td class="px-5 py-3 text-right font-mono font-black text-gray-900 dark:text-white bg-emerald-50/40 dark:bg-emerald-950/20">
                Rp {{ formatCurrency(jurnalData.saldo_awal_periode) }}
              </td>
            </tr>

            <!-- Entry Rows -->
            <tr 
              v-for="(row, idx) in jurnalData.entries" 
              :key="idx" 
              class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
            >
              <td class="px-5 py-3.5 whitespace-nowrap font-medium text-gray-900 dark:text-white">
                {{ formatDate(row.tanggal) }}
              </td>
              <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-gray-500 dark:text-gray-400">
                {{ row.no_bukti }}
              </td>
              <td class="px-5 py-3.5 font-medium text-gray-800 dark:text-gray-200">
                {{ row.uraian }}
              </td>
              <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs text-gray-600 dark:text-gray-300">
                {{ row.akun_lawan }}
              </td>
              <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap text-emerald-600 dark:text-emerald-400">
                {{ row.debit > 0 ? 'Rp ' + formatCurrency(row.debit) : '-' }}
              </td>
              <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap text-rose-600 dark:text-rose-400">
                {{ row.kredit > 0 ? 'Rp ' + formatCurrency(row.kredit) : '-' }}
              </td>
              <td class="px-5 py-3.5 text-right font-mono font-black whitespace-nowrap text-gray-900 dark:text-white bg-emerald-50/40 dark:bg-emerald-950/20">
                Rp {{ formatCurrency(row.saldo_berjalan) }}
              </td>
            </tr>
          </tbody>
          <tfoot class="bg-gray-100 dark:bg-gray-750 font-bold border-t-2 border-gray-300 dark:border-gray-600 text-xs sm:text-sm">
            <tr>
              <td class="px-5 py-4 text-gray-900 dark:text-white uppercase tracking-wider" colspan="4">
                TOTAL MUTASI & SALDO AKHIR
              </td>
              <td class="px-5 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-black">
                Rp {{ formatCurrency(jurnalData.total_debit) }}
              </td>
              <td class="px-5 py-4 text-right font-mono text-rose-600 dark:text-rose-400 font-black">
                Rp {{ formatCurrency(jurnalData.total_kredit) }}
              </td>
              <td class="px-5 py-4 text-right font-mono text-blue-600 dark:text-blue-400 font-black bg-emerald-100/50 dark:bg-emerald-900/30">
                Rp {{ formatCurrency(jurnalData.saldo_akhir_periode) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Loading / Empty -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Menghitung pembukuan jurnal kas...</p>
      </div>
      <div v-else-if="!jurnalData.entries || jurnalData.entries.length === 0" class="text-center py-16 text-gray-400">
        <BookMarked class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
        <p class="font-bold">Belum ada transaksi pada periode ini</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import { BookMarked, Printer } from 'lucide-vue-next'

const filterBulan = ref(new Date().toISOString().slice(0, 7))
const loading = ref(false)

const jurnalData = ref({
  periode: '',
  saldo_awal_periode: 0,
  total_debit: 0,
  total_kredit: 0,
  saldo_akhir_periode: 0,
  entries: [],
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })
}

const loadJurnal = async () => {
  loading.value = true
  try {
    const params = { bulan: filterBulan.value }
    const res = await api.get('/admin/keuangan/jurnal-kas', { params })
    jurnalData.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data jurnal kas:', err)
  } finally {
    loading.value = false
  }
}

const printJurnal = () => {
  window.print()
}

onMounted(() => {
  loadJurnal()
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
