import { ref, computed, readonly } from "vue";
import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { IPostComment } from "~/interfaces/post/comment.interface";
import { postCommentService } from "~/services/post/comment.service";
import { buildTree, type TreeNode } from "~/utils/nested-tree";

// Validation schema for comment creation
const commentSchema = z.object({
  content: z.string({ required_error: "Bạn chưa nhập nội dung bình luận" }).min(1, "Nội dung bình luận không được để trống"),
});

export type TCommentCreate = z.infer<typeof commentSchema>;
export const commentValidationSchema = toTypedSchema(commentSchema);

export const usePostComment = () => {
  const comments = ref<IPostComment[]>([]);
  const commentTree = ref<TreeNode<any>[]>([]);
  const isLoading = ref(false);
  const isSubmitting = ref(false);

  // Form handling
  const {
    handleSubmit,
    values: commentForm,
    resetForm,
  } = useForm<TCommentCreate>({
    validationSchema: toTypedSchema(commentSchema),
  });

  // Computed
  const totalComments = computed(() => comments.value.length);
  const rootComments = computed(() => commentTree.value);

  // Load comments for a post
  const loadComments = async (postId: number) => {
    isLoading.value = true;
    try {
      const response = await postCommentService.allByPostId(postId);
      if (response.data) {
        comments.value = response.data;
        buildCommentTree();
      }
    } catch (error) {
      console.error("Error load comments:", error);
    } finally {
      isLoading.value = false;
    }
  };

  // Build tree structure from flat comments
  const buildCommentTree = () => {
    commentTree.value = buildTree(comments.value as any[], {
      idKey: "id",
      parentKey: "parent_id",
      childrenKey: "replies",
      rootParentValues: [null, 0],
    });
  };

  // Create new comment
  const createComment = async (postId: number, parentId?: number) => {
    return handleSubmit(async (payload: TCommentCreate) => {
      isSubmitting.value = true;
      try {
        const commentData = {
          postId,
          parentId: parentId || 0,
          content: payload.content,
          publish: 2,
        };

        await postCommentService.store(commentData);
        
        // Reload comments after successful creation
        await loadComments(postId);
        
        // Reset form
        resetForm();
        
        return true;
      } catch (error: any) {
        console.error("Error creating comment:", error);
        throw error; // Re-throw để component có thể handle
      } finally {
        isSubmitting.value = false;
      }
    })();
  };

  return {
    // State
    comments: readonly(comments),
    commentTree: readonly(commentTree),
    isLoading: readonly(isLoading),
    isSubmitting: readonly(isSubmitting),

    // Form
    commentForm,
    commentValidationSchema,

    // Computed
    totalComments,
    rootComments,

    // Methods
    loadComments,
    createComment,
    resetForm,
  };
};
