export const SHIPPING_METHODS = {
  STANDARD: "standard",
  EXPRESS: "express",
} as const;

export const SHIPPING_FEES = {
  [SHIPPING_METHODS.STANDARD]: 25000,
  [SHIPPING_METHODS.EXPRESS]: 35000,
} as const;

export const PAYMENT_METHODS = {
  COD: "cod",
  VNPAY: "vnpay",
  MOMO: "momo",
} as const;

export const TAX_RATE = 0.1; // 10%

export const ORDER_STATUS_LABELS: Record<string, string> = {
  pending: "Chờ xác nhận",
  confirmed: "Đã xác nhận",
  processing: "Đang xử lý",
  shipped: "Đang giao hàng",
  delivered: "Đã giao hàng",
  cancelled: "Đã hoàn tác (Hủy)",
  refunded: "Đã hoàn tiền",
  failed: "Thất bại",
};

export const PAYMENT_METHOD_LABELS: Record<string, string> = {
  [PAYMENT_METHODS.COD]: "Thanh toán khi nhận hàng",
  [PAYMENT_METHODS.VNPAY]: "Thanh toán qua ví VNPAY",
  [PAYMENT_METHODS.MOMO]: "Thanh toán qua ví MoMo",
};

export const PAYMENT_STATUS_LABELS: Record<string, string> = {
  paid: "Đã thanh toán",
  unpaid: "Chưa thanh toán",
  pending: "Đang xử lý",
  refunded: "Đã hoàn tiền",
};

/** Timeline status configuration with labels and descriptions */
export const STATUS_CONFIG: Record<string, { label: string; description: string }> = {
  pending: { label: "Đặt hàng", description: "Đơn hàng đã được tạo và đang chờ xác nhận" },
  confirmed: { label: "Xác nhận", description: "Đơn hàng đã được xác nhận bởi hệ thống" },
  processing: { label: "Đang xử lý", description: "Đơn hàng đang được đóng gói và chuẩn bị" },
  shipped: { label: "Đang giao", description: "Đơn hàng đã được giao cho đơn vị vận chuyển" },
  delivered: { label: "Hoàn thành", description: "Đơn hàng đã được giao thành công" },
  cancelled: { label: "Đã hủy", description: "Đơn hàng đã bị hủy" },
  failed: { label: "Thất bại", description: "Đơn hàng giao không thành công" },
  refunded: { label: "Hoàn tiền", description: "Đơn hàng đã được hoàn tiền" },
};

/** Order of statuses for timeline display */
export const STATUS_ORDER = ["pending", "confirmed", "processing", "shipped", "delivered"] as const;
