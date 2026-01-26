import type { BasePayloadFilter } from "../base.interface";

export interface IPosition {
  id: number;
  name?: string;
  slug?: string;
  code?: string;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export type PositionPayloadFilter = BasePayloadFilter;
