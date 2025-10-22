import type { BasePayloadFilter } from "../base.interface";

export interface IProductCatalogue {
  id: number;
  parentId?: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  order?: number;
  isHot?: boolean;
  description?: string;
  publish?: number;
  metaTitle?: string;
  metaKeyword?: string;
  metaDescription?: string;
}

export interface Option {
  name: string;
  value: string | null;
}

export type CataloguePayloadFilter = BasePayloadFilter;
