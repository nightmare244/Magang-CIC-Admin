// src/services/api.js

import axios from "axios";

// Membuat instance Axios dengan base URL
const api = axios.create({
    baseURL: "http://127.0.0.1:8000/api", // Ganti dengan URL API Anda
});

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
