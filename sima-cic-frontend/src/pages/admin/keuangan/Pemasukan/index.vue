<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Daftar Pemasukan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Kelola data pemasukan bulanan</p>
      </div>
      <router-link 
        to="/admin/pemasukan/create" 
        class="px-4 py-2 bg-[#2d4a3e] text-white rounded-lg hover:bg-[#1f3329] transition"
      >
        + Tambah Pemasukan
      </router-link>
    </div>

    <!-- Filter dan Statistik -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Pemasukan</p>
        <p class="text-2xl font-bold text-gray-900 dark:text-white mt-2">Rp {{ formatCurrency(totalNominal) }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Tiket</p>
        <p class="text-2xl font-bold text-green-600 mt-2">{{ totalTiket }}</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <p class="text-sm text-gray-500 dark:text-gray-400">Total Catatan</p>
        <p class="text-2xl font-bold text-blue-600 mt-2">{{ pemasukans.length }}</p>
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
          v-model="filterTipe" 
          class="px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
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
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tipe</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Jumlah</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Nominal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Tanggal</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
            <tr v-for="p in pemasukans" :key="p.id" class="hover:bg-gray-50 dark:hover:bg-gray-700">
              <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">{{ p.nama_pemasukan }}</td>
              <td class="px-6 py-4 text-sm">
                <span class="px-2 py-1 rounded-full text-xs font-medium"
                  :class="getTipeClass(p.tipe)"
                >{{ formatTipe(p.tipe) }}</span>
              </td>
              <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">{{ p.jumlah }}</td>
              <td class="px-6 py-4 text-sm font-semibold text-gray-900 dark:text-white">Rp {{ formatCurrency(p.nominal) }}</td>
              <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">{{ formatDate(p.tanggal_pemasukan) }}</td>
              <td class="px-6 py-4 text-sm">
                <router-link 
                  :to="`/admin/pemasukan/${p.id}`"
                  class="text-blue-600 hover:text-blue-800 mr-3"
                >
                  Lihat
                </router-link>
                <router-link 
                  :to="`/admin/pemasukan/${p.id}/edit`"
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
      <div v-if="pemasukans.length === 0" class="text-center py-8 text-gray-500 dark:text-gray-400">
        Tidak ada data pemasukan
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'

const pemasukans = ref([])
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const filterTipe = ref('')
const totalNominal = ref(0)
const totalTiket = ref(0)

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
    'tiket_masuk': 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
    'donasi': 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
    'sponsor': 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
    'lainnya': 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-300'
  }
  return classes[tipe] || 'bg-gray-100 text-gray-800'
}

const loadData = () => {
  // Mock data - ganti dengan API call nanti
  const mockData = [
    {
      id: 1,
      nama_pemasukan: 'Tiket Masuk April',
      tipe: 'tiket_masuk',
      jumlah: 150,
      nominal: 7500000,
      tanggal_pemasukan: '2024-04-30',
      keterangan: 'Penjualan tiket bulan April'
    },
    {
      id: 2,
      nama_pemasukan: 'Donasi Sosial',
      tipe: 'donasi',
      jumlah: 1,
      nominal: 5000000,
      tanggal_pemasukan: '2024-04-28',
      keterangan: 'Donasi dari mitra'
    },
    {
      id: 3,
      nama_pemasukan: 'Sponsor Acara',
      tipe: 'sponsor',
      jumlah: 1,
      nominal: 10000000,
      tanggal_pemasukan: '2024-04-25',
      keterangan: 'Sponsor event tahunan'
    }
  ]
  
  pemasukans.value = mockData
  totalNominal.value = mockData.reduce((sum, p) => sum + p.nominal, 0)
  totalTiket.value = mockData.filter(p => p.tipe === 'tiket_masuk').reduce((sum, p) => sum + p.jumlah, 0)
}

const resetFilter = () => {
  filterBulan.value = new Date().toISOString().slice(0, 7)
  filterTipe.value = ''
  loadData()
}

const deleteData = (id) => {
  if (confirm('Yakin ingin menghapus data ini?')) {
    pemasukans.value = pemasukans.value.filter(p => p.id !== id)
  }
}

onMounted(() => {
  loadData()
})
</script>
