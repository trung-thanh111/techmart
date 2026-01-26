import type { BasePayloadFilter } from "../base.interface";

export interface IAttributeCatalogue {
  id: number;
  name?: string;
  slug?: string;
  order?: number;
  description?: string;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export type CataloguePayloadFilter = BasePayloadFilter;
