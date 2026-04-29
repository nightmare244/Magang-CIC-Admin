<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#080908] font-poppins pb-32 overflow-x-hidden transition-colors duration-500">
    
    <header class="relative pt-14 pb-24 px-6 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat"
        style="background-image: url('/images/background.jpg');" 
      ></div>
      
      <div class="absolute inset-0 z-10 bg-gradient-to-br from-[#1e332a]/95 via-[#1e332a]/85 to-[#1e332a]/40 dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>

      <div class="relative z-20 max-w-md mx-auto">
        <div class="flex justify-between items-center mb-6">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-full border-2 border-emerald-400/30 overflow-hidden shadow-inner bg-emerald-900/50 flex items-center justify-center backdrop-blur-sm">
              <img 
                v-if="auth.user" 
                :src="auth.user.foto_profil_url || auth.user.avatar" 
                class="w-full h-full object-cover"
                @error="(e) => e.target.src = `https://ui-avatars.com/api/?name=${auth.user?.name}&background=1e332a&color=fff`"
              />
              <UserIcon v-else class="w-6 h-6 text-emerald-400" />
            </div>
            <div>
 <p class="text-[10px] font-bold text-emerald-400/90 leading-none mb-1 tracking-widest capitalize">
  {{ salamWaktu }},
</p>
              <h2 class="text-white font-bold text-lg leading-tight tracking-tight capitalize">{{ auth.user?.name || 'Karyawan' }}</h2>
            </div>
          </div>
          
          <div class="flex gap-2">
            <router-link 
              :to="{ name: 'karyawan.pengumuman.index' }" 
              class="relative p-2.5 bg-white/10 backdrop-blur-md rounded-xl text-white active:scale-90 transition-all border border-white/10"
            >
              <Bell class="w-5 h-5" />
              <span 
                v-if="jumlahBelumBaca > 0"
                class="absolute -top-1 -right-1 flex items-center justify-center min-w-[18px] h-[18px] px-1 bg-rose-500 text-[10px] font-black text-white rounded-full border-2 border-[#1e332a] shadow-sm animate-pulse"
              >
                {{ jumlahBelumBaca }}
              </span>
            </router-link>
          </div>
        </div>

        <div class="bg-white/95 dark:bg-[#151815]/95 backdrop-blur-md rounded-3xl p-5 shadow-2xl shadow-black/10 flex items-center justify-between divide-x divide-slate-100 dark:divide-white/5 border border-white/20">
          <div class="flex-1 pr-4 flex items-center gap-3 group cursor-pointer" @click="handleInventarisClick">
            <div class="w-10 h-10 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600">
              <Box class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 leading-none mb-1 tracking-tighter capitalize">Aset pinjam</p>
              <p class="text-sm font-bold text-slate-800 dark:text-white capitalize">{{ summary.kpi?.barang_dipinjam ?? 0 }} Item</p>
            </div>
          </div>
          <div class="flex-1 pl-4 flex items-center justify-between group cursor-default">
            <div>
              <p class="text-[10px] font-bold text-slate-400 leading-none mb-1 tracking-tighter capitalize">Skor disiplin</p>
              <p class="text-sm font-bold text-emerald-600">{{ summary.kpi?.skor_disiplin ?? 0 }}%</p>
            </div>
            <ChevronRight class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" />
          </div>
        </div>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-10 relative z-30 space-y-6">
      
      <section class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-6 shadow-sm border border-slate-100 dark:border-white/5 grid grid-cols-4 gap-y-7">
        <router-link :to="{ name: 'karyawan.absensi.index' }" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 group-active:scale-90 transition-all shadow-sm">
            <ScanLine class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Presensi</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.izin.index' }" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-blue-50 dark:bg-blue-500/10 rounded-2xl flex items-center justify-center text-blue-600 group-active:scale-90 transition-all shadow-sm">
            <CalendarDays class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Izin</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.inventaris.index' }" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 group-active:scale-90 transition-all shadow-sm">
            <Archive class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Gudang</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.absensi.history' }" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-purple-50 dark:bg-purple-500/10 rounded-2xl flex items-center justify-center text-purple-600 group-active:scale-90 transition-all shadow-sm">
            <History class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Riwayat</span>
        </router-link>

        <router-link to="/karyawan/inventaris" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-teal-50 dark:bg-teal-500/10 rounded-2xl flex items-center justify-center text-teal-600 group-active:scale-90 transition-all shadow-sm">
            <Library class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Katalog</span>
        </router-link>

        <router-link to="/karyawan/peminjaman/keranjang" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-indigo-50 dark:bg-indigo-500/10 rounded-2xl flex items-center justify-center text-indigo-600 group-active:scale-90 transition-all shadow-sm">
            <ShoppingBag class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Keranjang</span>
        </router-link>

        <router-link to="/karyawan/peminjaman/riwayat" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-rose-50 dark:bg-rose-500/10 rounded-2xl flex items-center justify-center text-rose-600 group-active:scale-90 transition-all shadow-sm">
            <Archive class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Arsip</span>
        </router-link>

        <router-link :to="{ name: 'karyawan.profil' }" class="flex flex-col items-center gap-2 group">
          <div class="w-14 h-14 bg-slate-50 dark:bg-white/5 rounded-2xl flex items-center justify-center text-slate-500 group-active:scale-90 transition-all shadow-sm">
            <UserIcon class="w-7 h-7" />
          </div>
          <span class="text-[10px] font-bold text-slate-600 dark:text-slate-400 tracking-tighter capitalize">Profil</span>
        </router-link>
      </section>

      <section v-if="summary.absensi_today" class="animate-fade-in-up">
        <h3 class="text-[10px] font-black text-slate-800 dark:text-slate-400 mb-3 ml-2 tracking-widest capitalize">Status presensi</h3>
        <AbsensiTodayCard :data="summary.absensi_today" class="shadow-sm border border-slate-100 dark:border-white/5" />
      </section>

      <section v-if="summary.izin_aktif" class="animate-fade-in-up">
        <h3 class="text-[10px] font-black text-slate-800 dark:text-slate-400 mb-3 ml-2 tracking-widest capitalize">Agenda aktif</h3>
        <IzinAktifCard :izin="summary.izin_aktif" />
      </section>

      <section class="animate-fade-in-up" style="animation-delay: 200ms">
        <div class="flex items-center justify-between mb-4 px-2">
           <h3 class="text-[10px] font-black text-slate-800 dark:text-slate-400 tracking-widest capitalize">Analitik kehadiran</h3>
           <div class="text-[9px] font-black text-emerald-600 bg-emerald-50 dark:bg-emerald-500/10 px-3 py-1 rounded-full border border-emerald-100/50 dark:border-none uppercase">
             {{ summary.kpi?.total_hadir ?? 0 }}/{{ summary.kpi?.target_hari ?? 26 }} Hari
           </div>
        </div>
        <div class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 border border-slate-100 dark:border-white/5 shadow-sm">
           <AbsensiChart7Hari v-if="summary.chart_7_hari?.length && !loading" :chart-data="summary.chart_7_hari" />
           <div v-else class="flex flex-col items-center justify-center py-10 opacity-30">
             <BarChart3 class="w-12 h-12 mb-3 animate-pulse text-slate-300" />
             <p class="text-[10px] font-black text-slate-400 capitalize">Sinkronisasi data...</p>
           </div>
        </div>
      </section>

      <footer class="pt-10 pb-6 text-center">
        <p class="text-[10px] text-slate-400 dark:text-slate-600 font-black tracking-[0.3em] capitalize">Ciwangun indah camp</p>
      </footer>
    </div>

    <Transition name="sheet">
      <div v-if="showAsetModal" class="fixed inset-0 z-[100] flex items-end justify-center bg-black/60 backdrop-blur-sm" @click.self="showAsetModal = false">
        <div class="bg-white dark:bg-[#111311] w-full max-w-md rounded-t-[3rem] p-8 shadow-2xl animate-sheet-up">
          <div class="w-12 h-1.5 bg-slate-200 dark:bg-white/10 rounded-full mx-auto mb-8"></div>
          
          <div class="flex justify-between items-center mb-6 px-2">
            <div>
              <h3 class="text-xl font-bold text-slate-800 dark:text-white capitalize">Daftar inventaris</h3>
              <p class="text-[10px] text-slate-400 font-black tracking-tighter capitalize">Barang yang sedang anda pinjam</p>
            </div>
            <button @click="showAsetModal = false" class="p-2 bg-slate-100 dark:bg-white/5 rounded-full">
              <X class="w-5 h-5 text-slate-400"/>
            </button>
          </div>

          <div class="space-y-3 max-h-[400px] overflow-y-auto custom-scrollbar mb-8 pr-2">
            <template v-if="daftarBarang.length > 0">
              <div 
                v-for="(item, idx) in daftarBarang" 
                :key="idx" 
                class="group flex items-center gap-4 p-4 bg-slate-50 dark:bg-white/5 rounded-2xl border border-slate-100 dark:border-white/5 active:scale-[0.98] transition-all cursor-pointer"
                @click="router.push({ name: 'karyawan.peminjaman.detail', params: { id: item.id } })"
              >
                <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-600 shrink-0">
                  <Box class="w-6 h-6" />
                </div>
                
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-sm text-slate-800 dark:text-white truncate capitalize">
                    {{ item.barang?.nama_barang || 'Aset #' + item.inventaris_id }}
                  </h4>
                  <p class="text-[10px] text-slate-500 font-black capitalize">
                    {{ item.quantity }} unit • <span class="text-emerald-600 uppercase text-[9px] font-black">{{ item.status }}</span>
                  </p>
                </div>

                <div class="w-8 h-8 rounded-full bg-white dark:bg-white/10 flex items-center justify-center border border-slate-200 dark:border-white/10 shadow-sm">
                  <ChevronRight class="w-4 h-4 text-slate-400" />
                </div>
              </div>
            </template>

            <div v-else class="text-center py-10">
              <p class="text-[10px] font-black text-slate-400 capitalize">Tidak ada barang yang sedang dipinjam.</p>
            </div>
          </div>

          <button @click="router.push({ name: 'karyawan.peminjaman.riwayat_list' })" class="w-full py-4 bg-[#1e332a] text-white rounded-2xl font-bold text-sm active:scale-95 transition-all shadow-xl shadow-emerald-900/20 capitalize">
            Lihat semua riwayat
          </button>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from "vue";
import api from "@/services/api";
import { useAuthStore } from "@/stores/authStore";
import { useRouter } from "vue-router";

import { 
  ScanLine, CalendarDays, Archive, Handshake, History, 
  User as UserIcon, Bell, Box, ChevronRight,
  BarChart3, X, Library, ShoppingBag
} from "lucide-vue-next";

import AbsensiTodayCard from "./components/AbsensiTodayCard.vue";
import IzinAktifCard from "./components/IzinAktifCard.vue";
import AbsensiChart7Hari from "./components/AbsensiChart7Hari.vue";

const auth = useAuthStore();
const router = useRouter();
const loading = ref(false);
const showAsetModal = ref(false);

const summary = ref({
  absensi_today: null,
  izin_aktif: null,
  peminjaman_aktif: null,
  chart_7_hari: [],
  kpi: { skor_disiplin: 0, total_hadir: 0, target_hari: 26, barang_dipinjam: 0 }
});

const listPengumuman = ref([{ id: 1, judul: 'Rapat Bulanan', sudah_konfirmasi: false }]);
const jumlahBelumBaca = computed(() => listPengumuman.value.filter(p => !p.sudah_konfirmasi).length);

const daftarBarang = computed(() => {
  const data = summary.value.peminjaman_aktif;
  if (!data) return [];
  return Array.isArray(data) ? data : [data];
});

// logic untuk ucapan salam otomatis berdasarkan waktu wib
const salamWaktu = computed(() => {
  const jam = new Date().getHours();
  if (jam >= 5 && jam < 11) return 'Selamat pagi';
  if (jam >= 11 && jam < 15) return 'Selamat siang';
  if (jam >= 15 && jam < 18) return 'Selamat sore';
  return 'Selamat malam';
});

const handleInventarisClick = () => {
  if (summary.value.kpi?.barang_dipinjam > 0) showAsetModal.value = true;
};

const loadDashboard = async () => {
  loading.value = true;
  try {
    const res = await api.get("/karyawan/dashboard-stats");
    if (res.data?.success) {
      summary.value = res.data.summary;
      if (res.data.user) auth.user = { ...auth.user, ...res.data.user };
    }
  } catch (e) { console.error(e); } finally { loading.value = false; }
};

onMounted(() => { loadDashboard(); });
</script>

<style scoped>
.animate-fade-in-up { 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
  opacity: 0;
}
@keyframes fadeInUp { 
  from { transform: translateY(30px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

.sheet-enter-active, .sheet-leave-active { transition: opacity 0.3s ease; }
.sheet-enter-from, .sheet-leave-to { opacity: 0; }

.animate-sheet-up { animation: sheetUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
@keyframes sheetUp { 
  from { transform: translateY(100%); } 
  to { transform: translateY(0); } 
}

.custom-scrollbar::-webkit-scrollbar { width: 4px; }
.custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.05); border-radius: 10px; }
</style>