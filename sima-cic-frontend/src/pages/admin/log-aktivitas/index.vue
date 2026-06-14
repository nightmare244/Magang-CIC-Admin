<template>
  <div class="log-container">
    <!-- HEADER -->
    <div class="log-header">
      <div>
        <h1>Log Aktivitas Sistem</h1>
        <p class="subtitle">Riwayat semua aksi yang dilakukan di dalam sistem</p>
      </div>
    </div>

    <!-- STATS CARDS -->
    <div class="stats-row">
      <div class="stat-card today">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
        </div>
        <div>
          <span class="stat-value">{{ stats.total_hari_ini ?? 0 }}</span>
          <span class="stat-label">Aktivitas Hari Ini</span>
        </div>
      </div>
      <div class="stat-card week">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
        </div>
        <div>
          <span class="stat-value">{{ stats.total_minggu_ini ?? 0 }}</span>
          <span class="stat-label">7 Hari Terakhir</span>
        </div>
      </div>
      <div class="stat-card total">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
        </div>
        <div>
          <span class="stat-value">{{ stats.total_keseluruhan ?? 0 }}</span>
          <span class="stat-label">Total Keseluruhan</span>
        </div>
      </div>
      <div class="stat-card active-module">
        <div class="stat-icon">
          <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
        </div>
        <div>
          <span class="stat-value capitalize">{{ stats.modul_terbanyak?.modul ?? '-' }}</span>
          <span class="stat-label">Modul Paling Aktif</span>
        </div>
      </div>
    </div>

    <!-- FILTERS -->
    <div class="filters-bar">
      <div class="filter-group">
        <input 
          v-model="filters.search" 
          type="text" 
          placeholder="🔍 Cari judul, detail, atau nama user..." 
          class="filter-input search-input"
          @input="debouncedFetch"
        />
      </div>
      <div class="filter-group">
        <select v-model="filters.modul" class="filter-input" @change="fetchLogs(1)">
          <option value="">Semua Modul</option>
          <option v-for="m in modulOptions" :key="m" :value="m">{{ capitalize(m) }}</option>
        </select>
        <select v-model="filters.aksi" class="filter-input" @change="fetchLogs(1)">
          <option value="">Semua Aksi</option>
          <option v-for="a in aksiOptions" :key="a" :value="a">{{ aksiLabel(a) }}</option>
        </select>
        <select v-model="filters.role" class="filter-input" @change="fetchLogs(1)">
          <option value="">Semua Role</option>
          <option value="admin">Admin</option>
          <option value="karyawan">Karyawan</option>
        </select>
      </div>
      <div class="filter-group">
        <input v-model="filters.dari" type="date" class="filter-input" @change="fetchLogs(1)" />
        <span class="date-separator">—</span>
        <input v-model="filters.sampai" type="date" class="filter-input" @change="fetchLogs(1)" />
      </div>
    </div>

    <!-- LOADING STATE -->
    <div v-if="loading" class="loading-state">
      <div class="spinner"></div>
      <p>Memuat log aktivitas...</p>
    </div>

    <!-- EMPTY STATE -->
    <div v-else-if="logs.length === 0" class="empty-state">
      <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="empty-icon"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
      <h3>Belum Ada Log</h3>
      <p>Log aktivitas akan muncul saat pengguna melakukan aksi di sistem.</p>
    </div>

    <!-- LOG TIMELINE -->
    <div v-else class="timeline">
      <div 
        v-for="log in logs" 
        :key="log.id" 
        class="timeline-item"
        :class="'aksi-' + log.aksi"
      >
        <div class="timeline-dot" :class="'dot-' + log.aksi">
          <span class="dot-icon">{{ aksiEmoji(log.aksi) }}</span>
        </div>
        <div class="timeline-content">
          <div class="timeline-header">
            <h4 class="timeline-title">{{ log.judul }}</h4>
            <span class="timeline-badge" :class="'badge-' + log.aksi">{{ aksiLabel(log.aksi) }}</span>
            <span class="timeline-badge badge-modul">{{ capitalize(log.modul) }}</span>
          </div>
          <p v-if="log.detail" class="timeline-detail">{{ log.detail }}</p>
          <div class="timeline-meta">
            <span class="meta-user">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              {{ log.user_name }}
            </span>
            <span class="meta-role" :class="'role-' + log.role">{{ capitalize(log.role || 'system') }}</span>
            <span class="meta-time">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
              {{ formatTime(log.created_at) }}
            </span>
            <span v-if="log.ip_address" class="meta-ip">
              <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="8" rx="2" ry="2"/><rect x="2" y="14" width="20" height="8" rx="2" ry="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>
              {{ log.ip_address }}
            </span>
          </div>
        </div>
      </div>
    </div>

    <!-- PAGINATION -->
    <div v-if="meta.last_page > 1" class="pagination">
      <button 
        class="page-btn" 
        :disabled="meta.current_page <= 1" 
        @click="fetchLogs(meta.current_page - 1)"
      >
        ◀ Prev
      </button>
      <span class="page-info">
        Halaman {{ meta.current_page }} dari {{ meta.last_page }} 
        <span class="page-total">({{ meta.total }} log)</span>
      </span>
      <button 
        class="page-btn" 
        :disabled="meta.current_page >= meta.last_page" 
        @click="fetchLogs(meta.current_page + 1)"
      >
        Next ▶
      </button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import api from '@/services/api';

const logs = ref([]);
const stats = ref({});
const loading = ref(true);
const meta = ref({ current_page: 1, last_page: 1, total: 0 });

const filters = ref({
  search: '',
  modul: '',
  aksi: '',
  role: '',
  dari: '',
  sampai: '',
});

const modulOptions = ['auth', 'karyawan', 'absensi', 'izin', 'inventaris', 'peminjaman', 'pengumuman', 'pemasukan', 'pengeluaran', 'departemen'];
const aksiOptions = ['create', 'update', 'delete', 'login', 'logout', 'approve', 'reject', 'return'];

let debounceTimer = null;
const debouncedFetch = () => {
  clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => fetchLogs(1), 400);
};

const fetchLogs = async (page = 1) => {
  loading.value = true;
  try {
    const params = { page, per_page: 20 };
    if (filters.value.search) params.search = filters.value.search;
    if (filters.value.modul) params.modul = filters.value.modul;
    if (filters.value.aksi) params.aksi = filters.value.aksi;
    if (filters.value.role) params.role = filters.value.role;
    if (filters.value.dari) params.dari = filters.value.dari;
    if (filters.value.sampai) params.sampai = filters.value.sampai;

    const res = await api.get('/admin/log-aktivitas', { params });
    logs.value = res.data.data;
    meta.value = res.data.meta;
  } catch (e) {
    console.error('Error fetching logs:', e);
  } finally {
    loading.value = false;
  }
};

const fetchStats = async () => {
  try {
    const res = await api.get('/admin/log-aktivitas/stats');
    stats.value = res.data.data;
  } catch (e) {
    console.error('Error fetching stats:', e);
  }
};

const capitalize = (s) => s ? s.charAt(0).toUpperCase() + s.slice(1) : '';

const aksiLabel = (aksi) => {
  const map = {
    create: 'Tambah', update: 'Ubah', delete: 'Hapus',
    login: 'Login', logout: 'Logout',
    approve: 'Setuju', reject: 'Tolak', return: 'Kembali',
  };
  return map[aksi] || capitalize(aksi);
};

const aksiEmoji = (aksi) => {
  const map = {
    create: '➕', update: '✏️', delete: '🗑️',
    login: '🔑', logout: '🚪',
    approve: '✅', reject: '❌', return: '🔄',
  };
  return map[aksi] || '📝';
};

const formatTime = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  const now = new Date();
  const diff = Math.floor((now - d) / 1000);

  if (diff < 60) return 'Baru saja';
  if (diff < 3600) return Math.floor(diff / 60) + ' menit lalu';
  if (diff < 86400) return Math.floor(diff / 3600) + ' jam lalu';
  if (diff < 172800) return 'Kemarin ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });

  return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }) + ' ' + d.toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' });
};

onMounted(() => {
  fetchLogs();
  fetchStats();
});
</script>

<style scoped>
.log-container {
  padding: 2rem;
  max-width: 1200px;
  margin: 0 auto;
  font-family: 'Poppins', sans-serif;
}

/* HEADER */
.log-header {
  margin-bottom: 1.5rem;
}
.log-header h1 {
  font-size: 1.65rem;
  font-weight: 800;
  color: #1a2e28;
  margin: 0;
  letter-spacing: -0.5px;
}
.subtitle {
  color: #64748b;
  font-size: 0.85rem;
  margin-top: 0.25rem;
}

/* STATS */
.stats-row {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 1rem;
  margin-bottom: 1.5rem;
}
.stat-card {
  display: flex;
  align-items: center;
  gap: 1rem;
  padding: 1.25rem 1.5rem;
  border-radius: 1.25rem;
  background: white;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
  transition: all 0.3s ease;
}
.stat-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 25px rgba(0,0,0,0.08);
}
.stat-icon {
  width: 48px;
  height: 48px;
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  flex-shrink: 0;
}
.stat-card.today .stat-icon { background: #ecfdf5; color: #059669; }
.stat-card.week .stat-icon  { background: #eff6ff; color: #2563eb; }
.stat-card.total .stat-icon { background: #faf5ff; color: #7c3aed; }
.stat-card.active-module .stat-icon { background: #fff7ed; color: #ea580c; }
.stat-value {
  display: block;
  font-size: 1.3rem;
  font-weight: 800;
  color: #1e293b;
  line-height: 1.2;
}
.stat-label {
  display: block;
  font-size: 0.72rem;
  font-weight: 600;
  color: #94a3b8;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}

/* FILTERS */
.filters-bar {
  display: flex;
  flex-wrap: wrap;
  gap: 0.75rem;
  align-items: center;
  margin-bottom: 1.5rem;
  padding: 1rem 1.25rem;
  background: white;
  border-radius: 1.25rem;
  border: 1px solid #e2e8f0;
  box-shadow: 0 1px 3px rgba(0,0,0,0.04);
}
.filter-group {
  display: flex;
  align-items: center;
  gap: 0.5rem;
}
.filter-input {
  padding: 0.55rem 0.85rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  font-size: 0.8rem;
  font-family: 'Poppins', sans-serif;
  background: #f8fafc;
  color: #334155;
  transition: all 0.2s;
  outline: none;
}
.filter-input:focus {
  border-color: #2d4a3e;
  box-shadow: 0 0 0 3px rgba(45, 74, 62, 0.1);
}
.search-input {
  min-width: 280px;
}
.date-separator {
  color: #94a3b8;
  font-weight: 600;
}

/* LOADING */
.loading-state {
  text-align: center;
  padding: 4rem 0;
  color: #64748b;
}
.spinner {
  width: 40px;
  height: 40px;
  border: 3px solid #e2e8f0;
  border-top-color: #2d4a3e;
  border-radius: 50%;
  margin: 0 auto 1rem;
  animation: spin 0.8s linear infinite;
}
@keyframes spin { to { transform: rotate(360deg); } }

/* EMPTY */
.empty-state {
  text-align: center;
  padding: 4rem 0;
  color: #94a3b8;
}
.empty-icon { margin-bottom: 1rem; }
.empty-state h3 {
  color: #475569;
  font-weight: 700;
  margin: 0 0 0.5rem;
}

/* TIMELINE */
.timeline {
  position: relative;
  padding-left: 2.5rem;
}
.timeline::before {
  content: '';
  position: absolute;
  left: 17px;
  top: 0;
  bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, #2d4a3e 0%, #e2e8f0 100%);
  border-radius: 2px;
}
.timeline-item {
  position: relative;
  margin-bottom: 1rem;
  animation: fadeSlideUp 0.4s ease-out both;
}
.timeline-item:nth-child(1) { animation-delay: 0.05s; }
.timeline-item:nth-child(2) { animation-delay: 0.1s; }
.timeline-item:nth-child(3) { animation-delay: 0.15s; }
.timeline-item:nth-child(4) { animation-delay: 0.2s; }
.timeline-item:nth-child(5) { animation-delay: 0.25s; }

@keyframes fadeSlideUp {
  from { opacity: 0; transform: translateY(12px); }
  to { opacity: 1; transform: translateY(0); }
}

.timeline-dot {
  position: absolute;
  left: -2.5rem;
  top: 1.15rem;
  width: 34px;
  height: 34px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: white;
  border: 2.5px solid #cbd5e1;
  z-index: 1;
  font-size: 0.85rem;
  transition: all 0.3s;
}
.dot-create { border-color: #059669; background: #ecfdf5; }
.dot-update { border-color: #2563eb; background: #eff6ff; }
.dot-delete { border-color: #dc2626; background: #fef2f2; }
.dot-login  { border-color: #7c3aed; background: #faf5ff; }
.dot-logout { border-color: #64748b; background: #f1f5f9; }
.dot-approve { border-color: #059669; background: #ecfdf5; }
.dot-reject { border-color: #dc2626; background: #fef2f2; }
.dot-return { border-color: #ea580c; background: #fff7ed; }

.timeline-content {
  background: white;
  border: 1px solid #e2e8f0;
  border-radius: 1rem;
  padding: 1rem 1.25rem;
  transition: all 0.3s;
}
.timeline-item:hover .timeline-content {
  box-shadow: 0 8px 25px rgba(0,0,0,0.07);
  border-color: #cbd5e1;
}
.timeline-header {
  display: flex;
  align-items: center;
  gap: 0.5rem;
  flex-wrap: wrap;
}
.timeline-title {
  font-size: 0.88rem;
  font-weight: 700;
  color: #1e293b;
  margin: 0;
}
.timeline-badge {
  font-size: 0.62rem;
  font-weight: 700;
  padding: 0.2rem 0.6rem;
  border-radius: 9999px;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.badge-create  { background: #d1fae5; color: #065f46; }
.badge-update  { background: #dbeafe; color: #1e40af; }
.badge-delete  { background: #fee2e2; color: #991b1b; }
.badge-login   { background: #ede9fe; color: #5b21b6; }
.badge-logout  { background: #f1f5f9; color: #475569; }
.badge-approve { background: #d1fae5; color: #065f46; }
.badge-reject  { background: #fee2e2; color: #991b1b; }
.badge-return  { background: #ffedd5; color: #9a3412; }
.badge-modul   { background: #f1f5f9; color: #334155; }
.timeline-detail {
  margin: 0.5rem 0 0;
  font-size: 0.8rem;
  color: #64748b;
  line-height: 1.5;
}
.timeline-meta {
  display: flex;
  align-items: center;
  gap: 0.9rem;
  margin-top: 0.6rem;
  flex-wrap: wrap;
}
.timeline-meta span {
  display: flex;
  align-items: center;
  gap: 0.25rem;
  font-size: 0.72rem;
  font-weight: 600;
  color: #94a3b8;
}
.meta-user { color: #475569; }
.meta-role {
  padding: 0.1rem 0.5rem;
  border-radius: 9999px;
  font-size: 0.62rem;
  text-transform: uppercase;
  letter-spacing: 0.5px;
}
.role-admin    { background: #fef3c7; color: #92400e; }
.role-karyawan { background: #e0e7ff; color: #3730a3; }
.role-system   { background: #f1f5f9; color: #475569; }

/* PAGINATION */
.pagination {
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 1.5rem;
  margin-top: 2rem;
  padding: 1rem;
}
.page-btn {
  padding: 0.6rem 1.2rem;
  border-radius: 0.75rem;
  border: 1px solid #e2e8f0;
  background: white;
  color: #2d4a3e;
  font-weight: 700;
  font-size: 0.8rem;
  font-family: 'Poppins', sans-serif;
  cursor: pointer;
  transition: all 0.2s;
}
.page-btn:hover:not(:disabled) {
  background: #2d4a3e;
  color: white;
  border-color: #2d4a3e;
}
.page-btn:disabled {
  opacity: 0.4;
  cursor: not-allowed;
}
.page-info {
  font-size: 0.8rem;
  font-weight: 600;
  color: #475569;
}
.page-total {
  color: #94a3b8;
  font-weight: 500;
}

/* CAPITALIZE HELPER */
.capitalize { text-transform: capitalize; }

/* RESPONSIVE */
@media (max-width: 768px) {
  .log-container { padding: 1rem; }
  .stats-row { grid-template-columns: repeat(2, 1fr); }
  .filters-bar { flex-direction: column; }
  .filter-group { width: 100%; }
  .filter-input, .search-input { width: 100%; min-width: unset; }
  .timeline { padding-left: 2rem; }
  .timeline-dot { left: -2rem; width: 28px; height: 28px; font-size: 0.75rem; }
}

/* DARK MODE */
:root .dark .log-header h1 { color: #f1f5f9; }
:root .dark .subtitle { color: #64748b; }
:root .dark .stat-card { background: #0f1610; border-color: rgba(255,255,255,0.06); }
:root .dark .stat-value { color: #f1f5f9; }
:root .dark .filters-bar { background: #0f1610; border-color: rgba(255,255,255,0.06); }
:root .dark .filter-input { background: #1a2e28; border-color: rgba(255,255,255,0.08); color: #e2e8f0; }
:root .dark .timeline-content { background: #0f1610; border-color: rgba(255,255,255,0.06); }
:root .dark .timeline-title { color: #f1f5f9; }
:root .dark .page-btn { background: #0f1610; border-color: rgba(255,255,255,0.08); color: #a7f3d0; }
</style>
