<?php

namespace App\Exceptions\Payment;

use Exception;

/**
 * Exception cơ sở cho Payment module
 * Tất cả payment exception kế thừa từ class này
 */
class PaymentException extends Exception
{
    protected string $errorCode;
    protected array $context;

    public function __construct(
        string $message = 'Lỗi thanh toán',
        string $errorCode = 'PAYMENT_ERROR',
        array $context = [],
        int $code = 400,
        ?\Throwable $previous = null
    ) {
        $this->errorCode = $errorCode;
        $this->context = $context;
        parent::__construct($message, $code, $previous);
    }

    public function getErrorCode(): string
    {
        return $this->errorCode;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
