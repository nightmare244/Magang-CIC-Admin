<template>
  <div class="rekap-container">
    <!-- HEADER -->
    <div class="rekap-header">
      <div>
        <h1>Laporan Rekapitulasi Bulanan</h1>
        <p class="subtitle">Ringkasan performa organisasi per bulan</p>
      </div>
      <div class="month-selector">
        <button class="nav-btn" @click="changeMonth(-1)">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M15 18l-6-6 6-6"/></svg>
        </button>
        <div class="month-display">
          <span class="month-icon">📅</span>
          <span class="month-text">{{ displayMonth }}</span>
        </div>
        <button class="nav-btn" @click="changeMonth(1)" :disabled="isCurrentMonth">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg>
        </button>
      </div>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Memuat data rekapitulasi...</p>
    </div>

    <template v-else-if="data">
      <!-- KPI CARDS -->
      <div class="kpi-grid">
        <!-- Kehadiran -->
        <div class="kpi-card attendance">
          <div class="kpi-icon-wrap attendance-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
          </div>
          <div class="kpi-content">
            <span class="kpi-label">Total Kehadiran</span>
            <span class="kpi-value">{{ data.absensi.current.hadir }}</span>
            <div class="kpi-change" :class="changeClass(data.absensi.change)">
              <span class="change-arrow">{{ changeArrow(data.absensi.change) }}</span>
              {{ Math.abs(data.absensi.change.percent) }}% dari bulan lalu
            </div>
          </div>
        </div>

        <!-- Izin -->
        <div class="kpi-card leave">
          <div class="kpi-icon-wrap leave-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
          </div>
          <div class="kpi-content">
            <span class="kpi-label">Total Izin/Sakit/Cuti</span>
            <span class="kpi-value">{{ data.izin.current.total }}</span>
            <div class="kpi-change" :class="changeClassInverse(data.izin.change)">
              <span class="change-arrow">{{ changeArrow(data.izin.change) }}</span>
              {{ Math.abs(data.izin.change.percent) }}% dari bulan lalu
            </div>
          </div>
        </div>

        <!-- Pemasukan -->
        <div class="kpi-card income">
          <div class="kpi-icon-wrap income-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
          </div>
          <div class="kpi-content">
            <span class="kpi-label">Total Pemasukan</span>
            <span class="kpi-value">Rp {{ formatCurrency(data.keuangan.current.pemasukan) }}</span>
            <div class="kpi-change" :class="changeClass(data.keuangan.change_pemasukan)">
              <span class="change-arrow">{{ changeArrow(data.keuangan.change_pemasukan) }}</span>
              {{ Math.abs(data.keuangan.change_pemasukan.percent) }}% dari bulan lalu
            </div>
          </div>
        </div>

        <!-- Pengeluaran -->
        <div class="kpi-card expense">
          <div class="kpi-icon-wrap expense-icon">
            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
          </div>
          <div class="kpi-content">
            <span class="kpi-label">Total Pengeluaran</span>
            <span class="kpi-value">Rp {{ formatCurrency(data.keuangan.current.pengeluaran) }}</span>
            <div class="kpi-change" :class="changeClassInverse(data.keuangan.change_pengeluaran)">
              <span class="change-arrow">{{ changeArrow(data.keuangan.change_pengeluaran) }}</span>
              {{ Math.abs(data.keuangan.change_pengeluaran.percent) }}% dari bulan lalu
            </div>
          </div>
        </div>
      </div>

      <!-- PROFIT BANNER -->
      <div class="profit-banner" :class="data.keuangan.current.keuntungan >= 0 ? 'profit-positive' : 'profit-negative'">
        <div class="profit-left">
          <span class="profit-label">💰 Keuntungan Bersih Bulan Ini</span>
          <span class="profit-value">Rp {{ formatCurrency(data.keuangan.current.keuntungan) }}</span>
        </div>
        <div class="profit-change" :class="changeClass(data.keuangan.change_keuntungan)">
          <span>{{ changeArrow(data.keuangan.change_keuntungan) }} {{ Math.abs(data.keuangan.change_keuntungan.percent) }}%</span>
          <small>vs bulan lalu</small>
        </div>
      </div>

      <!-- CHARTS ROW -->
      <div class="charts-grid">
        <!-- Absensi Chart -->
        <div class="chart-card">
          <h3>📊 Kehadiran Harian</h3>
          <apexchart
            type="area"
            height="300"
            :options="absensiChartOptions"
            :series="absensiSeries"
          />
        </div>

        <!-- Keuangan Chart -->
        <div class="chart-card">
          <h3>💵 Pemasukan vs Pengeluaran (Mingguan)</h3>
          <apexchart
            type="bar"
            height="300"
            :options="keuanganChartOptions"
            :series="keuanganSeries"
          />
        </div>
      </div>

      <!-- DETAIL SECTIONS -->
      <div class="detail-grid">
        <!-- Absensi Detail -->
        <div class="detail-card">
          <h3>🕐 Rincian Kehadiran</h3>
          <div class="detail-stats">
            <div class="stat-row">
              <span class="stat-dot green"></span>
              <span class="stat-name">Hadir</span>
              <span class="stat-val">{{ data.absensi.current.hadir }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot blue"></span>
              <span class="stat-name">Tepat Waktu</span>
              <span class="stat-val">{{ data.absensi.current.tepat_waktu }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot orange"></span>
              <span class="stat-name">Terlambat</span>
              <span class="stat-val">{{ data.absensi.current.terlambat }}</span>
            </div>
          </div>
          <div class="stat-bar-wrap">
            <div class="stat-bar">
              <div class="bar-fill green-fill" :style="{ width: absensiTepatPercent + '%' }"></div>
              <div class="bar-fill orange-fill" :style="{ width: absensiTerlambatPercent + '%' }"></div>
            </div>
            <div class="bar-legend">
              <small>{{ absensiTepatPercent }}% Tepat Waktu</small>
              <small>{{ absensiTerlambatPercent }}% Terlambat</small>
            </div>
          </div>
        </div>

        <!-- Izin Detail -->
        <div class="detail-card">
          <h3>📋 Rincian Izin / Sakit / Cuti</h3>
          <div class="detail-stats">
            <div class="stat-row">
              <span class="stat-dot red"></span>
              <span class="stat-name">Sakit</span>
              <span class="stat-val">{{ data.izin.current.sakit }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot yellow"></span>
              <span class="stat-name">Izin</span>
              <span class="stat-val">{{ data.izin.current.izin }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot purple"></span>
              <span class="stat-name">Cuti</span>
              <span class="stat-val">{{ data.izin.current.cuti }}</span>
            </div>
          </div>
          <div class="divider"></div>
          <div class="detail-stats">
            <div class="stat-row">
              <span class="stat-dot gray"></span>
              <span class="stat-name">Pending</span>
              <span class="stat-val badge-pending">{{ data.izin.current.pending }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot green"></span>
              <span class="stat-name">Disetujui</span>
              <span class="stat-val badge-approved">{{ data.izin.current.disetujui }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot red"></span>
              <span class="stat-name">Ditolak</span>
              <span class="stat-val badge-rejected">{{ data.izin.current.ditolak }}</span>
            </div>
          </div>
        </div>

        <!-- Peminjaman Detail -->
        <div class="detail-card">
          <h3>📦 Rincian Peminjaman Inventaris</h3>
          <div class="detail-stats">
            <div class="stat-row">
              <span class="stat-dot blue"></span>
              <span class="stat-name">Total Peminjaman</span>
              <span class="stat-val">{{ data.peminjaman.current.total }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot gray"></span>
              <span class="stat-name">Pending</span>
              <span class="stat-val badge-pending">{{ data.peminjaman.current.pending }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot green"></span>
              <span class="stat-name">Disetujui</span>
              <span class="stat-val badge-approved">{{ data.peminjaman.current.disetujui }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot teal"></span>
              <span class="stat-name">Selesai/Dikembalikan</span>
              <span class="stat-val">{{ data.peminjaman.current.selesai }}</span>
            </div>
            <div class="stat-row">
              <span class="stat-dot red"></span>
              <span class="stat-name">Ditolak</span>
              <span class="stat-val badge-rejected">{{ data.peminjaman.current.ditolak }}</span>
            </div>
          </div>
        </div>

        <!-- Top Kategori Pengeluaran -->
        <div class="detail-card">
          <h3>🏷️ Top Kategori Pengeluaran</h3>
          <div class="detail-stats" v-if="data.keuangan.current.top_kategori.length > 0">
            <div class="stat-row" v-for="(cat, idx) in data.keuangan.current.top_kategori" :key="idx">
              <span class="stat-dot" :class="kategoriDotColor(idx)"></span>
              <span class="stat-name">{{ cat.kategori }}</span>
              <span class="stat-val">Rp {{ formatCurrency(cat.total) }}</span>
            </div>
          </div>
          <div v-else class="empty-state">
            <p>Tidak ada data pengeluaran bulan ini</p>
          </div>
          <div class="kategori-donut" v-if="data.keuangan.current.top_kategori.length > 0">
            <apexchart
              type="donut"
              height="200"
              :options="kategoriDonutOptions"
              :series="kategoriDonutSeries"
            />
          </div>
        </div>
      </div>
    </template>

    <!-- ERROR STATE -->
    <div v-else-if="error" class="error-state">
      <p>⚠️ {{ error }}</p>
      <button @click="fetchData" class="retry-btn">Coba Lagi</button>
    </div>
  </div>
</template>

<script>
import VueApexCharts from "vue3-apexcharts";
import api from "@/services/api";

export default {
  components: {
    apexchart: VueApexCharts,
  },

  data() {
    return {
      loading: true,
      error: null,
      data: null,
      selectedMonth: new Date(),

      // Chart data
      absensiSeries: [],
      absensiChartOptions: {
        chart: { toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        colors: ["#00C896", "#FF9F43"],
        dataLabels: { enabled: false },
        stroke: { curve: "smooth", width: 3 },
        xaxis: { categories: [] },
        fill: {
          type: "gradient",
          gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 },
        },
        legend: { position: 'top' },
      },

      keuanganSeries: [],
      keuanganChartOptions: {
        chart: { toolbar: { show: false }, fontFamily: 'Inter, sans-serif' },
        colors: ["#00C896", "#FF6B6B"],
        plotOptions: { bar: { borderRadius: 8, columnWidth: "50%" } },
        dataLabels: { enabled: false },
        xaxis: { categories: [] },
        legend: { position: 'top' },
        yaxis: {
          labels: {
            formatter: (val) => {
              if (val >= 1000000) return (val / 1000000).toFixed(1) + ' jt';
              if (val >= 1000) return (val / 1000).toFixed(0) + ' rb';
              return val;
            }
          }
        },
      },
    };
  },

  computed: {
    displayMonth() {
      const months = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
        'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];
      return `${months[this.selectedMonth.getMonth()]} ${this.selectedMonth.getFullYear()}`;
    },

    isCurrentMonth() {
      const now = new Date();
      return this.selectedMonth.getMonth() === now.getMonth() &&
        this.selectedMonth.getFullYear() === now.getFullYear();
    },

    absensiTepatPercent() {
      if (!this.data) return 0;
      const total = this.data.absensi.current.tepat_waktu + this.data.absensi.current.terlambat;
      return total === 0 ? 0 : Math.round((this.data.absensi.current.tepat_waktu / total) * 100);
    },

    absensiTerlambatPercent() {
      return 100 - this.absensiTepatPercent;
    },

    kategoriDonutSeries() {
      if (!this.data) return [];
      return this.data.keuangan.current.top_kategori.map(c => c.total);
    },

    kategoriDonutOptions() {
      if (!this.data) return {};
      return {
        labels: this.data.keuangan.current.top_kategori.map(c => c.kategori),
        colors: ['#6C63FF', '#00C896', '#FFB547', '#FF6B6B', '#8A84FF'],
        legend: { position: 'bottom', fontSize: '12px' },
        dataLabels: {
          formatter: (val) => val.toFixed(1) + '%',
        },
      };
    },
  },

  methods: {
    formatCurrency(value) {
      return new Intl.NumberFormat('id-ID').format(value || 0);
    },

    changeMonth(direction) {
      const newDate = new Date(this.selectedMonth);
      newDate.setMonth(newDate.getMonth() + direction);
      this.selectedMonth = newDate;
      this.fetchData();
    },

    changeClass(change) {
      if (!change) return '';
      return change.direction === 'up' ? 'change-up' : change.direction === 'down' ? 'change-down' : 'change-flat';
    },

    changeClassInverse(change) {
      if (!change) return '';
      // For izin/pengeluaran, "up" is bad, "down" is good
      return change.direction === 'up' ? 'change-down' : change.direction === 'down' ? 'change-up' : 'change-flat';
    },

    changeArrow(change) {
      if (!change) return '';
      return change.direction === 'up' ? '▲' : change.direction === 'down' ? '▼' : '—';
    },

    kategoriDotColor(idx) {
      const colors = ['purple', 'green', 'yellow', 'red', 'blue'];
      return colors[idx % colors.length];
    },

    async fetchData() {
      this.loading = true;
      this.error = null;
      try {
        const monthKey = `${this.selectedMonth.getFullYear()}-${String(this.selectedMonth.getMonth() + 1).padStart(2, '0')}`;
        const res = await api.get(`/admin/rekap-bulanan?bulan=${monthKey}`);
        this.data = res.data.data;

        // Update Absensi Chart
        this.absensiSeries = [
          { name: "Hadir", data: this.data.charts.absensi_harian.hadir },
          { name: "Terlambat", data: this.data.charts.absensi_harian.terlambat },
        ];
        this.absensiChartOptions = {
          ...this.absensiChartOptions,
          xaxis: { categories: this.data.charts.absensi_harian.labels },
        };

        // Update Keuangan Chart
        this.keuanganSeries = [
          { name: "Pemasukan", data: this.data.charts.keuangan.pemasukan },
          { name: "Pengeluaran", data: this.data.charts.keuangan.pengeluaran },
        ];
        this.keuanganChartOptions = {
          ...this.keuanganChartOptions,
          xaxis: { categories: this.data.charts.keuangan.labels },
        };

      } catch (err) {
        console.error("Gagal memuat rekap bulanan:", err);
        this.error = "Gagal memuat data rekapitulasi. Silakan coba lagi.";
      } finally {
        this.loading = false;
      }
    },
  },

  mounted() {
    this.fetchData();
  },
};
</script>

<style scoped>
/* ============ BASE ============ */
.rekap-container {
  padding: 24px;
  min-height: 100vh;
  font-family: 'Inter', 'Segoe UI', sans-serif;
}

/* ============ HEADER ============ */
.rekap-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 32px;
  flex-wrap: wrap;
  gap: 16px;
}

.rekap-header h1 {
  font-size: 28px;
  font-weight: 800;
  color: #1e293b;
  letter-spacing: -0.5px;
}

.dark .rekap-header h1 { color: #f1f5f9; }

.subtitle {
  color: #64748b;
  margin-top: 4px;
  font-size: 14px;
}

.dark .subtitle { color: #94a3b8; }

.month-selector {
  display: flex;
  align-items: center;
  gap: 12px;
  background: white;
  padding: 8px 16px;
  border-radius: 16px;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.06);
  border: 1px solid #e2e8f0;
}

.dark .month-selector {
  background: #1a1d19;
  border-color: rgba(255,255,255,0.08);
}

.nav-btn {
  width: 36px;
  height: 36px;
  border-radius: 12px;
  border: none;
  background: #f1f5f9;
  color: #475569;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: all 0.2s;
}

.dark .nav-btn {
  background: rgba(255,255,255,0.05);
  color: #94a3b8;
}

.nav-btn:hover:not(:disabled) {
  background: #2d4a3e;
  color: white;
  transform: scale(1.05);
}

.nav-btn:disabled {
  opacity: 0.3;
  cursor: not-allowed;
}

.month-display {
  display: flex;
  align-items: center;
  gap: 8px;
  font-weight: 700;
  font-size: 16px;
  color: #1e293b;
  min-width: 180px;
  justify-content: center;
}

.dark .month-display { color: #f1f5f9; }

.month-icon { font-size: 20px; }

/* ============ KPI CARDS ============ */
.kpi-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
  gap: 20px;
  margin-bottom: 24px;
}

.kpi-card {
  display: flex;
  align-items: flex-start;
  gap: 16px;
  padding: 24px;
  border-radius: 20px;
  background: white;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
  transition: all 0.3s;
}

.dark .kpi-card {
  background: #0f1210;
  border-color: rgba(255,255,255,0.05);
}

.kpi-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 32px rgba(0, 0, 0, 0.08);
}

.kpi-icon-wrap {
  width: 52px;
  height: 52px;
  border-radius: 16px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}

.attendance-icon { background: linear-gradient(135deg, #dbeafe, #bfdbfe); color: #2563eb; }
.leave-icon { background: linear-gradient(135deg, #fef3c7, #fde68a); color: #d97706; }
.income-icon { background: linear-gradient(135deg, #d1fae5, #a7f3d0); color: #059669; }
.expense-icon { background: linear-gradient(135deg, #fee2e2, #fecaca); color: #dc2626; }

.kpi-content {
  display: flex;
  flex-direction: column;
  gap: 4px;
  min-width: 0;
}

.kpi-label {
  font-size: 12px;
  font-weight: 600;
  color: #64748b;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

.dark .kpi-label { color: #94a3b8; }

.kpi-value {
  font-size: 24px;
  font-weight: 800;
  color: #1e293b;
  letter-spacing: -0.5px;
}

.dark .kpi-value { color: #f1f5f9; }

.kpi-change {
  font-size: 12px;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 4px;
}

.change-up { color: #059669; }
.change-down { color: #dc2626; }
.change-flat { color: #64748b; }

.change-arrow { font-size: 10px; }

/* ============ PROFIT BANNER ============ */
.profit-banner {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 24px 32px;
  border-radius: 20px;
  margin-bottom: 24px;
  color: white;
  flex-wrap: wrap;
  gap: 16px;
}

.profit-positive {
  background: linear-gradient(135deg, #059669, #10b981, #34d399);
  box-shadow: 0 8px 32px rgba(5, 150, 105, 0.3);
}

.profit-negative {
  background: linear-gradient(135deg, #dc2626, #ef4444, #f87171);
  box-shadow: 0 8px 32px rgba(220, 38, 38, 0.3);
}

.profit-left {
  display: flex;
  flex-direction: column;
  gap: 4px;
}

.profit-label {
  font-size: 14px;
  opacity: 0.9;
  font-weight: 500;
}

.profit-value {
  font-size: 32px;
  font-weight: 800;
  letter-spacing: -1px;
}

.profit-change {
  display: flex;
  flex-direction: column;
  align-items: flex-end;
  gap: 2px;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(10px);
  padding: 12px 20px;
  border-radius: 14px;
  font-weight: 700;
  font-size: 18px;
}

.profit-change small {
  font-size: 11px;
  opacity: 0.8;
  font-weight: 500;
}

/* ============ CHARTS ============ */
.charts-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
  margin-bottom: 24px;
}

.chart-card {
  background: white;
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
}

.dark .chart-card {
  background: #0f1210;
  border-color: rgba(255,255,255,0.05);
}

.chart-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 16px;
}

.dark .chart-card h3 { color: #f1f5f9; }

/* ============ DETAIL CARDS ============ */
.detail-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 20px;
}

.detail-card {
  background: white;
  padding: 24px;
  border-radius: 20px;
  box-shadow: 0 4px 24px rgba(0, 0, 0, 0.04);
  border: 1px solid #f1f5f9;
}

.dark .detail-card {
  background: #0f1210;
  border-color: rgba(255,255,255,0.05);
}

.detail-card h3 {
  font-size: 16px;
  font-weight: 700;
  color: #1e293b;
  margin-bottom: 20px;
}

.dark .detail-card h3 { color: #f1f5f9; }

.detail-stats {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.stat-row {
  display: flex;
  align-items: center;
  gap: 10px;
}

.stat-dot {
  width: 10px;
  height: 10px;
  border-radius: 50%;
  flex-shrink: 0;
}

.stat-dot.green { background: #10b981; }
.stat-dot.blue { background: #3b82f6; }
.stat-dot.orange { background: #f59e0b; }
.stat-dot.red { background: #ef4444; }
.stat-dot.yellow { background: #eab308; }
.stat-dot.purple { background: #8b5cf6; }
.stat-dot.gray { background: #94a3b8; }
.stat-dot.teal { background: #14b8a6; }

.stat-name {
  flex: 1;
  font-size: 14px;
  color: #475569;
  font-weight: 500;
}

.dark .stat-name { color: #94a3b8; }

.stat-val {
  font-weight: 700;
  font-size: 15px;
  color: #1e293b;
}

.dark .stat-val { color: #f1f5f9; }

.badge-pending {
  background: #fef3c7;
  color: #d97706;
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 13px;
}

.badge-approved {
  background: #d1fae5;
  color: #059669;
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 13px;
}

.badge-rejected {
  background: #fee2e2;
  color: #dc2626;
  padding: 2px 10px;
  border-radius: 20px;
  font-size: 13px;
}

.divider {
  height: 1px;
  background: #e2e8f0;
  margin: 16px 0;
}

.dark .divider { background: rgba(255,255,255,0.08); }

/* ============ STAT BAR ============ */
.stat-bar-wrap {
  margin-top: 20px;
}

.stat-bar {
  height: 10px;
  border-radius: 10px;
  background: #f1f5f9;
  display: flex;
  overflow: hidden;
}

.dark .stat-bar { background: rgba(255,255,255,0.05); }

.bar-fill {
  height: 100%;
  transition: width 0.6s ease;
}

.green-fill { background: linear-gradient(90deg, #10b981, #34d399); }
.orange-fill { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

.bar-legend {
  display: flex;
  justify-content: space-between;
  margin-top: 8px;
  font-size: 12px;
  color: #64748b;
}

.dark .bar-legend { color: #94a3b8; }

/* ============ LOADING & ERROR ============ */
.loading-state {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  padding: 80px 0;
  color: #64748b;
}

.spinner {
  width: 40px;
  height: 40px;
  border: 4px solid #e2e8f0;
  border-top-color: #2d4a3e;
  border-radius: 50%;
  animation: spin 0.8s linear infinite;
  margin-bottom: 16px;
}

@keyframes spin {
  to { transform: rotate(360deg); }
}

.error-state {
  text-align: center;
  padding: 80px 0;
  color: #ef4444;
  font-weight: 600;
}

.retry-btn {
  margin-top: 16px;
  padding: 10px 24px;
  border-radius: 12px;
  border: none;
  background: #2d4a3e;
  color: white;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.2s;
}

.retry-btn:hover {
  background: #1e3a2f;
  transform: scale(1.02);
}

.empty-state {
  text-align: center;
  padding: 24px;
  color: #94a3b8;
  font-size: 14px;
}

.kategori-donut {
  margin-top: 16px;
}

/* ============ RESPONSIVE ============ */
@media (max-width: 900px) {
  .charts-grid {
    grid-template-columns: 1fr;
  }
  .detail-grid {
    grid-template-columns: 1fr;
  }
  .profit-value {
    font-size: 24px;
  }
}

@media (max-width: 600px) {
  .rekap-header {
    flex-direction: column;
    align-items: flex-start;
  }
  .kpi-grid {
    grid-template-columns: 1fr;
  }
}
</style>
