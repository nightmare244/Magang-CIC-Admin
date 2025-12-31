<template>
  <div class="space-y-4">
    <div v-if="rows.length === 0" class="flex flex-col items-center justify-center py-10 opacity-40">
      <UserCheck class="w-10 h-10 mb-2 text-slate-400" />
      <p class="text-[10px] font-bold uppercase tracking-widest text-slate-400">Semua Izin Terproses</p>
    </div>

    <div class="space-y-3">
      <div v-for="r in rows" :key="r.id" 
        class="group relative bg-white dark:bg-[#161a16] border border-gray-100 dark:border-gray-800 p-4 rounded-2xl transition-all hover:border-emerald-500/30">
        
        <div class="flex flex-col gap-4">
          <div class="flex items-start justify-between">
            <div class="flex items-center gap-3">
              <div class="w-11 h-11 bg-emerald-50 dark:bg-emerald-500/10 rounded-xl flex items-center justify-center border border-emerald-100/50 dark:border-emerald-500/20">
                <span class="text-sm font-black text-[#2d4a3e] dark:text-emerald-500 uppercase">
                  {{ r.user?.name ? r.user.name.substring(0, 2) : '??' }}
                </span>
              </div>
              
              <div>
                <h4 class="text-sm font-black text-slate-800 dark:text-slate-100 uppercase tracking-tight leading-tight">
                  {{ r.user?.name || 'Karyawan CIC' }}
                </h4>
                <div class="flex items-center gap-2 mt-0.5">
                  <span class="text-[10px] font-bold text-emerald-600 uppercase tracking-tighter">{{ r.tipe_izin || 'Izin' }}</span>
                  <span class="text-slate-300 dark:text-slate-600">•</span>
                  <span class="text-[9px] text-slate-400 font-mono italic">NIP: {{ r.user?.nip || '-' }}</span>
                </div>
              </div>
            </div>
            
            <div class="flex flex-col items-end gap-1.5">
              <span class="px-2.5 py-1 bg-amber-50 dark:bg-amber-500/10 text-amber-600 text-[9px] font-bold rounded-lg border border-amber-100 dark:border-amber-900/30 uppercase tracking-wider">
                Menunggu
              </span>
            </div>
          </div>

          <div class="pt-3 border-t border-gray-50 dark:border-gray-800/50">
            <p class="text-[10px] text-slate-500 dark:text-slate-400 mb-3 leading-relaxed">
              <span class="font-bold text-slate-400 uppercase mr-1">[Ket]:</span> 
              "{{ r.keterangan || 'Tidak ada keterangan tambahan' }}"
            </p>
            
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-3">
                <span class="text-[10px] text-slate-400 flex items-center gap-1.5 font-medium">
                  <Calendar class="w-3.5 h-3.5 text-emerald-500" />
                  {{ formatDate(r.tanggal_mulai || r.tanggal) }} 
                  <span v-if="r.tanggal_selesai">— {{ formatDate(r.tanggal_selesai) }}</span>
                </span>
              </div>
              
              <div class="flex items-center gap-1.5">
                <div class="w-1.5 h-1.5 bg-amber-400 rounded-full animate-pulse"></div>
                <span class="text-[9px] font-bold text-slate-400 uppercase tracking-tighter italic">Perlu Verifikasi</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { UserCheck, Calendar } from 'lucide-vue-next';

const props = defineProps({
  rows: { type: Array, default: () => [] }
});

const formatDate = (dateStr) => {
  if (!dateStr) return '-';
  const d = new Date(dateStr);
  return d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric' });
};
</script>