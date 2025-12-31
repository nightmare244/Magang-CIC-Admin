import { createApp } from "vue";
import { createPinia } from "pinia";
import App from "./App.vue";
import router from "./router"; // Router Anda
import axios from "axios";
import { useAuthStore } from "@/stores/authStore"; 

// ===============================
// 🔥 IMPORT STYLE DI SINI
// ===============================
import "@/styles/tailwind.css"; // wajib untuk tailwind
import "@/styles/global.css";   // style tambahan kamu (reset body/html margin)
// ===============================

// 1. Inisialisasi Aplikasi Vue
const app = createApp(App);

// 2. Pasang Pinia Store Management (WAJIB sebelum menggunakan store)
app.use(createPinia()); 

// 3. Konfigurasi Global Axios
axios.defaults.baseURL = "http://localhost:8000/api";

// 4. Load Token dari LocalStorage dan Set Header
const token = localStorage.getItem("token");
if (token) {
  axios.defaults.headers.common["Authorization"] = `Bearer ${token}`;
} 

// 5. Inisialisasi Auth Store
const auth = useAuthStore();

// 6. Fetch User sebelum Mounting Router
// Ini adalah kunci untuk mencegah masalah otentikasi saat refresh/reload.
auth.fetchUser().finally(() => {
  // Setelah fetchUser selesai (sukses/gagal 401), 
  // status authStore.user dan authStore.isAuthenticated sudah benar.
  app.use(router);
  app.mount("#app");
});