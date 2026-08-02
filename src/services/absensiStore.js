import { defineStore } from "pinia";
import api from "./api";

export const useAbsensiStore = defineStore("absensiStore", {
  state: () => ({
    history: [],
    today: null,
  }),

  actions: {
    async getToday() {
      const res = await api.get("/karyawan/absensi/today");
      this.today = res.data.data;
    },

    async historyList() {
      const res = await api.get("/karyawan/absensi/history");
      this.history = res.data.data;
    },

    async checkIn(payload) {
      return await api.post("/karyawan/absensi/checkin", payload);
    },

    async checkOut(payload) {
      return await api.post("/karyawan/absensi/checkout", payload);
    },
  }
});
