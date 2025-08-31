<?php

// config/payments.php (Updated for multiple gateways)

return [
    'default' => env('PAYMENT_DEFAULT', 'fonepay'),
    'gateways' => [
        'fonepay' => [
            'merchant_id' => env('FONEPAY_MERCHANT_ID', 'your_merchant_id'),
            'shared_secret_key' => env('FONEPAY_SHARED_SECRET_KEY', 'your_secret_key'),
            'payment_url' => env('FONEPAY_PAYMENT_URL', 'https://dev-clientapi.fonepay.com/api/merchantRequest'),
            'verify_url' => env('FONEPAY_VERIFY_URL', 'https://dev-merchantapi.fonepay.com/api/merchant/merchantPaymentVerification'),
        ],
        'esewa' => [
            'merchant_code' => env('ESEWA_MERCHANT_CODE', 'EPAYTEST'),
            'secret_key' => env('ESEWA_SECRET_KEY', 'your_secret_key'),
            'base_url' => env('ESEWA_BASE_URL', 'https://rc-epay.esewa.com.np'),
            'failure_url' => env('ESEWA_FAILURE_URL', 'http://localhost/payment/failure'), // Use .env for flexibility
        ],
    ],
];