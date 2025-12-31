<template>
  <div class="p-4 rounded-2xl bg-white dark:bg-[#0f1410] shadow-sm border border-gray-100 dark:border-white/5 transition-colors">
    <div class="flex items-center justify-between mb-3">
      <div>
        <div class="text-sm text-gray-500 dark:text-gray-300">{{ title }}</div>
      </div>
    </div>

    <div class="w-full">
      <canvas ref="canvas"></canvas>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, watch, onBeforeUnmount } from "vue";
import {
  Chart,
  ArcElement,
  Tooltip,
  Legend,
  CategoryScale,
  LinearScale,
  PointElement,
  LineElement,
  BarElement,
} from "chart.js";

Chart.register(ArcElement, Tooltip, Legend, CategoryScale, LinearScale, PointElement, LineElement, BarElement);

const props = defineProps({
  title: { type: String, default: "" },
  type: { type: String, default: "pie" }, // pie | line | bar
  data: { type: Object, required: true },
  options: { type: Object, default: () => ({ responsive: true, maintainAspectRatio: false }) },
});

const canvas = ref(null);
let chart = null;

function generateColors(n) {
  const palette = [
    "#4b5d3a", "#7ba882", "#a8c9a8", "#f59e0b", "#ef4444", "#6366f1", "#10b981"
  ];
  const out = [];
  for (let i = 0; i < n; i++) out.push(palette[i % palette.length]);
  return out;
}

function renderChart() {
  if (!canvas.value) return;
  if (chart) chart.destroy();

  const labels = Object.keys(props.data || {});
  const values = Object.values(props.data || {});

  const cfg = {
    type: props.type,
    data: {
      labels,
      datasets: [
        {
          label: props.title || "",
          data: values,
          backgroundColor: generateColors(labels.length),
          borderWidth: 0,
        },
      ],
    },
    options: {
      ...props.options,
      plugins: {
        legend: { display: props.type !== 'line' },
        tooltip: { enabled: true },
      },
      scales: props.type === 'line' ? {
        x: { display: true },
        y: { display: true }
      } : undefined,
    },
  };

  chart = new Chart(canvas.value.getContext("2d"), cfg);
}

onMounted(renderChart);
watch(() => props.data, renderChart, { deep: true });
onBeforeUnmount(() => { if (chart) chart.destroy(); });
</script>

<style scoped>
canvas { width: 100% !important; height: 260px !important; }
</style>
