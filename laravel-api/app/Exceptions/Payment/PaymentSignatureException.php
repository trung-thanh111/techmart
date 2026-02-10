<?php

namespace App\Exceptions\Payment;

/**
 * Exception khi chữ ký số không hợp lệ
 * Dùng khi verify webhook signature thất bại — có thể là tấn công
 */
class PaymentSignatureException extends PaymentException
{
    public function __construct(
        string $gateway = '',
        array $context = [],
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            "Chữ ký không hợp lệ từ gateway: {$gateway}",
            'INVALID_SIGNATURE',
            $context,
            403,
            $previous
        );
    }
}
