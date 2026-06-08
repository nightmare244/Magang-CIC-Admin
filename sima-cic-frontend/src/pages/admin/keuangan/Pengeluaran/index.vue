<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Daftar Pengeluaran</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data pengeluaran</p>
      </div>
      <router-link 
        to="/admin/pengeluaran/create" 
        class="px-4 py-2 bg-[#2d4a3e] text-white rounded-lg hover:bg-[#1f3329] transition"
      >
        + Tambah Pengeluaran
      </router-link>
    </div>

    <!-- Filter dan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pengeluaran</p>
        <p class="text-2xl font-bold text-red-600 mt-2">Rp {{ formatCurrency(totalNominal) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Rata-rata per Item</p>
        <p class="text-2xl font-bold text-orange-600 mt-2">Rp {{ formatCurrency(rataRata) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Catatan</p>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ pengeluarans.length }}</p>
      </div>
    </div>

    <!-- Filter -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input 
          v-model="filterBulan" 
          type="month" 
          class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
          @change="loadData"
        />
        <select 
          v-model="filterKategori" 
          class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
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
          class="px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600"
        >
          Cari
        </button>
        <button 
          @click="resetFilter" 
          class="px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200"
        >
          Reset
        </button>
      </div>
    </div>

    <!-- Tabel -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 dark:bg-gray-700">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nama</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Kategori</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="p in pengeluarans" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ p.nama_pengeluaran }}</td>
              <td class="px-6 py-4 text-sm">
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                  :class="getKategoriClass(p.kategori)"
                >{{ formatKategori(p.kategori) }}</span>
              </td>
              <td class="px-6 py-4 text-sm font-semibold text-red-600">Rp {{ formatCurrency(p.nominal) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(p.tanggal_pengeluaran) }}</td>
              <td class="px-6 py-4 text-sm">
                <router-link 
                  :to="`/admin/pengeluaran/${p.id}`"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  Lihat
                </router-link>
                <router-link 
                  :to="`/admin/pengeluaran/${p.id}/edit`"
                  class="text-yellow-600 hover:text-yellow-800 mr-3"
                >
                  Edit
                </router-link>
                <button 
                  @click="deleteData(p.id)"
                  class="text-red-600 hover:text-red-800"
                >
                  Hapus
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div v-if="pengeluarans.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
        Tidak ada data pengeluaran
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import api from '@/services/api'

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
    'gaji': 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
    'operasional': 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300',
    'maintenance': 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
    'utility': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'lainnya': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
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
  if (confirm('Yakin ingin menghapus data ini?')) {
    try {
      await api.delete(`/admin/pengeluaran/${id}`)
      loadData()
    } catch (error) {
      console.error('Gagal menghapus data pengeluaran:', error)
      alert(error.response?.data?.message || 'Gagal menghapus data')
    }
  }
}

onMounted(() => {
  loadData()
})
</script>
