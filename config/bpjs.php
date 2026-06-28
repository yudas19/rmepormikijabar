<?php

return [
    'env' => env('BPJS_ENV', 'development'),
    'base_url' => env('BPJS_BASE_URL', 'https://apijkn-dev.bpjs-kesehatan.go.id/pcare-rest-dev'),
    'cons_id' => env('BPJS_CONS_ID', ''),
    'secret_key' => env('BPJS_SECRET_KEY', ''),
    'user_key' => env('BPJS_USER_KEY', ''),
    'pcare_username' => env('BPJS_PCARE_USERNAME', ''),
    'pcare_password' => env('BPJS_PCARE_PASSWORD', ''),
    'user_mjkn' => env('BPJS_MJKN_USER', ''),
];
