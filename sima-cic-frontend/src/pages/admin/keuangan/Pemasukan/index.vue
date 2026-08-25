<template>
  <div class="space-y-6 p-6 font-poppins">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Data Pemasukan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola semua data pemasukan keuangan</p>
      </div>
      <router-link
        to="/admin/pemasukan/create"
        class="flex items-center gap-2 px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm"
      >
        <Plus class="w-4 h-4" />
        Tambah Pemasukan
      </router-link>
    </div>

    <!-- Filter & Search -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="flex flex-wrap gap-3 items-center">
        <div class="relative flex-1 min-w-[200px]">
          <Search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" />
          <input
            v-model="search"
            type="text"
            placeholder="Cari nama pemasukan..."
            class="w-full pl-10 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition"
          />
        </div>
        <select
          v-model="filterTipe"
          class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition"
        >
          <option value="">Semua Tipe</option>
          <option value="tiket_masuk">Tiket Masuk</option>
          <option value="tiket_event">Tiket Event</option>
          <option value="pendapatan_jasa">Pendapatan Jasa</option>
          <option value="penjualan_produk">Penjualan Produk</option>
          <option value="donasi">Donasi</option>
          <option value="sponsor">Sponsor</option>
          <option value="grant">Hibah / Grant</option>
          <option value="lainnya">Lainnya</option>
        </select>
        <input
          v-model="filterBulan"
          type="month"
          class="px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none text-sm transition"
        />
        <button
          @click="resetFilter"
          class="px-4 py-2.5 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 rounded-xl hover:bg-gray-200 dark:hover:bg-gray-600 transition text-sm font-medium"
        >Reset</button>
      </div>
    </div>

    <!-- KPI Summary -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Total Data</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ filteredData.length }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-emerald-200 dark:border-emerald-900/40 p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-emerald-500 mb-1">Total Nominal</p>
        <p class="text-2xl font-bold text-emerald-600 dark:text-emerald-400">Rp {{ formatCurrency(totalNominal) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-5 shadow-sm">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-1">Rata-rata Nominal</p>
        <p class="text-2xl font-bold text-gray-700 dark:text-gray-200">Rp {{ formatCurrency(rataRataNominal) }}</p>
      </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm overflow-hidden">
      <div v-if="loading" class="flex justify-center items-center py-20">
        <div class="w-10 h-10 border-4 border-emerald-500 border-t-transparent rounded-full animate-spin"></div>
      </div>
      <div v-else-if="filteredData.length === 0" class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-16 h-16 rounded-2xl bg-gray-100 dark:bg-gray-700 flex items-center justify-center mb-4">
          <BanknoteArrowUp class="w-8 h-8 text-gray-400" />
        </div>
        <p class="text-lg font-bold text-gray-600 dark:text-gray-300">Tidak ada data pemasukan</p>
        <p class="text-sm text-gray-400 mt-1">Coba ubah filter atau tambahkan data baru.</p>
      </div>
      <div v-else class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-gray-400">#</th>
              <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-gray-400">Nama Pemasukan</th>
              <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-gray-400">Tipe</th>
              <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-widest text-gray-400">Jumlah</th>
              <th class="px-6 py-4 text-right text-xs font-black uppercase tracking-widest text-gray-400">Nominal</th>
              <th class="px-6 py-4 text-left text-xs font-black uppercase tracking-widest text-gray-400">Tanggal</th>
              <th class="px-6 py-4 text-center text-xs font-black uppercase tracking-widest text-gray-400">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
            <tr
              v-for="(item, index) in paginatedData"
              :key="item.id"
              class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition"
            >
              <td class="px-6 py-4 text-gray-400 text-xs">{{ (currentPage - 1) * perPage + index + 1 }}</td>
              <td class="px-6 py-4">
                <p class="font-semibold text-gray-900 dark:text-white">{{ item.nama_pemasukan }}</p>
                <p v-if="item.keterangan" class="text-xs text-gray-400 mt-0.5 truncate max-w-[180px]">{{ item.keterangan }}</p>
              </td>
              <td class="px-6 py-4">
                <span class="px-2.5 py-1 rounded-full text-xs font-bold" :class="getTipeClass(item.tipe)">
                  {{ formatTipe(item.tipe) }}
                </span>
              </td>
              <td class="px-6 py-4 text-right font-medium text-gray-700 dark:text-gray-300">{{ item.jumlah }} unit</td>
              <td class="px-6 py-4 text-right font-bold text-emerald-600 dark:text-emerald-400">
                Rp {{ formatCurrency(item.nominal) }}
              </td>
              <td class="px-6 py-4 text-gray-600 dark:text-gray-300 text-xs whitespace-nowrap">{{ formatDate(item.tanggal_pemasukan) }}</td>
              <td class="px-6 py-4">
                <div class="flex items-center justify-center gap-2">
                  <router-link :to="`/admin/pemasukan/${item.id}`"
                    class="p-2 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition" title="Detail">
                    <Eye class="w-4 h-4" />
                  </router-link>
                  <router-link :to="`/admin/pemasukan/${item.id}/edit`"
                    class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition" title="Edit">
                    <Pencil class="w-4 h-4" />
                  </router-link>
                  <button @click="deleteData(item)"
                    class="p-2 rounded-lg bg-rose-50 dark:bg-rose-900/20 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/40 transition" title="Hapus">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="totalPages > 1" class="flex items-center justify-between px-6 py-4 border-t border-gray-100 dark:border-gray-700">
        <p class="text-xs text-gray-400">
          Menampilkan {{ (currentPage - 1) * perPage + 1 }}-{{ Math.min(currentPage * perPage, filteredData.length) }} dari {{ filteredData.length }} data
        </p>
        <div class="flex gap-1">
          <button @click="currentPage--" :disabled="currentPage === 1"
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">‹</button>
          <button
            v-for="page in totalPages" :key="page" @click="currentPage = page"
            :class="currentPage === page ? 'bg-[#2d4a3e] text-white' : 'hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300'"
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition">{{ page }}</button>
          <button @click="currentPage++" :disabled="currentPage === totalPages"
            class="px-3 py-1.5 rounded-lg text-sm font-medium transition disabled:opacity-30 hover:bg-gray-100 dark:hover:bg-gray-700 text-gray-600 dark:text-gray-300">›</button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { Plus, Search, Eye, Pencil, Trash2, BanknoteArrowUp } from 'lucide-vue-next'
import api from '@/services/api'
import Swal from 'sweetalert2'

const loading     = ref(false)
const allData     = ref([])
const search      = ref('')
const filterTipe  = ref('')
const filterBulan = ref('')
const currentPage = ref(1)
const perPage     = 10

const formatCurrency = (v) => new Intl.NumberFormat('id-ID').format(v || 0)
const formatDate = (d) => d ? new Date(d).toLocaleDateString('id-ID', { year: 'numeric', month: 'short', day: 'numeric' }) : '-'

const formatTipe = (tipe) => ({
  tiket_masuk: 'Tiket Masuk', tiket_event: 'Tiket Event',
  pendapatan_jasa: 'Pendapatan Jasa', penjualan_produk: 'Penjualan Produk',
  donasi: 'Donasi', sponsor: 'Sponsor', grant: 'Hibah / Grant', lainnya: 'Lainnya',
}[tipe] || tipe)

const getTipeClass = (tipe) => ({
  tiket_masuk:      'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
  tiket_event:      'bg-teal-100 text-teal-800 dark:bg-teal-900/30 dark:text-teal-300',
  pendapatan_jasa:  'bg-cyan-100 text-cyan-800 dark:bg-cyan-900/30 dark:text-cyan-300',
  penjualan_produk: 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900/30 dark:text-indigo-300',
  donasi:           'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
  sponsor:          'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
  grant:            'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
  lainnya:          'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300',
}[tipe] || 'bg-gray-100 text-gray-800')

const filteredData = computed(() => {
  let data = allData.value
  if (search.value) {
    const q = search.value.toLowerCase()
    data = data.filter(d => d.nama_pemasukan?.toLowerCase().includes(q))
  }
  if (filterTipe.value) data = data.filter(d => d.tipe === filterTipe.value)
  if (filterBulan.value) {
    const [y, m] = filterBulan.value.split('-')
    data = data.filter(d => {
      const tgl = new Date(d.tanggal_pemasukan)
      return tgl.getFullYear() === +y && (tgl.getMonth() + 1) === +m
    })
  }
  return data
})

const totalNominal    = computed(() => filteredData.value.reduce((s, d) => s + (+d.nominal || 0), 0))
const rataRataNominal = computed(() => filteredData.value.length ? totalNominal.value / filteredData.value.length : 0)
const totalPages      = computed(() => Math.max(1, Math.ceil(filteredData.value.length / perPage)))
const paginatedData   = computed(() => {
  const start = (currentPage.value - 1) * perPage
  return filteredData.value.slice(start, start + perPage)
})

const resetFilter = () => {
  search.value = ''; filterTipe.value = ''; filterBulan.value = ''; currentPage.value = 1
}

const loadData = async () => {
  loading.value = true
  try {
    const res = await api.get('/admin/pemasukan')
    allData.value = res.data?.data || res.data || []
  } catch (e) {
    console.error('Gagal memuat data pemasukan:', e)
  } finally {
    loading.value = false
  }
}

const deleteData = async (item) => {
  const result = await Swal.fire({
    title: 'Hapus Pemasukan?',
    text: `"${item.nama_pemasukan}" akan dihapus secara permanen.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
    customClass: {
      popup: 'rounded-[2rem] font-poppins',
      confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3',
      cancelButton:  'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
    }
  })
  if (result.isConfirmed) {
    try {
      await api.delete(`/admin/pemasukan/${item.id}`)
      await loadData()
      Swal.fire({ icon: 'success', title: 'Berhasil Dihapus', timer: 1500, showConfirmButton: false,
        customClass: { popup: 'rounded-[2rem] font-poppins' } })
    } catch (e) {
      Swal.fire({ icon: 'error', title: 'Gagal Menghapus',
        text: e.response?.data?.message || 'Terjadi kesalahan.',
        confirmButtonColor: '#2d4a3e',
        customClass: { popup: 'rounded-[2rem] font-poppins',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3' } })
    }
  }
}

onMounted(loadData)
</script>
