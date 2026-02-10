<?php

namespace App\Services\Payment;

use App\Enums\PaymentEnum;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\MoMoGateway;
use App\Services\Payment\Gateways\VNPayGateway;
use InvalidArgumentException;

/**
 * Factory để tạo Payment Gateway instance
 *
 * Sử dụng:
 *   $factory = app(PaymentGatewayFactory::class);
 *   $gateway = $factory->make('momo');
 *   $result = $gateway->createPayment([...]);
 *
 * Thêm gateway mới:
 *   1. Tạo class implement PaymentGatewayInterface
 *   2. Đăng ký trong $gatewayMap bên dưới
 */
class PaymentGatewayFactory
{
    /**
     * Map tên gateway → class
     * Thêm gateway mới vào đây
     */
    protected array $gatewayMap = [
        PaymentEnum::GATEWAY_MOMO => MoMoGateway::class,
        PaymentEnum::GATEWAY_VNPAY => VNPayGateway::class,
        // PaymentEnum::GATEWAY_STRIPE => StripeGateway::class,
    ];

    /**
     * Cache các gateway instance — tránh khởi tạo lại
     */
    protected array $instances = [];

    /**
     * Tạo hoặc lấy gateway instance
     *
     * @param string|null $gateway Tên gateway ('momo', 'vnpay'...), null = default
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public function make(?string $gateway = null): PaymentGatewayInterface
    {
        $gateway = $gateway ?? config('payment.default', 'momo');

        // Trả về instance đã cache
        if (isset($this->instances[$gateway])) {
            return $this->instances[$gateway];
        }

        // Tìm class tương ứng
        $gatewayClass = $this->gatewayMap[$gateway] ?? null;

        if (!$gatewayClass) {
            throw new InvalidArgumentException(
                "Payment gateway '{$gateway}' không được hỗ trợ. "
                    . "Các gateway khả dụng: " . implode(', ', array_keys($this->gatewayMap))
            );
        }

        // Khởi tạo và cache
        $this->instances[$gateway] = app($gatewayClass);

        return $this->instances[$gateway];
    }

    /**
     * Kiểm tra gateway có tồn tại không
     */
    public function has(string $gateway): bool
    {
        return isset($this->gatewayMap[$gateway]);
    }

    /**
     * Lấy danh sách gateway khả dụng
     */
    public function getAvailableGateways(): array
    {
        return array_keys($this->gatewayMap);
    }
}
