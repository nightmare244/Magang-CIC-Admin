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
            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Tambah Transaksi Keuangan</h1>
            <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Catat transaksi pemasukan atau pengeluaran kas baru</p>
          </div>
        </div>
      </div>

      <!-- Quick Status Messages -->
      <div class="flex flex-col sm:flex-row gap-2 items-end">
        <Transition name="slide-fade">
          <div v-if="errorMessage" class="flex items-center gap-3 px-5 py-2.5 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-xl shadow-sm">
            <div class="w-2 h-2 bg-rose-500 rounded-full animate-pulse"></div>
            <span class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">{{ errorMessage }}</span>
          </div>
        </Transition>
      </div>
    </div>

    <!-- Switcher Jenis Transaksi -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-4 shadow-sm">
      <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-3">Langkah 1: Pilih Jenis Transaksi</p>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
        <!-- Pilihan Pemasukan -->
        <button 
          type="button"
          @click="setJenis('pemasukan')"
          class="flex items-center justify-between p-4 rounded-xl border-2 transition-all text-left"
          :class="form.jenis === 'pemasukan' 
            ? 'border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/20 shadow-sm' 
            : 'border-gray-200 dark:border-gray-700 hover:border-emerald-200 dark:hover:border-emerald-800'"
        >
          <div class="flex items-center gap-3.5">
            <div 
              class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg"
              :class="form.jenis === 'pemasukan' ? 'bg-emerald-600 text-white' : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300'"
            >
              <ArrowDownLeft class="w-5 h-5" />
            </div>
            <div>
              <p class="font-bold text-sm text-gray-900 dark:text-white">🟢 Pemasukan (Kas Masuk)</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Tiket masuk, donasi, sponsor, parkir, dll.</p>
            </div>
          </div>
          <div 
            class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
            :class="form.jenis === 'pemasukan' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-gray-300 dark:border-gray-600'"
          >
            <Check v-if="form.jenis === 'pemasukan'" class="w-3 h-3 stroke-[3]" />
          </div>
        </button>

        <!-- Pilihan Pengeluaran -->
        <button 
          type="button"
          @click="setJenis('pengeluaran')"
          class="flex items-center justify-between p-4 rounded-xl border-2 transition-all text-left"
          :class="form.jenis === 'pengeluaran' 
            ? 'border-rose-500 bg-rose-50/50 dark:bg-rose-950/20 shadow-sm' 
            : 'border-gray-200 dark:border-gray-700 hover:border-rose-200 dark:hover:border-rose-800'"
        >
          <div class="flex items-center gap-3.5">
            <div 
              class="w-10 h-10 rounded-xl flex items-center justify-center font-bold text-lg"
              :class="form.jenis === 'pengeluaran' ? 'bg-rose-600 text-white' : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300'"
            >
              <ArrowUpRight class="w-5 h-5" />
            </div>
            <div>
              <p class="font-bold text-sm text-gray-900 dark:text-white">🔴 Pengeluaran (Kas Keluar)</p>
              <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">Gaji, operasional harian, perbaikan, utilitas, dll.</p>
            </div>
          </div>
          <div 
            class="w-5 h-5 rounded-full border-2 flex items-center justify-center"
            :class="form.jenis === 'pengeluaran' ? 'border-rose-600 bg-rose-600 text-white' : 'border-gray-300 dark:border-gray-600'"
          >
            <Check v-if="form.jenis === 'pengeluaran'" class="w-3 h-3 stroke-[3]" />
          </div>
        </button>
      </div>
    </div>

    <!-- Form Input Transaksi -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 p-6 shadow-sm">
      <p class="text-xs font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-5">Langkah 2: Rincian Transaksi & Akun</p>

      <form @submit.prevent="submitForm" class="space-y-6">
        <!-- 1. Pilihan Daftar Akun (CoA) - WAJIB -->
        <div class="p-4 rounded-xl bg-slate-50 dark:bg-gray-750 border border-slate-200 dark:border-gray-700 space-y-2">
          <label class="flex items-center justify-between text-sm font-bold text-gray-900 dark:text-white">
            <span class="flex items-center gap-1.5">
              <FolderTree class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
              Daftar Akun / Chart of Accounts (CoA) <span class="text-rose-500">*</span>
            </span>
            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
              Wajib Dipilih untuk Pembukuan Otomatis
            </span>
          </label>
          <select 
            v-model="form.akun_id"
            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium text-sm focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
            @change="onAkunChange"
          >
            <option value="" disabled>-- Pilih Akun Terkait --</option>
            <option v-for="a in availableAccounts" :key="a.id" :value="a.id">
              [{{ a.kode_akun }}] {{ a.nama_akun }} ({{ a.kategori.toUpperCase() }})
            </option>
          </select>
          <p class="text-[11px] text-gray-500 dark:text-gray-400">
            Transaksi ini akan otomatis dicatat pada akun ini dan membentuk Jurnal Kas, Arus Kas, Laba Rugi, dan Neraca.
          </p>
        </div>

        <!-- 2. Nama Transaksi -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
            Nama Transaksi / Deskripsi <span class="text-rose-500">*</span>
          </label>
          <input 
            v-model="form.nama_transaksi"
            type="text"
            :placeholder="form.jenis === 'pemasukan' ? 'Contoh: Penjualan Tiket Masuk Akhir Pekan' : 'Contoh: Pembayaran Gaji Karyawan Bulan Ini'"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
            required
          />
        </div>

        <!-- 3. Grid 2 Kolom: Tanggal & Tipe/Kategori -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Tanggal Transaksi -->
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

          <!-- Kategori Spesifik -->
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

        <!-- 4. Grid 2 Kolom: Jumlah (jika tiket) & Nominal -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Jumlah Unit Tiket (khusus tiket masuk) -->
          <div v-if="form.jenis === 'pemasukan' && form.tipe_kategori === 'tiket_masuk'">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
              Jumlah Tiket (Unit/Orang) <span class="text-rose-500">*</span>
            </label>
            <input 
              v-model.number="form.jumlah"
              type="number"
              min="1"
              placeholder="1"
              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition"
              required
            />
          </div>

          <!-- Nominal Transaksi -->
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
                placeholder="0"
                class="w-full pl-12 pr-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-bold text-lg focus:ring-2 focus:ring-emerald-500 outline-none transition"
                required
              />
            </div>
            <p v-if="form.nominal" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 mt-1">
              Terbaca: Rp {{ formatCurrency(form.nominal) }}
            </p>
          </div>
        </div>

        <!-- 5. Keterangan / Catatan -->
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Keterangan / Catatan Tambahan</label>
          <textarea 
            v-model="form.keterangan"
            placeholder="Tuliskan catatan transaksi jika ada..."
            rows="3"
            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none transition text-sm"
          ></textarea>
        </div>

        <!-- 6. Pratinjau Dampak Akuntansi & Jurnal Kas -->
        <div class="p-4 rounded-xl bg-gray-50 dark:bg-gray-700/40 border border-gray-200 dark:border-gray-700 space-y-2">
          <p class="text-xs font-bold text-gray-600 dark:text-gray-300 uppercase tracking-wider flex items-center gap-1.5">
            <BookOpenCheck class="w-4 h-4 text-[#2d4a3e] dark:text-emerald-400" />
            Pratinjau Entri Jurnal Kas Otomatis
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 text-xs font-mono">
            <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
              <span class="font-bold text-emerald-600 dark:text-emerald-400">[DEBIT]</span>
              <p class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">
                {{ form.jenis === 'pemasukan' ? '1-10001 Kas Utama (Tunai)' : (selectedAkunName || 'Akun Beban') }}
              </p>
              <p class="text-gray-500">Rp {{ formatCurrency(form.nominal || 0) }}</p>
            </div>
            <div class="p-2.5 rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700">
              <span class="font-bold text-rose-600 dark:text-rose-400">[KREDIT]</span>
              <p class="font-semibold text-gray-800 dark:text-gray-200 mt-0.5">
                {{ form.jenis === 'pemasukan' ? (selectedAkunName || 'Akun Pendapatan') : '1-10001 Kas Utama (Tunai)' }}
              </p>
              <p class="text-gray-500">Rp {{ formatCurrency(form.nominal || 0) }}</p>
            </div>
          </div>
        </div>

        <!-- Buttons -->
        <div class="flex flex-wrap gap-3 pt-3">
          <button 
            type="submit"
            :disabled="loading"
            class="px-6 py-3 bg-[#2d4a3e] text-white rounded-xl font-bold text-sm hover:bg-[#1f3329] transition disabled:opacity-50 flex items-center justify-center min-w-[200px] shadow-sm hover:shadow-md"
          >
            <span v-if="loading" class="animate-pulse">Menyimpan Transaksi...</span>
            <span v-else>💾 Simpan & Catat Transaksi</span>
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
import { useRouter } from 'vue-router'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { 
  ArrowLeft, ArrowDownLeft, ArrowUpRight, Check, 
  FolderTree, BookOpenCheck 
} from 'lucide-vue-next'

const router = useRouter()
const loading = ref(false)
const errorMessage = ref(null)
const akuns = ref([])

const form = ref({
  jenis: 'pemasukan',
  akun_id: '',
  nama_transaksi: '',
  tipe_kategori: 'tiket_masuk',
  jumlah: 1,
  nominal: 0,
  tanggal: new Date().toISOString().split('T')[0],
  keterangan: '',
})

const availableAccounts = computed(() => {
  if (form.value.jenis === 'pemasukan') {
    return akuns.value.filter(a => a.kategori === 'pendapatan' || a.kategori === 'aset')
  } else {
    return akuns.value.filter(a => a.kategori === 'beban' || a.kategori === 'kewajiban')
  }
})

const selectedAkunName = computed(() => {
  const a = akuns.value.find(item => item.id === form.value.akun_id)
  return a ? `${a.kode_akun} ${a.nama_akun}` : ''
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const setJenis = (jenis) => {
  form.value.jenis = jenis
  if (jenis === 'pemasukan') {
    form.value.tipe_kategori = 'tiket_masuk'
    // Pick default revenue account
    const defaultAkun = akuns.value.find(a => a.kode_akun === '4-10001')
    if (defaultAkun) form.value.akun_id = defaultAkun.id
  } else {
    form.value.tipe_kategori = 'operasional'
    // Pick default expense account
    const defaultAkun = akuns.value.find(a => a.kode_akun === '5-10002')
    if (defaultAkun) form.value.akun_id = defaultAkun.id
  }
}

const onAkunChange = () => {
  const chosen = akuns.value.find(a => a.id === form.value.akun_id)
  if (!chosen) return

  if (form.value.jenis === 'pemasukan') {
    if (chosen.kode_akun === '4-10001') form.value.tipe_kategori = 'tiket_masuk'
    else if (chosen.kode_akun === '4-10002') form.value.tipe_kategori = 'donasi'
    else if (chosen.kode_akun === '4-10003') form.value.tipe_kategori = 'sponsor'
    else form.value.tipe_kategori = 'lainnya'
  } else {
    if (chosen.kode_akun === '5-10001') form.value.tipe_kategori = 'gaji'
    else if (chosen.kode_akun === '5-10002') form.value.tipe_kategori = 'operasional'
    else if (chosen.kode_akun === '5-10003') form.value.tipe_kategori = 'maintenance'
    else if (chosen.kode_akun === '5-10004') form.value.tipe_kategori = 'utility'
    else form.value.tipe_kategori = 'lainnya'
  }
}

const loadAkuns = async () => {
  try {
    const res = await api.get('/admin/akuns')
    akuns.value = res.data.data
    // Set default initial account
    setJenis('pemasukan')
  } catch (err) {
    console.error('Gagal mengambil daftar akun:', err)
  }
}

const submitForm = async () => {
  if (!form.value.akun_id) {
    Swal.fire({
      icon: 'warning',
      title: 'Akun Belum Dipilih',
      text: 'Harap pilih Daftar Akun (CoA) terlebih dahulu untuk melanjutkan.',
      confirmButtonColor: '#2d4a3e',
    })
    return
  }

  loading.value = true
  errorMessage.value = null

  try {
    const payload = {
      jenis: form.value.jenis,
      akun_id: form.value.akun_id,
      nama_transaksi: form.value.nama_transaksi,
      tipe_kategori: form.value.tipe_kategori,
      nominal: Number(form.value.nominal),
      tanggal: form.value.tanggal,
      keterangan: form.value.keterangan,
      jumlah: form.value.jenis === 'pemasukan' && form.value.tipe_kategori === 'tiket_masuk' ? Number(form.value.jumlah) : 1
    }

    const res = await api.post('/admin/keuangan/transaksi', payload)
    const msg = res.data?.message || 'Transaksi berhasil dicatat.'

    Swal.fire({
      icon: 'success',
      title: 'Transaksi Berhasil Disimpan',
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
      router.push('/admin/keuangan/transaksi')
    }, 1400)
  } catch (error) {
    console.error('Gagal menyimpan transaksi:', error)
    const errMsg = error.response?.data?.message || 'Gagal menyimpan transaksi.'
    errorMessage.value = 'GAGAL MENYIMPAN TRANSAKSI'
    setTimeout(() => (errorMessage.value = null), 4000)

    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: errMsg,
      confirmButtonColor: '#2d4a3e',
    })
  } finally {
    loading.value = false
  }
}

onMounted(() => {
  loadAkuns()
})
</script>

<style scoped>
.slide-fade-enter-active { transition: all 0.3s ease-out; }
.slide-fade-enter-from { transform: translateY(-10px); opacity: 0; }
</style>
