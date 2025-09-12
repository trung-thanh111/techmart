export interface IPostCatalogue {
  id: number;
  parentId?: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  icon?: string;
  order?: number;
  isHot?: boolean;
  publishedAt?: string;
  description?: string;
  publish?: number;
  metaTitle?: string;
  metaKeyword?: string;
  metaDescription?: string;
}

export interface Option {
  name: string;
  value: string | null;
}
export interface PayloadFilter {
  keyword: string;
  perpage: number;
  publish: string | number | null;
}
