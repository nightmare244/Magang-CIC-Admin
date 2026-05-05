import { defineStore } from "pinia";
import api from "@/services/api";

export const usePengumumanStore = defineStore("pengumumanStore", {
  state: () => ({
    list: [],
    detail: null,
  }),

  actions: {
    async getAll() {
      const res = await api.get("/pengumuman");
      this.list = res.data.data;
    },

    async getDetail(id) {
      const res = await api.get(`/pengumuman/${id}`);
      this.detail = res.data.data;
    },

    async create(data) {
      return await api.post("/admin/pengumuman", data);
    },

    async update(id, data) {
      return await api.put(`/admin/pengumuman/${id}`, data);
    },

    async delete(id) {
      return await api.delete(`/admin/pengumuman/${id}`);
    }
  }
});
