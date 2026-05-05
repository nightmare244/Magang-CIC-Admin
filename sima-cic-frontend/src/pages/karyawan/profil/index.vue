<template>
  <div class="min-h-screen bg-[#fcfdfe] dark:bg-[#080908] font-poppins pb-32 overflow-x-hidden transition-colors duration-500">
    
    <header class="relative pt-16 pb-36 px-8 overflow-hidden">
      <div 
        class="absolute inset-0 z-0 bg-cover bg-center bg-no-repeat scale-110"
        style="background-image: url('/images/background.jpg'); filter: blur(3px);" 
      ></div>
      <div class="absolute inset-0 z-10 bg-gradient-to-b from-[#1e332a]/95 via-[#1e332a]/80 to-transparent dark:from-[#0a0f0d]/98 dark:via-[#0a0f0d]/90 dark:to-transparent"></div>
      
      <div class="relative z-20 flex flex-col items-center animate-header-slide">
        <div class="relative group">
          <div class="w-36 h-44 rounded-[2.5rem] bg-gradient-to-tr from-emerald-400/40 to-teal-400/40 p-[3px] shadow-2xl">
            <div class="w-full h-full rounded-[2.35rem] overflow-hidden bg-[#1e332a] border-4 border-[#1e332a]/50">
              <img
                :src="fotoUrl(user.foto_profil)"
                class="w-full h-full object-cover"
                alt="profil"
                @error="(e) => (e.target.src = '/img/default-avatar.png')"
              />
            </div>
          </div>
          <button 
            @click="router.push('/karyawan/profil/upload-photo')"
            class="absolute -bottom-2 -right-1 p-3.5 bg-emerald-500 text-white rounded-2xl shadow-xl active:scale-90 border-4 border-[#1e332a] dark:border-[#0a0f0d]"
          >
            <Camera class="w-4 h-4" />
          </button>
        </div>
        
        <h1 class="mt-6 text-xl font-bold text-white tracking-tight text-center capitalize drop-shadow-md">
          {{ user.name || 'nama karyawan' }}
        </h1>
        <p class="text-[10px] font-bold text-emerald-400 uppercase tracking-[0.3em] mt-1.5 drop-shadow-sm">
          {{ user.departemen?.nama || 'personalia' }}
        </p>
      </div>
    </header>

    <div class="max-w-md mx-auto px-6 -mt-8 relative z-30 space-y-6">
      
      <section class="bg-white/80 dark:bg-[#111311]/80 backdrop-blur-xl rounded-[2.5rem] p-7 shadow-sm border border-white dark:border-white/5 animate-fade-in-up">
        <div class="flex items-center gap-3 mb-8 px-2">
          <div class="w-1 h-4 bg-emerald-500 rounded-full"></div>
          <h2 class="text-[11px] font-bold text-slate-400 dark:text-slate-500 tracking-[0.2em] uppercase">detail informasi</h2>
        </div>

        <div class="grid grid-cols-1 gap-6">
          <div class="flex items-center gap-5 p-1">
            <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-500/5 rounded-[1.2rem] flex items-center justify-center text-emerald-600/70 dark:text-emerald-500/50">
              <User class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 capitalize tracking-wider">nip / id karyawan</p>
              <p class="text-[13px] font-bold text-slate-700 dark:text-slate-200 tracking-tight">{{ user.nip || '-' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-5 p-1">
            <div class="w-11 h-11 bg-blue-50 dark:bg-blue-500/5 rounded-[1.2rem] flex items-center justify-center text-blue-600/70 dark:text-blue-500/50">
              <Phone class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 capitalize tracking-wider">nomor telepon</p>
              <p class="text-[13px] font-bold text-slate-700 dark:text-slate-200 tracking-tight">{{ user.nomor_hp || '-' }}</p>
            </div>
          </div>

          <div class="flex items-center gap-5 p-1">
            <div class="w-11 h-11 bg-purple-50 dark:bg-purple-500/5 rounded-[1.2rem] flex items-center justify-center text-purple-600/70 dark:text-purple-500/50">
              <Calendar class="w-5 h-5" />
            </div>
            <div>
              <p class="text-[10px] font-bold text-slate-400 capitalize tracking-wider">tgl. lahir</p>
              <p class="text-[13px] font-bold text-slate-700 dark:text-slate-200 tracking-tight">
                {{ user.tempat_lahir || '-' }}, {{ user.tanggal_lahir || '-' }}
              </p>
            </div>
          </div>

          <div class="flex items-start gap-5 p-1">
            <div class="w-11 h-11 bg-orange-50 dark:bg-orange-500/5 rounded-[1.2rem] flex items-center justify-center text-orange-600/70 dark:text-orange-500/50">
              <MapPin class="w-5 h-5" />
            </div>
            <div class="flex-1">
              <p class="text-[10px] font-bold text-slate-400 capitalize tracking-wider">alamat domisili</p>
              <p class="text-[12px] font-medium text-slate-500 dark:text-slate-400 leading-relaxed italic mt-0.5">
                "{{ user.alamat || 'alamat belum diatur' }}"
              </p>
            </div>
          </div>
        </div>
      </section>

      <section class="space-y-3 animate-fade-in-up" style="animation-delay: 150ms">
        <button 
          @click="router.push('/karyawan/profil/edit')"
          class="w-full flex items-center justify-between p-5 bg-white dark:bg-[#111311] rounded-[2rem] shadow-sm border border-slate-50 dark:border-white/5 active:scale-[0.98] transition-all group"
        >
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-emerald-500/10 rounded-xl flex items-center justify-center text-emerald-600">
              <Settings class="w-4 h-4" />
            </div>
            <span class="text-[12px] font-bold text-slate-600 dark:text-slate-200 capitalize tracking-tight">pengaturan profil</span>
          </div>
          <ChevronRight class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" />
        </button>

        <button 
          @click="router.push('/karyawan/profil/change-password')"
          class="w-full flex items-center justify-between p-5 bg-white dark:bg-[#111311] rounded-[2rem] shadow-sm border border-slate-50 dark:border-white/5 active:scale-[0.98] transition-all group"
        >
          <div class="flex items-center gap-4">
            <div class="w-10 h-10 bg-amber-500/10 rounded-xl flex items-center justify-center text-amber-600">
              <Lock class="w-4 h-4" />
            </div>
            <span class="text-[12px] font-bold text-slate-600 dark:text-slate-200 capitalize tracking-tight">kata sandi</span>
          </div>
          <ChevronRight class="w-4 h-4 text-slate-300 group-hover:translate-x-1 transition-transform" />
        </button>

        <button 
          @click="handleLogout"
          class="w-full flex items-center justify-center gap-3 py-5 bg-[#1e332a] text-white rounded-[2rem] border border-white/10 font-bold text-[11px] uppercase tracking-[0.25em] active:scale-[0.98] transition-all mt-4 shadow-lg shadow-emerald-900/20"
        >
          <LogOut class="w-4 h-4" /> keluar aplikasi
        </button>
      </section>

      <footer class="pt-10 pb-4 text-center">
        <p class="text-[9px] text-slate-300 dark:text-slate-700 font-bold uppercase tracking-[0.5em]">
          ciwangun indah camp
        </p>
      </footer>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from "vue";
import api from "@/services/api";
import { useRouter } from "vue-router";
import Swal from 'sweetalert2';
import { 
  User, MapPin, Phone, Calendar, 
  ChevronRight, Camera, Settings, 
  Lock, LogOut 
} from "lucide-vue-next";

const router = useRouter();
const loading = ref(true);
const user = ref({});

const fetchProfil = async () => {
    try {
        const { data } = await api.get("/karyawan/profil");
        user.value = data.data;
    } catch (error) {
        console.error("error fetching profil:", error);
    } finally {
        setTimeout(() => { loading.value = false; }, 600);
    }
};

const handleLogout = () => {
  Swal.fire({
    title: '<span class="text-[16px] font-bold">keluar?</span>',
    html: '<span class="text-[12px]">anda yakin ingin keluar dari aplikasi?</span>',
    showCancelButton: true,
    confirmButtonColor: '#1e332a',
    cancelButtonColor: '#f43f5e',
    confirmButtonText: 'ya, keluar',
    cancelButtonText: 'batal',
    background: '#ffffff',
    customClass: {
      popup: 'rounded-[2rem]',
      confirmButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3',
      cancelButton: 'rounded-xl font-bold text-[10px] uppercase tracking-widest px-6 py-3'
    }
  }).then((result) => {
    if (result.isConfirmed) {
      localStorage.clear();
      router.push('/login');
    }
  });
};

onMounted(fetchProfil);

const fotoUrl = (path) => {
    if (!path) return "/img/default-avatar.png"; 
    const baseUrl = import.meta.env.VITE_API_URL || "http://localhost:8000";
    const cleanPath = path.replace(/^\/?storage\//i, '');
    return `${baseUrl.replace(/\/$/, "")}/storage/${cleanPath}`;
};
</script>

<style scoped lang="postcss">
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

* {
  -webkit-tap-highlight-color: transparent;
}
</style>