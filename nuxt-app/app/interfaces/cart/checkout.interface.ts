export interface IPersonalInfo {
  fullName: string;
  email: string;
  phone: string;
  city: string;
  ward: string;
  street: string;
  customerNote: string;
}

export interface IAddressInfo {
  selectedAddress: number | null;
  shippingMethod: string | null;
}

export interface IPaymentInfo {
  selectedMethod: string;
}

export interface CheckoutFormData {
  personal_info: IPersonalInfo;
  address_info: IAddressInfo;
  payment_method: IPaymentInfo;
}

export type ICheckoutTab = "personal_info" | "address_info" | "payment_method";

export interface IPaymentMethod {
  id: string;
  name: string;
  icon: string;
}

export interface IAddress {
  id: string | number;
  customerName?: string;
  customerPhone?: string;
  label?: string;
  shippingAddress?: string;
  shippingCity?: string;
  shippingWard?: string;
  shippingStreet?: string;
}
