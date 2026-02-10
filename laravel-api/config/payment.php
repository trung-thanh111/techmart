<?php

/**
 * Cấu hình Payment Gateways
 *
 * Mỗi gateway có cấu hình riêng, hỗ trợ sandbox/production
 * Thêm gateway mới: thêm key vào 'gateways' array + tạo class implement PaymentGatewayInterface
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Default Payment Gateway
    |--------------------------------------------------------------------------
    | Gateway mặc định khi không chỉ định cụ thể
    */
    'default' => env('PAYMENT_DEFAULT_GATEWAY', 'momo'),

    /*
    |--------------------------------------------------------------------------
    | Payment Gateways Configuration
    |--------------------------------------------------------------------------
    | Cấu hình cho từng payment gateway
    */
    'gateways' => [

        /*
        | MoMo E-Wallet
        */
        'momo' => [
            'partner_code' => env('MOMO_PARTNER_CODE', ''),
            'access_key' => env('MOMO_ACCESS_KEY', ''),
            'secret_key' => env('MOMO_SECRET_KEY', ''),
            'environment' => env('MOMO_ENVIRONMENT', 'sandbox'),
            'endpoints' => [
                'sandbox' => 'https://test-payment.momo.vn',
                'production' => 'https://payment.momo.vn',
            ],
            'api_paths' => [
                'create' => '/v2/gateway/api/create',
                'query' => '/v2/gateway/api/query',
                'refund' => '/v2/gateway/api/refund',
                'confirm' => '/v2/gateway/api/confirm',
            ],
            'request_type' => 'captureWallet', // QR + app + web
            'timeout' => 30, // seconds — MoMo khuyến nghị tối thiểu 30s
            'ipn_url' => env('MOMO_IPN_URL', ''),
            'redirect_url' => env('MOMO_REDIRECT_URL', ''),
        ],

        /*
        | VNPay Payment Gateway
        */
        'vnpay' => [
            'tmn_code' => env('VNPAY_TMN_CODE', ''),
            'hash_secret' => env('VNPAY_HASH_SECRET', ''),
            'environment' => env('VNPAY_ENVIRONMENT', 'sandbox'),
            'endpoints' => [
                'sandbox' => 'https://sandbox.vnpayment.vn',
                'production' => 'https://pay.vnpay.vn',
            ],
            'api_paths' => [
                'create' => '/paymentv2/vpcpay.html',
                'query' => '/merchant_webapi/api/transaction',
                'refund' => '/merchant_webapi/api/transaction',
            ],
            'timeout' => 30,
            'return_url' => env('VNPAY_RETURN_URL', ''),
            'ipn_url' => env('VNPAY_IPN_URL', ''),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Transaction Settings
    |--------------------------------------------------------------------------
    */
    'transaction' => [
        // Thời gian hết hạn thanh toán (phút)
        'expiry_minutes' => env('PAYMENT_EXPIRY_MINUTES', 10),

        // Số lần retry tối đa khi network fail
        'max_retries' => env('PAYMENT_MAX_RETRIES', 3),

        // Thời gian chờ giữa các lần retry (seconds)
        'retry_delay' => env('PAYMENT_RETRY_DELAY', 3),
    ],

    /*
    |--------------------------------------------------------------------------
    | Logging
    |--------------------------------------------------------------------------
    */
    'logging' => [
        // Channel riêng cho payment logs
        'channel' => env('PAYMENT_LOG_CHANNEL', 'payment'),

        // Fields nhạy cảm sẽ bị redact trong log
        'redact_fields' => [
            'secret_key',
            'access_key',
            'signature',
            'card_number',
            'cvv',
        ],
    ],
];
