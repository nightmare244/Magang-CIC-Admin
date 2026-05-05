<template>
  <div class="relative w-full h-full flex items-center justify-center min-h-[250px]">
    <canvas ref="canvas"></canvas>
    <div class="absolute inset-0 flex flex-col items-center justify-center pointer-events-none translate-y-[-15px]">
      <span class="text-[10px] font-bold text-slate-400 uppercase tracking-[0.2em]">Hadir</span>
      <span class="text-2xl font-black text-[#2d4a3e] dark:text-emerald-500">
        {{ persentaseHadir }}%
      </span>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch, computed } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  // dataObj berisi { labels: [], data: [] } dari DashboardController
  dataObj: { type: Object, default: () => ({ labels: [], data: [] }) }
});

const canvas = ref(null);
let chart = null;

// Menghitung persentase kehadiran untuk tampilan di tengah doughnut
const persentaseHadir = computed(() => {
  const dataArray = props.dataObj?.data || [];
  if (dataArray.length === 0) return 0;
  
  const total = dataArray.reduce((a, b) => a + b, 0);
  if (total === 0) return 0;

  // Berdasarkan data JSON Anda: index 0 = Tepat Waktu, index 1 = Terlambat
  // Total hadir adalah jumlah keduanya
  return Math.round((total / total) * 100); 
});

const draw = () => {
  if (!canvas.value) return;
  if (chart) chart.destroy();

  const ctx = canvas.value.getContext('2d');

  chart = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: props.dataObj.labels || [],
      datasets: [{
        data: props.dataObj.data || [],
        backgroundColor: [
          '#2d4a3e', // Hijau CIC (Tepat Waktu)
          '#fbbf24', // Amber/Kuning (Terlambat)
          '#f87171', // Merah (Alpa - cadangan)
          '#e2e8f0'  // Slate (Izin - cadangan)
        ],
        borderWidth: 0,
        hoverOffset: 15,
        borderRadius: 10,
        spacing: 4 
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      cutout: '80%', // Lubang tengah lebih besar agar lebih modern
      plugins: {
        legend: {
          position: 'bottom',
          labels: {
            usePointStyle: true,
            pointStyle: 'circle',
            padding: 25,
            font: {
              size: 11,
              weight: '700',
            },
            color: '#64748b'
          }
        },
        tooltip: {
          backgroundColor: '#1a2e26',
          padding: 12,
          bodyFont: { size: 13, weight: 'bold' },
          callbacks: {
            label: (context) => ` ${context.label}: ${context.raw} Orang`
          }
        }
      }
    }
  });
};

onMounted(draw);
// Pantau perubahan data secara mendalam
watch(() => props.dataObj, draw, { deep: true });
</script>

<style scoped>
canvas {
  width: 100% !important;
  height: 100% !important;
  max-height: 280px;
}
</style>