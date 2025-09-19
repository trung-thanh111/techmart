import type { BasePayloadFilter } from "../base.interface";

export interface IAgencyWorkingHour  {
  id: number;
  dayOfWeek?: string;
  openTime?: string;
  closeTime?: string;
  publish?: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export type AgencyWorkingHourPayloadFilter = BasePayloadFilter
