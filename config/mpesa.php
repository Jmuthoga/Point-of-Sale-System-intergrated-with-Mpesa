<?php

return [
    'env' => env('MPESA_ENV', 'sandbox'),
    'shortcode' => env('MPESA_SHORTCODE'),
    'consumer_key' => env('MPESA_CONSUMER_KEY'),
    'consumer_secret' => env('MPESA_CONSUMER_SECRET'),
    'passkey' => env('MPESA_PASSKEY'),
    'stk_callback_url' => env('MPESA_STK_CALLBACK_URL'),
    'c2b_callback_url' => env('MPESA_CALLBACK_URL'),
    'base_url' => env('MPESA_ENV') === 'production'
        ? 'https://api.safaricom.co.ke'
        : 'https://sandbox.safaricom.co.ke',
];
