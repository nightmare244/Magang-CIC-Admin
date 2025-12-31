<template>
  <div class="relative w-full" style="min-height: 300px; height: 300px;">
    <canvas ref="canvasRef"></canvas>
  </div>
</template>

<script setup>
import { onMounted, ref, nextTick, watch } from 'vue';
import Chart from 'chart.js/auto';

const props = defineProps({
  series: { type: Object, default: () => ({ labels: [], datasets: [] }) }
});

const canvasRef = ref(null);
let chartInstance = null;

const draw = async () => {
  await nextTick();
  if (!canvasRef.value) return;

  const ctx = canvasRef.value.getContext('2d');
  
  // Berdasarkan JSON Anda: datasets adalah [0,0,0,0,0,0,1]
  const labels = props.series?.labels || [];
  const rawData = props.series?.datasets || [];

  if (chartInstance) {
    chartInstance.destroy();
  }

  // Jika tidak ada label, jangan gambar
  if (labels.length === 0) return;

  chartInstance = new Chart(ctx, {
    type: 'line',
    data: {
      labels: labels,
      datasets: [{
        label: 'Hadir',
        data: rawData,
        borderColor: '#2d4a3e',
        backgroundColor: 'rgba(45, 74, 62, 0.1)',
        fill: true,
        tension: 0.4
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false, // Sangat penting agar mengikuti tinggi div
      scales: {
        y: { beginAtZero: true, ticks: { stepSize: 1 } }
      }
    }
  });
};

onMounted(draw);
watch(() => props.series, draw, { deep: true });
</script>