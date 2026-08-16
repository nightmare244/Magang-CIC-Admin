<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Daftar Pengeluaran</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data pengeluaran</p>
      </div>
      <router-link 
        to="/admin/pengeluaran/create" 
        class="px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm"
      >
        + Tambah Pengeluaran
      </router-link>
    </div>

    <!-- Filter dan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pengeluaran</p>
        <p class="text-2xl font-bold text-rose-600 mt-2">Rp {{ formatCurrency(totalNominal) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Rata-rata per Item</p>
        <p class="text-2xl font-bold text-amber-600 mt-2">Rp {{ formatCurrency(rataRata) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Catatan</p>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ pengeluarans.length }}</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input 
          v-model="filterBulan" 
          type="month" 
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        />
        <select 
          v-model="filterKategori" 
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        >
          <option value="">Semua Kategori</option>
          <option value="gaji">Gaji</option>
          <option value="operasional">Operasional</option>
          <option value="maintenance">Maintenance</option>
          <option value="utility">Utility</option>
          <option value="lainnya">Lainnya</option>
        </select>
        <button 
          @click="loadData" 
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-xl hover:bg-gray-300 dark:hover:bg-gray-600 transition font-medium text-sm"
        >
          Cari
        </button>
        <button 
          @click="resetFilter" 
          class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl hover:bg-gray-200 transition font-medium text-sm"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700">
            <tr>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nama</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Kategori</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="p in pengeluarans" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
              <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ p.nama_pengeluaran }}</td>
              <td class="px-6 py-4 text-sm">
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="getKategoriClass(p.kategori)"
                >{{ formatKategori(p.kategori) }}</span>
              </td>
              <td class="px-6 py-4 text-sm font-bold text-rose-600">Rp {{ formatCurrency(p.nominal) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(p.tanggal_pengeluaran) }}</td>
              <td class="px-6 py-4 text-sm">
                <router-link 
                  :to="`/admin/pengeluaran/${p.id}`"
                  class="text-blue-600 hover:text-blue-800 font-medium mr-3 transition"
                >
                  Lihat
                </router-link>
                <router-link 
                  :to="`/admin/pengeluaran/${p.id}/edit`"
                  class="text-amber-600 hover:text-amber-800 font-medium mr-3 transition"
                >
                  Edit
                </router-link>
                <button 
                  @click="deleteData(p.id)"
                  class="text-rose-600 hover:text-rose-800 font-medium transition"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="pengeluarans.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400 font-medium">
        Tidak ada data pengeluaran
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

const pengeluarans = ref([])
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const filterKategori = ref('')
const totalNominal = ref(0)
const loading = ref(false)

const rataRata = computed(() => {
  return pengeluarans.value.length > 0 ? totalNominal.value / pengeluarans.value.length : 0
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const formatDate = (date) => {
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const formatKategori = (kategori) => {
  const categories = {
    'gaji': 'Gaji',
    'operasional': 'Operasional',
    'maintenance': 'Maintenance',
    'utility': 'Utility',
    'lainnya': 'Lainnya'
  }
  return categories[kategori] || kategori
}

const getKategoriClass = (kategori) => {
  const classes = {
    'gaji': 'bg-rose-100 text-rose-800 dark:bg-rose-900/30 dark:text-rose-300',
    'operasional': 'bg-amber-100 text-amber-800 dark:bg-amber-900/30 dark:text-amber-300',
    'maintenance': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-300',
    'utility': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'lainnya': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
  }
  return classes[kategori] || 'bg-gray-100 text-gray-800'
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {}
    if (filterBulan.value) params.bulan = filterBulan.value
    if (filterKategori.value) params.kategori = filterKategori.value

    const res = await api.get('/admin/pengeluaran', { params })
    pengeluarans.value = res.data.data
    totalNominal.value = pengeluarans.value.reduce((sum, p) => sum + Number(p.nominal), 0)
  } catch (error) {
    console.error('Gagal mengambil data pengeluaran:', error)
  } finally {
    loading.value = false
  }
}

const resetFilter = () => {
  filterBulan.value = new Date().toISOString().slice(0, 7)
  filterKategori.value = ''
  loadData()
}

const deleteData = async (id) => {
  const result = await Swal.fire({
    title: 'Hapus Pengeluaran?',
    text: 'Data pengeluaran ini akan dihapus secara permanen.',
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
      await api.delete(`/admin/pengeluaran/${id}`)
      Swal.fire({
        icon: 'success',
        title: 'Berhasil Dihapus',
        text: 'Data pengeluaran telah dihapus dari sistem.',
        timer: 1500,
        showConfirmButton: false,
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]'
        }
      })
      loadData()
    } catch (error) {
      console.error('Gagal menghapus data pengeluaran:', error)
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menghapus',
        text: error.response?.data?.message || 'Terjadi kesalahan saat menghapus data.',
        confirmButtonColor: '#2d4a3e',
        customClass: {
          popup: 'rounded-[2rem] font-poppins',
          title: 'text-[16px] font-bold',
          htmlContainer: 'text-[12px]',
          confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
        }
      })
    }
  }
}

onMounted(() => {
  loadData()
})
</script>
