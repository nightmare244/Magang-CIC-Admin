<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Tambah Pemasukan</h1>
        <p class="text-gray-500 dark:text-gray-400 mt-1">Tambahkan data pemasukan baru</p>
      </div>

      <div class="flex flex-col sm:flex-row gap-2 items-end">
        <Transition name="slide-fade">
          <div v-if="errorMessage" class="flex items-center gap-3 px-6 py-3 bg-rose-50 dark:bg-rose-900/20 border border-rose-100 dark:border-rose-800 rounded-xl shadow-sm">
            <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-rose-600 dark:text-rose-400 uppercase tracking-widest">{{ errorMessage }}</span>
          </div>
        </Transition>
        <Transition name="slide-fade">
          <div v-if="successMessage" class="flex items-center gap-3 px-6 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800 rounded-xl shadow-sm">
            <div class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">{{ successMessage }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <!-- Form -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
      <form @submit.prevent="submitForm" class="space-y-6">
        
        <!-- Nama Pemasukan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nama Pemasukan <span class="text-rose-500">*</span></label>
          <input 
            v-model="form.nama_pemasukan"
            type="text"
            placeholder="Contoh: Tiket Masuk April"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          />
        </div>

        <!-- Tipe -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tipe Pemasukan <span class="text-rose-500">*</span></label>
          <select 
            v-model="form.tipe"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          >
            <option value="">Pilih Tipe</option>
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
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Jumlah (unit) <span class="text-rose-500">*</span></label>
            <input 
              v-model.number="form.jumlah"
              type="number"
              placeholder="0"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              min="1"
              required
            />
          </div>

          <!-- Nominal -->
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Nominal (Rp) <span class="text-rose-500">*</span></label>
            <input 
              v-model.number="form.nominal"
              type="number"
              placeholder="0"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              min="0"
              step="0.01"
              required
            />
          </div>
        </div>

        <!-- Tanggal Pemasukan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tanggal Pemasukan <span class="text-rose-500">*</span></label>
          <input 
            v-model="form.tanggal_pemasukan"
            type="date"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          />
        </div>

        <!-- Keterangan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan</label>
          <textarea 
            v-model="form.keterangan"
            placeholder="Tambahkan keterangan..."
            rows="4"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
          ></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex gap-4 pt-4">
          <button 
            type="submit"
            :disabled="loading"
            class="px-6 py-3 bg-[#2d4a3e] text-white rounded-xl font-medium text-sm hover:bg-[#1f3329] transition disabled:opacity-50 flex items-center justify-center min-w-[170px]"
          >
            <span v-if="loading" class="animate-pulse">Menyimpan...</span>
            <span v-else>Simpan Pemasukan</span>
          </button>
          <router-link 
            to="/admin/pemasukan"
            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-medium text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition"
          >
            Batal
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'

const router = useRouter()
const loading = ref(false)
const successMessage = ref(null)
const errorMessage = ref(null)

const form = ref({
  nama_pemasukan: '',
  tipe: 'tiket_masuk',
  jumlah: 1,
  nominal: 0,
  tanggal_pemasukan: new Date().toISOString().split('T')[0],
  keterangan: ''
})

const submitForm = async () => {
  loading.value = true
  successMessage.value = null
  errorMessage.value = null

  try {
    const res = await api.post('/admin/pemasukan', form.value)
    const msg = res.data?.message || 'Data pemasukan berhasil ditambahkan.'
    successMessage.value = 'PEMASUKAN BERHASIL DITAMBAHKAN'

    Swal.fire({
      icon: 'success',
      title: 'Berhasil Disimpan',
      text: msg,
      timer: 2000,
      showConfirmButton: false,
      customClass: {
        popup: 'rounded-[2rem] font-poppins',
        title: 'text-[16px] font-bold',
        htmlContainer: 'text-[12px]'
      }
    })

    setTimeout(() => {
      router.push('/admin/pemasukan')
    }, 1500)
  } catch (error) {
    console.error('Gagal menambahkan pemasukan:', error)
    const errMsg = error.response?.data?.message || 'Gagal menambahkan data pemasukan.'
    errorMessage.value = 'GAGAL MENAMBAHKAN PEMASUKAN'
    setTimeout(() => (errorMessage.value = null), 4000)

    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: errMsg,
      confirmButtonColor: '#2d4a3e',
      customClass: {
        popup: 'rounded-[2rem] font-poppins',
        title: 'text-[16px] font-bold',
        htmlContainer: 'text-[12px]',
        confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
      }
    })
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>
