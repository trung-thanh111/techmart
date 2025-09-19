import type { BasePayloadFilter } from "../base.interface";

export interface IAgency {
  id: number;
  name?: string;
  slug?: string;
  logo: { path: string | null; url: string | null };
  description?: string;
  website?: string;
  email?: string;
  phone?: string;
  address?: string;
  iframeMap?: string;
  publish?: number;
  agencyWorkingHours?: number[]
}

export interface Option {
  name: string;
  value: string | null;
}

export type AgencyPayloadFilter = BasePayloadFilter;
