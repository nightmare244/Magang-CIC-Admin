import { defineStore } from "pinia";
import api from "./api";

export const useUserStore = defineStore("userStore", {
  state: () => ({
    list: [],
    detail: null,
  }),

  actions: {
    async getAll() {
      const res = await api.get("/admin/karyawan");
      this.list = res.data.data;
    },

    async getDetail(id) {
      const res = await api.get(`/admin/karyawan/${id}`);
      this.detail = res.data.data;
    },

    async create(payload) {
      return await api.post("/admin/karyawan", payload);
    },

    async update(id, payload) {
      return await api.put(`/admin/karyawan/${id}`, payload);
    },

    async delete(id) {
      return await api.delete(`/admin/karyawan/${id}`);
    },
  }
});
