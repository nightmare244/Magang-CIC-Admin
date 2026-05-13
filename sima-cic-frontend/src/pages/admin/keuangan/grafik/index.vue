<template>
  <div class="dashboard-container">
    <div class="header">
      <h1>Dashboard Keuangan</h1>
      <p>Statistik pemasukan dan pengeluaran</p>
    </div>

    <!-- CARD STATISTIK -->
    <div class="stats-grid">
      <div class="stat-card income">
        <h3>Total Pemasukan</h3>
        <h2>Rp 24.500.000</h2>
      </div>

      <div class="stat-card expense">
        <h3>Total Pengeluaran</h3>
        <h2>Rp 12.300.000</h2>
      </div>

      <div class="stat-card profit">
        <h3>Total Keuntungan</h3>
        <h2>Rp 12.200.000</h2>
      </div>
    </div>

    <!-- GRAFIK -->
    <div class="chart-grid">
      <!-- Area Chart -->
      <div class="chart-card">
        <h3>Pemasukan Bulanan</h3>

        <apexchart
          type="area"
          height="350"
          :options="areaChartOptions"
          :series="areaSeries"
        />
      </div>

      <!-- Bar Chart -->
      <div class="chart-card">
        <h3>Pengeluaran Bulanan</h3>

        <apexchart
          type="bar"
          height="350"
          :options="barChartOptions"
          :series="barSeries"
        />
      </div>
    </div>

    <!-- Donut Chart -->
    <div class="chart-card full-width">
      <h3>Kategori Pengeluaran</h3>

      <apexchart
        type="donut"
        height="350"
        :options="donutOptions"
        :series="donutSeries"
      />
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";

export default {
  components: {
    apexchart: VueApexCharts,
  },

  data() {
    return {
      areaSeries: [
        {
          name: "Pemasukan",
          data: [4000, 7000, 6000, 9000, 12000, 15000],
        },
      ],

      areaChartOptions: {
        chart: {
          toolbar: {
            show: false,
          },
        },

        colors: ["#00C896"],

        dataLabels: {
          enabled: false,
        },

        stroke: {
          curve: "smooth",
          width: 4,
        },

        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
          ],
        },

        fill: {
          type: "gradient",
          gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
          },
        },
      },

      barSeries: [
        {
          name: "Pengeluaran",
          data: [3000, 5000, 4000, 7000, 6000, 8000],
        },
      ],

      barChartOptions: {
        chart: {
          toolbar: {
            show: false,
          },
        },

        colors: ["#FF6B6B"],

        plotOptions: {
          bar: {
            borderRadius: 8,
            columnWidth: "45%",
          },
        },

        dataLabels: {
          enabled: false,
        },

        xaxis: {
          categories: [
            "Jan",
            "Feb",
            "Mar",
            "Apr",
            "Mei",
            "Jun",
          ],
        },
      },

      donutSeries: [44, 25, 18, 13],

      donutOptions: {
        labels: [
          "Operasional",
          "Gaji",
          "Transport",
          "Lainnya",
        ],

        legend: {
          position: "bottom",
        },

        colors: [
          "#6C63FF",
          "#00C896",
          "#FFB547",
          "#FF6B6B",
        ],
      },
    };
  },
};
</script>

<style scoped>
.dashboard-container {
  padding: 24px;
  background: #f5f7fb;
  min-height: 100vh;
}

.header {
  margin-bottom: 24px;
}

.header h1 {
  font-size: 32px;
  font-weight: 700;
  color: #1e293b;
}

.header p {
  color: #64748b;
  margin-top: 6px;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.stat-card {
  padding: 24px;
  border-radius: 20px;
  color: white;
  box-shadow: 0 10px 30px rgba(0,0,0,0.08);
}

.stat-card h3 {
  font-size: 16px;
  margin-bottom: 10px;
}

.stat-card h2 {
  font-size: 28px;
  font-weight: bold;
}

.income {
  background: linear-gradient(135deg, #00c896, #00e6aa);
}

.expense {
  background: linear-gradient(135deg, #ff6b6b, #ff8e8e);
}

.profit {
  background: linear-gradient(135deg, #6c63ff, #8a84ff);
}

.chart-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 20px;
}

.chart-card {
  background: white;
  padding: 20px;
  border-radius: 24px;
  box-shadow: 0 10px 25px rgba(0,0,0,0.05);
}

.chart-card h3 {
  margin-bottom: 20px;
  color: #1e293b;
}

.full-width {
  width: 100%;
}

@media (max-width: 900px) {
  .chart-grid {
    grid-template-columns: 1fr;
  }
}
</style>