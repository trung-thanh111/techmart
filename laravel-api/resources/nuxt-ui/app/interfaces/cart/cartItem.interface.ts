import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductVariant } from "~/interfaces/product/variant.interface";

export interface ICartItem {
  id?: number;
  cartId?: number;
  purchasableType?: string;
  purchasableId?: number;
  quantity?: number;
  price?: number;
  meta?: Record<string, any> | null;
  publish?: number;
  product?: IProduct;
  variant?: IProductVariant;
  createdAt?: string;
  updatedAt?: string;
}

export interface CartItemPayload {
  cartId: number;
  purchasableType: string;
  purchasableId: number;
  quantity: number;
  price: number;
  meta?: Record<string, any> | null;
  publish: number;
}
