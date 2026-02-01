import type { BasePayloadFilter } from "../base.interface";

export interface IAddressOrder {
  id: number;
  title: string;
  slug: string;
  fullName?: string;
  phone?: string;
  email?: string;
  address?: string;
  street?: string;
  city?: string;
  ward?: string;
  postalCode?: string;
  countryCode?: string;
  isDefault?: boolean;
  userId: number;
  publish: number;
  createdAt?: string;
  updatedAt?: string;
}

export interface Option {
  name: string;
  value: string | number | null;
}

export interface AddressOrderPayloadFilter extends BasePayloadFilter {
  user_id: number | null;
}
