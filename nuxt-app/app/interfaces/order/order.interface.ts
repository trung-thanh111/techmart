// Order item for creating orders (minimal)
export interface IOrderItemCreate {
  productId: number;
  productVariantId?: number | null;
  quantity: number;
}

export interface IOrderItemResponse {
  id?: number;
  orderId?: number;
  productId?: number;
  productVariantId?: number | null;
  productSku?: string;
  productName?: string;
  productDescription?: string;
  productAttributes?: string | Record<string, any> | any[];
  productImage?: string | { path?: string; url?: string };
  quantity?: number;
  unitPrice?: number;
  originalPrice?: number;
  discountAmount?: number;
  taxAmount?: number;
  subtotal?: number;
  total?: number;
  warrantyMonths?: number;
  warrantyExpiresAt?: string;
  status?: string;
  isReturnable?: boolean;
  returnDay?: number;
  note?: string;
  createdAt?: string;
  updatedAt?: string;
}

// Customer info object from API
export interface IOrderCustomer {
  name?: string;
  email?: string;
  phone?: string;
}

// Order from API response
export interface IOrder {
  id?: number;
  code?: string;
  userId?: number;
  customer?: IOrderCustomer;
  customerEmail?: string;
  customerPhone?: string;
  customerName?: string;
  subtotal?: number;
  taxAmount?: number;
  shippingFee?: number;
  discountAmount?: number;
  totalAmount?: number;
  currency?: string;
  status?: string;
  paymentStatus?: string;
  paymentMethod?: string;
  paymentGateway?: string;
  transactionId?: string;
  shippingMethod?: string;
  shippingProvider?: string;
  trackingNumber?: string;
  shippingAddress?: string;
  shippingCity?: string;
  shippingWard?: string;
  shippingStreet?: string;
  shippingCountryCode?: string;
  shippingPostalCode?: string;
  customerNote?: string;
  adminNote?: string;
  items?: IOrderItemResponse[];
  statusHistories?: any[];
  payments?: any[];
  shipments?: any[];
  refunds?: any[];
  createdAt?: string;
  updatedAt?: string;
  paidAt?: string;
  confirmedAt?: string;
  shippedAt?: string;
  deliveredAt?: string;
  cancelledAt?: string;
}

// Order group for display (grouped by date)
export interface IOrderGroup {
  dateLabel: string;
  dateKey: string;
  orders: IOrder[];
}

// Tab types for order list
export type OrderTab = "all-orders" | "pending" | "shipping" | "completed" | "canceled" | "returned";

// Backward compatibility aliases
export type IOrderItem = IOrderItemCreate;
