// Contoh konfigurasi di file utama Axios Anda
import axios from 'axios';

const api = axios.create({
    baseURL: 'http://localhost:8000/api', // Sesuaikan dengan base URL Laravel Anda
    withCredentials: true, // <== WAJIB: Agar cookie sesi/sanctum dikirim
    headers: {
        'Accept': 'application/json',
    },
});

export default api;