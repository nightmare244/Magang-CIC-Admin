// src/services/api.js

import axios from "axios";

// Gunakan VITE_API_URL jika tersedia, fallback ke localhost
const rawUrl = import.meta.env.VITE_API_URL || "http://127.0.0.1:8000";
const BACKEND_BASE = rawUrl.replace(/\/api\/?$/, "").replace(/\/+$/, "");

// Membuat instance Axios dengan base URL (akan mengarah ke /api)
const api = axios.create({
    baseURL: `${BACKEND_BASE}/api`,
    withCredentials: true,
});

// Pastikan default axios juga bisa menggunakan withCredentials jika diperlukan
axios.defaults.withCredentials = true;

// Interceptor untuk menambahkan token pada header Authorization
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token"); // Mengambil token dari localStorage
    if (token) {
        config.headers.Authorization = `Bearer ${token}`; // Menambahkan token ke header
    }
    return config; // Kembalikan config untuk melanjutkan request
});

// Interceptor untuk menangani error, terutama token yang tidak valid
api.interceptors.response.use(
    (response) => response, // Pass through jika response sukses
    (error) => {
        if (error.response && error.response.status === 401) {
            // Jika token tidak valid atau expired, hapus token dan redirect ke halaman login
            localStorage.removeItem("token");
            window.location.href = "/login"; // Redirect ke halaman login
        }
        return Promise.reject(error); // Menolak promise jika terjadi error lain
    }
);

export default api;
