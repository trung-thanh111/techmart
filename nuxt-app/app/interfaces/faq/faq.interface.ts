import type { BasePayloadFilter } from "../base.interface";

export interface IFaq {
  id: number;
  question?: string;
  answer?: string;
  slug?: string;
  order?: number;
  isHot?: boolean;
  faqCatalogueId?: number;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface FaqPayloadFilter extends BasePayloadFilter{
  faq_catalogue_id?: number | null;
}
