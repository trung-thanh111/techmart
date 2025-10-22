export interface IPostLike {
  id: number;
  postId: number;
  userId: number;
}

export interface IPostLikeResponse {
  isLiked: boolean;
  likeCount: number;
}
