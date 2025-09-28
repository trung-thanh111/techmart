import type { BasePayloadFilter } from "../base.interface";

export interface IPostComment {
  id: number;
  parentId: number | null;
  content?: string;
  likeCount?: number;
  commentCount?: number;
  postId?: number;
  publish?: number;
  author?: ICreator;
  replies?: IPostComment[];
  createdAt?: string;
  updatedAt?: string;
}

export interface ICreator {
  id: number;
  name: string;
  avatar: {
    path?: string;
    url?: string;
  };
}

export interface IPostCommentCreate {
  postId: number;
  parentId?: number | null;
  content: string;
  publish: number;
}

export interface Option {
  name: string;
  value: string | null;
}

export interface IPostCommentPayloadFilter extends BasePayloadFilter {
  post_id?: number | null;
}
