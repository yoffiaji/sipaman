<?php

/*
|--------------------------------------------------------------------------
| config/cors.php — Konfigurasi CORS untuk Frontend
|--------------------------------------------------------------------------
|
| Sesuaikan 'allowed_origins' dengan URL frontend teman Anda.
| Jika frontend berjalan di localhost:3000 atau localhost:5173,
| tambahkan ke daftar di bawah.
|
*/

return [
    /*
    |--------------------------------------------------------------------------
    | Path yang terkena CORS
    |--------------------------------------------------------------------------
    | Gunakan 'api/*' untuk semua endpoint API.
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Methods
    |--------------------------------------------------------------------------
    */
    'allowed_methods' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins
    |--------------------------------------------------------------------------
    | Daftar origin frontend yang diizinkan.
    | Sesuaikan dengan URL frontend teman Anda.
    |
    | Contoh development:
    |   - http://localhost:3000   (React/Next.js dev server)
    |   - http://localhost:5173   (Vite/React)
    |   - http://localhost:4200   (Angular)
    |   - http://pirt-app.test    (Laragon virtual host)
    */
    'allowed_origins' => [
        env('APP_FRONTEND_URL', 'http://localhost:3000'),
        'http://localhost:3000',
        'http://localhost:5173',
        'http://localhost:4200',
        'http://localhost:8080',
        'http://pirt-app.test',
        'http://127.0.0.1:5173',
    ],

    /*
    |--------------------------------------------------------------------------
    | Allowed Origins Patterns (Regex)
    |--------------------------------------------------------------------------
    */
    'allowed_origins_patterns' => [],

    /*
    |--------------------------------------------------------------------------
    | Allowed Headers
    |--------------------------------------------------------------------------
    */
    'allowed_headers' => ['*'],

    /*
    |--------------------------------------------------------------------------
    | Exposed Headers
    |--------------------------------------------------------------------------
    */
    'exposed_headers' => [],

    /*
    |--------------------------------------------------------------------------
    | Max Age (cache preflight)
    |--------------------------------------------------------------------------
    */
    'max_age' => 0,

    /*
    |--------------------------------------------------------------------------
    | Supports Credentials
    |--------------------------------------------------------------------------
    | Set true jika menggunakan cookie-based Sanctum.
    | Set false jika menggunakan token (Bearer) — lebih umum untuk SPA modern.
    */
    'supports_credentials' => true,
];
