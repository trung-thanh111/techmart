import { STATUS_COLORS, PAYMENT_STATUS_COLORS, ORDER_STATUS_LABELS, ORDER_DELETE_MONTHS } from "./order.constants";
import type { IOrder } from "~/interfaces/order/order.interface";

/**
 * Get CSS class for order status badge
 */
export const getStatusClass = (status?: string): string => {
    if (!status) return "bg-secondary-subtle text-secondary";
    return STATUS_COLORS[status] || "bg-secondary-subtle text-secondary";
};

/**
 * Get CSS class for payment status badge
 */
export const getPaymentStatusClass = (status?: string): string => {
    if (!status) return "bg-secondary-subtle text-secondary";
    return PAYMENT_STATUS_COLORS[status] || "bg-secondary-subtle text-secondary";
};

/**
 * Format order status to display label
 */
export const formatOrderStatus = (status?: string): string => {
    if (!status) return "-";
    return ORDER_STATUS_LABELS[status] || status;
};

/**
 * Check if order can be deleted (created > 12 months ago)
 */
export const canDeleteOrder = (createdAt?: string): boolean => {
    if (!createdAt) return false;
    const orderDate = new Date(createdAt);
    const monthsAgo = new Date();
    monthsAgo.setMonth(monthsAgo.getMonth() - ORDER_DELETE_MONTHS);
    return orderDate < monthsAgo;
};

/**
 * Get days since order created
 */
export const getDaysSinceCreated = (createdAt?: string): number => {
    if (!createdAt) return 0;
    const orderDate = new Date(createdAt);
    const now = new Date();
    const diffTime = Math.abs(now.getTime() - orderDate.getTime());
    return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
};

/**
 * Format full address from order
 */
export const formatAddress = (order?: IOrder): string => {
    if (!order) return "-";
    const parts = [order.shippingStreet, order.shippingWard, order.shippingCity, order.shippingAddress].filter(Boolean);
    return parts.length > 0 ? parts.join(", ") : "-";
};

/**
 * Check if status transition is valid for admin
 */
export const canTransitionStatus = (currentStatus?: string, newStatus?: string): boolean => {
    if (!currentStatus || !newStatus) return false;

    const transitions: Record<string, string[]> = {
        pending: ["confirmed", "cancelled"],
        confirmed: ["processing", "cancelled"],
        processing: ["shipped", "cancelled"],
        shipped: ["delivered"],
        delivered: ["refunded"],
        cancelled: [],
        refunded: [],
        failed: [],
    };

    return transitions[currentStatus]?.includes(newStatus) ?? false;
};

/**
 * Format price with Vietnamese currency
 */
export const formatPrice = (price: number): string => {
    return new Intl.NumberFormat("vi-VN", {
        style: "currency",
        currency: "VND",
    }).format(price);
};

/**
 * Format date to Vietnamese locale
 */
export const formatDate = (dateString?: string): string => {
    if (!dateString) return "-";
    return new Date(dateString).toLocaleDateString("vi-VN", {
        year: "numeric",
        month: "2-digit",
        day: "2-digit",
        hour: "2-digit",
        minute: "2-digit",
    });
};

/**
 * Get remaining days until deletion allowed
 */
export const getDaysUntilDeletable = (createdAt?: string): number => {
    if (!createdAt) return ORDER_DELETE_MONTHS * 30;
    const orderDate = new Date(createdAt);
    const deletableDate = new Date(orderDate);
    deletableDate.setMonth(deletableDate.getMonth() + ORDER_DELETE_MONTHS);
    const now = new Date();
    const diffTime = deletableDate.getTime() - now.getTime();
    return Math.max(0, Math.ceil(diffTime / (1000 * 60 * 60 * 24)));
};
