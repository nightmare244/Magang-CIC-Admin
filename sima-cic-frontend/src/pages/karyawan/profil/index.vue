<template>
  <div class="min-h-screen bg-[#F8FAFB] dark:bg-[#080908] font-poppins pb-32 overflow-x-hidden transition-colors duration-500">
    
    <!-- Premium Moving Wave Header -->
    <header class="relative bg-gradient-to-br from-[#1b3329] via-[#2d4a3e] to-[#1e332a] dark:from-[#0a0f0d] dark:to-[#050505] pt-14 pb-44 px-8 overflow-hidden">
      <!-- Decorative Backdrop -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -right-10 -top-10 w-72 h-72 bg-emerald-400/10 rounded-full blur-[100px] animate-pulse"></div>
      </div>
      
      <!-- Profile Identification Area -->
      <div class="relative z-20 flex flex-col items-center animate-header-slide">
        <!-- Squircle Profile Picture -->
        <div class="relative group">
          <div class="w-36 h-36 rounded-[2.5rem] bg-gradient-to-br from-emerald-400/30 to-teal-500/30 p-[3px] shadow-2xl rotate-2 group-hover:rotate-0 transition-transform duration-500">
            <div class="w-full h-full rounded-[2.35rem] overflow-hidden bg-[#1b3329] border border-white/10">
              <img
                :src="fotoUrl(user.foto_profil)"
                class="w-full h-full object-cover scale-110 group-hover:scale-100 transition-transform duration-700"
                alt="Profil"
                @error="(e) => (e.target.src = '/img/default-avatar.png')"
              />
            </div>
          </div>
          
          <!-- Camera Action Button -->
          <button 
            @click="router.push('/karyawan/profil/upload-photo')"
            class="absolute -bottom-2 -right-2 p-3.5 bg-emerald-500 text-white rounded-2xl shadow-xl hover:bg-emerald-600 transition-all active:scale-90 border-4 border-[#1b3329] dark:border-[#0a0f0d]"
          >
            <Camera class="w-5 h-5" />
          </button>
        </div>
        
        <h1 class="mt-8 text-2xl font-extrabold text-white tracking-tight text-center drop-shadow-md">
          {{ user.name || 'Nama Karyawan' }}
        </h1>
        <div class="mt-2 px-4 py-1 bg-white/10 backdrop-blur-md rounded-full border border-white/10">
          <p class="text-[10px] font-bold text-emerald-300 uppercase tracking-[0.2em]">
            {{ user.departemen?.nama || 'Personalia' }}
          </p>
        </div>
      </div>

      <!-- Animated Waves (4 Layers) -->
      <div class="absolute bottom-0 left-0 w-full leading-[0]">
        <svg class="waves h-[110px] min-h-[110px] w-full" viewBox="0 24 150 28" preserveAspectRatio="none">
          <defs>
            <path id="gentle-wave" d="M-160 44c30 0 58-18 88-18s 58 18 88 18 58-18 88-18 58 18 88 18 v44h-352z" />
          </defs>
          <g class="parallax-waves">
            <use xlink:href="#gentle-wave" x="48" y="0" fill="currentColor" class="text-white/20 dark:text-[#080908]/20 animate-wave-1" />
            <use xlink:href="#gentle-wave" x="48" y="3" fill="currentColor" class="text-white/40 dark:text-[#080908]/40 animate-wave-2" />
            <use xlink:href="#gentle-wave" x="48" y="5" fill="currentColor" class="text-white/60 dark:text-[#080908]/60 animate-wave-3" />
            <use xlink:href="#gentle-wave" x="48" y="7" fill="currentColor" class="text-white dark:text-[#080908] animate-wave-4" />
          </g>
        </svg>
      </div>
    </header>

    <!-- Content Area -->
    <div class="max-w-md mx-auto px-6 -mt-20 relative z-30 space-y-7">
      
      <!-- Basic Information Card -->
      <section class="bg-white dark:bg-[#111311] rounded-[2.5rem] p-8 shadow-2xl shadow-emerald-900/5 border border-slate-50 dark:border-white/5 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-8">
          <div class="w-1.5 h-5 bg-emerald-500 rounded-full"></div>
          <h2 class="text-sm font-black text-slate-800 dark:text-emerald-400 tracking-wide">Informasi Dasar</h2>
        </div>

        <div class="space-y-7">
          <div class="flex items-center gap-5">
            <div class="w-12 h-12 bg-slate-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-slate-400 dark:text-emerald-500 shadow-sm">
              <User class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">NIP / ID Karyawan</p>
              <p class="text-sm font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ user.nip || '-' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-5">
            <div class="w-12 h-12 bg-slate-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-slate-400 dark:text-emerald-500 shadow-sm">
              <Phone class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Nomor Telepon</p>
              <p class="text-sm font-black text-slate-800 dark:text-slate-100 tracking-tight">{{ user.nomor_hp || '-' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-5">
            <div class="w-12 h-12 bg-slate-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-slate-400 dark:text-emerald-500 shadow-sm">
              <Calendar class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Tempat, Tanggal Lahir</p>
              <p class="text-sm font-black text-slate-800 dark:text-slate-100 tracking-tight">
                {{ user.tempat_lahir || '-' }}, {{ user.tanggal_lahir || '-' }}
              </p>
            </div>
          </div>

          <div class="flex items-start gap-5">
            <div class="w-12 h-12 bg-slate-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-slate-400 dark:text-emerald-500 shadow-sm">
              <MapPin class="w-5 h-5" />
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest mb-0.5">Alamat Domisili</p>
              <p class="text-xs font-medium text-slate-500 dark:text-slate-400 leading-relaxed">
                {{ user.alamat || 'Alamat belum diatur' }}
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="space-y-4 animate-fade-in-up" style="animation-delay: 200ms">
        <button 
          @click="router.push('/karyawan/profil/edit')"
          class="w-full flex items-center justify-between p-5 bg-white dark:bg-[#111311] rounded-[2.2rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-50 dark:border-white/5 group active:scale-[0.97] transition-all"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-emerald-50 dark:bg-emerald-500/10 rounded-2xl flex items-center justify-center text-emerald-600 transition-transform group-hover:rotate-6">
              <Edit3 class="w-5 h-5" />
            </div>
            <span class="text-sm font-black text-slate-700 dark:text-slate-100 tracking-tight">Perbarui Profil</span>
          </div>
          <ChevronRight class="w-5 h-5 text-slate-300 group-hover:text-emerald-500 transition-colors" />
        </button>

        <button 
          @click="router.push('/karyawan/profil/change-password')"
          class="w-full flex items-center justify-between p-5 bg-white dark:bg-[#111311] rounded-[2.2rem] shadow-xl shadow-slate-200/40 dark:shadow-none border border-slate-50 dark:border-white/5 group active:scale-[0.97] transition-all"
        >
          <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-amber-50 dark:bg-amber-500/10 rounded-2xl flex items-center justify-center text-amber-600 transition-transform group-hover:rotate-6">
              <ShieldCheck class="w-5 h-5" />
            </div>
            <span class="text-sm font-black text-slate-700 dark:text-slate-100 tracking-tight">Keamanan Akun</span>
          </div>
          <ChevronRight class="w-5 h-5 text-slate-300 group-hover:text-amber-500 transition-colors" />
        </button>
      </section>

      <!-- Minimalist Branding -->
      <footer class="pt-8 pb-4 text-center">
        <div class="w-12 h-1 bg-slate-100 dark:bg-white/5 mx-auto mb-6 rounded-full"></div>
        <p class="text-[10px] text-slate-300 dark:text-slate-700 font-bold uppercase tracking-[0.4em]">
          PT Ciwangun Indah Camp
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import { 
  User, MapPin, Phone, Calendar, 
  ChevronRight, Camera, ShieldCheck, Edit3 
} from "lucide-vue-next";

const router = useRouter();
const loading = ref(true);
const user = ref({});

const fetchProfil = async () => {
    try {
        const { data } = await api.get("/karyawan/profil");
        user.value = data.data;
    } catch (error) {
        console.error("Gagal memuat profil:", error);
    } finally {
        loading.value = false;
    }
};

onMounted(fetchProfil);

const fotoUrl = (path) => {
    if (!path) return "/img/default-avatar.png"; 
    const baseUrl = import.meta.env.VITE_API_URL || "http://localhost:8000";
    const cleanPath = path.replace(/^\/?storage\//i, '');
    return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};
</script>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800;900&display=swap');

.font-poppins { font-family: 'Poppins', sans-serif; }

/* ANIMATIONS */
.animate-header-slide { 
  animation: headerSlide 1s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes headerSlide { 
  from { transform: translateY(-20px); opacity: 0; } 
  to { transform: translateY(0); opacity: 1; } 
}

.animate-fade-in-up { 
  opacity: 0; 
  animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}
@keyframes fadeInUp {
  from { opacity: 0; transform: translateY(30px); }
  to { opacity: 1; transform: translateY(0); }
}

/* SEAMLESS WAVE ANIMATION */
.animate-wave-1 { animation: move-forever 25s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-2 { animation: move-forever 18s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-3 { animation: move-forever 13s cubic-bezier(.55,.5,.45,.5) infinite; }
.animate-wave-4 { animation: move-forever 10s cubic-bezier(.55,.5,.45,.5) infinite; }

@keyframes move-forever {
  0% { transform: translate3d(-90px, 0, 0); }
  100% { transform: translate3d(85px, 0, 0); }
}

/* Squircle transition smoothing */
.transition-transform {
  transition-timing-function: cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
</style>