<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
        'login', // Tambahkan jika route login Anda di luar prefix api
        'logout',
    ],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        // Development
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:5174',
        'http://127.0.0.1:5174',
        'http://localhost:3000',
        // Production (HTTPS only — kamera WAJIB HTTPS)
        'https://sima.ciwangunparkland.id',
        'https://api-sima.ciwangunparkland.id',
    ],

    // Izinkan semua origin yang match pola domain untuk Median.co WebView
    'allowed_origins_patterns' => [
        '#^https://.*\.ciwangunparkland\.id$#',
    ],

    'allowed_headers' => ['*'],

    /* | PENTING: Tambahkan 'Content-Disposition' di sini.
    | Tanpa ini, browser sering memblokir request download file karena
    | tidak bisa membaca header nama file dari server.
    */
    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => true,

];