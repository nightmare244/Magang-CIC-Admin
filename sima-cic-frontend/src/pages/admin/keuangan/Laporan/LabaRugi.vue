<template>
  <div class="space-y-6">
    <!-- Header & Summary Cards (Disembunyikan saat dicetak) -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 no-print">
      <div class="bg-blue-50/70 dark:bg-blue-950/20 p-5 rounded-2xl border border-blue-200 dark:border-blue-800">
        <p class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Total Pendapatan</p>
        <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">
          Rp {{ formatCurrency(report.pendapatan?.total || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">Penjualan tiket & sumber dana</p>
      </div>

      <div class="bg-rose-50/70 dark:bg-rose-950/20 p-5 rounded-2xl border border-rose-200 dark:border-rose-800">
        <p class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">Total Beban Operasional</p>
        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
          Rp {{ formatCurrency(report.beban?.total || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">Gaji, operasional & perawatan</p>
      </div>

      <div 
        class="p-5 rounded-2xl border"
        :class="report.laba_bersih >= 0 
          ? 'bg-emerald-50/70 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800' 
          : 'bg-amber-50/70 dark:bg-amber-950/20 border-amber-200 dark:border-amber-800'"
      >
        <p 
          class="text-xs font-bold uppercase tracking-wider"
          :class="report.laba_bersih >= 0 ? 'text-emerald-800 dark:text-emerald-300' : 'text-amber-800 dark:text-amber-300'"
        >
          Laba / (Rugi) Bersih
        </p>
        <p 
          class="text-2xl font-black mt-1"
          :class="report.laba_bersih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'"
        >
          Rp {{ formatCurrency(report.laba_bersih || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">
          Margin: <span class="font-bold">{{ report.rasio_laba_bersih || 0 }}%</span> ({{ report.status || '-' }})
        </p>
      </div>
    </div>

    <!-- Structured Income Statement Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 shadow-sm space-y-6 printable-area">
      <!-- Title -->
      <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">LAPORAN LABA RUGI (INCOME STATEMENT)</h2>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">Periode Berjalan: {{ report.periode || '-' }}</p>
      </div>

      <!-- I. PENDAPATAN OPERASIONAL -->
      <div class="space-y-3">
        <div class="flex justify-between items-center bg-blue-50/60 dark:bg-blue-950/30 px-4 py-2.5 rounded-xl">
          <h3 class="font-bold text-sm text-blue-900 dark:text-blue-200 uppercase tracking-wider">
            I. PENDAPATAN OPERASIONAL
          </h3>
          <span class="text-xs font-bold text-blue-700 dark:text-blue-300">Nominal (Rp)</span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700/70 text-xs sm:text-sm pl-2">
          <div 
            v-for="(item, idx) in report.pendapatan?.items || []" 
            :key="idx"
            class="py-2.5 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/20 px-2 rounded-lg transition"
          >
            <div class="flex items-center gap-2">
              <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                {{ item.kode_akun }}
              </span>
              <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.nama_akun }}</span>
              <span class="text-[11px] text-gray-400">({{ item.persentase }}%)</span>
            </div>
            <span class="font-mono font-bold text-gray-900 dark:text-white">
              Rp {{ formatCurrency(item.nominal) }}
            </span>
          </div>
        </div>

        <div class="flex justify-between items-center px-4 py-3 rounded-xl bg-blue-50/80 dark:bg-blue-950/40 font-black text-sm text-blue-950 dark:text-blue-200">
          <span>TOTAL PENDAPATAN OPERASIONAL</span>
          <span class="font-mono text-base text-blue-700 dark:text-blue-300">Rp {{ formatCurrency(report.pendapatan?.total || 0) }}</span>
        </div>
      </div>

      <!-- II. BEBAN OPERASIONAL -->
      <div class="space-y-3 pt-2">
        <div class="flex justify-between items-center bg-rose-50/60 dark:bg-rose-950/30 px-4 py-2.5 rounded-xl">
          <h3 class="font-bold text-sm text-rose-900 dark:text-rose-200 uppercase tracking-wider">
            II. BEBAN OPERASIONAL
          </h3>
          <span class="text-xs font-bold text-rose-700 dark:text-rose-300">Nominal (Rp)</span>
        </div>

        <div class="divide-y divide-gray-100 dark:divide-gray-700/70 text-xs sm:text-sm pl-2">
          <div 
            v-for="(item, idx) in report.beban?.items || []" 
            :key="idx"
            class="py-2.5 flex justify-between items-center hover:bg-gray-50 dark:hover:bg-gray-700/20 px-2 rounded-lg transition"
          >
            <div class="flex items-center gap-2">
              <span class="font-mono text-xs px-2 py-0.5 rounded bg-gray-100 dark:bg-gray-700 font-bold text-gray-700 dark:text-gray-300">
                {{ item.kode_akun }}
              </span>
              <span class="text-gray-800 dark:text-gray-200 font-medium">{{ item.nama_akun }}</span>
              <span class="text-[11px] text-gray-400">({{ item.persentase }}%)</span>
            </div>
            <span class="font-mono font-bold text-gray-900 dark:text-white">
              Rp {{ formatCurrency(item.nominal) }}
            </span>
          </div>
        </div>

        <div class="flex justify-between items-center px-4 py-3 rounded-xl bg-rose-50/80 dark:bg-rose-950/40 font-black text-sm text-rose-950 dark:text-rose-200">
          <span>TOTAL BEBAN OPERASIONAL</span>
          <span class="font-mono text-base text-rose-700 dark:text-rose-300">(Rp {{ formatCurrency(report.beban?.total || 0) }})</span>
        </div>
      </div>

      <!-- III. HASIL BERSIH LABA / (RUGI) -->
      <div class="pt-4 border-t-2 border-gray-300 dark:border-gray-600">
        <div 
          class="flex justify-between items-center p-5 rounded-2xl border-2 font-black"
          :class="report.laba_bersih >= 0 
            ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-300 dark:border-emerald-700 text-emerald-950 dark:text-emerald-200' 
            : 'bg-amber-50 dark:bg-amber-950/30 border-amber-300 dark:border-amber-700 text-amber-950 dark:text-amber-200'"
        >
          <div>
            <p class="text-sm sm:text-base uppercase tracking-wider">LABA / (RUGI) BERSIH OPERASIONAL</p>
            <p class="text-xs font-normal opacity-80 mt-0.5">Total Pendapatan dikurangi Total Beban Operasional</p>
          </div>
          <p 
            class="font-mono text-xl sm:text-2xl font-black"
            :class="report.laba_bersih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-amber-600 dark:text-amber-400'"
          >
            Rp {{ formatCurrency(report.laba_bersih || 0) }}
          </p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
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
</script>
