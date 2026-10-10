import type { BasePayloadFilter } from "../base.interface";

export interface IProductReviewProduct {
    id: number;
    name: string;
    slug: string;
    image?: string | null;
}

export interface IProductReview {
    id: number;
    product_id: number;
    product?: IProductReviewProduct;
    user_id?: number | null;
    user_name: string;
    user_email?: string | null;
    user_avatar?: string | null;
    rating: number;
    comment: string;
    images?: string[];
    publish: number;
    likes_count: number;
    created_at?: string;
    created_at_human?: string;
}

export interface IProductReviewFilter extends BasePayloadFilter {
    rating?: number | string | null;
    product_id?: number | string | null;
}
