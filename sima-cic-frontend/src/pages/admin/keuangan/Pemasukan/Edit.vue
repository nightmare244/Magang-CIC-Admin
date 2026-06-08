<template>
  <div class="space-y-6 p-6">
    <!-- Header -->
    <div>
      <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Edit Pemasukan</h1>
      <p class="text-gray-500 dark:text-gray-400 mt-1">Ubah data pemasukan</p>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 p-6">
      <form @submit.prevent="submitForm" class="space-y-6">
        
        <!-- Nama Pemasukan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Pemasukan</label>
          <input 
            v-model="form.nama_pemasukan"
            type="text"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            required
          />
        </div>

        <!-- Tipe -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Pemasukan</label>
          <select 
            v-model="form.tipe"
            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
            required
          >
            <option value="tiket_masuk">Tiket Masuk</option>
            <option value="donasi">Donasi</option>
            <option value="sponsor">Sponsor</option>
            <option value="lainnya">Lainnya</option>
          </select>
        </div>

        <!-- Grid 2 Kolom -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Jumlah -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah (unit)</label>
            <input 
              v-model.number="form.jumlah"
              type="number"
              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"
              min="1"
              required
            />
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
        </div>

        <!-- Tanggal Pemasukan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Pemasukan</label>
          <input 
            v-model="form.tanggal_pemasukan"
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
            to="/admin/pemasukan"
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
import api from '@/services/api'

const router = useRouter()
const route = useRoute()
const loading = ref(false)

const form = ref({
  nama_pemasukan: '',
  tipe: 'tiket_masuk',
  jumlah: 1,
  nominal: 0,
  tanggal_pemasukan: new Date().toISOString().split('T')[0],
  keterangan: ''
})

const loadData = async () => {
  try {
    const res = await api.get(`/admin/pemasukan/${route.params.id}`)
    form.value = res.data.data
  } catch (error) {
    console.error('Gagal mengambil data pemasukan:', error)
    alert(error.response?.data?.message || 'Gagal mengambil data')
    router.push('/admin/pemasukan')
  }
}

const submitForm = async () => {
  loading.value = true
  try {
    await api.put(`/admin/pemasukan/${route.params.id}`, form.value)
    alert('Pemasukan berhasil diupdate')
    router.push('/admin/pemasukan')
  } catch (error) {
    console.error('Gagal memperbarui pemasukan:', error)
    alert(error.response?.data?.message || 'Gagal memperbarui pemasukan')
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
