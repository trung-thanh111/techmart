import type { BasePayloadFilter } from "../base.interface";

export interface IFaqCatalogue {
  id: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  order?: number;
  isHot?: boolean;
  description?: string;
  publish?: number;
  faqCatalogues?: number[] 
}

export interface Option {
  name: string;
  value: string | null;
}

export type CataloguePayloadFilter = BasePayloadFilter;
