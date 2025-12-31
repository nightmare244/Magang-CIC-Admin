import { defineStore } from "pinia";
import api from "@/services/api";

export const useDashboardAdminStore = defineStore("dashboardAdminStore", {
  state: () => ({
    statistik: null,
    aktivitas: [],
  }),

  actions: {
    async fetchDashboard() {
      const res = await api.get("/admin/dashboard");
      this.statistik = res.data.data;
    },

    async fetchAktivitas() {
      const res = await api.get("/admin/aktivitas");
      this.aktivitas = res.data.data;
    },
  }
});
