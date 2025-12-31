import { defineStore } from "pinia";
import api from "@/services/api";

export const useDashboardKaryawanStore = defineStore("dashboardKaryawanStore", {
  state: () => ({
    todayAbsensi: null,
    pengumumanTerbaru: [],
    ringkasanBulan: null,
  }),

  actions: {
    async getTodayAbsensi() {
      const res = await api.get("/karyawan/absensi/today");
      this.todayAbsensi = res.data.data;
    },

    async getPengumuman() {
      const res = await api.get("/karyawan/pengumuman");
      this.pengumumanTerbaru = res.data.data;
    },

    async getRingkasan() {
      const res = await api.get("/karyawan/dashboard");
      this.ringkasanBulan = res.data.data;
    }
  }
});
