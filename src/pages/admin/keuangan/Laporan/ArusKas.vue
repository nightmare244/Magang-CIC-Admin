<template>
  <div class="space-y-6">
    <!-- Header & Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-emerald-50/70 dark:bg-emerald-950/20 p-5 rounded-2xl border border-emerald-200 dark:border-emerald-800">
        <p class="text-xs font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider">Total Kas Masuk</p>
        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
          Rp {{ formatCurrency(report.arus_kas_masuk?.total || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">Dari operasional & tiket</p>
      </div>

      <div class="bg-rose-50/70 dark:bg-rose-950/20 p-5 rounded-2xl border border-rose-200 dark:border-rose-800">
        <p class="text-xs font-bold text-rose-800 dark:text-rose-300 uppercase tracking-wider">Total Kas Keluar</p>
        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">
          Rp {{ formatCurrency(report.arus_kas_keluar?.total || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">Beban operasional & gaji</p>
      </div>

      <div class="bg-blue-50/70 dark:bg-blue-950/20 p-5 rounded-2xl border border-blue-200 dark:border-blue-800">
        <p class="text-xs font-bold text-blue-800 dark:text-blue-300 uppercase tracking-wider">Saldo Kas Akhir</p>
        <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">
          Rp {{ formatCurrency(report.saldo_akhir_kas || 0) }}
        </p>
        <p class="text-[11px] text-gray-500 mt-1">Kas & bank posisi akhir</p>
      </div>
    </div>

    <!-- Structured Statement of Cash Flows -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 shadow-sm space-y-6 printable-area">
      <!-- Title -->
      <div class="text-center border-b border-gray-200 dark:border-gray-700 pb-4">
        <h2 class="text-xl font-black text-gray-900 dark:text-white uppercase tracking-tight">LAPORAN ARUS KAS (CASH FLOW)</h2>
        <p class="text-xs font-semibold text-gray-500 dark:text-gray-400 mt-0.5">Metode Langsung (Direct Method) — Periode: {{ report.periode || '-' }}</p>
      </div>

      <!-- I. ARUS KAS DARI AKTIVITAS OPERASIONAL -->
      <div class="space-y-4">
        <h3 class="font-bold text-sm text-gray-900 dark:text-white flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-[#2d4a3e]"></span>
          Aktivitas Operasional
        </h3>

        <!-- A. Kas Masuk -->
        <div class="space-y-2 pl-4 border-l-2 border-emerald-500/40">
          <p class="text-xs font-bold text-emerald-700 dark:text-emerald-400 uppercase tracking-wider">Arus Kas Masuk (Penerimaan):</p>
          <div class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
            <div 
              v-for="(item, idx) in report.arus_kas_masuk?.rincian || []" 
              :key="idx"
              class="py-2.5 flex justify-between items-center"
            >
              <span class="text-gray-700 dark:text-gray-300 font-medium">{{ item.nama_pos }}</span>
              <span class="font-mono font-bold text-gray-900 dark:text-white">Rp {{ formatCurrency(item.nominal) }}</span>
            </div>
            <div v-if="!report.arus_kas_masuk?.rincian || report.arus_kas_masuk.rincian.length === 0" class="py-2 text-xs text-gray-400 italic">
              Tidak ada penerimaan kas pada periode ini
            </div>
          </div>
          <div class="pt-2 flex justify-between items-center font-bold text-xs sm:text-sm text-emerald-800 dark:text-emerald-300 border-t border-dashed border-gray-200 dark:border-gray-700">
            <span>Total Penerimaan Kas Operasional</span>
            <span class="font-mono">Rp {{ formatCurrency(report.arus_kas_masuk?.total || 0) }}</span>
          </div>
        </div>

        <!-- B. Kas Keluar -->
        <div class="space-y-2 pl-4 border-l-2 border-rose-500/40 mt-4">
          <p class="text-xs font-bold text-rose-700 dark:text-rose-400 uppercase tracking-wider">Arus Kas Keluar (Pengeluaran):</p>
          <div class="divide-y divide-gray-100 dark:divide-gray-700 text-xs sm:text-sm">
            <div 
              v-for="(item, idx) in report.arus_kas_keluar?.rincian || []" 
              :key="idx"
              class="py-2.5 flex justify-between items-center"
            >
              <span class="text-gray-700 dark:text-gray-300 font-medium">{{ item.nama_pos }}</span>
              <span class="font-mono font-bold text-gray-900 dark:text-white">(Rp {{ formatCurrency(item.nominal) }})</span>
            </div>
            <div v-if="!report.arus_kas_keluar?.rincian || report.arus_kas_keluar.rincian.length === 0" class="py-2 text-xs text-gray-400 italic">
              Tidak ada pengeluaran kas pada periode ini
            </div>
          </div>
          <div class="pt-2 flex justify-between items-center font-bold text-xs sm:text-sm text-rose-800 dark:text-rose-300 border-t border-dashed border-gray-200 dark:border-gray-700">
            <span>Total Pengeluaran Kas Operasional</span>
            <span class="font-mono">(Rp {{ formatCurrency(report.arus_kas_keluar?.total || 0) }})</span>
          </div>
        </div>
      </div>

      <!-- II. ARUS KAS BERSIH & REKONSILIASI SALDO KAS -->
      <div class="pt-4 border-t-2 border-gray-300 dark:border-gray-600 space-y-3 font-semibold text-xs sm:text-sm">
        <div class="flex justify-between items-center p-3 rounded-xl bg-gray-50 dark:bg-gray-750 font-bold">
          <span class="text-gray-900 dark:text-white uppercase">Arus Kas Bersih Periode Berjalan</span>
          <span 
            class="font-mono text-base font-black"
            :class="report.arus_kas_bersih >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
          >
            {{ report.arus_kas_bersih >= 0 ? '+' : '' }} Rp {{ formatCurrency(report.arus_kas_bersih || 0) }}
          </span>
        </div>

        <div class="flex justify-between items-center px-3 py-1 text-gray-600 dark:text-gray-400">
          <span>Saldo Kas Pada Awal Periode</span>
          <span class="font-mono font-bold text-gray-800 dark:text-gray-200">Rp {{ formatCurrency(report.saldo_awal_kas || 0) }}</span>
        </div>

        <div class="flex justify-between items-center p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800 font-extrabold text-sm sm:text-base">
          <span class="text-emerald-950 dark:text-emerald-200 uppercase">SALDO KAS PADA AKHIR PERIODE</span>
          <span class="font-mono text-emerald-700 dark:text-emerald-300 font-black text-lg">
            Rp {{ formatCurrency(report.saldo_akhir_kas || 0) }}
          </span>
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
