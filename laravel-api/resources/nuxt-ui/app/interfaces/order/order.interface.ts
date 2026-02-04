import type { BasePayloadFilter } from "../base.interface";

// Main Order interface - from OrderResource
export interface IOrder {
    id?: number;
    code?: string;
    userId?: number;

    // Customer info
    customer?: {
        name?: string;
        email?: string;
        phone?: string;
    };

    // Pricing
    subtotal?: number;
    taxAmount?: number;
    shippingFee?: number;
    discountAmount?: number;
    totalAmount?: number;
    currency?: string;

    // Status
    status?: string;
    paymentStatus?: string;
    paymentMethod?: string;
    paymentGateway?: string;
    transactionId?: string;

    // Shipping method
    shippingMethod?: string;
    shippingProvider?: string;
    trackingNumber?: string;

    // Shipping address
    shippingAddress?: string;
    shippingCity?: string;
    shippingWard?: string;
    shippingStreet?: string;
    shippingCountryCode?: string;
    shippingPostalCode?: string;

    // Notes
    customerNote?: string;
    adminNote?: string;

    // Relations
    items?: IOrderItem[];
    statusHistories?: IOrderStatusHistory[];
    payments?: IOrderPayment[];
    shipments?: IOrderShipment[];
    refunds?: IOrderRefund[];

    // Timestamps
    confirmedAt?: string;
    paidAt?: string;
    shippedAt?: string;
    deliveredAt?: string;
    cancelledAt?: string;
    createdAt?: string;
    updatedAt?: string;
}

// Order Item - from OrderItemResource
export interface IOrderItem {
    id?: number;
    orderId?: number;
    productId?: number;
    productVariantId?: number;
    productSku?: string;
    productName?: string;
    productDescription?: string;
    productAttributes?: Record<string, any>;
    productImage?: {
        path?: string;
        url?: string;
    };
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

// Order Status History - from OrderStatusHistoryResource
export interface IOrderStatusHistory {
    id?: number;
    orderId?: number;
    oldStatus?: string;
    newStatus?: string;
    userId?: number;
    userType?: string;
    user?: {
        id: number;
        name: string;
        email?: string;
    };
    note?: string;
    metaData?: Record<string, any>;
    createdAt?: string;
    updatedAt?: string;
}

// Order Payment - from OrderPaymentResource
export interface IOrderPayment {
    id?: number;
    orderId?: number;
    transactionId?: string;
    paymentMethod?: string;
    paymentGateway?: string;
    amount?: number;
    currency?: string;
    status?: string;
    cardBrand?: string;
    cardLast4?: string;
    gatewayResponse?: Record<string, any>;
    failureReason?: string;
    paidAt?: string;
    refundedAt?: string;
    createdAt?: string;
    updatedAt?: string;
}

// Order Shipment - from OrderShipmentResource
export interface IOrderShipment {
    id?: number;
    orderId?: number;
    trackingNumber?: string;
    carrier?: string;
    serviceType?: string;
    status?: string;
    weight?: number;
    dimensions?: string;
    packageCount?: number;
    shippingCost?: number;
    estimatedDeliveryAt?: string;
    pickedUpAt?: string;
    deliveredAt?: string;
    receiverName?: string;
    deliveryNote?: string;
    trackingData?: Record<string, any>;
    createdAt?: string;
    updatedAt?: string;
}

// Order Refund - from OrderRefundResource
export interface IOrderRefund {
    id?: number;
    orderId?: number;
    paymentId?: number;
    refundNumber?: string;
    amount?: number;
    currency?: string;
    reason?: string;
    reasonDescription?: string;
    status?: string;
    approvedBy?: number;
    approvedAt?: string;
    refundItems?: Record<string, any>;
    createdAt?: string;
    updatedAt?: string;
}

// Filter payload for pagination
export interface OrderPayloadFilter extends BasePayloadFilter {
    status?: string | null;
    paymentStatus?: string | null;
    paymentMethod?: string | null;
    dateFrom?: string | null;
    dateTo?: string | null;
}

// Update payload
export interface OrderUpdatePayload {
    status?: string;
    adminNote?: string;
    shippingStreet?: string;
    shippingWard?: string;
    shippingCity?: string;
    trackingNumber?: string;
    shippingProvider?: string;
}
