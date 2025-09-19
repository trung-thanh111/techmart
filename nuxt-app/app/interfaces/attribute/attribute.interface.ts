import type { BasePayloadFilter } from "../base.interface";

export interface IAttribute {
  id: number;
  name?: string;
  description?: string;
  slug?: string;
  order?: number;
  attributeCatalogueId?: number;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface AttributePayloadFilter extends BasePayloadFilter{
  attribute_catalogue_id?: number | null;
}
