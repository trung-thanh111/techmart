import type { BasePayloadFilter } from "../base.interface";

export interface IPost {
  id: number;
  name?: string;
  slug?: string;
  image: { path: string | null; url: string | null };
  order?: number;
  isHot?: boolean;
  description?: string;
  content?: string;
  tableOfContent?: boolean;
  viewCount?: number;
  tagId?: number;
  postCatalogueId?: number;
  postCatalogues?: number[];
  tags?: number[];
  posts?: number[];
  publish?: number;
  metaTitle?: string;
  metaKeyword?: string;
  metaDescription?: string;
  publishedAt?: string | null;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface PostPayloadFilter extends BasePayloadFilter{
  tag_id?: number | null;
  post_catalogue_id?: number | null;

}
