<template>
  <div class="card-eco-premium group font-poppins">
    <div class="absolute top-0 left-0 w-1.5 h-full bg-[#2d4a3e] opacity-60 group-hover:opacity-100 transition-opacity duration-500"></div>

    <div class="flex flex-col md:flex-row justify-between gap-6 p-6">
      <div class="flex-1 space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
          <div class="flex items-center gap-2">
            <span class="badge-nomor-eco">
              <Hash class="w-3 h-3 mr-1" />
              {{ pengumuman.nomor_surat || 'B-000/SURAT/2025' }}
            </span>
          </div>
          <span class="text-[10px] font-bold text-slate-400 uppercase tracking-widest bg-slate-50 dark:bg-white/5 px-3 py-1.5 rounded-xl border border-slate-100 dark:border-slate-800 flex items-center gap-2">
            <Calendar class="w-3 h-3" />
            {{ formatDate(pengumuman.created_at) }}
          </span>
        </div>

        <h3 class="text-lg font-bold text-slate-800 dark:text-white leading-tight group-hover:text-[#2d4a3e] dark:group-hover:text-emerald-500 transition-colors line-clamp-1 font-poppins">
          {{ pengumuman.judul }}
        </h3>

        <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed line-clamp-2 italic font-medium">
          "{{ pengumuman.isi }}"
        </p>

        <div class="flex flex-wrap items-center gap-4 pt-4 border-t border-slate-50 dark:border-white/5 mt-2">
          <div class="flex items-center text-[10px] font-black text-[#2d4a3e] dark:text-emerald-500 uppercase tracking-[0.2em]">
            <Building2 class="w-3.5 h-3.5 mr-2 opacity-70" />
            {{ pengumuman.target_departemen?.nama_departemen || 'Global / Broadcast Unit' }}
          </div>

          <div v-if="pengumuman.file_path" class="flex items-center text-[10px] font-black text-rose-500 uppercase tracking-[0.2em] bg-rose-50 dark:bg-rose-500/10 px-2 py-1 rounded-lg">
            <FileText class="w-3.5 h-3.5 mr-1.5" />
            Dokumen Atase
          </div>
        </div>
      </div>

      <div class="flex items-center justify-end gap-2 self-end md:self-center pt-4 md:pt-0 border-t md:border-t-0 border-slate-50 dark:border-white/5 w-full md:w-auto">
        
        <router-link 
          :to="`/admin/pengumuman/${pengumuman.id}`" 
          title="Detail Otoritas"
          class="btn-action-eco btn-detail-eco"
        >
          <Eye class="w-4 h-4" />
        </router-link>

        <router-link 
          :to="`/admin/pengumuman/${pengumuman.id}/edit`" 
          title="Koreksi Data"
          class="btn-action-eco btn-edit-eco"
        >
          <Edit3 class="w-4 h-4" />
        </router-link>

        <button 
          @click="$emit('delete')" 
          title="Likuidasi Berkas"
          class="btn-action-eco btn-delete-eco"
        >
          <Trash2 class="w-4 h-4" />
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { Hash, Calendar, Building2, FileText, Eye, Edit3, Trash2 } from 'lucide-vue-next';

const props = defineProps({
  pengumuman: { type: Object, required: true }
});

defineEmits(['delete']);

const formatDate = (dateStr) => {
  if (!dateStr) return '---';
  return new Date(dateStr).toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  });
};
</script>

<style scoped lang="postcss">
.font-poppins { font-family: 'Poppins', sans-serif; }

.card-eco-premium {
  @apply relative bg-white dark:bg-[#121512] border border-gray-100 dark:border-gray-800 
         rounded-[1.8rem] overflow-hidden shadow-sm transition-all duration-500 hover:shadow-xl;
}

.badge-nomor-eco {
  @apply bg-[#2d4a3e]/10 text-[#2d4a3e] dark:text-emerald-500 text-[10px] font-black px-3 py-1.5 
         rounded-xl border border-[#2d4a3e]/10 uppercase tracking-[0.15em] flex items-center;
}

/* Action Buttons Premium Style */
.btn-action-eco {
  @apply p-3 rounded-2xl transition-all duration-300 active:scale-90 shadow-sm border flex items-center justify-center;
}

.btn-detail-eco {
  @apply bg-sky-50 dark:bg-sky-500/10 text-sky-600 dark:text-sky-400 border-sky-100 dark:border-sky-500/20
         hover:bg-sky-600 hover:text-white;
}

.btn-edit-eco {
  @apply bg-amber-50 dark:bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-100 dark:border-amber-500/20
         hover:bg-amber-600 hover:text-white;
}

.btn-delete-eco {
  @apply bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-100 dark:border-rose-500/20
         hover:bg-rose-600 hover:text-white;
}

.line-clamp-1 { display: -webkit-box; -webkit-line-clamp: 1; -webkit-box-orient: vertical; overflow: hidden; }
.line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
</style>