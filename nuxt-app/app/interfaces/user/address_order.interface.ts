export interface IAddressOrder {
  id: number;
  title: string,
  slug: string,
  fullName?: string;
  phone?: string;
  email?: string;
  address?: string;
  state?: string;
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
export interface PayloadFilter {
  keyword: string;
  perpage: number;
  publish: string | number | null;
}
