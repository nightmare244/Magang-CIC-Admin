<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Pengeluaran</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-1">Ubah data pengeluaran</p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <form @submit.prevent="submitForm" class="space-y-6">
        
        <!-- Nama Pengeluaran -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Pengeluaran</label>
          <input 
            v-model="form.nama_pengeluaran"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            required
          />
        </div>

        <!-- Kategori -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Kategori</label>
          <select 
            v-model="form.kategori"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            required
          >
            <option value="gaji">Gaji</option>
            <option value="operasional">Operasional</option>
            <option value="maintenance">Maintenance</option>
            <option value="utility">Utility</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>

        <!-- Nominal -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nominal (Rp)</label>
          <input 
            v-model.number="form.nominal"
            type="number"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            min="0"
            step="0.01"
            required
          />
        </div>

        <!-- Tanggal Pengeluaran -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Pengeluaran</label>
          <input 
            v-model="form.tanggal_pengeluaran"
            type="date"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            required
          />
        </div>

        <!-- Keterangan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan</label>
          <textarea 
            v-model="form.keterangan"
            rows="4"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
          ></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4">
          <button 
            type="submit"
            class="px-6 py-2 bg-[#2d4a3e] text-white rounded-lg hover:bg-[#1f3329] transition"
          >
            Simpan Perubahan
          </button>
          <router-link 
            to="/admin/pengeluaran"
            class="px-6 py-2 bg-gray-200 dark:bg-gray-700 text-gray-900 dark:text-white rounded-lg hover:bg-gray-300"
          >
            Batal
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

const router = useRouter()
const route = useRoute()

const form = ref({
  nama_pengeluaran: '',
  kategori: 'operasional',
  nominal: 0,
  tanggal_pengeluaran: new Date().toISOString().split('T')[0],
  keterangan: ''
})

const loadData = () => {
  // Mock load data berdasarkan ID
  const mockData = {
    id: route.params.id,
    nama_pengeluaran: 'Gaji Karyawan',
    kategori: 'gaji',
    nominal: 50000000,
    tanggal_pengeluaran: '2024-04-30',
    keterangan: 'Gaji bulanan April'
  }
  
  form.value = { ...mockData }
}

const submitForm = async () => {
  console.log('Form updated:', form.value)
  alert('Pengeluaran berhasil diupdate')
  router.push('/admin/pengeluaran')
}

onMounted(() => {
  loadData()
})
</script>
