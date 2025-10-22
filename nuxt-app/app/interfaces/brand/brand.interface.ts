import type { BasePayloadFilter } from "../base.interface";

export interface IBrand {
  id: number;
  name?: string;
  slug?: string;
  logo: { path: string | null; url: string | null };
  description?: string;
  website?: string;
  country?: string;
  email?: string;
  order?: number;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export type BrandPayloadFilter = BasePayloadFilter;
