import type { BasePayloadFilter } from "../base.interface";

export interface IProduct {
  id: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  order?: number;
  isHot?: boolean;
  description?: string;
  content?: string;
  tableOfContent?: boolean;
  viewCount?: number;
  tagId?: number;
  productCatalogueId?: number;
  productCatalogues?: number[];
  tags?: number[];
  relateds?: number[];
  publish?: number;
  metaTitle?: string;
  metaKeyword?: string;
  metaDescription?: string;
  publishedAt?: string | null;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface ProductPayloadFilter extends BasePayloadFilter{
  tag_id?: number | null;
  product_catalogue_id?: number | null;

}
