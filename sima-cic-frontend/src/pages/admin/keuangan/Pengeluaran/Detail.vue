<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Pengeluaran</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap pengeluaran</p>
      </div>
      <div class="flex gap-3">
        <router-link 
          :to="`/admin/pengeluaran/${$route.params.id}/edit`"
          class="px-4 py-2 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700"
        >
          Edit
        </router-link>
        <button 
          @click="deleteData"
          class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700"
        >
          Hapus
        </button>
        <router-link 
          to="/admin/pengeluaran"
          class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700"
        >
          Kembali
        </router-link>
      </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-8">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
        <!-- Kolom Kiri -->
        <div class="space-y-6">
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nama Pengeluaran</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ pengeluaran.nama_pengeluaran }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Kategori</p>
            <span class="px-3 py-1 rounded-full text-sm font-medium"
              :class="getKategoriClass(pengeluaran.kategori)"
            >{{ formatKategori(pengeluaran.kategori) }}</span>
          </div>

          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tanggal Pengeluaran</p>
            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ formatDate(pengeluaran.tanggal_pengeluaran) }}</p>
          </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-6">
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nominal</p>
            <p class="text-2xl font-bold text-red-600">Rp {{ formatCurrency(pengeluaran.nominal) }}</p>
          </div>
        </div>
      </div>

      <!-- Keterangan -->
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Keterangan</p>
        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ pengeluaran.keterangan || 'Tidak ada keterangan' }}</p>
      </div>

      <!-- Timestamps -->
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4 text-sm">
        <div>
          <p class="text-gray-500 dark:text-gray-400">Dibuat pada</p>
          <p class="text-gray-900 dark:text-white font-medium">{{ formatDate(pengeluaran.created_at) }}</p>
        </div>
        <div>
          <p class="text-gray-500 dark:text-gray-400">Diubah pada</p>
          <p class="text-gray-900 dark:text-white font-medium">{{ formatDate(pengeluaran.updated_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const pengeluaran = ref({
  id: 1,
  nama_pengeluaran: '',
  kategori: '',
  nominal: 0,
  tanggal_pengeluaran: '',
  keterangan: '',
  created_at: '',
  updated_at: ''
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

const deleteData = () => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    alert('Data berhasil dihapus')
    router.push('/admin/pengeluaran')
  }
}

const loadData = () => {
  // Mock load data berdasarkan ID
  const mockData = {
    id: route.params.id,
    nama_pengeluaran: 'Gaji Karyawan',
    kategori: 'gaji',
    nominal: 50000000,
    tanggal_pengeluaran: '2024-04-30',
    keterangan: 'Gaji bulanan April untuk seluruh karyawan tetap',
    created_at: '2024-04-30T10:00:00',
    updated_at: '2024-04-30T10:00:00'
  }
  
  pengeluaran.value = { ...mockData }
}

onMounted(() => {
  loadData()
})
</script>
