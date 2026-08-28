<template>
  <div class="space-y-6 p-6 font-poppins max-w-5xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div>
        <div class="flex items-center gap-2">
          <router-link 
            to="/admin/keuangan/transaksi" 
            class="p-2 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition"
          >
            <ArrowLeft class="w-5 h-5" />
          </router-link>
          <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Edit Transaksi Keuangan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Perbarui rincian transaksi, akun CoA, atau nominal</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Status Loading -->
    <div v-if="fetching" class="text-center py-16">
      <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
      <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Memuat data transaksi...</p>
    </div>

    <!-- Form Edit -->
    <div v-else class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 shadow-sm">
      <!-- Jenis Badge Indicator -->
      <div class="flex items-center justify-between p-3.5 rounded-xl border mb-6"
        :class="form.jenis === 'pemasukan' 
          ? 'bg-emerald-50/50 dark:bg-emerald-950/20 border-emerald-200 dark:border-emerald-800' 
          : 'bg-rose-50/50 dark:bg-rose-950/20 border-rose-200 dark:border-rose-800'"
      >
        <div class="flex items-center gap-3">
          <span 
            class="px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
            :class="form.jenis === 'pemasukan' ? 'bg-emerald-600 text-white' : 'bg-rose-600 text-white'"
          >
            {{ form.jenis === 'pemasukan' ? 'Pemasukan (Kas Masuk)' : 'Pengeluaran (Kas Keluar)' }}
          </span>
          <span class="font-mono text-xs text-gray-500 dark:text-gray-400">
            No. Ref: {{ transactionCode }}
          </span>
        </div>
      </div>

      <form @submit.prevent="updateForm" class="space-y-6">
        <!-- 1. Pilihan Daftar Akun (CoA) - WAJIB -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-750 border border-slate-200 dark:border-gray-700 space-y-2">
          <label class="flex items-center justify-between text-sm font-bold text-gray-900 dark:text-white">
            <span class="flex items-center gap-1.5">
              <FolderTree class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Daftar Akun / Chart of Accounts (CoA) <span class="text-rose-500">*</span>
            </span>
          </label>
          <select 
            v-model="form.akun_id"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          >
            <option value="" disabled>-- Pilih Akun Terkait --</option>
            <option v-for="a in availableAccounts" :key="a.id" :value="a.id">
              [{{ a.kode_akun }}] {{ a.nama_akun }} ({{ a.kategori.toUpperCase() }})
            </option>
          </select>
        </div>

        <!-- 2. Nama Transaksi -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Nama Transaksi / Deskripsi <span class="text-rose-500">*</span>
          </label>
          <input 
            v-model="form.nama_transaksi"
            type="text"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          />
        </div>

        <!-- 3. Grid 2 Kolom: Tanggal & Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Tanggal Transaksi <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model="form.tanggal"
              type="date"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              required
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Klasifikasi / Tipe <span class="text-rose-500">*</span>
            </label>
            <select 
              v-if="form.jenis === 'pemasukan'"
              v-model="form.tipe_kategori"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              required
            >
              <option value="tiket_masuk">Tiket Masuk</option>
              <option value="donasi">Donasi</option>
              <option value="sponsor">Sponsor</option>
              <option value="lainnya">Lainnya</option>
            </select>
            <select 
              v-else
              v-model="form.tipe_kategori"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              required
            >
              <option value="gaji">Gaji</option>
              <option value="operasional">Operasional</option>
              <option value="maintenance">Maintenance / Pemeliharaan</option>
              <option value="utility">Utility (Listrik, Air & Internet)</option>
              <option value="lainnya">Lainnya</option>
            </select>
          </div>
        </div>

        <!-- 4. Grid 2 Kolom: Jumlah & Nominal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div v-if="form.jenis === 'pemasukan' && form.tipe_kategori === 'tiket_masuk'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Jumlah Tiket (Unit/Orang) <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model.number="form.jumlah"
              type="number"
              min="1"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              required
            />
          </div>

          <div :class="{ 'md:col-span-2': !(form.jenis === 'pemasukan' && form.tipe_kategori === 'tiket_masuk') }">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Nominal Transaksi (Rp) <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-4 top-3 font-bold text-sm text-gray-400">Rp</span>
              <input 
                v-model.number="form.nominal"
                type="number"
                min="0"
                step="any"
                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-bold text-lg focus:ring-2 focus:ring-emerald-500 outline-none transition"
                required
              />
            </div>
            <p v-if="form.nominal" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
              Terbaca: Rp {{ formatCurrency(form.nominal) }}
            </p>
          </div>
        </div>

        <!-- 5. Keterangan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan / Catatan Tambahan</label>
          <textarea 
            v-model="form.keterangan"
            rows="3"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm"
          ></textarea>
        </div>

        <!-- Buttons -->
        <div class="flex flex-wrap gap-3 pt-3">
          <button 
            type="submit"
            :disabled="saving"
            class="px-6 py-3 bg-[#2d4a3e] text-white rounded-xl font-bold text-sm hover:bg-[#1f3329] transition disabled:opacity-50 flex items-center justify-center min-w-[200px] shadow-sm hover:shadow-md"
          >
            <span v-if="saving" class="animate-pulse">Menyimpan Perubahan...</span>
            <span v-else>💾 Simpan Perubahan</span>
          </button>
          <router-link 
            to="/admin/keuangan/transaksi"
            class="px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-xl font-semibold text-sm hover:bg-gray-200 dark:hover:bg-gray-600 transition"
          >
            Batal
          </router-link>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { ArrowLeft, FolderTree } from 'lucide-vue-next'

const router = useRouter()
const route = useRoute()

const fetching = ref(true)
const saving = ref(false)
const akuns = ref([])
const transactionCode = ref('')

const form = ref({
  jenis: 'pemasukan',
  akun_id: '',
  nama_transaksi: '',
  tipe_kategori: '',
  jumlah: 1,
  nominal: 0,
  tanggal: '',
  keterangan: '',
})

const availableAccounts = computed(() => {
  if (form.value.jenis === 'pemasukan') {
    return akuns.value.filter(a => a.kategori === 'pendapatan' || a.kategori === 'aset')
  } else {
    return akuns.value.filter(a => a.kategori === 'beban' || a.kategori === 'kewajiban')
  }
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const loadData = async () => {
  fetching.value = true
  try {
    const resAkuns = await api.get('/admin/akuns')
    akuns.value = resAkuns.data.data

    const id = route.params.id
    const res = await api.get(`/admin/keuangan/transaksi/${id}`)
    const item = res.data.data

    transactionCode.value = item.kode_transaksi
    form.value = {
      jenis: item.jenis,
      akun_id: item.akun_id || '',
      nama_transaksi: item.nama_transaksi,
      tipe_kategori: item.tipe_kategori,
      jumlah: item.jumlah || 1,
      nominal: item.nominal,
      tanggal: item.tanggal,
      keterangan: item.keterangan || '',
    }
  } catch (err) {
    console.error('Gagal mengambil detail transaksi:', err)
    Swal.fire({
      icon: 'error',
      title: 'Data Tidak Ditemukan',
      text: 'Gagal memuat data transaksi keuangan yang diminta.',
      confirmButtonColor: '#2d4a3e',
    }).then(() => {
      router.push('/admin/keuangan/transaksi')
    })
  } finally {
    fetching.value = false
  }
}

const updateForm = async () => {
  saving.value = true
  try {
    const id = route.params.id
    const payload = {
      akun_id: form.value.akun_id,
      nama_transaksi: form.value.nama_transaksi,
      tipe_kategori: form.value.tipe_kategori,
      nominal: Number(form.value.nominal),
      tanggal: form.value.tanggal,
      keterangan: form.value.keterangan,
      jumlah: form.value.jumlah ? Number(form.value.jumlah) : 1,
    }

    await api.put(`/admin/keuangan/transaksi/${id}`, payload)

    Swal.fire({
      icon: 'success',
      title: 'Berhasil Diperbarui',
      text: 'Transaksi telah diperbarui dan otomatis menyelaraskan seluruh laporan keuangan.',
      timer: 1800,
      showConfirmButton: false,
      customClass: {
        popup: 'rounded-[2rem] font-poppins',
        title: 'text-[16px] font-bold',
        htmlContainer: 'text-[12px]'
      }
    })

    setTimeout(() => {
      router.push('/admin/keuangan/transaksi')
    }, 1400)
  } catch (err) {
    console.error('Gagal memperbarui transaksi:', err)
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memperbarui',
      text: err.response?.data?.message || 'Terjadi kesalahan saat menyimpan perubahan.',
      confirmButtonColor: '#2d4a3e',
    })
  } finally {
    saving.value = false
  }
}

onMounted(() => {
  loadData()
})
</script>
