<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Detail Pengeluaran</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Informasi lengkap pengeluaran</p>
      </div>
      <div class="flex gap-3">
        <router-link 
          :to="`/admin/pengeluaran/${$route.params.id}/edit`"
          class="px-4 py-2 bg-yellow-600 text-white rounded-xl hover:bg-yellow-700 transition text-sm font-medium"
        >
          Edit
        </router-link>
        <button 
          @click="deleteData"
          class="px-4 py-2 bg-red-600 text-white rounded-xl hover:bg-red-700 transition text-sm font-medium"
        >
          Hapus
        </button>
        <router-link 
          to="/admin/pengeluaran"
          class="px-4 py-2 bg-gray-600 text-white rounded-xl hover:bg-gray-700 transition text-sm font-medium"
        >
          Kembali
        </router-link>
      </div>
    </div>

    <!-- Detail Card -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-8 shadow-sm">
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
            <p class="text-2xl font-bold text-rose-600">Rp {{ formatCurrency(pengeluaran.nominal) }}</p>
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
import api from '@/services/api'
import Swal from 'sweetalert2'

const router = useRouter()
const route = useRoute()

const pengeluaran = ref({
  id: '',
  nama_pengeluaran: '',
  kategori: '',
  nominal: 0,
  tanggal_pengeluaran: '',
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

const deleteData = async () => {
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
      await api.delete(`/admin/pengeluaran/${route.params.id}`)
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
      router.push('/admin/pengeluaran')
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

const loadData = async () => {
  try {
    const res = await api.get(`/admin/pengeluaran/${route.params.id}`)
    pengeluaran.value = res.data.data
  } catch (error) {
    console.error('Gagal mengambil detail pengeluaran:', error)
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memuat Detail',
      text: error.response?.data?.message || 'Gagal mengambil detail data.',
      confirmButtonColor: '#2d4a3e',
      customClass: {
        popup: 'rounded-[2rem] font-poppins',
        confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
      }
    })
    router.push('/admin/pengeluaran')
  }
}

onMounted(() => {
  loadData()
})
</script>
