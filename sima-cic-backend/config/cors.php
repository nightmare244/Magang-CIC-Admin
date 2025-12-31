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
        'http://localhost:5173',
        'http://127.0.0.1:5173',
        'http://localhost:3000', // Tambahan jika sewaktu-waktu ganti port
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    /* | PENTING: Tambahkan 'Content-Disposition' di sini.
    | Tanpa ini, browser sering memblokir request download file karena
    | tidak bisa membaca header nama file dari server.
    */
    'exposed_headers' => ['Content-Disposition'],

    'max_age' => 0,

    'supports_credentials' => true,

];