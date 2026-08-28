<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <div class="flex items-center gap-2">
          <div class="p-2 rounded-xl bg-[#2d4a3e]/10 dark:bg-emerald-500/10 text-[#2d4a3e] dark:text-emerald-400">
            <ArrowLeftRight class="w-6 h-6" />
          </div>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Pemasukan & Pengeluaran</h1>
            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Kelola seluruh transaksi keuangan masuk dan keluar secara terpadu</p>
          </div>
        </div>
      </div>
      <div class="flex items-center gap-2.5 w-full sm:w-auto">
        <router-link 
          to="/admin/keuangan/transaksi/create" 
          class="flex-1 sm:flex-initial inline-flex items-center justify-center gap-2 px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm hover:shadow-md"
        >
          <Plus class="w-4 h-4" />
          <span>+ Tambah Transaksi</span>
        </router-link>
      </div>
    </div>

    <!-- Ringkasan Statistik KPI Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <!-- Total Pemasukan -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden group hover:border-emerald-500/40 transition">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pemasukan</p>
          <div class="w-8 h-8 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 flex items-center justify-center">
            <ArrowDownLeft class="w-4 h-4" />
          </div>
        </div>
        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">Rp {{ formatCurrency(summary.total_pemasukan) }}</p>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 flex items-center gap-1 font-medium">
          <span class="text-emerald-500 font-semibold">{{ summary.total_tiket }} tiket</span> terjual pada periode ini
        </p>
      </div>

      <!-- Total Pengeluaran -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden group hover:border-rose-500/40 transition">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pengeluaran</p>
          <div class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/30 text-rose-600 flex items-center justify-center">
            <ArrowUpRight class="w-4 h-4" />
          </div>
        </div>
        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-2">Rp {{ formatCurrency(summary.total_pengeluaran) }}</p>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
          Biaya & beban operasional
        </p>
      </div>

      <!-- Saldo Bersih / Arus Kas Bersih -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden group hover:border-blue-500/40 transition">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Saldo Bersih (Net)</p>
          <div class="w-8 h-8 rounded-xl bg-blue-100 dark:bg-blue-900/30 text-blue-600 flex items-center justify-center">
            <Wallet class="w-4 h-4" />
          </div>
        </div>
        <p 
          class="text-2xl font-black mt-2" 
          :class="summary.saldo_bersih >= 0 ? 'text-blue-600 dark:text-blue-400' : 'text-rose-600 dark:text-rose-400'"
        >
          Rp {{ formatCurrency(summary.saldo_bersih) }}
        </p>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
          {{ summary.saldo_bersih >= 0 ? 'Surplus operasional berjalan' : 'Defisit operasional berjalan' }}
        </p>
      </div>

      <!-- Total Catatan Transaksi -->
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm relative overflow-hidden group hover:border-purple-500/40 transition">
        <div class="flex items-center justify-between">
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Catatan</p>
          <div class="w-8 h-8 rounded-xl bg-purple-100 dark:bg-purple-900/30 text-purple-600 flex items-center justify-center">
            <ReceiptText class="w-4 h-4" />
          </div>
        </div>
        <p class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-2">{{ summary.total_transaksi }} Transaksi</p>
        <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1 font-medium">
          Tersinkronisasi ke CoA & Jurnal
        </p>
      </div>
    </div>

    <!-- Filter Tab & Search Controls -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm space-y-4">
      <!-- Jenis Transaksi Filter Tab -->
      <div class="flex flex-wrap items-center justify-between gap-3 border-b border-gray-100 dark:border-gray-700 pb-4">
        <div class="inline-flex p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl">
          <button 
            @click="setJenisTab('semua')"
            class="px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all"
            :class="filterJenis === 'semua' ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
          >
            Semua Transaksi
          </button>
          <button 
            @click="setJenisTab('pemasukan')"
            class="px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5"
            :class="filterJenis === 'pemasukan' ? 'bg-emerald-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-emerald-600'"
          >
            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
            Pemasukan (Masuk)
          </button>
          <button 
            @click="setJenisTab('pengeluaran')"
            class="px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all flex items-center gap-1.5"
            :class="filterJenis === 'pengeluaran' ? 'bg-rose-600 text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-rose-600'"
          >
            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
            Pengeluaran (Keluar)
          </button>
        </div>

        <div class="text-xs font-semibold text-gray-500 dark:text-gray-400">
          Menampilkan <span class="text-gray-900 dark:text-white font-bold">{{ filteredTransactions.length }}</span> transaksi
        </div>
      </div>

      <!-- Controls Grid: Bulan, CoA, Search, Reset -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
        <!-- Filter Bulan -->
        <div>
          <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Periode Bulan</label>
          <input 
            v-model="filterBulan" 
            type="month" 
            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
            @change="loadData"
          />
        </div>

        <!-- Filter Daftar Akun (CoA) -->
        <div>
          <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Daftar Akun (CoA)</label>
          <select 
            v-model="filterAkunId" 
            class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
            @change="loadData"
          >
            <option value="">Semua Akun / Pos</option>
            <optgroup label="Akun Pendapatan">
              <option v-for="a in akunsPendapatan" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
            </optgroup>
            <optgroup label="Akun Beban">
              <option v-for="a in akunsBeban" :key="a.id" :value="a.id">{{ a.kode_akun }} - {{ a.nama_akun }}</option>
            </optgroup>
          </select>
        </div>

        <!-- Search Input -->
        <div>
          <label class="block text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase mb-1">Pencarian</label>
          <div class="relative">
            <Search class="w-4 h-4 text-gray-400 absolute left-3.5 top-3" />
            <input 
              v-model="searchQuery" 
              type="text" 
              placeholder="Cari transaksi / keterangan..."
              class="w-full pl-9 pr-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
              @input="loadData"
            />
          </div>
        </div>

        <!-- Reset Button -->
        <div class="flex items-end">
          <button 
            @click="resetFilter" 
            class="w-full py-2.5 px-4 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition font-bold text-xs uppercase tracking-wider flex items-center justify-center gap-2"
          >
            <RotateCcw class="w-3.5 h-3.5" />
            <span>Reset Filter</span>
          </button>
        </div>
      </div>
    </div>

    <!-- Tabel Transaksi Terpadu -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Tanggal & Bukti</th>
              <th class="px-5 py-3.5">Nama Transaksi</th>
              <th class="px-5 py-3.5">Jenis</th>
              <th class="px-5 py-3.5">Daftar Akun (CoA)</th>
              <th class="px-5 py-3.5 text-right">Nominal (Rp)</th>
              <th class="px-5 py-3.5 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60 text-sm">
            <tr 
              v-for="t in filteredTransactions" 
              :key="t.id" 
              class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition group"
            >
              <!-- Tanggal & Kode Bukti -->
              <td class="px-5 py-3.5 whitespace-nowrap">
                <p class="font-bold text-gray-900 dark:text-white">{{ formatDate(t.tanggal) }}</p>
                <span class="text-[11px] font-mono text-gray-400 dark:text-gray-500">{{ t.kode_transaksi }}</span>
              </td>

              <!-- Nama Transaksi & Keterangan -->
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-900 dark:text-white group-hover:text-emerald-700 dark:group-hover:text-emerald-400 transition">
                  {{ t.nama_transaksi }}
                </p>
                <p v-if="t.keterangan" class="text-xs text-gray-500 dark:text-gray-400 line-clamp-1 mt-0.5">
                  {{ t.keterangan }}
                </p>
                <div v-if="t.jenis === 'pemasukan' && t.tipe_kategori === 'tiket_masuk'" class="text-[11px] text-emerald-600 dark:text-emerald-400 font-medium mt-0.5">
                  🎟️ {{ t.jumlah }} Unit Tiket
                </div>
              </td>

              <!-- Jenis (Badge) -->
              <td class="px-5 py-3.5 whitespace-nowrap">
                <span 
                  class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                  :class="t.jenis === 'pemasukan' 
                    ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800' 
                    : 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-200 dark:border-rose-800'"
                >
                  <ArrowDownLeft v-if="t.jenis === 'pemasukan'" class="w-3 h-3" />
                  <ArrowUpRight v-else class="w-3 h-3" />
                  {{ t.jenis === 'pemasukan' ? 'Pemasukan' : 'Pengeluaran' }}
                </span>
              </td>

              <!-- Daftar Akun (CoA) -->
              <td class="px-5 py-3.5 whitespace-nowrap">
                <div v-if="t.akun" class="flex items-center gap-2">
                  <span class="px-2 py-0.5 rounded-md bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 font-mono text-xs font-semibold">
                    {{ t.akun.kode_akun }}
                  </span>
                  <span class="text-xs font-medium text-gray-700 dark:text-gray-300">
                    {{ t.akun.nama_akun }}
                  </span>
                </div>
                <span v-else class="text-xs text-gray-400 italic">Belum dipetakan</span>
              </td>

              <!-- Nominal -->
              <td class="px-5 py-3.5 text-right whitespace-nowrap">
                <p 
                  class="font-extrabold text-sm"
                  :class="t.jenis === 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
                >
                  {{ t.jenis === 'pemasukan' ? '+' : '-' }} Rp {{ formatCurrency(t.nominal) }}
                </p>
              </td>

              <!-- Aksi -->
              <td class="px-5 py-3.5 text-center whitespace-nowrap">
                <div class="inline-flex items-center gap-1.5">
                  <router-link 
                    :to="`/admin/keuangan/transaksi/${t.id}`"
                    class="p-1.5 rounded-lg text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition"
                    title="Lihat Detail"
                  >
                    <Eye class="w-4 h-4" />
                  </router-link>
                  <router-link 
                    :to="`/admin/keuangan/transaksi/${t.id}/edit`"
                    class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition"
                    title="Edit Transaksi"
                  >
                    <Edit2 class="w-4 h-4" />
                  </router-link>
                  <button 
                    @click="deleteData(t)"
                    class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition"
                    title="Hapus Transaksi"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- State Kosong / Loading -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Memuat data transaksi keuangan...</p>
      </div>
      <div v-else-if="filteredTransactions.length === 0" class="text-center py-16 text-gray-500 dark:text-gray-400">
        <div class="w-12 h-12 rounded-2xl bg-gray-100 dark:bg-gray-700/50 flex items-center justify-center mx-auto mb-3 text-gray-400">
          <ReceiptText class="w-6 h-6" />
        </div>
        <p class="font-bold text-gray-700 dark:text-gray-300">Tidak ada data transaksi</p>
        <p class="text-xs text-gray-400 mt-1">Gunakan tombol "+ Tambah Transaksi" untuk mencatat pemasukan atau pengeluaran baru.</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { 
  ArrowLeftRight, ArrowDownLeft, ArrowUpRight, Plus, 
  Wallet, ReceiptText, Search, RotateCcw, Eye, Edit2, Trash2 
} from 'lucide-vue-next'

const transactions = ref([])
const akuns = ref([])
const filterJenis = ref('semua')
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const filterAkunId = ref('')
const searchQuery = ref('')
const loading = ref(false)

const summary = ref({
  total_pemasukan: 0,
  total_pengeluaran: 0,
  saldo_bersih: 0,
  total_transaksi: 0,
  total_tiket: 0,
})

const akunsPendapatan = computed(() => akuns.value.filter(a => a.kategori === 'pendapatan'))
const akunsBeban = computed(() => akuns.value.filter(a => a.kategori === 'beban'))

const filteredTransactions = computed(() => {
  return transactions.value
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' })
}

const setJenisTab = (jenis) => {
  filterJenis.value = jenis
  loadData()
}

const loadAkuns = async () => {
  try {
    const res = await api.get('/admin/akuns')
    akuns.value = res.data.data
  } catch (err) {
    console.error('Gagal memuat akun CoA:', err)
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {}
    if (filterBulan.value) params.bulan = filterBulan.value
    if (filterJenis.value && filterJenis.value !== 'semua') params.jenis = filterJenis.value
    if (filterAkunId.value) params.akun_id = filterAkunId.value
    if (searchQuery.value) params.q = searchQuery.value

    const res = await api.get('/admin/keuangan/transaksi', { params })
    transactions.value = res.data.data || []
    if (res.data.summary) {
      summary.value = res.data.summary
    }
  } catch (error) {
    console.error('Gagal mengambil data transaksi:', error)
  } finally {
    loading.value = false
  }
}

const resetFilter = () => {
  filterBulan.value = new Date().toISOString().slice(0, 7)
  filterJenis.value = 'semua'
  filterAkunId.value = ''
  searchQuery.value = ''
  loadData()
}

const deleteData = async (transaction) => {
  const isPemasukan = transaction.jenis === 'pemasukan'
  const title = isPemasukan ? 'Hapus Pemasukan?' : 'Hapus Pengeluaran?'
  const text = `Data "${transaction.nama_transaksi}" sebesar Rp ${formatCurrency(transaction.nominal)} akan dihapus dan otomatis memperbarui Jurnal, Arus Kas, Laba Rugi, dan Neraca.`

  const result = await Swal.fire({
    title,
    text,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    customClass: {
      popup: 'rounded-[2rem] font-poppins',
      confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3',
      cancelButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
    }
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/admin/keuangan/transaksi/${transaction.id}`)
      Swal.fire({
        icon: 'success',
        title: 'Berhasil Dihapus',
        text: 'Transaksi telah dihapus dan seluruh laporan keuangan diperbarui otomatis.',
        timer: 1800,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]'
        }
      })
      loadData()
    } catch (error) {
      console.error('Gagal menghapus transaksi:', error)
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menghapus',
        text: error.response?.data?.message || 'Terjadi kesalahan saat menghapus data.',
        confirmButtonColor: '#2d4a3e',
      })
    }
  }
}

onMounted(() => {
  loadAkuns()
  loadData()
})
</script>
