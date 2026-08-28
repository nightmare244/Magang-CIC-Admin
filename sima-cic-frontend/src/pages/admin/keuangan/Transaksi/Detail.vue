<template>
  <div class="space-y-6 p-6 font-poppins max-w-4xl mx-auto">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <router-link 
          to="/admin/keuangan/transaksi" 
          class="p-2.5 rounded-xl bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-300 hover:bg-gray-200 transition"
        >
          <ArrowLeft class="w-5 h-5" />
        </router-link>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Detail Transaksi</h1>
          <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Informasi lengkap transaksi dan pembukuan akun</p>
        </div>
      </div>

      <div v-if="item" class="flex items-center gap-2">
        <router-link 
          :to="`/admin/keuangan/transaksi/${item.id}/edit`" 
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-amber-600 text-white rounded-xl hover:bg-amber-700 transition font-medium text-xs shadow-sm"
        >
          <Edit2 class="w-3.5 h-3.5" />
          <span>Edit</span>
        </router-link>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="text-center py-20">
      <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
      <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Memuat rincian transaksi...</p>
    </div>

    <!-- Detail Card -->
    <div v-else-if="item" class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm">
      <!-- Status Top Bar -->
      <div 
        class="p-6 border-b flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4"
        :class="item.jenis === 'pemasukan' 
          ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-100 dark:border-emerald-900/30' 
          : 'bg-rose-50/60 dark:bg-rose-950/20 border-rose-100 dark:border-rose-900/30'"
      >
        <div class="flex items-center gap-3.5">
          <div 
            class="w-12 h-12 rounded-2xl flex items-center justify-center font-black shadow-inner"
            :class="item.jenis === 'pemasukan' 
              ? 'bg-emerald-600 text-white shadow-emerald-700/30' 
              : 'bg-rose-600 text-white shadow-rose-700/30'"
          >
            <ArrowDownLeft v-if="item.jenis === 'pemasukan'" class="w-6 h-6" />
            <ArrowUpRight v-else class="w-6 h-6" />
          </div>
          <div>
            <span 
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-widest"
              :class="item.jenis === 'pemasukan' 
                ? 'bg-emerald-200/80 text-emerald-900 dark:bg-emerald-800/60 dark:text-emerald-200' 
                : 'bg-rose-200/80 text-rose-900 dark:bg-rose-800/60 dark:text-rose-200'"
            >
              {{ item.jenis === 'pemasukan' ? 'Pemasukan Kas' : 'Pengeluaran Kas' }}
            </span>
            <h2 class="text-xl font-black text-gray-900 dark:text-white mt-1">{{ item.nama_transaksi }}</h2>
          </div>
        </div>

        <div class="text-left sm:text-right">
          <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">Nominal Transaksi</p>
          <p 
            class="text-2xl sm:text-3xl font-black mt-1"
            :class="item.jenis === 'pemasukan' ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400'"
          >
            Rp {{ formatCurrency(item.nominal) }}
          </p>
        </div>
      </div>

      <!-- Information Grid -->
      <div class="p-6 grid grid-cols-1 sm:grid-cols-2 gap-6">
        <!-- Kode / No Ref -->
        <div>
          <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Nomor Referensi Transaksi</p>
          <p class="text-sm font-mono font-bold text-gray-800 dark:text-gray-200 mt-1">{{ item.kode_transaksi }}</p>
        </div>

        <!-- Tanggal -->
        <div>
          <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Tanggal Transaksi</p>
          <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 mt-1">{{ formatDate(item.tanggal) }}</p>
        </div>

        <!-- Akun CoA -->
        <div>
          <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Daftar Akun Terkait (CoA)</p>
          <div v-if="item.akun" class="flex items-center gap-2 mt-1">
            <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 font-mono text-xs font-bold">
              {{ item.akun.kode_akun }}
            </span>
            <span class="text-sm font-semibold text-gray-800 dark:text-gray-200">{{ item.akun.nama_akun }}</span>
          </div>
          <p v-else class="text-sm text-gray-400 italic mt-1">Belum terhubung akun</p>
        </div>

        <!-- Tipe / Klasifikasi -->
        <div>
          <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Klasifikasi Kategori</p>
          <p class="text-sm font-semibold text-gray-800 dark:text-gray-200 capitalize mt-1">
            {{ formatKategori(item.tipe_kategori) }}
            <span v-if="item.jenis === 'pemasukan' && item.jumlah > 1" class="text-xs text-emerald-600 font-normal">
              ({{ item.jumlah }} Unit)
            </span>
          </p>
        </div>

        <!-- Keterangan -->
        <div class="sm:col-span-2">
          <p class="text-xs font-bold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Catatan / Keterangan</p>
          <p class="text-sm text-gray-700 dark:text-gray-300 mt-1 bg-gray-50 dark:bg-gray-700/30 p-3.5 rounded-xl border border-gray-200 dark:border-gray-700/60">
            {{ item.keterangan || 'Tidak ada catatan tambahan.' }}
          </p>
        </div>

        <!-- Pratinjau Jurnal Kas -->
        <div class="sm:col-span-2 p-4 rounded-xl bg-slate-50 dark:bg-gray-700/40 border border-slate-200 dark:border-gray-700 space-y-2">
          <p class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider flex items-center gap-1.5">
            <BookOpenCheck class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            Entri Pada Jurnal Kas
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs font-mono">
            <div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
              <span class="font-bold text-emerald-600 dark:text-emerald-400">[DEBIT]</span>
              <p class="font-bold text-gray-800 dark:text-gray-200 mt-1">
                {{ item.jenis === 'pemasukan' ? '1-10001 Kas Utama (Tunai)' : (item.akun ? item.akun.kode_akun + ' ' + item.akun.nama_akun : 'Akun Beban') }}
              </p>
              <p class="text-emerald-600 font-extrabold mt-0.5">Rp {{ formatCurrency(item.nominal) }}</p>
            </div>
            <div class="p-3 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
              <span class="font-bold text-rose-600 dark:text-rose-400">[KREDIT]</span>
              <p class="font-bold text-gray-800 dark:text-gray-200 mt-1">
                {{ item.jenis === 'pemasukan' ? (item.akun ? item.akun.kode_akun + ' ' + item.akun.nama_akun : 'Akun Pendapatan') : '1-10001 Kas Utama (Tunai)' }}
              </p>
              <p class="text-rose-600 font-extrabold mt-0.5">Rp {{ formatCurrency(item.nominal) }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { ArrowLeft, ArrowDownLeft, ArrowUpRight, Edit2, BookOpenCheck } from 'lucide-vue-next'

const route = useRoute()
const router = useRouter()
const item = ref(null)
const loading = ref(true)

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const formatDate = (date) => {
  if (!date) return '-'
  return new Date(date).toLocaleDateString('id-ID', { year: 'numeric', month: 'long', day: 'numeric' })
}

const formatKategori = (val) => {
  const map = {
    'tiket_masuk': 'Tiket Masuk',
    'donasi': 'Donasi',
    'sponsor': 'Sponsor',
    'gaji': 'Gaji Pegawai',
    'operasional': 'Operasional Harian',
    'maintenance': 'Pemeliharaan / Perbaikan',
    'utility': 'Utilitas (Listrik & Air)',
    'lainnya': 'Lain-lain'
  }
  return map[val] || val
}

const loadDetail = async () => {
  loading.value = true
  try {
    const id = route.params.id
    const res = await api.get(`/admin/keuangan/transaksi/${id}`)
    item.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data transaksi:', err)
    Swal.fire({
      icon: 'error',
      title: 'Data Tidak Ditemukan',
      text: 'Transaksi yang dicari tidak ditemukan.',
      confirmButtonColor: '#2d4a3e',
    }).then(() => {
      router.push('/admin/keuangan/transaksi')
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadDetail()
})
</script>
