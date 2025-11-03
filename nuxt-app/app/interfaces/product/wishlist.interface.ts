import type { IProductVariant } from "./variant.interface";

export interface IWishlist {
  id?: number;
  userId?: number;
  productVariantId?: number;
  productVariant?: IProductVariant;
}

export interface IWishlistGroupedVariant {
  id: number;
  name: string;
  price: number;
  attributeData?: Record<string, { id: number; name: string }> | null;
  wishlistId?: number;
  productVariantId?: number;
}

export interface IWishlistGroupedProduct {
  id: number;
  name: string;
  slug: string;
  sku?: string;
  priceOrigin: number;
  album: string | null;
  season?: string | null;
  warranty?: string | null;
  sortOrder?: number;
  isHot?: boolean;
  likeCount?: number;
  productCatalogueId?: number;
  brandId?: number;
}

export interface IWishlistGrouped {
  id: number; // wishlist id
  userId: number;
  productVariantId: number;
  product: IWishlistGroupedProduct & {
    variant: IWishlistGroupedVariant;
  };
}
