import { ref, computed, readonly } from 'vue'
import { z } from 'zod'
import { useForm } from 'vee-validate'
import { toTypedSchema } from '@vee-validate/zod'
import type { IPostComment, IPostCommentCreate } from '~/interfaces/post/comment.interface'
import { postCommentService } from '~/services/post/comment.service'
import { buildTree, type TreeNode } from '~/utils/nested-tree'
import { useAppToast } from '~/composables/useAppToast'

const commentSchema = z.object({
  content: z.string({ required_error: 'Nội dung bình luận là bắt buộc' })
    .min(1, 'Nội dung bình luận là bắt buộc')
    .max(5000, 'Nội dung bình luận không được vượt quá 5000 ký tự')
    .trim(),
  postId: z.number({ required_error: 'Post ID là bắt buộc' }).min(1, 'Post ID là bắt buộc'),
  parentId: z.number().nullable().optional(),
  publish: z.number().int().min(0).max(2, 'Trạng thái publish không hợp lệ').default(2)
})

export type TCommentCreate = z.infer<typeof commentSchema>

export const usePostComment = () => {
  const toast = useAppToast()
  
  // State
  const comments = ref<IPostComment[]>([])
  const commentTree = ref<TreeNode<any>[]>([])
  const loading = ref(false)
  const submitting = ref(false)
  const replyingTo = ref<number | null>(null)
  const likedComments = ref<Set<number>>(new Set())

  // Form validation
  const {
    handleSubmit: handleCommentSubmit,
    values: commentForm,
    resetForm: resetCommentForm,
    errors: commentErrors,
    setValues: setCommentValues,
  } = useForm<TCommentCreate>({
    validationSchema: toTypedSchema(commentSchema),
  })

  // Computed
  const totalComments = computed(() => comments.value.length)
  const rootComments = computed(() => commentTree.value)

  // Load comments for a post
  const loadComments = async (postId: number) => {
    loading.value = true
    try {
      const response = await postCommentService.getByPostId(postId)
      if (response.data) {
        comments.value = response.data
        buildCommentTree()
      }
    } catch (error) {
      console.error('Error loading comments:', error)
      toast.error('Không thể tải bình luận')
    } finally {
      loading.value = false
    }
  }

  // Build tree structure from flat comments
  const buildCommentTree = () => {
    commentTree.value = buildTree(comments.value as any[], {
      idKey: 'id',
      parentKey: 'parentId',
      childrenKey: 'replies',
      rootParentValues: [null, 0]
    })
  }

  // Create new comment
  const createComment = handleCommentSubmit(async (payload: TCommentCreate) => {
    submitting.value = true
    try {
      const response = await postCommentService.store({
        post_id: payload.postId,
        parent_id: payload.parentId,
        content: payload.content,
        publish: payload.publish
      } as any)
      if (response.data) {
        await loadComments(payload.postId)
        resetCommentForm()
        toast.success('Bình luận đã được thêm')
        return response.data
      }
    } catch (error) {
      console.error('Error creating comment:', error)
      toast.error('Không thể thêm bình luận')
      throw error
    } finally {
      submitting.value = false
    }
  })

  // Reply to comment
  const replyToComment = async (parentId: number, content: string, postId: number) => {
    setCommentValues({
      postId,
      parentId,
      content,
      publish: 2
    })
    return await createComment()
  }

  // Like/Unlike comment
  const toggleLike = async (commentId: number) => {
    try {
      const isLiked = likedComments.value.has(commentId)
      
      if (isLiked) {
        await postCommentService.unlike(commentId)
        likedComments.value.delete(commentId)
        updateLikeCount(commentId, -1)
      } else {
        await postCommentService.like(commentId)
        likedComments.value.add(commentId)
        updateLikeCount(commentId, 1)
      }
      
    } catch (error) {
      console.error('Error toggling like:', error)
      toast.error('Không thể thực hiện thao tác')
    }
  }

  // Update like count locally
  const updateLikeCount = (commentId: number, delta: number) => {
    const updateComment = (comment: IPostComment) => {
      if (comment.id === commentId) {
        comment.likeCount = (comment.likeCount || 0) + delta
      }
      if (comment.replies) {
        comment.replies.forEach(updateComment)
      }
    }
    
    comments.value.forEach(updateComment)
    buildCommentTree()
  }

  // Check if comment is liked
  const isLiked = (commentId: number) => {
    return likedComments.value.has(commentId)
  }

  // Set replying state
  const setReplying = (commentId: number | null) => {
    replyingTo.value = commentId
  }

  // Get comment count for a specific comment
  const getCommentCount = (comment: IPostComment): number => {
    return comment.commentCount || 0
  }

  // Get like count for a specific comment
  const getLikeCount = (comment: IPostComment): number => {
    return comment.likeCount || 0
  }

  return {
    // State
    comments: readonly(comments),
    commentTree: readonly(commentTree),
    loading: readonly(loading),
    submitting: readonly(submitting),
    replyingTo: readonly(replyingTo),
    
    // Form
    commentForm,
    commentErrors,
    resetCommentForm,
    setCommentValues,
    
    // Computed
    totalComments,
    rootComments,
    
    // Methods
    loadComments,
    createComment,
    replyToComment,
    toggleLike,
    isLiked,
    setReplying,
    getCommentCount,
    getLikeCount
  }
}