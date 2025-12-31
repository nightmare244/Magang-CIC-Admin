<template>
  <canvas ref="canvas"></canvas>
</template>

<script setup>
import { ref, onMounted } from "vue";
import { Chart, PieController, ArcElement, Tooltip, Legend } from "chart.js";

Chart.register(PieController, ArcElement, Tooltip, Legend);

const props = defineProps({
  data: Object,
});

const canvas = ref(null);

onMounted(() => {
  new Chart(canvas.value, {
    type: "pie",
    data: {
      labels: Object.keys(props.data),
      datasets: [
        {
          data: Object.values(props.data),
        },
      ],
    },
  });
});
</script>
