<?php

return [
    'api_key'    => env('IYZICO_API_KEY',    'sandbox-afbZkEkqV3lLUcNPwMIGEEAO0mW6eTr8'),
    'secret_key' => env('IYZICO_SECRET_KEY', 'sandbox-cfgfVEsH2dqYMnNqlHPAvpHDHfNuXkJF'),
    'base_url'   => env('IYZICO_BASE_URL',   'https://sandbox-api.iyzipay.com'),
    'prices' => [
        'monthly' => env('IYZICO_PRICE_MONTHLY', '299.00'),
        'yearly'  => env('IYZICO_PRICE_YEARLY',  '2990.00'),
    ],
];
