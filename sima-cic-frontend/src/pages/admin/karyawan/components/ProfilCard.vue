<template>
  <div class="card-eco overflow-hidden shadow-xl border-none font-poppins animate-fade-in">
    <div class="h-32 bg-gradient-to-r from-[#2d4a3e] to-[#3d6353] relative overflow-hidden">
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" fill="none" viewBox="0 0 100 100" preserveAspectRatio="none">
          <path d="M0 100 L100 0 L100 100 Z" fill="white" />
        </svg>
      </div>
    </div>

    <div class="px-8 pb-10">
      <div class="relative flex flex-col sm:flex-row items-center sm:items-end -mt-16 mb-8 gap-6">
        <div class="relative group">
          <img
            :src="getFotoUrl(data.foto_profil)"
            @error="setDefaultFoto"
            alt="Profile Picture"
            class="w-40 h-40 rounded-[2.5rem] object-cover border-8 border-[#f9fafb] dark:border-[#0a0c0a] shadow-2xl bg-white transition-transform group-hover:scale-105 duration-500"
          />
          <div v-if="data.is_active" class="absolute bottom-3 right-3 w-6 h-6 bg-emerald-500 border-4 border-white dark:border-[#1a1d19] rounded-full shadow-lg" title="Status Aktif"></div>
        </div>

        <div class="text-center sm:text-left flex-1 mb-2">
          <h2 class="text-3xl font-bold text-slate-800 dark:text-white tracking-tight">{{ data.name }}</h2>
          <div class="flex flex-wrap justify-center sm:justify-start gap-3 mt-3">
            <span class="px-4 py-1 bg-[#2d4a3e]/10 text-[#2d4a3e] dark:text-emerald-400 text-[10px] font-black rounded-xl uppercase tracking-[0.2em] border border-[#2d4a3e]/10 shadow-sm">
              {{ data.role }}
            </span>
            <span class="px-4 py-1 bg-slate-100 dark:bg-white/5 text-slate-500 dark:text-slate-400 text-[10px] font-bold rounded-xl tracking-widest font-mono border border-slate-200 dark:border-white/5 shadow-sm">
              NIP: {{ data.nip }}
            </span>
          </div>
        </div>
      </div>

      <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 mt-12">
        <div class="space-y-6">
          <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 font-bold uppercase tracking-widest flex items-center gap-2">
             <Briefcase class="w-4 h-4" /> Kontak & Kepegawaian
          </h3>
          <div class="space-y-4">
            <div class="info-row">
              <span class="info-label">Unit Departemen</span>
              <span class="info-value text-[#2d4a3e] dark:text-emerald-500 font-bold uppercase tracking-tighter">{{ data.departemen?.nama_departemen || 'Unassigned' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Email Node</span>
              <span class="info-value italic">{{ data.email }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Kontak HP</span>
              <span class="info-value font-mono">{{ data.nomor_hp || '-' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Status Akses</span>
              <span :class="data.is_active ? 'text-emerald-600' : 'text-rose-500'" class="info-value font-black uppercase tracking-widest flex items-center gap-1">
                <div :class="data.is_active ? 'bg-emerald-500' : 'bg-rose-500'" class="w-1.5 h-1.5 rounded-full animate-pulse"></div>
                {{ data.is_active ? 'Aktif' : 'Non-Aktif' }}
              </span>
            </div>
          </div>
        </div>

        <div class="space-y-6">
          <h3 class="kpi-label !text-emerald-600 dark:!text-emerald-400 border-b border-slate-100 dark:border-slate-800 pb-3 font-bold uppercase tracking-widest flex items-center gap-2">
            <User class="w-4 h-4" /> Informasi Pribadi
          </h3>
          <div class="space-y-4">
            <div class="info-row">
              <span class="info-label">Tempat Lahir</span>
              <span class="info-value">{{ data.tempat_lahir || '-' }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Tanggal Lahir</span>
              <span class="info-value">{{ formatTanggal(data.tanggal_lahir) }}</span>
            </div>
            <div class="info-row">
              <span class="info-label">Jenis Kelamin</span>
              <span class="info-value">{{ data.jenis_kelamin_lengkap || '-' }}</span>
            </div>
            <div class="flex flex-col gap-2 pt-2">
              <span class="kpi-label !text-slate-400 !mb-0 lowercase tracking-normal">Domisili Lengkap</span>
              <p class="text-slate-600 dark:text-slate-300 leading-relaxed text-sm font-medium italic bg-slate-50 dark:bg-white/5 p-4 rounded-2xl border border-slate-100 dark:border-white/5">
                "{{ data.alamat || 'Alamat belum terdaftar dalam sistem.' }}"
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Briefcase, User } from 'lucide-vue-next';

const props = defineProps({
  data: Object,
  backendUrl: String
});

const getFotoUrl = (foto) => {
  if (!foto) return '/default-user-avatar.png';
  return `${props.backendUrl}/storage/${foto}`;
};

const formatTanggal = (dateString) => {
  if (!dateString) return '-';
  try {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' });
  } catch (e) { return dateString; }
};

const setDefaultFoto = (event) => {
  event.target.src = '/default-user-avatar.png';
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco {
  @apply bg-white dark:bg-[#121512] rounded-[2.5rem] border border-gray-100 dark:border-gray-800 shadow-sm transition-all;
}

.kpi-label {
  @apply text-[10px] font-black text-slate-400 uppercase tracking-[0.2em] mb-1;
}

.info-row {
  @apply flex justify-between items-center py-1;
}

.info-label {
  @apply text-xs font-semibold text-slate-400;
}

.info-value {
  @apply text-sm font-bold text-slate-700 dark:text-slate-200 font-poppins;
}

.animate-fade-in { 
  animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards; 
}

@keyframes fadeIn { 
  from { opacity: 0; transform: translateY(20px); } 
  to { opacity: 1; transform: translateY(0); } 
}
</style>