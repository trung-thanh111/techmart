<template>
  <div class="bg-white border-gray-200 p-4 rounded-lg shadow-sm">
    <div class="flex items-center justify-between mb-2">
      <div class="flex items-center space-x-3">
        <NuxtImg class="w-8 h-8 rounded-full" :src="avatar" :alt="node.author?.name || 'User'" loading="lazy" />
        <div>
          <p class="text-sm font-semibold text-gray-800">{{ node.author?.name || "Anonymous" }}</p>
          <p class="text-xs text-gray-500">{{ node.createdAtHuman }}</p>
        </div>
      </div>
      <div class="relative inline-block text-left group">
        <button class="text-gray-400 hover:bg-gray-200 p-2 rounded">
          <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M6 10a2 2 0 1 1-4 0 2 2 0 0 1 4 0Zm6-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Zm4-2a2 2 0 1 0 0 4 2 2 0 0 0 0-4Z" /></svg>
        </button>
        <div class="absolute top-full right-0 w-full h-3 group-hover:block hidden z-0"></div>
        <div class="absolute right-0 z-10 mt-2 w-48 origin-top-right divide-y divide-gray-100 rounded-md bg-white shadow-lg ring-1 ring-black/5 hidden group-hover:block transition-all duration-300 ease-in-out">
          <div class="w-full bg-white rounded-md border-0 shadow-xl font-normal ring-black/10 divide-y divide-gray-100">
            <div class="py-1">
              <button @click="replyToComment" class="flex items-center w-full px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                <i class="fa-solid fa-reply text-gray-400 text-sm mr-2"></i>
                <span>Trả lời</span>
              </button>
              <button v-if="canHide" @click="hideComment" class="flex items-center w-full px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                <i class="fas fa-eye-slash text-gray-400 text-sm mr-2"></i>
                <span>Ẩn bình luận</span>
              </button>
              <button class="flex items-center w-full px-4 py-2 text-xs text-gray-700 hover:bg-gray-100">
                <i class="fa-solid fa-circle-exclamation text-gray-400 text-sm mr-2"></i>
                <span>Báo cáo</span>
              </button>
            </div>
            <div v-if="canDelete" class="border-t border-gray-200">
              <div class="py-1">
                <button @click="openDeleteModal" class="flex items-center w-full px-4 py-2 text-xs text-red-700 hover:bg-red-100">
                  <i class="fa-solid fa-trash text-sm mr-2"></i>
                  <span>Xóa</span>
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <p class="text-gray-700 text-sm mb-3">{{ node.content }}</p>
    <div class="flex items-center space-x-6 text-gray-500 text-xs">
      <button @click="onToggleLike" class="flex items-center space-x-1 hover:text-red-500 transition-colors">
        <svg v-if="state.isLiked" class="w-4 h-4 text-red-500" fill="currentColor" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24">
          <path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z" />
        </svg>
        <svg v-else class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
        <span>{{ state.likeCount }} thích</span>
      </button>
      <span v-if="replyCount > 0" class="text-gray-500">bình luận ({{ replyCount }})</span>
      <button @click="replyToComment" class="hover:underline hover:text-sky-500">trả lời</button>
    </div>

    <!-- đệ quy chính nó -> hiển thị cmt reply -->
    <div v-if="node.replies && node.replies.length" class="ml-8 space-y-4 mt-4">
      <CommentTree v-for="child in node.replies" :key="child.id" :node="child" @refresh="emit('refresh')" @reply="emit('reply', $event)" />
    </div>

    <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
      <div class="absolute inset-0 bg-black/50" @click="closeDeleteModal"></div>
      <div class="relative bg-white rounded-lg shadow-lg w-full max-w-xl">
        <div class="grid grid-cols-[48px_1fr] gap-4 p-5">
          <div class="flex items-start justify-center">
            <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
              <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
              </svg>
            </div>
          </div>
          <div>
            <h3 class="text-base font-semibold text-gray-900">Xóa bình luận</h3>
            <p class="mt-2 text-sm text-gray-600">Bạn có chắc chắn muốn xóa bình luận này?</p>
            <div class="mt-4">
              <p class="text-[12px] text-gray-500 mb-1">Bình luận</p>
              <input :value="node.content" type="text" class="w-full border border-red-300 rounded px-3 py-2 text-sm text-red-600 bg-red-50" readonly />
            </div>
            <div class="mt-5 flex justify-end gap-3">
              <button type="button" class="px-4 py-1 rounded border border-gray-300 bg-white text-gray-700 text-xs hover:bg-gray-50" @click="closeDeleteModal">Hủy</button>
              <button type="button" class="px-4 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700 disabled:opacity-50" @click="deleteComment">Xóa</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { defineProps } from "vue";
import { useAuth } from "~/composables/auth/useAuth";
import { postCommentService } from "~/services/post/comment.service";
import { useAppToast } from "~/composables/useAppToast";
import { usePostCommentLike } from "~/composables/client/post/usePostCommentLike";

const props = defineProps<{ node: any }>();
const { isAdmin, user } = useAuth();
const toast = useAppToast();

const showDeleteModal = ref(false);
const { commentLikeState, getStatus, toggle, resetFor } = usePostCommentLike();
const state = computed(() => commentLikeState[props.node.id] || { isLiked: false, likeCount: props.node.like_count || 0, currentLikeId: null });
const DEFAULT = "/shared/images/defaults/avatar.jpg";
const avatar = props.node.author?.avatar?.path ? props.node.author?.avatar?.url : DEFAULT;

// Check permission for hide/public
const isAuthor = computed(() => {
  return user.value?.id === props.node.author?.id;
});
const canHide = computed(() => isAdmin.value);
const canDelete = computed(() => isAdmin.value || isAuthor.value);

const hideComment = async () => {
  try {
    await postCommentService.update(props.node.id, { publish: 0 });
    toast.success("Đã ẩn bình luận");
    emit("refresh"); // lăng nghe sk refresh báo cho cha biết reload lại cmt
  } catch (error) {
    toast.error("Không thể ẩn bình luận");
  }
};

// đếm reply
const replyCount = computed(() => {
  const countReplies = (node: any): number => {
    if (!node.replies || !Array.isArray(node.replies)) return 0;
    let count = node.replies.length;
    node.replies.forEach((reply: any) => {
      count += countReplies(reply);
    });
    return count;
  };
  return countReplies(props.node);
});

const openDeleteModal = () => {
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
};

const deleteComment = async () => {
  try {
    await postCommentService.delete(props.node.id);
    toast.success("Đã xóa bình luận");
    closeDeleteModal();
    emit("refresh");
  } catch (error) {
    toast.error("Không thể xóa bình luận");
  }
};

const onToggleLike = async () => {
  const ok = await toggle(props.node.id);
  if (!ok) {
    toast.error("Không thể thực hiện thao tác");
  }
};

const replyToComment = () => {
  emit("reply", props.node.id);
};

const emit = defineEmits<{
  refresh: [];
  reply: [parentId: number];
}>();

onMounted(async () => {
  resetFor(props.node.id);
  await getStatus(props.node.id);
});

watch(
  () => props.node.id,
  async (id) => {
    if (!id) return;
    resetFor(id);
    await getStatus(id);
  }
);
</script>
