// Order Status Labels
export const ORDER_STATUS_LABELS: Record<string, string> = {
    pending: "Chờ xử lý",
    confirmed: "Đã xác nhận",
    processing: "Đang xử lý",
    shipped: "Đang giao",
    delivered: "Đã giao",
    cancelled: "Đã hủy",
    refunded: "Đã hoàn tiền",
    failed: "Thất bại",
};

// Order Status Options for dropdown
export const ORDER_STATUS_OPTIONS = [
    { label: "Chờ xử lý", value: "pending" },
    { label: "Đã xác nhận", value: "confirmed" },
    { label: "Đang xử lý", value: "processing" },
    { label: "Đang giao", value: "shipped" },
    { label: "Đã giao", value: "delivered" },
    { label: "Đã hủy", value: "cancelled" },
    { label: "Đã hoàn tiền", value: "refunded" },
    { label: "Thất bại", value: "failed" },
];

// Admin allowed status transitions
export const ADMIN_ALLOWED_STATUS = ["confirmed", "processing", "shipped", "delivered", "cancelled"];

// Payment Status Labels
export const PAYMENT_STATUS_LABELS: Record<string, string> = {
    unpaid: "Chưa thanh toán",
    pending: "Chờ xử lý",
    processing: "Đang xử lý",
    paid: "Đã thanh toán",
    completed: "Đã thanh toán",
    partially_paid: "Thanh toán 1 phần",
    refunded: "Đã hoàn tiền",
    failed: "Thất bại",
    cancelled: "Đã hủy",
};

// Payment Status Options
export const PAYMENT_STATUS_OPTIONS = [
    { label: "Chưa thanh toán", value: "unpaid" },
    { label: "Chờ xử lý", value: "pending" },
    { label: "Đã thanh toán", value: "paid" },
    { label: "Thanh toán 1 phần", value: "partially_paid" },
    { label: "Đã hoàn tiền", value: "refunded" },
    { label: "Thất bại", value: "failed" },
];

// Payment Method Labels
export const PAYMENT_METHOD_LABELS: Record<string, string> = {
    cod: "Thanh toán khi nhận hàng",
    bank_transfer: "Chuyển khoản ngân hàng",
    credit_card: "Thẻ tín dụng",
    momo: "Ví MoMo",
    vnpay: "VNPay",
    zalopay: "ZaloPay",
};

// Payment Method Options
export const PAYMENT_METHOD_OPTIONS = [
    { label: "Thanh toán khi nhận hàng", value: "cod" },
    { label: "Chuyển khoản ngân hàng", value: "bank_transfer" },
    { label: "Thẻ tín dụng", value: "credit_card" },
    { label: "Ví MoMo", value: "momo" },
    { label: "VNPay", value: "vnpay" },
    { label: "ZaloPay", value: "zalopay" },
];

// Shipping Method Labels
export const SHIPPING_METHOD_LABELS: Record<string, string> = {
    standard: "Tiêu chuẩn",
    express: "Hỏa tốc",
    fast: "Nhanh",
    saving: "Tiết kiệm",
};

// Status Colors for badges
export const STATUS_COLORS: Record<string, string> = {
    pending: "bg-warning-subtle text-warning",
    confirmed: "bg-info-subtle text-info",
    processing: "bg-primary-subtle text-primary",
    shipped: "bg-secondary-subtle text-secondary",
    delivered: "bg-success-subtle text-success",
    cancelled: "bg-danger-subtle text-danger",
    refunded: "bg-warning-subtle text-warning",
    failed: "bg-danger-subtle text-danger",
};

// Payment Status Colors
export const PAYMENT_STATUS_COLORS: Record<string, string> = {
    unpaid: "bg-light text-muted border",
    pending: "bg-warning-subtle text-warning",
    processing: "bg-primary-subtle text-primary",
    paid: "bg-success-subtle text-success",
    completed: "bg-success-subtle text-success",
    partially_paid: "bg-info-subtle text-info",
    refunded: "bg-secondary-subtle text-secondary",
    failed: "bg-danger-subtle text-danger",
    cancelled: "bg-dark-subtle text-dark",
};

// Order status flow order
export const STATUS_ORDER = ["pending", "confirmed", "processing", "shipped", "delivered"] as const;

// Shipment Status Labels
export const SHIPMENT_STATUS_LABELS: Record<string, string> = {
    pending: "Chờ lấy hàng",
    picked_up: "Đã lấy hàng",
    in_transit: "Đang luân chuyển",
    out_for_delivery: "Đang giao hàng",
    delivered: "Giao thành công",
    failed: "Giao thất bại",
    returned: "Đã trả về",
};

// Shipment Service Type Labels
export const SHIPMENT_SERVICE_LABELS: Record<string, string> = {
    standard: "Tiêu chuẩn",
    express: "Hỏa tốc",
    fast: "Nhanh",
    saving: "Tiết kiệm",
};

// Refund Status Labels
export const REFUND_STATUS_LABELS: Record<string, string> = {
    pending: "Chờ duyệt",
    approved: "Đã duyệt",
    processing: "Đang xử lý",
    completed: "Hoàn tiền thành công",
    rejected: "Từ chối",
};

// Refund Reason Labels
export const REFUND_REASON_LABELS: Record<string, string> = {
    customer_request: "Khách yêu cầu",
    defective_product: "Sản phẩm lỗi",
    wrong_product: "Giao sai hàng",
    not_as_described: "Không giống mô tả",
    damaged: "Hư hỏng do vận chuyển",
    other: "Khác",
};

// Delete restriction months
export const ORDER_DELETE_MONTHS = 12;
