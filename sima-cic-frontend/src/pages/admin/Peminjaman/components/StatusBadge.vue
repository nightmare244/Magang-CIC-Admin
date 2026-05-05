<template>
  <div class="inline-flex items-center font-poppins">
    <span
      class="status-badge-eco"
      :class="badgeStyles(status)"
    >
      <span class="dot-indicator" :class="dotStyles(status)"></span>
      
      <span class="tracking-[0.2em] leading-none uppercase">
        {{ statusLabel(status) }}
      </span>
    </span>
  </div>
</template>

<script setup>
/**
 * Komponen StatusBadge dengan gaya Modern Eco-Industrial.
 * Menggunakan standar tipografi Poppins dan palet warna sistem.
 */
const props = defineProps({
  status: {
    type: String,
    default: 'pending'
  }
});

/**
 * Pemetaan Label untuk User Interface dengan format kapital industri.
 */
const statusLabel = (s) => {
  const lowerStatus = s ? s.toLowerCase() : '';
  switch (lowerStatus) {
    case 'pending': return 'Waiting Verification';
    case 'disetujui': return 'Authorized / In Use';
    case 'selesai': return 'Returned / Archived';
    case 'ditolak': return 'Access Denied';
    default: return s;
  }
};

/**
 * Logika Pewarnaan Badge menggunakan palet Eco-Industrial.
 */
const badgeStyles = (s) => {
  const lowerStatus = s ? s.toLowerCase() : '';
  
  switch (lowerStatus) {
    case "pending":
      return "bg-amber-50 text-amber-700 border-amber-100 dark:bg-amber-500/10 dark:text-amber-400 dark:border-amber-500/20 shadow-lg shadow-amber-500/5";
    case "disetujui":
      return "bg-emerald-50 text-emerald-700 border-emerald-100 dark:bg-emerald-500/10 dark:text-emerald-400 dark:border-emerald-500/20 shadow-lg shadow-emerald-500/5";
    case "selesai":
      return "bg-sky-50 text-sky-700 border-sky-100 dark:bg-sky-500/10 dark:text-sky-400 dark:border-sky-500/20 shadow-lg shadow-sky-500/5";
    case "ditolak":
      return "bg-rose-50 text-rose-700 border-rose-100 dark:bg-rose-500/10 dark:text-rose-400 dark:border-rose-500/20 shadow-lg shadow-rose-500/5";
    default:
      return "bg-slate-50 text-slate-600 border-slate-200 dark:bg-slate-700/30 dark:text-slate-400 dark:border-slate-700";
  }
};

/**
 * Logika Indikator Cahaya sesuai status.
 */
const dotStyles = (s) => {
  const lowerStatus = s ? s.toLowerCase() : '';
  switch (lowerStatus) {
    case "pending": return "bg-amber-500 shadow-amber-500/40";
    case "disetujui": return "bg-emerald-500 shadow-emerald-500/40";
    case "selesai": return "bg-sky-500 shadow-sky-500/40";
    case "ditolak": return "bg-rose-500 shadow-rose-500/40";
    default: return "bg-slate-400 shadow-slate-400/40";
  }
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.status-badge-eco {
  @apply flex items-center px-4 py-1.5 rounded-xl text-[9px] font-black border 
         transition-all duration-500 select-none backdrop-blur-sm shadow-sm;
}

.dot-indicator {
  @apply w-1.5 h-1.5 rounded-full mr-2.5 animate-pulse shadow-[0_0_10px];
}

/* Interaksi Hover Premium */
.status-badge-eco:hover {
  @apply scale-105 brightness-105 shadow-md;
}
</style>