export interface IPostCommentLike {
  id: number;
  commentId: number;
  userId: number;
}

export interface IPostCommentLikeResponse {
  isLiked: boolean;
  likeCount: number;
}
