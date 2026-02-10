<?php

namespace App\Enums;

/**
 * Enum cho Payment module
 * Tập trung tất cả constants liên quan đến thanh toán
 */
enum PaymentEnum: string
{
    // ============================================================
    // Gateway Names
    // ============================================================
    public const GATEWAY_MOMO = 'momo';
    public const GATEWAY_VNPAY = 'vnpay';
    public const GATEWAY_STRIPE = 'stripe';

    public const AVAILABLE_GATEWAYS = [
        self::GATEWAY_MOMO,
        self::GATEWAY_VNPAY,
    ];

    // ============================================================
    // Transaction Types
    // ============================================================
    public const TYPE_PAYMENT = 'payment';
    public const TYPE_REFUND = 'refund';
    public const TYPE_QUERY = 'query';

    // ============================================================
    // Transaction Statuses
    // ============================================================
    public const STATUS_PENDING = 'pending';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_FAILED = 'failed';
    public const STATUS_REFUNDED = 'refunded';
    public const STATUS_EXPIRED = 'expired';
    public const STATUS_CANCELLED = 'cancelled';

    // ============================================================
    // MoMo Result Codes
    // ============================================================
    public const MOMO_SUCCESS = 0;
    public const MOMO_PENDING = 1000;
    public const MOMO_FAILED = 1001;
    public const MOMO_REJECTED = 1002;
    public const MOMO_DUPLICATE = 1003;
    public const MOMO_AMOUNT_LIMIT = 1004;
    public const MOMO_INVALID_URL = 1005;
    public const MOMO_INVALID_TRANS = 1006;
    public const MOMO_MAINTENANCE = 10;
    public const MOMO_ACCESS_DENIED = 11;
    public const MOMO_UNSUPPORTED_API = 12;
    public const MOMO_MERCHANT_AUTH_FAILED = 13;
    public const MOMO_BAD_FORMAT = 20;
    public const MOMO_INVALID_AMOUNT = 21;
    public const MOMO_INVALID_REQUEST_ID = 40;
    public const MOMO_INVALID_ORDER_ID = 41;
    public const MOMO_INVALID_SIGNATURE = 44;

    /**
     * Map MoMo result code sang message tiếng Việt
     */
    public const MOMO_RESULT_MESSAGES = [
        self::MOMO_SUCCESS => 'Giao dịch thành công',
        self::MOMO_PENDING => 'Giao dịch đang chờ xử lý',
        self::MOMO_FAILED => 'Giao dịch thất bại',
        self::MOMO_REJECTED => 'Giao dịch bị từ chối',
        self::MOMO_DUPLICATE => 'Giao dịch trùng lặp (duplicate requestId)',
        self::MOMO_AMOUNT_LIMIT => 'Số tiền thanh toán không hợp lệ',
        self::MOMO_INVALID_URL => 'URL không hợp lệ',
        self::MOMO_INVALID_TRANS => 'Giao dịch không tồn tại',
        self::MOMO_MAINTENANCE => 'Hệ thống MoMo đang bảo trì',
        self::MOMO_ACCESS_DENIED => 'Truy cập bị từ chối',
        self::MOMO_UNSUPPORTED_API => 'Phiên bản API không được hỗ trợ',
        self::MOMO_MERCHANT_AUTH_FAILED => 'Xác thực merchant thất bại',
        self::MOMO_BAD_FORMAT => 'Sai định dạng request',
        self::MOMO_INVALID_AMOUNT => 'Số tiền không hợp lệ',
        self::MOMO_INVALID_REQUEST_ID => 'requestId không hợp lệ',
        self::MOMO_INVALID_ORDER_ID => 'orderId không hợp lệ',
        self::MOMO_INVALID_SIGNATURE => 'Chữ ký không hợp lệ',
    ];

    /**
     * Lấy message tiếng Việt từ MoMo result code
     */
    public static function getMoMoResultMessage(int $resultCode): string
    {
        return self::MOMO_RESULT_MESSAGES[$resultCode]
            ?? "Lỗi không xác định từ MoMo (code: {$resultCode})";
    }

    /**
     * Kiểm tra MoMo result code có phải thành công không
     */
    public static function isMoMoSuccess(int $resultCode): bool
    {
        return $resultCode === self::MOMO_SUCCESS;
    }

    // VNPay Response Codes
    public const VNPAY_SUCCESS = '00';
    public const VNPAY_SUSPECT = '07'; // Giao dịch bị nghi ngờ
    public const VNPAY_NOT_REGISTERED = '09'; // Chưa đăng ký InternetBanking
    public const VNPAY_AUTH_FAILED = '10'; // Xác thực thất bại > 3 lần
    public const VNPAY_TIMEOUT = '11'; // Hết hạn chờ thanh toán
    public const VNPAY_LOCKED = '12'; // Tài khoản bị khóa
    public const VNPAY_WRONG_OTP = '13'; // Sai OTP
    public const VNPAY_CANCELLED = '24'; // Khách hủy giao dịch
    public const VNPAY_INSUFFICIENT = '51'; // Không đủ số dư
    public const VNPAY_LIMIT_EXCEEDED = '65'; // Vượt hạn mức
    public const VNPAY_BANK_MAINTENANCE = '75'; // Ngân hàng bảo trì
    public const VNPAY_WRONG_PASSWORD = '79'; // Sai mật khẩu thanh toán
    public const VNPAY_UNKNOWN = '99'; // Lỗi không xác định

    public const VNPAY_RESULT_MESSAGES = [
        self::VNPAY_SUCCESS => 'Giao dịch thành công',
        self::VNPAY_SUSPECT => 'Trừ tiền thành công. Giao dịch bị nghi ngờ gian lận',
        self::VNPAY_NOT_REGISTERED => 'Thẻ/Tài khoản chưa đăng ký InternetBanking',
        self::VNPAY_AUTH_FAILED => 'Xác thực thất bại quá 3 lần',
        self::VNPAY_TIMEOUT => 'Hết hạn chờ thanh toán',
        self::VNPAY_LOCKED => 'Tài khoản bị khóa',
        self::VNPAY_WRONG_OTP => 'Nhập sai mật khẩu xác thực (OTP)',
        self::VNPAY_CANCELLED => 'Khách hàng hủy giao dịch',
        self::VNPAY_INSUFFICIENT => 'Tài khoản không đủ số dư',
        self::VNPAY_LIMIT_EXCEEDED => 'Vượt quá hạn mức giao dịch trong ngày',
        self::VNPAY_BANK_MAINTENANCE => 'Ngân hàng thanh toán đang bảo trì',
        self::VNPAY_WRONG_PASSWORD => 'Nhập sai mật khẩu thanh toán quá số lần quy định',
        self::VNPAY_UNKNOWN => 'Lỗi không xác định',
    ];

    public static function getVNPayResultMessage(string $responseCode): string
    {
        return self::VNPAY_RESULT_MESSAGES[$responseCode]
            ?? "Lỗi không xác định từ VNPay (code: {$responseCode})";
    }

    public static function isVNPaySuccess(string $responseCode): bool
    {
        return $responseCode === self::VNPAY_SUCCESS;
    }
}
