export interface IProductReview {
  id: number;
  product_id: number;
  user_id?: number | null;
  user_name: string;
  user_email?: string | null;
  user_avatar?: string | null;
  rating: number;
  comment: string;
  images?: string[];
  publish: number;
  likes_count: number;
  created_at: string;
  created_at_human?: string;
}

export interface IReviewSummary {
  total_reviews: number;
  average_rating: number;
  star_counts: Record<number, number>;
  star_percentages: Record<number, number>;
}

export interface IProductReviewResponse {
  summary: IReviewSummary;
  reviews: IProductReview[];
  pagination: {
    total: number;
    per_page: number;
    current_page: number;
    last_page: number;
  };
}

export interface IStoreReviewPayload {
  product_id: number;
  rating: number;
  comment: string;
  user_name?: string;
  user_email?: string;
  images?: string[];
}
