import { ref, computed, readonly } from "vue";
import { z } from "zod";
import { useForm } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import type { IPostComment } from "~/interfaces/post/comment.interface";
import { postCommentService } from "~/services/post/comment.service";
import { buildTree, type TreeNode } from "~/utils/nested-tree";

const schema = z.object({
  content: z.string({ required_error: "Bạn chưa nhập nội dung bình luận" }).min(1, "Nội dung bình luận không được để trống"),
});

export type TCommentRequest = z.infer<typeof schema>;
export const commentValidationSchema = toTypedSchema(schema);

export const usePostComment = () => {
  const comments = ref<IPostComment[]>([]);
  const commentTree = ref<TreeNode<any>[]>([]);
  const isLoading = ref(false);
  const isSubmitting = ref(false);

  const {
    handleSubmit,
    values: commentForm,
    resetForm,
  } = useForm<TCommentRequest>({
    validationSchema: toTypedSchema(schema),
  });

  // analytic
  const totalComments = computed(() => comments.value.length);
  const rootComments = computed(() => commentTree.value);

  // Load comments for post: flattan -> tree
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

  // create cmt
  const createComment = async (postId: number, parentId?: number) => {
    return handleSubmit(async (payload: TCommentRequest) => {
      isSubmitting.value = true;
      try {
        const commentData = {
          postId,
          parentId: parentId || 0,
          content: payload.content,
          publish: 2,
        };

        await postCommentService.store(commentData);
        await loadComments(postId);
        resetForm();

        return true;
      } catch (error: any) {
        throw error;
      } finally {
        isSubmitting.value = false;
      }
    })();
  };

  return {
    comments: readonly(comments),
    commentTree: readonly(commentTree),
    isLoading: readonly(isLoading),
    isSubmitting: readonly(isSubmitting),
    commentForm,
    commentValidationSchema,
    totalComments,
    rootComments,
    loadComments,
    createComment,
    resetForm,
  };
};
