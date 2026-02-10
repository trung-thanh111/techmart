<?php

namespace App\Exceptions\Payment;

/**
 * Exception khi gọi API gateway thất bại
 * Network timeout, gateway trả lỗi, maintenance...
 */
class PaymentGatewayException extends PaymentException
{
    protected ?int $gatewayResultCode;

    public function __construct(
        string $message = 'Lỗi kết nối payment gateway',
        string $gateway = '',
        ?int $gatewayResultCode = null,
        array $context = [],
        ?\Throwable $previous = null
    ) {
        $this->gatewayResultCode = $gatewayResultCode;

        parent::__construct(
            "[{$gateway}] {$message}",
            'GATEWAY_ERROR',
            array_merge($context, [
                'gateway' => $gateway,
                'gateway_result_code' => $gatewayResultCode,
            ]),
            502,
            $previous
        );
    }

    public function getGatewayResultCode(): ?int
    {
        return $this->gatewayResultCode;
    }
}
