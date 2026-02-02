import type { BasePayloadFilter } from "../base.interface";

// Kiểu dữ liệu người dùng trả về từ BE
export interface IUser {
  id: number;
  name: string;
  email: string;
  firstName: string | null;
  lastName: string | null;
  description: string | null;
  birthday: string | null;
  gender: string | null;
  phone: string | null;
  street: string | null;
  city: string | null;
  ward: string | null;
  userCatalogueId: number;
  isAdmin: boolean;
  image: { path: string | null; url: string | null };
  addressOrder: any[];
  userCatalogue: any[];
  publish: number | null;
  createdAt: string | null;
  updatedAt: string | null;
  deletedAt: string | null;
  createdAtHuman: string | null;
  updatedAtHuman: string | null;
}

export interface IJWTTokens {
  accessToken: string;
  refreshToken: string | null;
  csrfToken: string;
  expireAt: number;
}

// Response khi login/me thành công (BE trả theo mẫu của bạn)
export interface IAuthSuccessResponse {
  accessToken: string;
  tokenType: string;
  expireAt: number;
  csrfToken: string;
  user: IUser;
}

// State của Auth store (phẳng, dễ truy cập)
export interface IAuthState {
  user: IUser | null;
  accessToken: string | null;
  refreshToken: string | null;
  csrfToken: string | null;
  expireAt: number | null;
  isAuthenticated: boolean;
  isAdmin: boolean;
}

export interface PayloadFilter {
  keyword: string;
  perpage: number;
  publish: string | number | null;
}

export interface UserPayloadFilter extends BasePayloadFilter {
  user_catalogue_id: number | null;
}

export interface ILoginPayload {
  email: string;
  password: string;
  remember?: boolean;
}

export interface IRegisterPayload {
  email: string;
  password: string;
  name: string;
  phone?: string;
  address?: string;
}

export interface IForgotRequest {
  email: string;
}

export interface IForgotVerify {
  email: string;
  otp: string;
}

export interface IForgotReset {
  email: string;
  otp: string;
  password: string;
  password_confirmation: string;
}

export interface IChangePasswordPayload {
  current_password: string;
  password: string;
  password_confirmation: string;
}
