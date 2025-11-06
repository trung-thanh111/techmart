import { ICartItem } from "~/interfaces/cart/cart.interface";

export interface ICart {
  id?: number;
  customerId?: number | null;
  guestId?: string | null;
  mergedId?: number | null;
  couponCode?: string | null;
  meta?: Record<string, any> | null;
  publish?: number;
  cartItems?: ICartItem[];
  createdAt?: string;
  updatedAt?: string;
}

/**
 * Payload để tạo/cập nhật Cart
 */
export interface CartPayload {
  customerId?: number | null;
  guestId?: string | null;
  mergedId?: number | null;
  couponCode?: string | null;
  meta?: Record<string, any> | null;
  publish: number;
}
