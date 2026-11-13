<template>
  <div class="space-y-6 p-6 font-poppins">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
      <div class="flex items-center gap-3">
        <div class="p-2.5 rounded-xl bg-[#2d4a3e]/10 dark:bg-emerald-500/10 text-[#2d4a3e] dark:text-emerald-400">
          <BookMarked class="w-6 h-6" />
        </div>
        <div>
          <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Jurnal Kas</h1>
          <p class="text-gray-500 dark:text-gray-400 text-xs sm:text-sm mt-0.5">Buku kas masuk & keluar kronologis dengan saldo berjalan otomatis</p>
        </div>
      </div>

      <div class="flex items-center gap-2">
        <button 
          @click="toggleTambahBaris"
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-[#2d4a3e] text-white rounded-xl hover:bg-[#1f3329] transition font-medium text-xs shadow-sm"
        >
          <Plus class="w-4 h-4" />
          <span>{{ showInputRow ? 'Tutup Input' : '+ Tambah Baris' }}</span>
        </button>
        <button 
          @click="printJurnal" 
          class="inline-flex items-center gap-1.5 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-200 rounded-xl hover:bg-gray-200 transition font-medium text-xs shadow-sm"
        >
          <Printer class="w-4 h-4" />
          <span>Cetak Jurnal</span>
        </button>
      </div>
    </div>

    <!-- Filter Bar -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-2xl border border-gray-200 dark:border-gray-700/80 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
      <div class="flex flex-wrap items-center gap-4 w-full md:w-auto">
        <div>
          <label class="block text-[11px] font-bold text-gray-400 uppercase mb-1.5">Periode</label>
          <div class="flex items-center gap-2">
            <!-- Mode Selector (Tabs) -->
            <div class="flex items-center p-1 bg-gray-100 dark:bg-gray-700/70 rounded-xl">
              <button 
                type="button"
                @click="setMode('bulanan')"
                class="px-3 py-1.5 text-xs font-bold rounded-lg transition"
                :class="filterMode === 'bulanan' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
              >
                Bulanan
              </button>
              <button 
                type="button"
                @click="setMode('tahunan')"
                class="px-3 py-1.5 text-xs font-bold rounded-lg transition"
                :class="filterMode === 'tahunan' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
              >
                Tahunan
              </button>
              <button 
                type="button"
                @click="setMode('semua')"
                class="px-3 py-1.5 text-xs font-bold rounded-lg transition"
                :class="filterMode === 'semua' ? 'bg-white dark:bg-gray-800 text-emerald-600 dark:text-emerald-400 shadow-sm' : 'text-gray-500 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white'"
              >
                Semua
              </button>
            </div>

            <!-- Input Bulanan -->
            <input 
              v-if="filterMode === 'bulanan'"
              v-model="filterBulan" 
              type="month" 
              class="px-3.5 py-1.5 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500 outline-none"
              @change="loadJurnal"
            />

            <!-- Input Tahunan -->
            <select
              v-else-if="filterMode === 'tahunan'"
              v-model="filterTahun"
              class="px-3.5 py-1.5 text-xs sm:text-sm border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white font-medium focus:ring-2 focus:ring-emerald-500 outline-none"
              @change="loadJurnal"
            >
              <option v-for="y in availableYears" :key="y" :value="y">{{ y }}</option>
            </select>
          </div>
        </div>

        <div class="self-end pb-1">
          <p class="text-xs font-bold text-gray-700 dark:text-gray-300">
            Periode: <span class="text-emerald-600 dark:text-emerald-400">{{ jurnalData.periode || displayPeriodeText }}</span>
          </p>
        </div>
      </div>

      <div class="text-xs text-gray-500 dark:text-gray-400 font-semibold">
        Menampilkan <span class="text-gray-900 dark:text-white font-bold">{{ jurnalData.entries ? jurnalData.entries.length : 0 }}</span> transaksi kas
      </div>
    </div>

    <!-- Panel Input Baris Baru (Buku Kas Langsung) -->
    <div v-if="showInputRow" class="bg-emerald-50/50 dark:bg-gray-800/90 p-5 rounded-2xl border-2 border-emerald-500/40 dark:border-emerald-500/30 shadow-md">
      <div class="flex items-center justify-between pb-3 border-b border-emerald-100 dark:border-gray-700 mb-4">
        <h3 class="text-sm font-bold text-gray-900 dark:text-white flex items-center gap-2">
          <Plus class="w-4 h-4 text-emerald-600" />
          <span>Tambah Baris Transaksi Kas Baru</span>
        </h3>
        <span class="text-[11px] text-gray-500 dark:text-gray-400 font-medium">Satu baris = satu transaksi kas</span>
      </div>

      <form @submit.prevent="submitTambahBaris" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-start">
          <!-- Nomor Akun (Typeahead) -->
          <div class="md:col-span-3 relative">
            <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">
              Nomor Akun <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <input 
                v-model="newRow.nomor_akun" 
                type="text" 
                placeholder="Ketik nomor / nama akun..."
                class="w-full px-3 py-2 text-xs font-mono font-bold border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
                autocomplete="off"
                @input="onInputSearch"
                @focus="onInputSearch"
              />
              <Search class="w-3.5 h-3.5 text-gray-400 absolute right-3 top-2.5 pointer-events-none" />
            </div>

            <!-- Error Akun Tidak Ditemukan -->
            <p v-if="accountNotFound" class="text-[11px] text-rose-500 font-semibold mt-1">
              Nomor akun tidak ditemukan
            </p>

            <!-- Dropdown Typeahead Suggestions -->
            <div 
              v-if="showSuggestions && suggestions.length > 0"
              class="absolute z-30 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-56 overflow-y-auto"
            >
              <div 
                v-for="sug in suggestions" 
                :key="sug.id"
                @click="pilihAkun(sug)"
                class="px-3 py-2 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 cursor-pointer border-b border-gray-100 dark:border-gray-700/60 last:border-b-0 transition text-xs"
              >
                <div class="flex items-center justify-between">
                  <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ sug.nomor_akun }}</span>
                  <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ sug.nama_kategori }}</span>
                </div>
                <p class="font-medium text-gray-800 dark:text-gray-200 mt-0.5 truncate">{{ sug.nama_akun }}</p>
              </div>
            </div>
          </div>

          <!-- Nama Akun (Read-only) -->
          <div class="md:col-span-3">
            <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Akun</label>
            <input 
              :value="newRow.nama_akun" 
              type="text" 
              placeholder="Terisi otomatis..."
              readonly
              class="w-full px-3 py-2 text-xs bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 font-semibold cursor-not-allowed outline-none truncate"
            />
          </div>

          <!-- Quantity -->
          <div class="md:col-span-1">
            <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Qty</label>
            <input 
              v-model.number="newRow.quantity" 
              type="number" 
              step="any"
              min="0.01"
              placeholder="1"
              class="w-full px-2.5 py-2 text-xs font-mono font-bold border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
              required
            />
          </div>

          <!-- Pemasukan (Rp) -->
          <div class="md:col-span-2">
            <label class="block text-[11px] font-bold text-emerald-700 dark:text-emerald-400 uppercase mb-1">Pemasukan (Rp)</label>
            <input 
              v-model.number="newRow.pemasukan" 
              type="number" 
              min="0"
              step="any"
              placeholder="0"
              class="w-full px-3 py-2 text-xs font-mono font-bold border border-emerald-300 dark:border-emerald-700 rounded-xl bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
              @input="onPemasukanInput"
            />
          </div>

          <!-- Pengeluaran (Rp) -->
          <div class="md:col-span-2">
            <label class="block text-[11px] font-bold text-rose-700 dark:text-rose-400 uppercase mb-1">Pengeluaran (Rp)</label>
            <input 
              v-model.number="newRow.pengeluaran" 
              type="number" 
              min="0"
              step="any"
              placeholder="0"
              class="w-full px-3 py-2 text-xs font-mono font-bold border border-rose-300 dark:border-rose-700 rounded-xl bg-white dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-rose-500 outline-none"
              @input="onPengeluaranInput"
            />
          </div>

          <!-- Tanggal & Tombol Simpan -->
          <div class="md:col-span-1">
            <label class="block text-[11px] font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Tanggal</label>
            <input 
              v-model="newRow.tanggal" 
              type="date" 
              class="w-full px-1.5 py-2 text-[11px] border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none"
              required
            />
          </div>
        </div>

        <!-- Baris Keterangan Tambahan & Aksi -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 pt-2">
          <div class="flex-1">
            <input 
              v-model="newRow.keterangan" 
              type="text" 
              placeholder="Catatan / keterangan transaksi (opsional)..."
              class="w-full px-3 py-1.5 text-xs border border-gray-200 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none"
            />
          </div>
          <div class="flex items-center gap-2 justify-end">
            <button 
              type="button" 
              @click="resetNewRow(); showInputRow = false"
              class="px-4 py-1.5 text-xs font-semibold text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700 rounded-xl transition"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="savingRow || !newRow.akun_id"
              class="inline-flex items-center gap-1.5 px-5 py-2 text-xs font-bold text-white bg-[#2d4a3e] rounded-xl hover:bg-[#1f3329] transition disabled:opacity-50 disabled:cursor-not-allowed shadow-sm"
            >
              <Check class="w-3.5 h-3.5" />
              <span>{{ savingRow ? 'Menyimpan...' : 'Simpan Baris' }}</span>
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- Tabel Jurnal Kas -->
    <div class="bg-white dark:bg-gray-800 rounded-2xl border border-gray-200 dark:border-gray-700/80 overflow-hidden shadow-sm printable-area">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
          <thead class="bg-gray-50 dark:bg-gray-700/50 border-b border-gray-200 dark:border-gray-700 text-[11px] font-bold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
            <tr>
              <th class="px-5 py-3.5">Nomor Akun</th>
              <th class="px-5 py-3.5">Nama Akun</th>
              <th class="px-4 py-3.5 text-center">Quantity</th>
              <th class="px-5 py-3.5 text-right bg-emerald-50/40 dark:bg-emerald-950/20 text-emerald-700 dark:text-emerald-400">Pemasukan (Rp)</th>
              <th class="px-5 py-3.5 text-right bg-rose-50/40 dark:bg-rose-950/20 text-rose-700 dark:text-rose-400">Pengeluaran (Rp)</th>
              <th class="px-5 py-3.5 text-right bg-blue-50/30 dark:bg-blue-950/20 text-blue-800 dark:text-blue-300">Saldo (Rp)</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 dark:divide-gray-700/60 text-xs sm:text-sm">
            <!-- 1. BARIS SALDO AWAL PERIODE -->
            <tr class="bg-gray-50/90 dark:bg-gray-800/95 font-bold text-gray-700 dark:text-gray-200 border-b-2 border-gray-200 dark:border-gray-700">
              <!-- Nomor Akun -->
              <td class="px-5 py-3.5 text-gray-400 dark:text-gray-500 font-mono">-</td>
              <!-- Nama Akun -->
              <td class="px-5 py-3.5">
                <span class="inline-flex items-center gap-1.5 text-xs font-black uppercase tracking-wider text-gray-800 dark:text-gray-100">
                  SALDO AWAL PERIODE
                </span>
                <span class="text-[11px] text-gray-400 dark:text-gray-400 font-normal ml-2">
                  (Posisi kas sebelum transaksi periode ini)
                </span>
              </td>
              <!-- Quantity -->
              <td class="px-4 py-3.5 text-center text-gray-400 dark:text-gray-500">-</td>
              <!-- Pemasukan -->
              <td class="px-5 py-3.5 text-right text-gray-400 dark:text-gray-500">-</td>
              <!-- Pengeluaran -->
              <td class="px-5 py-3.5 text-right text-gray-400 dark:text-gray-500">-</td>
              <!-- Saldo Awal -->
              <td class="px-5 py-3.5 text-right font-mono font-black text-gray-900 dark:text-white bg-blue-50/20 dark:bg-blue-950/10">
                Rp {{ formatCurrency(jurnalData.saldo_awal_periode) }}
              </td>
            </tr>

            <!-- 2. BARIS TRANSAKSI KAS KRONOLOGIS -->
            <tr 
              v-for="(row, idx) in jurnalData.entries" 
              :key="row.tipe_transaksi + '-' + row.id" 
              class="hover:bg-gray-50/80 dark:hover:bg-gray-700/30 transition group"
            >
              <!-- 1. Nomor Akun -->
              <td class="px-5 py-3.5 whitespace-nowrap font-mono text-xs font-bold">
                <span 
                  v-if="row.nomor_akun && row.nomor_akun !== '-'" 
                  class="px-2 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-600 dark:text-emerald-400"
                >
                  {{ row.nomor_akun }}
                </span>
                <span v-else class="text-gray-400 dark:text-gray-500">-</span>
              </td>

              <!-- 2. Nama Akun -->
              <td class="px-5 py-3.5">
                <div class="flex items-center gap-2">
                  <span class="font-semibold text-gray-900 dark:text-white">{{ row.nama_akun }}</span>
                  <span 
                    v-if="row.is_unmapped" 
                    class="px-2 py-0.5 text-[10px] font-bold rounded-full bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300"
                    title="Transaksi belum memiliki relasi akun CoA"
                  >
                    Belum dipetakan
                  </span>
                </div>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                  <span>{{ formatDate(row.tanggal) }}</span>
                  <span v-if="row.keterangan"> &bull; {{ row.keterangan }}</span>
                </p>
              </td>

              <!-- 3. Quantity -->
              <td class="px-4 py-3.5 text-center font-mono font-medium text-gray-700 dark:text-gray-300">
                {{ formatQuantity(row.quantity) }}
              </td>

              <!-- 4. Pemasukan (Rp) -->
              <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap bg-emerald-50/30 dark:bg-emerald-950/10" :class="row.pemasukan > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-gray-300 dark:text-gray-600'">
                {{ row.pemasukan > 0 ? 'Rp ' + formatTotal(row.pemasukan) : '-' }}
              </td>

              <!-- 5. Pengeluaran (Rp) -->
              <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap bg-rose-50/30 dark:bg-rose-950/10" :class="row.pengeluaran > 0 ? 'text-rose-600 dark:text-rose-400' : 'text-gray-300 dark:text-gray-600'">
                {{ row.pengeluaran > 0 ? 'Rp ' + formatTotal(row.pengeluaran) : '-' }}
              </td>

              <!-- 6. Saldo (Rp) -->
              <td class="px-5 py-3.5 text-right font-mono font-bold whitespace-nowrap bg-blue-50/20 dark:bg-blue-950/10 text-gray-900 dark:text-white">
                Rp {{ formatTotal(row.saldo) }}
              </td>
            </tr>
          </tbody>

          <!-- 3. BARIS TOTAL KESELURUHAN (DARK MODE FRIENDLY) -->
          <tfoot class="bg-gray-100 dark:bg-gray-900 font-bold border-t-2 border-gray-300 dark:border-gray-700 text-xs sm:text-sm text-gray-900 dark:text-gray-100">
            <tr>
              <td class="px-5 py-4 text-gray-900 dark:text-white uppercase tracking-wider" colspan="3">
                TOTAL KESELURUHAN PERIODE
              </td>
              <td class="px-5 py-4 text-right font-mono text-emerald-600 dark:text-emerald-400 font-black bg-emerald-100/50 dark:bg-emerald-900/30">
                Rp {{ formatTotal(jurnalData.total_debit) }}
              </td>
              <td class="px-5 py-4 text-right font-mono text-rose-600 dark:text-rose-400 font-black bg-rose-100/50 dark:bg-rose-900/30">
                Rp {{ formatTotal(jurnalData.total_kredit) }}
              </td>
              <td class="px-5 py-4 text-right font-mono text-blue-700 dark:text-blue-300 font-black bg-blue-100/50 dark:bg-blue-900/30">
                Rp {{ formatTotal(jurnalData.saldo_akhir_periode) }}
              </td>
            </tr>
          </tfoot>
        </table>
      </div>

      <!-- Loading / Empty -->
      <div v-if="loading" class="text-center py-16">
        <div class="w-8 h-8 border-4 border-emerald-600 border-t-transparent rounded-full animate-spin mx-auto mb-3"></div>
        <p class="text-sm font-semibold text-gray-500 dark:text-gray-400">Menghitung buku jurnal kas...</p>
      </div>
      <div v-else-if="!jurnalData.entries || jurnalData.entries.length === 0" class="text-center py-16 text-gray-400">
        <BookMarked class="w-10 h-10 mx-auto mb-2 text-gray-300 dark:text-gray-600" />
        <p class="font-bold">Belum ada transaksi pada periode ini</p>
      </div>
    </div>

    <!-- Modal Edit Transaksi -->
    <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-xs">
      <div class="bg-white dark:bg-gray-800 w-full max-w-lg rounded-2xl border border-gray-200 dark:border-gray-700 shadow-2xl overflow-hidden animate-in fade-in zoom-in-95 duration-200">
        <div class="p-5 border-b border-gray-100 dark:border-gray-700 flex justify-between items-center">
          <h3 class="font-bold text-lg text-gray-900 dark:text-white">
            Edit Transaksi {{ editRow.tipe_transaksi === 'pemasukan' ? 'Pemasukan Kas' : 'Pengeluaran Kas' }}
          </h3>
          <button @click="showEditModal = false" class="p-1.5 rounded-lg text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition">
            <X class="w-5 h-5" />
          </button>
        </div>

        <form @submit.prevent="submitEditBaris" class="p-6 space-y-4">
          <div v-if="editRow.is_unmapped" class="p-3 bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 rounded-xl text-xs text-amber-800 dark:text-amber-200">
            <strong>Perhatian:</strong> Transaksi lama ini belum memiliki akun lawan. Silakan cari dan pilih akun CoA untuk melengkapi pemetaan.
          </div>

          <!-- Nomor Akun (Typeahead Edit) -->
          <div class="relative">
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">
              Nomor Akun Lawan <span class="text-rose-500">*</span>
            </label>
            <div class="relative">
              <input 
                v-model="editRow.nomor_akun" 
                type="text" 
                placeholder="Ketik nomor / nama akun..."
                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-emerald-500 outline-none"
                autocomplete="off"
                @input="onEditSearchInput"
                @focus="onEditSearchInput"
              />
              <Search class="w-4 h-4 text-gray-400 absolute right-3 top-2.5 pointer-events-none" />
            </div>

            <p v-if="editAccountNotFound" class="text-[11px] text-rose-500 font-semibold mt-1">
              Nomor akun tidak ditemukan
            </p>

            <div 
              v-if="showEditSuggestions && editSuggestions.length > 0"
              class="absolute z-30 left-0 right-0 mt-1 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl max-h-48 overflow-y-auto"
            >
              <div 
                v-for="sug in editSuggestions" 
                :key="sug.id"
                @click="pilihAkunEdit(sug)"
                class="px-3 py-2 hover:bg-emerald-50 dark:hover:bg-emerald-950/40 cursor-pointer border-b border-gray-100 dark:border-gray-700/60 last:border-b-0 transition text-xs"
              >
                <div class="flex items-center justify-between">
                  <span class="font-mono font-bold text-emerald-600 dark:text-emerald-400">{{ sug.nomor_akun }}</span>
                  <span class="text-[10px] font-semibold uppercase px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">{{ sug.nama_kategori }}</span>
                </div>
                <p class="font-medium text-gray-800 dark:text-gray-200 mt-0.5 truncate">{{ sug.nama_akun }}</p>
              </div>
            </div>
          </div>

          <!-- Nama Akun (Read-only) -->
          <div>
            <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Nama Akun</label>
            <input 
              :value="editRow.nama_akun" 
              type="text" 
              readonly
              class="w-full px-3.5 py-2.5 text-xs bg-gray-100 dark:bg-gray-700/60 border border-gray-200 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 font-semibold cursor-not-allowed outline-none"
            />
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Quantity</label>
              <input 
                v-model.number="editRow.quantity" 
                type="number" 
                step="any"
                min="0.01"
                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Nominal (Rp) <span class="text-rose-500">*</span></label>
              <input 
                v-model.number="editRow.nominal" 
                type="number" 
                min="0.01"
                step="any"
                class="w-full px-3.5 py-2.5 text-xs font-mono font-bold border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"
                required
              />
            </div>
          </div>

          <div class="grid grid-cols-2 gap-3">
            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Tanggal</label>
              <input 
                v-model="editRow.tanggal" 
                type="date" 
                class="w-full px-3.5 py-2.5 text-xs border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"
                required
              />
            </div>
            <div>
              <label class="block text-xs font-bold text-gray-600 dark:text-gray-300 uppercase mb-1">Keterangan</label>
              <input 
                v-model="editRow.keterangan" 
                type="text" 
                placeholder="Catatan transaksi..."
                class="w-full px-3.5 py-2.5 text-xs border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white outline-none focus:ring-2 focus:ring-emerald-500"
              />
            </div>
          </div>

          <div class="flex justify-end gap-3 pt-3">
            <button 
              type="button" 
              @click="showEditModal = false"
              class="px-4 py-2 text-xs font-bold text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-xl hover:bg-gray-200 transition"
            >
              Batal
            </button>
            <button 
              type="submit" 
              :disabled="savingEdit || !editRow.akun_id"
              class="px-5 py-2 text-xs font-bold text-white bg-[#2d4a3e] rounded-xl hover:bg-[#1f3329] transition disabled:opacity-50"
            >
              <span v-if="savingEdit">Menyimpan...</span>
              <span v-else>Simpan Perubahan</span>
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import api from '@/services/api'
import Swal from 'sweetalert2'
import { BookMarked, Printer, Plus, Search, Edit2, Trash2, X, Check } from 'lucide-vue-next'

const filterMode = ref('bulanan') // 'bulanan' | 'tahunan' | 'semua'
const filterBulan = ref(new Date().toISOString().slice(0, 7))
const currentYear = new Date().getFullYear()
const filterTahun = ref(currentYear.toString())
const availableYears = ref([
  (currentYear - 2).toString(),
  (currentYear - 1).toString(),
  currentYear.toString(),
  (currentYear + 1).toString(),
])
const loading = ref(false)

const jurnalData = ref({
  mode: 'bulanan',
  periode: '',
  saldo_awal_periode: 0,
  total_debit: 0,
  total_kredit: 0,
  saldo_akhir_periode: 0,
  entries: [],
})

// Input Baris Baru State
const showInputRow = ref(false)
const savingRow = ref(false)
const showSuggestions = ref(false)
const suggestions = ref([])
const accountNotFound = ref(false)
let searchTimeout = null

const todayStr = new Date().toISOString().slice(0, 10)

const newRow = ref({
  nomor_akun: '',
  akun_id: null,
  nama_akun: '',
  nomor_kategori: null,
  nama_kategori: '',
  quantity: 1,
  pemasukan: null,
  pengeluaran: null,
  tanggal: todayStr,
  keterangan: '',
})

// Edit Modal State
const showEditModal = ref(false)
const savingEdit = ref(false)
const showEditSuggestions = ref(false)
const editSuggestions = ref([])
const editAccountNotFound = ref(false)
let editSearchTimeout = null

const editRow = ref({
  id: null,
  tipe_transaksi: 'pemasukan',
  akun_id: null,
  nomor_akun: '',
  nama_akun: '',
  nomor_kategori: null,
  nama_kategori: '',
  is_unmapped: false,
  quantity: 1,
  nominal: 0,
  tanggal: todayStr,
  keterangan: '',
})

const toggleTambahBaris = () => {
  showInputRow.value = !showInputRow.value
  if (!showInputRow.value) {
    resetNewRow()
  }
}

const resetNewRow = () => {
  newRow.value = {
    nomor_akun: '',
    akun_id: null,
    nama_akun: '',
    nomor_kategori: null,
    nama_kategori: '',
    quantity: 1,
    pemasukan: null,
    pengeluaran: null,
    tanggal: todayStr,
    keterangan: '',
  }
  suggestions.value = []
  showSuggestions.value = false
  accountNotFound.value = false
}

const onInputSearch = () => {
  accountNotFound.value = false
  newRow.value.akun_id = null
  newRow.value.nama_akun = ''
  newRow.value.nomor_kategori = null

  if (searchTimeout) clearTimeout(searchTimeout)

  const q = newRow.value.nomor_akun.trim()
  if (!q) {
    suggestions.value = []
    showSuggestions.value = false
    return
  }

  searchTimeout = setTimeout(async () => {
    try {
      const res = await api.get('/admin/keuangan/akuns/search', { params: { q } })
      suggestions.value = res.data.data || []
      showSuggestions.value = true

      // Cek apakah ada exact match pada nomor akun
      const exact = suggestions.value.find(s => s.nomor_akun.toLowerCase() === q.toLowerCase())
      if (exact && suggestions.value.length === 1) {
        pilihAkun(exact)
      } else if (suggestions.value.length === 0) {
        accountNotFound.value = true
      }
    } catch (err) {
      console.error('Gagal mencari akun:', err)
    }
  }, 300)
}

const pilihAkun = (sug) => {
  newRow.value.nomor_akun = sug.nomor_akun
  newRow.value.akun_id = sug.id
  newRow.value.nama_akun = sug.nama_akun
  newRow.value.nomor_kategori = sug.nomor_kategori
  newRow.value.nama_kategori = sug.nama_kategori
  showSuggestions.value = false
  accountNotFound.value = false
}

const onPemasukanInput = () => {
  if (newRow.value.pemasukan && Number(newRow.value.pemasukan) > 0) {
    newRow.value.pengeluaran = null
  }
}

const onPengeluaranInput = () => {
  if (newRow.value.pengeluaran && Number(newRow.value.pengeluaran) > 0) {
    newRow.value.pemasukan = null
  }
}

const submitTambahBaris = async () => {
  if (!newRow.value.akun_id) {
    accountNotFound.value = true
    Swal.fire({
      icon: 'warning',
      title: 'Akun Belum Dipilih',
      text: 'Nomor akun tidak ditemukan atau belum dipilih dari daftar CoA.',
      confirmButtonColor: '#2d4a3e',
    })
    return
  }

  const masuk = Number(newRow.value.pemasukan) || 0
  const keluar = Number(newRow.value.pengeluaran) || 0

  if ((masuk <= 0 && keluar <= 0) || (masuk > 0 && keluar > 0)) {
    Swal.fire({
      icon: 'warning',
      title: 'Periksa Nominal',
      text: 'Hanya salah satu dari Pemasukan atau Pengeluaran yang boleh diisi dan harus lebih dari 0.',
      confirmButtonColor: '#2d4a3e',
    })
    return
  }

  const jenis = masuk > 0 ? 'pemasukan' : 'pengeluaran'
  const nominal = masuk > 0 ? masuk : keluar
  const kat = newRow.value.nomor_kategori

  // Peringatan Lunak (Soft Warning)
  if (jenis === 'pemasukan' && kat >= 15 && kat <= 17) {
    const result = await Swal.fire({
      icon: 'question',
      title: 'Konfirmasi Pemasukan',
      text: `Akun "${newRow.value.nama_akun}" tergolong Beban / HPP (kategori ${kat}), apakah Anda yakin mencatatnya sebagai Pemasukan?`,
      showCancelButton: true,
      confirmButtonColor: '#2d4a3e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Yakin',
      cancelButtonText: 'Periksa Kembali',
    })
    if (!result.isConfirmed) return
  } else if (jenis === 'pengeluaran' && (kat === 13 || kat === 14)) {
    const result = await Swal.fire({
      icon: 'question',
      title: 'Konfirmasi Pengeluaran',
      text: `Akun "${newRow.value.nama_akun}" tergolong Pendapatan (kategori ${kat}), apakah Anda yakin mencatatnya sebagai Pengeluaran?`,
      showCancelButton: true,
      confirmButtonColor: '#2d4a3e',
      cancelButtonColor: '#6b7280',
      confirmButtonText: 'Ya, Yakin',
      cancelButtonText: 'Periksa Kembali',
    })
    if (!result.isConfirmed) return
  }

  savingRow.value = true
  try {
    await api.post('/admin/keuangan/jurnal-kas/transaksi', {
      jenis,
      akun_id: newRow.value.akun_id,
      nominal,
      quantity: Number(newRow.value.quantity) || 1,
      tanggal: newRow.value.tanggal,
      keterangan: newRow.value.keterangan || null,
    })

    Swal.fire({
      icon: 'success',
      title: 'Transaksi Tersimpan',
      timer: 1500,
      showConfirmButton: false,
    })

    resetNewRow()
    showInputRow.value = false
    loadJurnal()
  } catch (err) {
    console.error('Gagal menyimpan transaksi:', err)
    Swal.fire({
      icon: 'error',
      title: 'Gagal Menyimpan',
      text: err.response?.data?.message || 'Terjadi kesalahan saat menyimpan transaksi.',
      confirmButtonColor: '#2d4a3e',
    })
  } finally {
    savingRow.value = false
  }
}

// Edit Modal Functions
const openModalEdit = (row) => {
  editRow.value = {
    id: row.id,
    tipe_transaksi: row.tipe_transaksi,
    akun_id: row.akun_id,
    nomor_akun: row.nomor_akun && row.nomor_akun !== '-' ? row.nomor_akun : '',
    nama_akun: row.nama_akun,
    nomor_kategori: row.nomor_kategori,
    nama_kategori: row.nama_kategori || '',
    is_unmapped: row.is_unmapped,
    quantity: row.quantity || 1,
    nominal: row.tipe_transaksi === 'pemasukan' ? row.pemasukan : row.pengeluaran,
    tanggal: row.tanggal || todayStr,
    keterangan: row.keterangan || '',
  }
  editSuggestions.value = []
  showEditSuggestions.value = false
  editAccountNotFound.value = false
  showEditModal.value = true
}

const onEditSearchInput = () => {
  editAccountNotFound.value = false
  editRow.value.akun_id = null
  editRow.value.nama_akun = ''
  editRow.value.nomor_kategori = null

  if (editSearchTimeout) clearTimeout(editSearchTimeout)

  const q = editRow.value.nomor_akun.trim()
  if (!q) {
    editSuggestions.value = []
    showEditSuggestions.value = false
    return
  }

  editSearchTimeout = setTimeout(async () => {
    try {
      const res = await api.get('/admin/keuangan/akuns/search', { params: { q } })
      editSuggestions.value = res.data.data || []
      showEditSuggestions.value = true

      const exact = editSuggestions.value.find(s => s.nomor_akun.toLowerCase() === q.toLowerCase())
      if (exact && editSuggestions.value.length === 1) {
        pilihAkunEdit(exact)
      } else if (editSuggestions.value.length === 0) {
        editAccountNotFound.value = true
      }
    } catch (err) {
      console.error('Gagal mencari akun saat edit:', err)
    }
  }, 300)
}

const pilihAkunEdit = (sug) => {
  editRow.value.nomor_akun = sug.nomor_akun
  editRow.value.akun_id = sug.id
  editRow.value.nama_akun = sug.nama_akun
  editRow.value.nomor_kategori = sug.nomor_kategori
  editRow.value.nama_kategori = sug.nama_kategori
  showEditSuggestions.value = false
  editAccountNotFound.value = false
}

const submitEditBaris = async () => {
  if (!editRow.value.akun_id) {
    editAccountNotFound.value = true
    Swal.fire({
      icon: 'warning',
      title: 'Akun Belum Dipilih',
      text: 'Wajib memilih akun lawan yang valid dari daftar CoA.',
      confirmButtonColor: '#2d4a3e',
    })
    return
  }

  const nominal = Number(editRow.value.nominal) || 0
  if (nominal <= 0) {
    Swal.fire({
      icon: 'warning',
      title: 'Nominal Tidak Valid',
      text: 'Nominal transaksi harus lebih dari 0.',
      confirmButtonColor: '#2d4a3e',
    })
    return
  }

  savingEdit.value = true
  try {
    await api.put(`/admin/keuangan/jurnal-kas/transaksi/${editRow.value.tipe_transaksi}/${editRow.value.id}`, {
      akun_id: editRow.value.akun_id,
      nominal,
      quantity: Number(editRow.value.quantity) || 1,
      tanggal: editRow.value.tanggal,
      keterangan: editRow.value.keterangan || null,
    })

    Swal.fire({
      icon: 'success',
      title: 'Perubahan Disimpan',
      timer: 1500,
      showConfirmButton: false,
    })

    showEditModal.value = false
    loadJurnal()
  } catch (err) {
    console.error('Gagal memperbarui transaksi:', err)
    Swal.fire({
      icon: 'error',
      title: 'Gagal Memperbarui',
      text: err.response?.data?.message || 'Terjadi kesalahan saat memperbarui transaksi.',
      confirmButtonColor: '#2d4a3e',
    })
  } finally {
    savingEdit.value = false
  }
}

const hapusBaris = async (row) => {
  const result = await Swal.fire({
    title: 'Hapus Transaksi Kas?',
    text: `Transaksi "${row.nama_akun}" sebesar Rp ${formatCurrency(row.pemasukan || row.pengeluaran)} akan dihapus dari buku kas.`,
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#dc2626',
    cancelButtonColor: '#6b7280',
    confirmButtonText: 'Ya, Hapus',
    cancelButtonText: 'Batal',
  })

  if (result.isConfirmed) {
    try {
      await api.delete(`/admin/keuangan/jurnal-kas/transaksi/${row.tipe_transaksi}/${row.id}`)
      Swal.fire({
        icon: 'success',
        title: 'Transaksi Dihapus',
        timer: 1500,
        showConfirmButton: false,
      })
      loadJurnal()
    } catch (err) {
      console.error('Gagal menghapus transaksi:', err)
      Swal.fire({
        icon: 'error',
        title: 'Gagal Menghapus',
        text: err.response?.data?.message || 'Transaksi gagal dihapus.',
        confirmButtonColor: '#2d4a3e',
      })
    }
  }
}

const setMode = (mode) => {
  filterMode.value = mode
  loadJurnal()
}

const displayPeriodeText = computed(() => {
  if (filterMode.value === 'semua') return 'Semua Waktu'
  if (filterMode.value === 'tahunan') return `Tahun ${filterTahun.value}`
  return filterBulan.value
})

const formatCurrency = (val) => {
  if (!val && val !== 0) return '0'
  return new Intl.NumberFormat('id-ID').format(val)
}

const formatTotal = (val) => {
  const num = Number(val) || 0
  return num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })
}

const formatQuantity = (val) => {
  const num = Number(val) || 1
  return num % 1 === 0 ? num.toString() : num.toLocaleString('id-ID', { minimumFractionDigits: 1, maximumFractionDigits: 2 })
}

const formatDate = (val) => {
  if (!val) return '-'
  try {
    const d = new Date(val)
    return new Intl.DateTimeFormat('id-ID', { day: 'numeric', month: 'short', year: 'numeric' }).format(d)
  } catch {
    return val
  }
}

const loadJurnal = async () => {
  loading.value = true
  try {
    const params = { mode: filterMode.value }
    if (filterMode.value === 'bulanan') {
      params.bulan = filterBulan.value
    } else if (filterMode.value === 'tahunan') {
      params.tahun = filterTahun.value
    }
    const res = await api.get('/admin/keuangan/jurnal-kas', { params })
    jurnalData.value = res.data.data
  } catch (err) {
    console.error('Gagal mengambil data jurnal kas:', err)
  } finally {
    loading.value = false
  }
}

const printJurnal = () => {
  window.print()
}

onMounted(() => {
  loadJurnal()
})
</script>

<style scoped>
@media print {
  body * {
    visibility: hidden;
  }
  .printable-area, .printable-area * {
    visibility: visible;
  }
  .printable-area {
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
  }
}
</style>
