<template>
  <div class="space-y-6">
    <!-- Balance Indicator Banner (Disembunyikan saat dicetak) -->
    <div 
      class="p-4 rounded-2xl border flex items-center justify-between no-print"
      :class="report.is_balanced 
        ? 'bg-emerald-50 dark:bg-emerald-950/20 border-emerald-300 dark:border-emerald-800 text-emerald-900 dark:text-emerald-200' 
        : 'bg-rose-50 dark:bg-rose-950/20 border-rose-300 dark:border-rose-800 text-rose-900 dark:text-rose-200'"
    >
      <div class="flex items-center gap-3">
        <div 
          class="w-10 h-10 rounded-xl flex items-center justify-center font-bold"
          :class="report.is_balanced ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'"
        >
          <Scale class="w-5 h-5" />
        </div>
        <div>
          <p class="font-bold text-sm">
            {{ report.is_balanced ? 'Status Neraca: Seimbang (Balanced)' : 'Status Neraca: Tidak Seimbang' }}
          </p>
          <p class="text-xs opacity-80">
            Total Aktiva (Aset) = Total Pasiva (Kewajiban + Ekuitas) per tanggal {{ formatDate(report.per_tanggal) }}
          </p>
        </div>
      </div>
      <div class="text-right font-mono text-xs font-bold">
        Selisih: Rp {{ formatCurrency(report.selisih || 0) }}
      </div>
    </div>

    <!-- Statement of Financial Position (Neraca) -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 shadow-sm space-y-6 printable-area">
      <!-- Title -->
      <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">NERACA KEUANGAN (BALANCE SHEET)</h2>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">Posisi Per Tanggal: {{ formatDate(report.per_tanggal) }}</p>
      </div>

      <!-- 2 Columns Grid: Aset (Aktiva) vs Kewajiban & Ekuitas (Pasiva) -->
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        
        <!-- ================= SISI KIRI: ASET (AKTIVA) ================= -->
        <div class="space-y-4">
          <div class="bg-emerald-50/60 dark:bg-emerald-950/30 px-4 py-2.5 rounded-xl flex justify-between items-center border border-emerald-100 dark:border-emerald-900/40">
            <h3 class="font-bold text-sm text-emerald-900 dark:text-emerald-200 uppercase tracking-wider">
              ASET (AKTIVA)
            </h3>
            <span class="text-xs font-bold text-emerald-700 dark:text-emerald-300">Nominal (Rp)</span>
          </div>

          <!-- Aset Lancar -->
          <div class="space-y-2 pl-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Aset Lancar (Kas & Bank):</p>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/70 text-xs sm:text-sm">
              <div 
                v-for="(item, idx) in report.aset?.items || []" 
                :key="idx"
                class="py-2.5 flex justify-between items-center"
              >
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                    {{ item.kode_akun }}
                  </span>
                  <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.nama_akun }}</span>
                </div>
                <span class="font-mono font-bold text-gray-900 dark:text-white">
                  Rp {{ formatCurrency(item.nominal) }}
                </span>
              </div>
            </div>
          </div>

          <!-- Total Aset -->
          <div class="p-4 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/40 font-black text-sm text-emerald-950 dark:text-emerald-200 flex justify-between items-center border-t-2 border-emerald-500">
            <span class="uppercase">TOTAL ASET (AKTIVA)</span>
            <span class="font-mono text-base text-emerald-700 dark:text-emerald-300 font-black">
              Rp {{ formatCurrency(report.aset?.total || 0) }}
            </span>
          </div>
        </div>

        <!-- ================= SISI KANAN: KEWAJIBAN & EKUITAS (PASIVA) ================= -->
        <div class="space-y-4">
          <div class="bg-blue-50/60 dark:bg-blue-950/30 px-4 py-2.5 rounded-xl flex justify-between items-center border border-blue-100 dark:border-blue-900/40">
            <h3 class="font-bold text-sm text-blue-900 dark:text-blue-200 uppercase tracking-wider">
              KEWAJIBAN & EKUITAS (PASIVA)
            </h3>
            <span class="text-xs font-bold text-blue-700 dark:text-blue-300">Nominal (Rp)</span>
          </div>

          <!-- 1. Kewajiban -->
          <div class="space-y-2 pl-2">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">1. Kewajiban (Liabilitas):</p>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/70 text-xs sm:text-sm">
              <div 
                v-for="(item, idx) in report.kewajiban?.items || []" 
                :key="idx"
                class="py-2.5 flex justify-between items-center"
              >
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                    {{ item.kode_akun }}
                  </span>
                  <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.nama_akun }}</span>
                </div>
                <span class="font-mono font-bold text-gray-900 dark:text-white">
                  Rp {{ formatCurrency(item.nominal) }}
                </span>
              </div>
            </div>
            <div class="flex justify-between items-center text-xs font-bold text-gray-600 dark:text-gray-400 pt-1">
              <span>Total Kewajiban</span>
              <span class="font-mono">Rp {{ formatCurrency(report.kewajiban?.total || 0) }}</span>
            </div>
          </div>

          <!-- 2. Ekuitas / Modal -->
          <div class="space-y-2 pl-2 pt-2 border-t border-gray-100 dark:border-gray-700">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">2. Ekuitas / Modal:</p>
            <div class="divide-y divide-gray-100 dark:divide-gray-700/70 text-xs sm:text-sm">
              <div 
                v-for="(item, idx) in report.ekuitas?.items || []" 
                :key="idx"
                class="py-2.5 flex justify-between items-center"
              >
                <div class="flex items-center gap-2">
                  <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                    {{ item.kode_akun }}
                  </span>
                  <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.nama_akun }}</span>
                </div>
                <span class="font-mono font-bold text-gray-900 dark:text-white">
                  Rp {{ formatCurrency(item.nominal) }}
                </span>
              </div>
            </div>
            <div class="flex justify-between items-center text-xs font-bold text-gray-600 dark:text-gray-400 pt-1">
              <span>Total Ekuitas</span>
              <span class="font-mono">Rp {{ formatCurrency(report.ekuitas?.total || 0) }}</span>
            </div>
          </div>

          <!-- Total Kewajiban & Ekuitas -->
          <div class="p-4 rounded-xl bg-blue-50/80 dark:bg-blue-950/40 font-black text-sm text-blue-950 dark:text-blue-200 flex justify-between items-center border-t-2 border-blue-500">
            <span class="uppercase">TOTAL PASIVA</span>
            <span class="font-mono text-base text-blue-700 dark:text-blue-300 font-black">
              Rp {{ formatCurrency(report.total_kewajiban_ekuitas || 0) }}
            </span>
          </div>
        </div>

      </div>
    </div>
  </div>
</template>

<script setup>
import { Scale } from 'lucide-vue-next'

defineProps({
  report: {
    type: Object,
    required: true,
  }
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}
</script>
