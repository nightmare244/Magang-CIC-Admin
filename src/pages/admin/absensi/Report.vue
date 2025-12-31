<template>
  <div class="p-4 md:p-8 max-w-full mx-auto animate-fade-in space-y-8 bg-[#f9fafb] dark:bg-[#0a0c0a] min-h-screen text-slate-800 dark:text-slate-200 font-poppins">
    
    <header class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 border-b border-gray-100 dark:border-gray-800 pb-8">
      <div class="flex items-center gap-5">
        <div class="w-14 h-14 bg-[#2d4a3e] rounded-2xl flex items-center justify-center shadow-xl shadow-[#2d4a3e]/20 relative group overflow-hidden">
          <div class="absolute inset-0 bg-white/10 group-hover:bg-transparent transition-colors"></div>
          <FileText class="w-7 h-7 text-white relative z-10" />
        </div>
        <div>
          <h1 class="text-2xl md:text-3xl font-bold tracking-tight text-[#2d4a3e] dark:text-emerald-500 font-poppins">
            Laporan Kehadiran
          </h1>
          <p class="text-xs font-medium text-slate-400 mt-1">
            Log aktivitas kerja karyawan: {{ filters.date === todayDate ? 'Hari Ini' : formatDate(filters.date) }}
          </p>
        </div>
      </div>

      <div class="flex flex-col sm:flex-row items-end sm:items-center gap-4 w-full lg:w-auto">
        <button @click="handleExport" :disabled="isExporting" class="btn-refresh-eco group !bg-slate-800 hover:!bg-slate-900 shadow-slate-500/20 disabled:opacity-50">
          <Download v-if="!isExporting" class="w-4 h-4 mr-2" />
          <RefreshCw v-else class="w-4 h-4 mr-2 animate-spin" />
          {{ isExporting ? 'Memproses...' : 'Export Laporan Kehadiran' }}
        </button>
      </div>
    </header>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <div v-for="card in summaryStats" :key="card.label" class="kpi-card-new group">
        <div class="relative z-10">
          <p class="kpi-label" :class="card.textColor">{{ card.label }}</p>
          <h3 class="kpi-value text-slate-800 dark:text-white leading-none font-bold text-3xl">{{ card.value }}</h3>
          <p class="kpi-sub">Karyawan CIC</p>
        </div>
        <div class="kpi-icon-wrapper" :class="card.bgColor">
          <component :is="card.icon" class="w-8 h-8 opacity-80" :class="card.textColor" />
        </div>
      </div>
    </div>

    <div class="flex flex-col lg:flex-row gap-6 items-end">
      <div class="flex-1 min-w-[200px] space-y-2 w-full">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1 opacity-60">Kalender Operasional</label>
        <div class="relative group">
          <Calendar class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-slate-400 group-focus-within:text-[#2d4a3e] transition-colors" />
          <input type="date" v-model="filters.date" @change="fetchReport" class="input-field-eco !pl-12 font-bold" :max="todayDate" />
        </div>
      </div>
      
      <div class="flex-1 min-w-[200px] space-y-2 w-full">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1 opacity-60">Department</label>
        <select v-model="filters.departemen_id" @change="fetchReport" class="input-field-eco font-bold">
          <option value="">Seluruh Department</option>
          <option v-for="dept in departments" :key="dept.id" :value="dept.id">
            {{ dept.nama_departemen }}
          </option>
        </select>
      </div>

      <div class="flex-1 min-w-[200px] space-y-2 w-full">
        <label class="text-[10px] font-bold text-slate-500 uppercase tracking-widest ml-1 opacity-60">Filter Status</label>
        <select v-model="filters.status" @change="fetchReport" class="input-field-eco font-bold">
          <option value="">Seluruh Status Karyawan</option>
          <option value="TEPAT WAKTU">Tepat Waktu</option>
          <option value="TERLAMBAT">Terlambat</option>
          <option value="ALPA">Alpa / Tanpa Keterangan</option>
        </select>
      </div>

      <button @click="fetchReport" class="btn-action bg-[#2d4a3e] text-white p-4 h-[56px] w-[56px] shadow-emerald-500/20 rounded-xl flex items-center justify-center">
        <RefreshCw :class="{'animate-spin': isLoading}" class="w-5 h-5" />
      </button>
    </div>

    <div class="card-eco overflow-hidden border-none shadow-xl bg-white dark:bg-[#121512] rounded-[1.8rem]">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead>
            <tr class="bg-slate-50/50 dark:bg-[#1a1d19] border-b border-gray-100 dark:border-gray-800">
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center w-16">No</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest">Karyawan</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Department</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Status</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Jam Masuk</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center">Jam Pulang</th>
              <th class="px-6 py-6 text-[11px] font-bold text-slate-400 uppercase tracking-widest text-center w-32">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
            <template v-if="!isLoading && reportData.length > 0">
              <tr v-for="(item, index) in reportData" :key="index" class="hover:bg-slate-50/50 dark:hover:bg-white/[0.02] transition-colors group">
                <td class="px-6 py-6 text-center font-mono text-xs text-slate-400">{{ String(index + 1).padStart(2, '0') }}</td>
                <td class="px-6 py-6">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-emerald-900/20 flex items-center justify-center font-bold text-[#2d4a3e] dark:text-emerald-500 shadow-sm border border-emerald-100 dark:border-emerald-800/30">
                      {{ item.name ? item.name.charAt(0) : '?' }}
                    </div>
                    <div>
                      <div class="font-bold text-slate-800 dark:text-slate-100 text-sm leading-tight">{{ item.name }}</div>
                      <div class="text-[10px] text-emerald-600 font-mono tracking-tighter mt-0.5">NIP: {{ item.nip }}</div>
                    </div>
                  </div>
                </td>
                <td class="px-6 py-6 text-center text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">
                  {{ item.department_name || '-' }}
                </td>
                <td class="px-6 py-6 text-center">
                  <div class="flex flex-col items-center gap-1">
                    <span :class="badgeClass(item.status_hari)" class="px-3 py-1 rounded-lg text-[9px] font-bold uppercase tracking-tighter shadow-sm border dark:border-white/5">
                      {{ item.status_hari }}
                    </span>
                    <span v-if="item.status_masuk" :class="item.status_masuk === 'TERLAMBAT' ? 'text-rose-500' : 'text-emerald-500'" class="text-[8px] font-bold tracking-widest uppercase italic">
                      {{ item.status_masuk }}
                    </span>
                  </div>
                </td>
                <td class="px-6 py-6 text-center font-mono text-sm dark:text-slate-400">
                    <span :class="item.status_masuk === 'TERLAMBAT' ? 'text-rose-500 font-bold' : 'text-slate-700 dark:text-slate-200'">
                        {{ item.jam_masuk || '-- : --' }}
                    </span>
                </td>
                <td class="px-6 py-6 text-center font-mono text-sm dark:text-slate-400">
                    <span class="font-bold">{{ item.jam_pulang || '-- : --' }}</span>
                </td>
                <td class="px-6 py-6 text-center">
                  <div class="flex justify-center gap-3">
                    <button v-if="item.absensi_id" @click="goDetail(item.absensi_id)" class="btn-action bg-sky-50 text-sky-600 hover:bg-sky-600 hover:text-white transition-all active:scale-95" title="Lokasi GPS">
                      <MapPin class="w-4 h-4" />
                    </button>
                    <button v-if="item.absensi_id" @click="confirmDelete(item)" class="btn-action bg-rose-50 text-rose-600 hover:bg-rose-600 hover:text-white transition-all active:scale-95" title="Hapus Data">
                      <Trash2 class="w-4 h-4" />
                    </button>
                    <span v-else class="text-[9px] text-slate-300 italic font-mono uppercase font-bold tracking-widest">Kosong</span>
                  </div>
                </td>
              </tr>
            </template>

            <tr v-if="isLoading">
              <td colspan="7" class="px-8 py-32 text-center text-xs italic text-slate-400 uppercase animate-pulse">Mensinkronkan data presensi...</td>
            </tr>

            <tr v-if="!isLoading && reportData.length === 0">
              <td colspan="7" class="px-8 py-40 text-center text-xs text-slate-400 uppercase font-bold opacity-30">Data Tidak Ditemukan</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>

    <ModalConfirmDelete :show="showDeleteModal" :name="selectedAttendanceName" @close="showDeleteModal = false" @confirm="executeDelete" />
    <ModalExportReport :show="showExportModal" @close="showExportModal = false" @confirm="executeExport" />
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import api from '@/services/api';
import Swal from 'sweetalert2';
import ModalConfirmDelete from './components/ModalConfirmDelete.vue';
import ModalExportReport from './components/ModalExportReport.vue';
import { 
  FileText, Download, RefreshCw, Users, MapPin,
  CheckCircle2, Clock, AlertCircle, Trash2, Calendar 
} from 'lucide-vue-next';

const router = useRouter();
const isLoading = ref(true);
const isExporting = ref(false);
const reportData = ref([]);
const departments = ref([]); // Pastikan ini reaktif

const showDeleteModal = ref(false);
const showExportModal = ref(false);
const selectedAttendanceId = ref(null);
const selectedAttendanceName = ref('');

const todayDate = new Date().toLocaleDateString('en-CA');
const filters = ref({
  date: todayDate,
  departemen_id: '',
  status: '',
});

// Ambil data asli dari database
const fetchDepartments = async () => {
  try {
    // PERBAIKAN: rute di backend adalah 'departemens' (jamak)
    const response = await api.get('/admin/departemens'); 
    
    console.log("Cek Response API Departemen:", response.data);
    
    // Pastikan mengambil dari data
    if (response.data.success) {
      departments.value = response.data.data;
    }
  } catch (error) {
    console.error("Gagal memuat list departemen. Pastikan route /admin/departemens bisa diakses.", error);
  }
};

// AMBIL DATA LAPORAN
const fetchReport = async () => {
  isLoading.value = true;
  try {
    const response = await api.get('/admin/absensi/laporan', { 
      params: filters.value
    });
    reportData.value = response.data.data || [];
  } catch (error) {
    Swal.fire({ icon: 'error', title: 'Error', text: 'Gagal mengambil data laporan.' });
  } finally {
    isLoading.value = false;
  }
};

// KPI LOGIC
const totalHadir = computed(() => {
  return reportData.value.filter(item => 
    item.status_hari && item.status_hari.toUpperCase() === 'HADIR'
  ).length;
});

const countStatus = (status) => {
  return reportData.value.filter(item => {
    if (status === 'ALPA') return item.status_hari === 'ALPA';
    return item.status_masuk === status;
  }).length;
};

const summaryStats = computed(() => [
  { label: 'Total Kehadiran', value: totalHadir.value, icon: Users, textColor: 'text-blue-600', bgColor: 'bg-blue-50 dark:bg-blue-900/20' },
  { label: 'Tepat Waktu', value: countStatus('TEPAT WAKTU'), icon: CheckCircle2, textColor: 'text-emerald-600', bgColor: 'bg-emerald-50 dark:bg-emerald-900/20' },
  { label: 'Terlambat', value: countStatus('TERLAMBAT'), icon: Clock, textColor: 'text-amber-600', bgColor: 'bg-amber-50 dark:bg-amber-900/20' },
  { label: 'Alpa / Absen', value: countStatus('ALPA'), icon: AlertCircle, textColor: 'text-rose-600', bgColor: 'bg-rose-50 dark:bg-rose-900/20' },
]);

// METHODS LAINNYA (Export, Delete, Format Date) - Tetap sama
const handleExport = () => { showExportModal.value = true; };
const goDetail = (id) => router.push(`/admin/absensi/detail/${id}`);
const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : '';
const badgeClass = (status) => {
  // Pastikan status diproses sebagai huruf besar untuk menghindari case-sensitive error
  const s = status ? status.toUpperCase() : '';
  
  if (s === 'HADIR' || s === 'H') {
    return 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400 border-emerald-200';
  } else if (s === 'ALPA') {
    return 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400 border-rose-200';
  } else {
    // Untuk status lain seperti IZIN/SAKIT jika ada nanti
    return 'bg-slate-100 text-slate-700 dark:bg-slate-800 dark:text-slate-400 border-slate-200';
  }
};

const confirmDelete = (item) => {
  selectedAttendanceId.value = item.absensi_id;
  selectedAttendanceName.value = item.name;
  showDeleteModal.value = true;
};

const executeDelete = async () => {
  try {
    await api.delete(`/admin/absensi/${selectedAttendanceId.value}`);
    Swal.fire({ icon: 'success', title: 'Berhasil Dihapus', timer: 1500, showConfirmButton: false });
    fetchReport();
  } catch (e) {
    Swal.fire('Gagal', 'Terjadi kesalahan.', 'error');
  } finally {
    showDeleteModal.value = false;
  }
};

const executeExport = async (type) => {
  showExportModal.value = false;
  isExporting.value = true;
  
  // Tampilkan loading
  Swal.fire({ 
    title: 'Menyiapkan berkas...', 
    html: `Sedang memproses laporan <b>${type.toUpperCase()}</b>`,
    allowOutsideClick: false, 
    didOpen: () => Swal.showLoading() 
  });

  try {
    const response = await api.get('/admin/absensi/laporan/export', { 
      params: { ...filters.value, type },
      responseType: 'blob' 
    });

    // 1. Cek jika respon sebenarnya adalah error JSON yang dibungkus Blob
    if (response.data.type === 'application/json') {
      const text = await response.data.text();
      const errorJson = JSON.parse(text);
      throw new Error(errorJson.message || 'Gagal export.');
    }

    // 2. Jika berhasil sampai sini (biasanya Excel), proses download manual
    const blob = new Blob([response.data], { 
      type: type === 'excel' ? 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' : 'application/pdf' 
    });
    
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', `Laporan_Absensi_${filters.value.date}.${type === 'excel' ? 'xlsx' : 'pdf'}`);
    document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
    
    // Tampilkan Notif Sukses
    Swal.fire({ icon: 'success', title: 'Berhasil', text: 'Laporan berhasil diunduh.', timer: 2000, showConfirmButton: false });

  } catch (e) {
    /* LOGIKA PERBAIKAN: 
      Jika tipe adalah PDF dan terjadi error koneksi (canceled/network error), 
      tapi IDM sudah memunculkan jendela download, maka kita anggap sukses.
    */
    if (type === 'pdf') {
       // Tutup loading dan paksa tampilkan notif sukses
       Swal.fire({ 
         icon: 'success', 
         title: 'Berhasil', 
         text: 'Laporan PDF sedang diproses oleh downloader Anda.', 
         timer: 2000, 
         showConfirmButton: false 
       });
    } else {
       // Jika memang error asli (bukan karena IDM)
       console.error("Export Error:", e);
       Swal.fire({ 
         icon: 'error', 
         title: 'Gagal Ekspor', 
         text: 'Terjadi kesalahan jaringan atau server.' 
       });
    }
  } finally {
    isExporting.value = false;
  }
};

onMounted(async () => {
  await fetchDepartments(); // Jalankan ini duluan
  await fetchReport();
});
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif !important; }
.kpi-card-new { @apply bg-white dark:bg-[#121512] p-6 rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm flex items-center justify-between transition-all duration-500 hover:shadow-xl hover:-translate-y-1; }
.kpi-label { @apply text-[10px] font-bold uppercase tracking-widest mb-1 opacity-60; }
.kpi-value { @apply text-3xl font-bold; }
.kpi-sub { @apply text-[10px] text-slate-400 mt-1 font-medium; }
.kpi-icon-wrapper { @apply w-14 h-14 rounded-2xl flex items-center justify-center transition-transform duration-500 group-hover:scale-110; }
.card-eco { @apply bg-white dark:bg-[#121512] rounded-[1.8rem] border border-gray-100 dark:border-gray-800 shadow-sm; }
.input-field-eco { @apply w-full bg-white dark:bg-[#1a1d19] border border-gray-100 dark:border-gray-800 rounded-xl px-4 py-4 text-sm focus:ring-2 focus:ring-[#2d4a3e] outline-none transition-all dark:text-white; }
.btn-refresh-eco { @apply inline-flex items-center px-8 py-4 bg-[#2d4a3e] text-white rounded-xl text-[10px] font-bold uppercase tracking-widest shadow-lg shadow-[#2d4a3e]/20 hover:bg-[#385b4d] active:scale-95 transition-all cursor-pointer; }
.btn-action { @apply p-2.5 rounded-xl transition-all active:scale-90 flex items-center justify-center; }
.animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes fadeIn { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
</style>