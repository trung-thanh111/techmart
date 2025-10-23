import type { BasePayloadFilter } from "../base.interface";
import type { IProductVariant } from "./variant.interface";

export interface IProduct {
  id?: number;
  name?: string;
  slug?: string;
  sku?: string;
  sortOrder?: number;
  isHot?: boolean;
  description?: string;
  content?: string;
  priceOrigin?: number;
  album?: { path: string }[];
  season?: "spring" | "summer" | "autumn" | "winter" | "none";
  warranty?: string;
  viewCount?: number;
  likeCount?: number;
  tagId?: number;
  productCatalogueId?: number;
  brandId?: number;
  faqId?: number;
  userId?: number;
  productCatalogues?: number[];
  productVariants?: IProductVariant[];
  productTags?: number[];
  relateds?: number[];
  publish?: number;
  metaTitle?: string;
  metaKeyword?: string;
  metaDescription?: string;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface ProductPayloadFilter extends BasePayloadFilter {
  tag_id?: number | null;
  product_catalogue_id?: number | null;
  brand_id?: number | null;
}
