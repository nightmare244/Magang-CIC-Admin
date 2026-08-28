<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-[#2d4a3e]/10 dark:bg-emerald-500/10 text-[#2d4a3e] dark:text-emerald-400">
          <FolderTree class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Daftar Akun (Chart of Accounts)</h1>
          <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Master kode akun keuangan untuk klasifikasi otomatis transaksi & laporan</p>
        </div>
      </div>

      <button 
        @click="openModalCreate"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-sm shadow-sm hover:shadow-md"
      >
        <Plus class="w-4 h-4" />
        <span>+ Tambah Akun Baru</span>
      </button>
    </div>

    <!-- Summary KPI Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Total Akun</p>
        <p class="text-2xl font-black text-gray-800 dark:text-white mt-1">{{ summary.total }} Akun</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider">Akun Aset / Kas</p>
        <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ summary.aset }} Akun</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider">Akun Pendapatan</p>
        <p class="text-2xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ summary.pendapatan }} Akun</p>
      </div>
      <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm">
        <p class="text-xs font-bold text-rose-600 dark:text-rose-400 uppercase tracking-wider">Akun Beban / Biaya</p>
        <p class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ summary.beban }} Akun</p>
      </div>
    </div>

    <!-- Filter Tab & Search -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex flex-col md:flex-row items-stretch md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-1.5 p-1 bg-gray-100 dark:bg-gray-700/60 rounded-xl overflow-x-auto">
        <button 
          v-for="cat in categoryTabs" 
          :key="cat.value"
          @click="activeCategory = cat.value; loadData()"
          class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all whitespace-nowrap"
          :class="activeCategory === cat.value ? 'bg-white dark:bg-gray-800 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900'"
        >
          {{ cat.label }}
        </button>
      </div>

      <div class="relative min-w-[240px]">
        <Search class="w-4 h-4 text-gray-400 absolute left-3 top-2.5" />
        <input 
          v-model="searchQuery" 
          type="text" 
          placeholder="Cari kode / nama akun..."
          class="w-full pl-9 pr-3.5 py-2 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
          @input="loadData"
        />
      </div>
    </div>

    <!-- Table -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Kode Akun</th>
              <th class="px-5 py-3.5">Nama Akun</th>
              <th class="px-5 py-3.5">Kategori</th>
              <th class="px-5 py-3.5">Saldo Normal</th>
              <th class="px-5 py-3.5 text-right">Saldo Awal (Rp)</th>
              <th class="px-5 py-3.5 text-center">Status</th>
              <th class="px-5 py-3.5 text-center">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60 text-sm">
            <tr v-for="a in akuns" :key="a.id" class="hover:bg-gray-50 dark:hover:bg-gray-700/30 transition">
              <!-- Kode Akun -->
              <td class="px-5 py-3.5 whitespace-nowrap font-mono font-bold text-gray-900 dark:text-white">
                <span class="px-2.5 py-1 rounded-lg bg-gray-100 dark:bg-gray-700">
                  {{ a.kode_akun }}
                </span>
              </td>

              <!-- Nama Akun -->
              <td class="px-5 py-3.5">
                <p class="font-semibold text-gray-900 dark:text-white">{{ a.nama_akun }}</p>
                <p v-if="a.keterangan" class="text-xs text-gray-400 mt-0.5 line-clamp-1">{{ a.keterangan }}</p>
              </td>

              <!-- Kategori -->
              <td class="px-5 py-3.5 whitespace-nowrap">
                <span 
                  class="px-2.5 py-1 rounded-full text-xs font-bold uppercase tracking-wider"
                  :class="getCategoryBadgeClass(a.kategori)"
                >
                  {{ a.kategori }}
                </span>
              </td>

              <!-- Saldo Normal -->
              <td class="px-5 py-3.5 whitespace-nowrap capitalize text-xs font-semibold text-gray-600 dark:text-gray-300">
                {{ a.saldo_normal }}
              </td>

              <!-- Saldo Awal -->
              <td class="px-5 py-3.5 whitespace-nowrap text-right font-mono font-bold text-gray-800 dark:text-gray-200">
                Rp {{ formatCurrency(a.saldo_awal) }}
              </td>

              <!-- Status -->
              <td class="px-5 py-3.5 whitespace-nowrap text-center">
                <span 
                  class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                  :class="a.is_active ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300' : 'bg-gray-100 text-gray-600'"
                >
                  <span class="w-1.5 h-1.5 rounded-full" :class="a.is_active ? 'bg-emerald-500' : 'bg-gray-400'"></span>
                  {{ a.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>

              <!-- Aksi -->
              <td class="px-5 py-3.5 whitespace-nowrap text-center">
                <div class="inline-flex items-center gap-2">
                  <button 
                    @click="openModalEdit(a)"
                    class="p-1.5 rounded-lg text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition"
                    title="Edit Akun"
                  >
                    <Edit2 class="w-4 h-4" />
                  </button>
                  <button 
                    @click="deleteAkun(a)"
                    class="p-1.5 rounded-lg text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition"
                    title="Hapus Akun"
                  >
                    <Trash2 class="w-4 h-4" />
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty / Loading -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Memuat daftar akun...</p>
      </div>
      <div v-else-if="akuns.length === 0" class="text-center py-16 text-gray-500">
        <FolderTree class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
        <p class="font-bold">Belum ada akun pada kategori ini</p>
      </div>
    </div>

    <!-- Modal Form (Tambah / Edit Akun) -->
    <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
      <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <!-- Modal Header -->
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-bold text-lg text-gray-900 dark:text-white">
            {{ isEditing ? 'Edit Daftar Akun' : 'Tambah Akun Baru' }}
          </h3>
          <button @click="showModal = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
            <X class="w-5 h-5" />
          </button>
        </div>

        <!-- Modal Body -->
        <form @submit.prevent="saveAkun" class="p-6 space-y-4">
          <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Kode Akun <span class="text-rose-500">*</span></label>
            <input 
              v-model="formAkun.kode_akun" 
              type="text" 
              placeholder="Contoh: 1-10001 / 4-10001"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-mono font-bold focus:ring-2 focus:ring-emerald-500 outline-none"
              required
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Akun <span class="text-rose-500">*</span></label>
            <input 
              v-model="formAkun.nama_akun" 
              type="text" 
              placeholder="Contoh: Kas Operasional, Pendapatan Tiket"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500 outline-none"
              required
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Kategori <span class="text-rose-500">*</span></label>
              <select 
                v-model="formAkun.kategori" 
                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
                required
                @change="onKategoriModalChange"
              >
                <option value="aset">Aset (Aktiva)</option>
                <option value="kewajiban">Kewajiban</option>
                <option value="ekuitas">Ekuitas / Modal</option>
                <option value="pendapatan">Pendapatan</option>
                <option value="beban">Beban / Biaya</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Saldo Normal <span class="text-rose-500">*</span></label>
              <select 
                v-model="formAkun.saldo_normal" 
                class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
                required
              >
                <option value="debit">Debit</option>
                <option value="kredit">Kredit</option>
              </select>
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Saldo Awal (Rp)</label>
            <input 
              v-model.number="formAkun.saldo_awal" 
              type="number" 
              min="0"
              class="w-full px-3.5 py-2.5 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-mono focus:ring-2 focus:ring-emerald-500 outline-none"
            />
          </div>

          <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Keterangan</label>
            <textarea 
              v-model="formAkun.keterangan" 
              rows="2" 
              placeholder="Catatan fungsi akun ini..."
              class="w-full px-3.5 py-2 text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
            ></textarea>
          </div>

          <!-- Actions -->
          <div class="flex justify-end gap-3 pt-3">
            <button 
              type="button" 
              @click="showModal = false"
              class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 transition"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="saving"
              class="px-5 py-2 text-xs font-bold text-white bg-[#2d4a3e] rounded-xl hover:bg-[#1f3329] transition disabled:opacity-50"
            >
              <span v-if="saving">Menyimpan...</span>
              <span v-else>Simpan Akun</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { FolderTree, Plus, Search, Edit2, Trash2, X } from 'lucide-vue-next'

const akuns = ref([])
const loading = ref(false)
const saving = ref(false)
const searchQuery = ref('')
const activeCategory = ref('semua')
const showModal = ref(false)
const isEditing = ref(false)
const editingId = ref(null)

const summary = ref({
  total: 0,
  aset: 0,
  pendapatan: 0,
  beban: 0,
})

const categoryTabs = [
  { label: 'Semua Akun', value: 'semua' },
  { label: 'Aset', value: 'aset' },
  { label: 'Kewajiban', value: 'kewajiban' },
  { label: 'Ekuitas', value: 'ekuitas' },
  { label: 'Pendapatan', value: 'pendapatan' },
  { label: 'Beban', value: 'beban' },
]

const formAkun = ref({
  kode_akun: '',
  nama_akun: '',
  kategori: 'aset',
  saldo_normal: 'debit',
  saldo_awal: 0,
  is_active: true,
  keterangan: '',
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const getCategoryBadgeClass = (cat) => {
  const map = {
    'aset': 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300',
    'kewajiban': 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300',
    'ekuitas': 'bg-purple-100 text-purple-800 dark:bg-purple-900/40 dark:text-purple-300',
    'pendapatan': 'bg-blue-100 text-blue-800 dark:bg-blue-900/40 dark:text-blue-300',
    'beban': 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300',
  }
  return map[cat] || 'bg-gray-100 text-gray-800'
}

const onKategoriModalChange = () => {
  if (['aset', 'beban'].includes(formAkun.value.kategori)) {
    formAkun.value.saldo_normal = 'debit'
  } else {
    formAkun.value.saldo_normal = 'kredit'
  }
}

const loadData = async () => {
  loading.value = true
  try {
    const params = {}
    if (activeCategory.value !== 'semua') params.kategori = activeCategory.value
    if (searchQuery.value) params.q = searchQuery.value

    const res = await api.get('/admin/akuns', { params })
    akuns.value = res.data.data
    if (res.data.summary) {
      summary.value = res.data.summary
    }
  } catch (err) {
    console.error('Gagal mengambil daftar akun:', err)
  } finally {
    loading.value = false
  }
}

const openModalCreate = () => {
  isEditing.value = false
  editingId.value = null
  formAkun.value = {
    kode_akun: '',
    nama_akun: '',
    kategori: 'pendapatan',
    saldo_normal: 'kredit',
    saldo_awal: 0,
    is_active: true,
    keterangan: '',
  }
  showModal.value = true
}

const openModalEdit = (akun) => {
  isEditing.value = true
  editingId.value = akun.id
  formAkun.value = {
    kode_akun: akun.kode_akun,
    nama_akun: akun.nama_akun,
    kategori: akun.kategori,
    saldo_normal: akun.saldo_normal,
    saldo_awal: akun.saldo_awal,
    is_active: akun.is_active,
    keterangan: akun.keterangan || '',
  }
  showModal.value = true
}

const saveAkun = async () => {
  saving.value = true
  try {
    if (isEditing.value) {
      await api.put(`/admin/akuns/${editingId.value}`, formAkun.value)
    } else {
      await api.post('/admin/akuns', formAkun.value)
    }

    Swal.fire({
      icon: 'success',
      title: isEditing.value ? 'Akun Diperbarui' : 'Akun Ditambahkan',
      timer: 1500,
      showConfirmButton: false,
    })

    showModal.value = false
    loadData()
  } catch (err) {
    console.error('Gagal menyimpan akun:', err)
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: err.response?.data?.message || 'Terjadi kesalahan saat menyimpan akun.',
      confirmButtonColor: '#2d4a3e',
    })
  } finally {
    saving.value = false
  }
}

const deleteAkun = async (akun) => {
  const result = await Swal.fire({
    title: 'Hapus Akun?',
    text: `Akun "${akun.kode_akun} - ${akun.nama_akun}" akan dihapus.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/admin/akuns/${akun.id}`)
      Swal.fire({
        icon: 'success',
        title: 'Akun Dihapus',
        timer: 1500,
        showConfirmButton: false,
      })
      loadData()
    } catch (err) {
      console.error('Gagal menghapus akun:', err)
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menghapus',
        text: err.response?.data?.message || 'Akun tidak dapat dihapus.',
        confirmButtonColor: '#2d4a3e',
      })
    }
  }
}

onMounted(() => {
  loadData()
})
</script>
