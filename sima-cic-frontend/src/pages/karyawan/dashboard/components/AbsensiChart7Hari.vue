<template>
  <div class="w-full p-2 transition-all">
    <div class="flex items-center justify-between mb-6 px-1">
      <div>
        <h2 class="text-xs font-semibold text-slate-600 dark:text-emerald-400">
          Analitik kehadiran
        </h2>
        <p class="text-[10px] text-slate-400 font-medium mt-0.5">7 hari terakhir</p>
      </div>
      <div class="w-8 h-8 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center">
        <BarChart3 class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
      </div>
    </div>

    <div class="relative h-44 w-full">
      <canvas v-show="props.chartData && props.chartData.length" ref="canvas"></canvas>
      
      <div v-if="!props.chartData || props.chartData.length === 0" class="absolute inset-0 flex flex-col items-center justify-center space-y-2">
        <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-full flex items-center justify-center">
           <Activity class="w-5 h-5 text-slate-200 dark:text-slate-700" />
        </div>
        <p class="text-[10px] font-medium text-slate-400">Data belum tersedia</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, onUnmounted, ref, watch, nextTick } from "vue";
import { BarChart3, Activity } from 'lucide-vue-next';
import { Chart, BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend } from "chart.js";

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const props = defineProps({
  chartData: { type: Array, required: true, default: () => [] }
});

const canvas = ref(null);
let barChart = null;

const renderChart = () => {
  if (!canvas.value || !props.chartData || props.chartData.length === 0) return;

  if (barChart) barChart.destroy();

  const ctx = canvas.value.getContext('2d');
  const isDark = document.documentElement.classList.contains('dark');

  // Gradient Emerald ala Grab
  const gradient = ctx.createLinearGradient(0, 0, 0, 150);
  if (isDark) {
    gradient.addColorStop(0, '#10b981'); 
    gradient.addColorStop(1, 'rgba(16, 185, 129, 0.05)'); 
  } else {
    gradient.addColorStop(0, '#10b981'); 
    gradient.addColorStop(1, '#6ee7b7'); 
  }

  const labels = props.chartData.map(d => d.tanggal);
  const values = props.chartData.map(d => Number(d.total));

  barChart = new Chart(canvas.value, {
    type: "bar",
    data: {
      labels: labels,
      datasets: [{
        label: "Kehadiran",
        data: values,
        backgroundColor: gradient,
        borderRadius: 4, 
        barThickness: 10,
        hoverBackgroundColor: '#059669',
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      scales: {
        x: { 
          grid: { display: false },
          ticks: { 
            font: { family: 'Poppins', size: 9, weight: '500' }, 
            color: isDark ? '#475569' : '#94a3b8',
            padding: 5
          }
        },
        y: {
          beginAtZero: true,
          max: 1, 
          grid: { 
            color: isDark ? 'rgba(255,255,255,0.02)' : '#f1f5f9', 
            drawTicks: false 
          },
          ticks: {
            stepSize: 1,
            color: '#cbd5e1',
            font: { size: 9, weight: '600' },
            padding: 10,
            callback: (value) => value === 1 ? 'On' : 'Off'
          }
        }
      },
      plugins: {
        legend: { display: false },
        tooltip: {
          backgroundColor: isDark ? '#1e293b' : '#111827',
          padding: 10,
          cornerRadius: 8,
          titleFont: { size: 10 },
          bodyFont: { size: 10, weight: 'bold' },
          displayColors: false,
          callbacks: {
            label: (ctx) => ctx.raw === 1 ? ' Status: Hadir' : ' Status: Alpa'
          }
        }
      }
    }
  });
};

watch(() => props.chartData, async () => {
  await nextTick();
  renderChart();
}, { deep: true });

onMounted(() => {
  renderChart();
});

onUnmounted(() => { 
  if (barChart) barChart.destroy(); 
});
</script>

<style scoped>
canvas {
  filter: drop-shadow(0px 2px 4px rgba(16, 185, 129, 0.1));
}

/* Memastikan transisi warna smooth saat switch dark mode */
div {
  transition: background-color 0.3s ease, border-color 0.3s ease;
}
</style>