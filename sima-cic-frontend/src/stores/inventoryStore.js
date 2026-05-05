import { defineStore } from "pinia";
import axios from "axios";

export const useInventoryStore = defineStore("inventory", {
    state: () => ({
        inventoryList: [],
        pagination: {},
        selectedItem: null,
        isLoading: false,
        error: null,
    }),

    getters: {
        getInventoryList: (state) => state.inventoryList,
        getIsLoading: (state) => state.isLoading,
    },

    actions: {
        /**
         * ============================
         * FETCH LIST (ADMIN)
         * ============================
         */
        async fetchAdminInventory(params = {}) {
            this.isLoading = true;
            this.error = null;

            try {
                const res = await axios.get("/admin/inventaris", { params });

                this.inventoryList = res.data.data;

                const { data, ...meta } = res.data;
                this.pagination = meta;

                return res.data;

            } catch (err) {
                this.error = err.response?.data || {
                    message: "Gagal mengambil daftar inventaris.",
                };
                throw err;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * ============================
         * CREATE ITEM (ADMIN)
         * FormData Wajib!
         * ============================
         */
        async createInventoryItem(formData) {
            this.isLoading = true;
            this.error = null;

            try {
                const res = await axios.post("/admin/inventaris", formData, {
                    headers: {
                        "Content-Type": "multipart/form-data",
                    },
                });

                // Jika API pakai data wrapping
                const newItem = res.data.data ?? res.data;

                this.inventoryList.unshift(newItem);

                return newItem;

            } catch (err) {
                this.error = err.response?.data || {
                    message: "Gagal membuat item inventaris baru.",
                };
                throw err;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * ============================
         * UPDATE ITEM (ADMIN)
         * Gunakan _method=PUT agar FormData bisa dipakai
         * ============================
         */
        async updateInventoryItem(id, formData) {
            this.isLoading = true;
            this.error = null;

            try {
                const res = await axios.post(
                    `/admin/inventaris/${id}?_method=PUT`,
                    formData,
                    {
                        headers: {
                            "Content-Type": "multipart/form-data",
                        },
                    }
                );

                const updatedItem = res.data.data ?? res.data;

                const index = this.inventoryList.findIndex(
                    (item) => item.id === id
                );

                if (index !== -1) {
                    this.inventoryList[index] = updatedItem;
                }

                return updatedItem;

            } catch (err) {
                this.error = err.response?.data || {
                    message: `Gagal memperbarui item ${id}.`,
                };
                throw err;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * ============================
         * DELETE ITEM
         * ============================
         */
        async deleteInventoryItem(id) {
            this.isLoading = true;
            this.error = null;

            try {
                await axios.delete(`/admin/inventaris/${id}`);

                this.inventoryList = this.inventoryList.filter(
                    (item) => item.id !== id
                );

                return true;

            } catch (err) {
                this.error = err.response?.data || {
                    message: `Gagal menghapus item ${id}.`,
                };
                throw err;
            } finally {
                this.isLoading = false;
            }
        },

        /**
         * ============================
         * FETCH DETAIL (ADMIN/KARYAWAN)
         * ============================
         */
        async fetchInventoryItem(identifier, role = "admin") {
            this.isLoading = true;
            this.error = null;

            const endpoint =
                role === "karyawan"
                    ? `/karyawan/inventaris/${identifier}` // kode_barang
                    : `/admin/inventaris/${identifier}`; // id

            try {
                const res = await axios.get(endpoint);
                this.selectedItem = res.data;
                return res.data;

            } catch (err) {
                this.error = err.response?.data || {
                    message: `Gagal mengambil detail item ${identifier}.`,
                };
                throw err;
            } finally {
                this.isLoading = false;
            }
        },
    },
});
