<template>
  <div class="relative w-full h-full min-h-[250px] p-2">
    <canvas ref="canvas"></canvas>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  dataObj: { type: Object, default: () => ({ labels: [], data: [] }) }
});

const canvas = ref(null);
let chart = null;

const draw = () => {
  if (!canvas.value) return;
  if (chart) chart.destroy();

  const ctx = canvas.value.getContext('2d');
  
  // Membuat gradient horizontal untuk bar
  const gradient = ctx.createLinearGradient(0, 0, 400, 0);
  gradient.addColorStop(0, '#2d4a3e'); // CIC Primary Green
  gradient.addColorStop(1, '#4ade80'); // Emerald 400

  chart = new Chart(ctx, {
    type: 'bar',
    data: {
      labels: props.dataObj.labels || [],
      datasets: [{
        label: 'Jumlah Izin',
        data: props.dataObj.data || [],
        backgroundColor: gradient,
        borderRadius: 6,
        barThickness: 12, // Membuat bar lebih ramping dan elegan
        maxBarThickness: 16,
      }]
    },
    options: {
      indexAxis: 'y', // Mengubah menjadi horizontal bar
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: '#1a2e26',
          titleFont: { size: 10 },
          bodyFont: { size: 12 },
          cornerRadius: 8,
          displayColors: false,
        }
      },
      scales: {
        x: {
          beginAtZero: true,
          grid: {
            display: true,
            color: 'rgba(0, 0, 0, 0.05)',
            drawBorder: false,
          },
          ticks: {
            font: { size: 10 },
            color: '#94a3b8',
            stepSize: 1
          }
        },
        y: {
          grid: { display: false },
          ticks: {
            font: { size: 10, weight: '600' },
            color: '#475569',
          }
        }
      }
    }
  });
};

onMounted(draw);
watch(() => props.dataObj, draw, { deep: true });
</script>

<style scoped>
canvas {
  width: 100% !important;
  height: 100% !important;
}
</style>