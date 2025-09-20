import type { BasePayloadFilter } from "../base.interface";

export interface IBanner {
  id: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  type?: string;
  position?: string;
  startAt?: string;
  endAt?: string;
  order?: number;
  isHot?: boolean;
  linkUrl?: string;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export type BannerPayloadFilter = BasePayloadFilter;
