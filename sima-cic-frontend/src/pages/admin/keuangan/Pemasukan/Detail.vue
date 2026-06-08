<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Pemasukan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap pemasukan</p>
      </div>
      <div class="flex gap-3">
        <router-link 
          :to="`/admin/pemasukan/${$route.params.id}/edit`"
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
          to="/admin/pemasukan"
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
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nama Pemasukan</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ pemasukan.nama_pemasukan }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tipe Pemasukan</p>
            <span class="px-3 py-1 rounded-full text-sm font-medium"
              :class="getTipeClass(pemasukan.tipe)"
            >{{ formatTipe(pemasukan.tipe) }}</span>
          </div>

          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Jumlah</p>
            <p class="text-xl font-bold text-gray-900 dark:text-white">{{ pemasukan.jumlah }} unit</p>
          </div>
        </div>

        <!-- Kolom Kanan -->
        <div class="space-y-6">
          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Nominal</p>
            <p class="text-2xl font-bold text-green-600">Rp {{ formatCurrency(pemasukan.nominal) }}</p>
          </div>

          <div>
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-1">Tanggal Pemasukan</p>
            <p class="text-lg font-medium text-gray-900 dark:text-white">{{ formatDate(pemasukan.tanggal_pemasukan) }}</p>
          </div>
        </div>
      </div>

      <!-- Keterangan -->
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Keterangan</p>
        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-wrap">{{ pemasukan.keterangan || 'Tidak ada keterangan' }}</p>
      </div>

      <!-- Timestamps -->
      <div class="mt-8 pt-8 border-t border-gray-200 dark:border-gray-700 grid grid-cols-2 gap-4 text-sm">
        <div>
          <p class="text-gray-500 dark:text-gray-400">Dibuat pada</p>
          <p class="text-gray-900 dark:text-white font-medium">{{ formatDate(pemasukan.created_at) }}</p>
        </div>
        <div>
          <p class="text-gray-500 dark:text-gray-400">Diubah pada</p>
          <p class="text-gray-900 dark:text-white font-medium">{{ formatDate(pemasukan.updated_at) }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'

const router = useRouter()
const route = useRoute()

const pemasukan = ref({
  id: '',
  nama_pemasukan: '',
  tipe: '',
  jumlah: 0,
  nominal: 0,
  tanggal_pemasukan: '',
  keterangan: '',
  created_at: '',
  updated_at: ''
})

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value || 0)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const formatTipe = (tipe) => {
  const types = {
    'tiket_masuk': 'Tiket Masuk',
    'donasi': 'Donasi',
    'sponsor': 'Sponsor',
    'lainnya': 'Lainnya'
  }
  return types[tipe] || tipe
}

const getTipeClass = (tipe) => {
  const classes = {
    'tiket_masuk': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'donasi': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-green-300',
    'sponsor': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    'lainnya': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
  return classes[tipe] || 'bg-gray-100 text-gray-800'
}

const deleteData = async () => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    try {
      await api.delete(`/admin/pemasukan/${route.params.id}`)
      alert('Data berhasil dihapus')
      router.push('/admin/pemasukan')
    } catch (error) {
      console.error('Gagal menghapus data pemasukan:', error)
      alert(error.response?.data?.message || 'Gagal menghapus data')
    }
  }
}

const loadData = async () => {
  try {
    const res = await api.get(`/admin/pemasukan/${route.params.id}`)
    pemasukan.value = res.data.data
  } catch (error) {
    console.error('Gagal mengambil detail pemasukan:', error)
    alert(error.response?.data?.message || 'Gagal mengambil detail data')
    router.push('/admin/pemasukan')
  }
}

onMounted(() => {
  loadData()
})
</script>
