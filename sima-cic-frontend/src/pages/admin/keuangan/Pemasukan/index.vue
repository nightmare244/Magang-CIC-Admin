<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Daftar Pemasukan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data pemasukan bulanan</p>
      </div>
      <router-link 
        to="/admin/pemasukan/create" 
        class="px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm"
      >
        + Tambah Pemasukan
      </router-link>
    </div>

    <!-- Filter dan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Pemasukan</p>
        <p class="text-2xl font-bold text-emerald-600 mt-2">Rp {{ formatCurrency(totalNominal) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Tiket</p>
        <p class="text-2xl font-bold text-emerald-600 mt-2">{{ totalTiket }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-5 rounded-2xl border border-gray-200 dark:border-gray-700 shadow-sm">
        <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Catatan</p>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ pemasukans.length }}</p>
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
          v-model="filterTipe" 
          class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @change="loadData"
        >
          <option value="">Semua Tipe</option>
          <option value="tiket_masuk">Tiket Masuk</option>
          <option value="donasi">Donasi</option>
          <option value="sponsor">Sponsor</option>
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
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tipe</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Jumlah</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Tanggal</th>
              <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="p in pemasukans" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition">
              <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">{{ p.nama_pemasukan }}</td>
              <td class="px-6 py-4 text-sm">
                <span class="px-3 py-1 rounded-full text-xs font-semibold"
                  :class="getTipeClass(p.tipe)"
                >{{ formatTipe(p.tipe) }}</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ p.jumlah }}</td>
              <td class="px-6 py-4 text-sm font-bold text-emerald-600">Rp {{ formatCurrency(p.nominal) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(p.tanggal_pemasukan) }}</td>
              <td class="px-6 py-4 text-sm">
                <router-link 
                  :to="`/admin/pemasukan/${p.id}`"
                  class="text-blue-600 hover:text-blue-800 font-medium mr-3 transition"
                >
                  Lihat
                </router-link>
                <router-link 
                  :to="`/admin/pemasukan/${p.id}/edit`"
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
      <div v-if="pemasukans.length === 0" class="text-center py-12 text-gray-500 dark:text-gray-400 font-medium">
        Tidak ada data pemasukan
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'

const pemasukans = ref([])
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const filterTipe = ref('')
const totalNominal = ref(0)
const totalTiket = ref(0)
const loading = ref(false)

const formatCurrency = (value) => {
  return new Intl.NumberFormat('id-ID').format(value)
}

const formatDate = (date) => {
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
    'tiket_masuk': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/30 dark:text-emerald-300',
    'donasi': 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300',
    'sponsor': 'bg-purple-100 text-purple-800 dark:bg-purple-900/30 dark:text-purple-300',
    'lainnya': 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-300'
  }
  return classes[tipe] || 'bg-gray-100 text-gray-800'
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {}
    if (filterBulan.value) params.bulan = filterBulan.value
    if (filterTipe.value) params.tipe = filterTipe.value

    const res = await api.get('/admin/pemasukan', { params })
    pemasukans.value = res.data.data
    
    totalNominal.value = pemasukans.value.reduce((sum, p) => sum + Number(p.nominal), 0)
    totalTiket.value = pemasukans.value.filter(p => p.tipe === 'tiket_masuk').reduce((sum, p) => sum + Number(p.jumlah), 0)
  } catch (error) {
    console.error('Gagal mengambil data pemasukan:', error)
  } finally {
    loading.value = false
  }
}

const resetFilter = () => {
  filterBulan.value = new Date().toISOString().slice(0, 7)
  filterTipe.value = ''
  loadData()
}

const deleteData = async (id) => {
  const result = await Swal.fire({
    title: 'Hapus Pemasukan?',
    text: 'Data pemasukan ini akan dihapus secara permanen.',
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
      await api.delete(`/admin/pemasukan/${id}`)
      Swal.fire({
        icon: 'success',
        title: 'Berhasil Dihapus',
        text: 'Data pemasukan telah dihapus dari sistem.',
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
      console.error('Gagal menghapus data pemasukan:', error)
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
