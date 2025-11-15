import type { BasePayloadFilter } from "../base.interface";

export interface IPromotionTarget {
  id?: number;
  promotionId?: number;
  productId?: number | null;
  productVariantId?: number | null;
  productCatalogueId?: number | null;
  isGift?: boolean;
  minQuantity?: number;
  maxQuantity?: number | null;
  minValue?: number | null;
  maxValue?: number | null;
  product?: any;
  productVariant?: any;
  productCatalogue?: any;
}

export interface IPromotion {
  id?: number;
  name?: string;
  couponCode?: string | null;
  description?: string | null;
  promotionCatalogueId?: number;
  discountValue?: number;
  discountType?: "percent" | "amount";
  maxDiscountValue?: number | null;
  startDate?: string | null;
  endDate?: string | null;
  priority?: number;
  canCombine?: boolean;
  usedCount?: number;
  publish?: number;
  promotionTargets?: IPromotionTarget[];
  promotionCatalogue?: any;
  createdAt?: string | null;
  updatedAt?: string | null;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface PromotionPayloadFilter extends BasePayloadFilter {
  promotion_catalogue_id?: number | null;
  discount_type?: string | null;
}
