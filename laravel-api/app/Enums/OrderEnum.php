<?php

namespace App\Enums;

enum OrderEnum: string
{
    // Shipping Methods
    public const SHIPPING_METHOD_STANDARD = 'standard';
    public const SHIPPING_METHOD_EXPRESS = 'express';

    // Shipping Fees
    public const SHIPPING_FEE_STANDARD = 25000;
    public const SHIPPING_FEE_EXPRESS = 35000;

    // Payment Methods
    public const PAYMENT_METHOD_COD = 'cod';
    public const PAYMENT_METHOD_VNPAY = 'vnpay';
    public const PAYMENT_METHOD_MOMO = 'momo';

    // Tax Rate
    public const TAX_RATE = 0.1; // 10%

    // Order Status
    public const STATUS_PENDING = 'pending';
    public const STATUS_CONFIRMED = 'confirmed';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_SHIPPED = 'shipped';
    public const STATUS_DELIVERED = 'delivered';
    public const STATUS_CANCELLED = 'cancelled';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_FAILED = 'failed';

    // Payment Status
    public const PAYMENT_STATUS_UNPAID = 'unpaid';
    public const PAYMENT_STATUS_PENDING = 'pending';
    public const PAYMENT_STATUS_PAID = 'paid';
    public const PAYMENT_STATUS_PARTIALLY_PAID = 'partially_paid';
    public const PAYMENT_STATUS_REFUNDED = 'refunded';
    public const PAYMENT_STATUS_FAILED = 'failed';

    // Available Shipping Methods
    public const AVAILABLE_SHIPPING_METHODS = [
        self::SHIPPING_METHOD_STANDARD,
        self::SHIPPING_METHOD_EXPRESS,
    ];

    // Shipping Fees Map
    public const SHIPPING_FEES = [
        self::SHIPPING_METHOD_STANDARD => self::SHIPPING_FEE_STANDARD,
        self::SHIPPING_METHOD_EXPRESS => self::SHIPPING_FEE_EXPRESS,
    ];

    // Available Payment Methods
    public const AVAILABLE_PAYMENT_METHODS = [
        self::PAYMENT_METHOD_COD,
        self::PAYMENT_METHOD_VNPAY,
        self::PAYMENT_METHOD_MOMO,
    ];

    // Available Statuses
    public const AVAILABLE_STATUSES = [
        self::STATUS_PENDING,
        self::STATUS_CONFIRMED,
        self::STATUS_PROCESSING,
        self::STATUS_SHIPPED,
        self::STATUS_DELIVERED,
        self::STATUS_CANCELLED,
        self::STATUS_REFUNDED,
        self::STATUS_FAILED,
    ];

    // Available Payment Statuses
    public const AVAILABLE_PAYMENT_STATUSES = [
        self::PAYMENT_STATUS_UNPAID,
        self::PAYMENT_STATUS_PENDING,
        self::PAYMENT_STATUS_PAID,
        self::PAYMENT_STATUS_PARTIALLY_PAID,
        self::PAYMENT_STATUS_REFUNDED,
        self::PAYMENT_STATUS_FAILED,
    ];
}
