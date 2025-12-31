<template>
  <div class="bg-white dark:bg-[#121512] rounded-[2rem] p-5 shadow-lg border border-slate-50 dark:border-white/5 transition-all">
    <div class="flex items-center justify-between mb-5 px-1">
      <div>
        <h2 class="text-xs font-bold text-[#2d4a3e] dark:text-emerald-500">
          Statistik Mingguan
        </h2>
        <p class="text-[10px] text-slate-400 font-medium mt-0.5">Kehadiran 7 hari terakhir</p>
      </div>
      <BarChart3 class="w-4 h-4 text-slate-300 opacity-50" />
    </div>

    <div class="relative h-48">
      <canvas v-if="chartData && chartData.length" ref="canvas"></canvas>
      
      <div v-else class="absolute inset-0 flex flex-col items-center justify-center space-y-3">
        <div class="w-10 h-10 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center animate-pulse">
           <Activity class="w-5 h-5 text-slate-200" />
        </div>
        <p class="text-[10px] font-medium text-slate-400 italic">
          {{ chartData === null ? 'Menyusun grafik...' : 'Belum ada data' }}
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { onMounted, ref, watch } from "vue";
import { BarChart3, Activity } from 'lucide-vue-next';
import {
  Chart,
  BarController,
  BarElement,
  CategoryScale,
  LinearScale,
  Tooltip,
  Legend
} from "chart.js";

Chart.register(BarController, BarElement, CategoryScale, LinearScale, Tooltip, Legend);

const canvas = ref(null);
let barChart = null;

const props = defineProps({
  chartData: { 
    type: [Array, null], 
    required: true 
  }
});

const renderChart = () => {
    if (props.chartData && props.chartData.length && canvas.value) {
        if (barChart) {
            barChart.destroy();
        }

        barChart = new Chart(canvas.value, {
            type: "bar",
            data: {
                labels: props.chartData.map((d) => d.tanggal),
                datasets: [
                    {
                        label: "Kehadiran",
                        data: props.chartData.map((d) => d.total),
                        backgroundColor: "#2d4a3e", 
                        hoverBackgroundColor: "#10b981", 
                        borderRadius: 8, // Sedikit lebih kecil agar proporsional
                        borderSkipped: false,
                        barThickness: 16, // Ukuran bar yang lebih rapi
                    }
                ],
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                      backgroundColor: '#1e293b',
                      titleFont: { family: 'Poppins', size: 10, weight: '600' },
                      bodyFont: { family: 'Poppins', size: 10 },
                      padding: 10,
                      cornerRadius: 10,
                      displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { 
                          font: { family: 'Poppins', size: 9, weight: '500' },
                          color: '#94a3b8' 
                        }
                    },
                    y: {
                        beginAtZero: true,
                        max: 1, 
                        grid: { color: '#f8fafc', drawTicks: false },
                        ticks: { 
                          stepSize: 1,
                          font: { family: 'Poppins', size: 9 },
                          color: '#cbd5e1',
                          callback: (value) => value === 1 ? 'Hadir' : '' // Tidak kapital semua
                        }
                    }
                }
            }
        });
    }
};

onMounted(renderChart);

watch(() => props.chartData, () => {
    if (props.chartData) {
        renderChart();
    }
}, { deep: true });
</script>

<style scoped>
/* Menghapus gaya manual, mengandalkan global Poppins */
</style>