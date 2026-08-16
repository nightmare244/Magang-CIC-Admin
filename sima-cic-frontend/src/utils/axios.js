// Contoh konfigurasi di file utama Axios Anda
import axios from 'axios';

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || 'http://localhost:8000/api', // Sesuaikan dengan base URL Laravel Anda
    withCredentials: true, // <== WAJIB: Agar cookie sesi/sanctum dikirim
    headers: {
        'Accept': 'application/json',
    },
});

export default api;