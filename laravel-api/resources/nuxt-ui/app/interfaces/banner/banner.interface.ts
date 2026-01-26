import type { BasePayloadFilter } from "../base.interface";

export interface IBanner {
  id: number;
  name?: string;
  slug?: string;
  album: { path: string }[];
  pageId?: number;
  positionId?: number;
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
