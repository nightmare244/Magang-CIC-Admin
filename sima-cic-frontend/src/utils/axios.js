// Contoh konfigurasi di file utama Axios Anda
import axios from 'axios';

const rawUtilApiUrl = import.meta.env.VITE_API_URL || 'http://localhost:8000';
const utilBackendBase = rawUtilApiUrl.replace(/\/api\/?$/, "").replace(/\/+$/, "");

const api = axios.create({
    baseURL: `${utilBackendBase}/api`, // Sesuaikan dengan base URL Laravel Anda
    withCredentials: true, // <== WAJIB: Agar cookie sesi/sanctum dikirim
    headers: {
        'Accept': 'application/json',
    },
});

export default api;