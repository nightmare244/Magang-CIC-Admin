import { defineStore } from "pinia";
import axios from "axios";

// Atur Base URL Axios di sini
axios.defaults.baseURL = "http://localhost:8000/api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    // Gunakan getter untuk status login
  }),

  getters: {
    // Getter yang lebih baik untuk memeriksa status otentikasi
    isAuthenticated: (state) => !!state.token && !!state.user,
    isLoading: (state) => state.user === null && state.token !== null,
  },

  actions: {
    // --- Login Action ---
    async login(payload) {
      try {
        const res = await axios.post("/login", {
          nip: payload.nip,
          password: payload.password,
        });

        this.user = res.data.user;
        this.token = res.data.token;
        localStorage.setItem("token", this.token);

        // [PENTING] Set Authorization header untuk request berikutnya
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

        return true;
      } catch (error) {
        console.error("LOGIN ERROR:", error.response?.data || error.message);
        throw error;
      }
    },

    // --- Fetch User Action ---
    async fetchUser() {
      // Jika tidak ada token di lokal, anggap sudah logout
      if (!this.token) {
        this.user = null; // Pastikan user null
        return;
      }

      // [PENTING] Pastikan header sudah terpasang sebelum fetch
      if (!axios.defaults.headers.common["Authorization"]) {
          axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
      }

      try {
        // Baris 55 (sesuai trace error sebelumnya)
        const res = await axios.get("/user"); 
        this.user = res.data;
      } catch (error) {
        // Jika token expired/invalid (401), panggil logout
        if (error.response && error.response.status === 401) {
          console.log("Token invalid/expired saat fetch user. Melakukan logout...");
          this.logout(); 
        } else {
          console.error("FETCH USER ERROR:", error.response?.data || error.message);
        }
      }
    },

    // --- Logout Action ---
    async logout() {
      // 1. Coba hubungi API logout jika token masih ada
      if (this.token) {
        try {
          // Baris 76 (sesuai trace error sebelumnya)
          await axios.post("/logout"); 
        } catch (error) {
          // Tangkap error 401/400/422 (token invalid/expired) dan abaikan
          const status = error.response?.status;
          if (status && (status === 401 || status === 400 || status === 422)) {
            console.log("LOGOUT API: Token sudah tidak valid atau sesi berakhir. Membersihkan state lokal.");
          } else {
            // Log error yang benar-benar tidak terduga
            console.error("LOGOUT API ERROR (Server issue):", error.response?.data || error.message);
          }
        } 
      }
      
      // 2. Bersihkan state lokal (Ini yang paling penting!)
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");

      // 3. Hapus Authorization header global
      delete axios.defaults.headers.common["Authorization"];
    },
  },
});