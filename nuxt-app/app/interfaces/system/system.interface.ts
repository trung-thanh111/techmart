export interface ISystem {
  id?: number;
  keyword?: string;
  content?: string | null;
  publish?: number;
  userId?: number | null;
  createdAt?: string;
  updatedAt?: string;
}

export interface SystemPayloadFilter {
  keyword?: string | null;
  publish?: number | null;
  perpage?: number;
  page?: number;
}
