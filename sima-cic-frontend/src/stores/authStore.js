import { defineStore } from "pinia";
import axios from "axios";

// Atur Base URL Axios di sini
axios.defaults.baseURL = import.meta.env.VITE_API_URL || "http://localhost:8000/api";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: null,
    token: localStorage.getItem("token") || null,
    errorMessage: "", // Tambahkan state untuk menyimpan pesan error
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isLoading: (state) => state.user === null && state.token !== null,
  },

  actions: {
    // --- Login Action ---
    async login(payload) {
      try {
        this.errorMessage = ""; // Reset error setiap kali mencoba login
        const res = await axios.post("/login", {
          nip: payload.nip,
          password: payload.password,
        });

        this.user = res.data.user;
        this.token = res.data.token;
        localStorage.setItem("token", this.token);

        // Set Authorization header untuk request berikutnya
        axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;

        return true; // Berhasil
      } catch (error) {
        // Ambil pesan error dari backend jika ada (422 Unprocessable Content)
        if (error.response && error.response.status === 422) {
          this.errorMessage = error.response.data.message || "NIP atau Password salah.";
        } else {
          this.errorMessage = "Terjadi kesalahan pada server. Silahkan coba lagi.";
        }
        
        console.error("LOGIN ERROR:", error.response?.data || error.message);
        return false; // Kembalikan false agar Login.vue tahu login gagal tapi tidak crash
      }
    },

    // --- Fetch User Action ---
    async fetchUser() {
      if (!this.token) {
        this.user = null;
        return;
      }

      if (!axios.defaults.headers.common["Authorization"]) {
          axios.defaults.headers.common["Authorization"] = `Bearer ${this.token}`;
      }

      try {
        const res = await axios.get("/user"); 
        this.user = res.data;
      } catch (error) {
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
      if (this.token) {
        try {
          await axios.post("/logout"); 
        } catch (error) {
          const status = error.response?.status;
          if (status && (status === 401 || status === 400 || status === 422)) {
            console.log("LOGOUT API: Token sudah tidak valid. Membersihkan state lokal.");
          } else {
            console.error("LOGOUT API ERROR:", error.response?.data || error.message);
          }
        } 
      }
      
      this.user = null;
      this.token = null;
      localStorage.removeItem("token");
      delete axios.defaults.headers.common["Authorization"];
    },
  },
});