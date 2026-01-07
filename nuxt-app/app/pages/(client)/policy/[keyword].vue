<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb />
    <section class="w-full overflow-hidden">
      <div class="flex items-center">
        <div class="backdrop-blur-md bg-gray-200/40 p-8 rounded-lg shadow-lg w-full">
          <h1 class="text-xl md:text-3xl font-bold text-transparent bg-clip-text bg-gradient-to-r from-[#007c8b] to-[#00ca9b] mb-2">
            {{ isLoading ? "Đang tải..." : policy ? getPolicyTitle(policy.keyword as any) : "Không tìm thấy!" }}
          </h1>
          <div class="h-1 max-w-64 my-2 relative">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-white via-green-300 to-green-300"></div>
          </div>
          <p class="text-xs md:text-sm leading-6 mt-2 text-gray-900">
            Nếu bạn có thắc mắc liên quan đến chính sách, cách chúng tôi hỗ trợ bạn chi tiết hơn có thể
            <NuxtLink to="#" class="underline text-sky-500">liên hệ ngay</NuxtLink>
            với chúng tôi.
          </p>
        </div>
      </div>
    </section>
    <section class="flex flex-col md:flex-row bg-white shadow-md rounded-lg md:my-5 my-2">
      <SidebarPolicy />
      <main class="flex-1 px-4 md:px-6 lg:px-8 mx-auto">
        <div v-if="isLoading" class="flex items-center justify-center min-h-[400px]">
          <div class="text-center">
            <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-500 mx-auto mb-4"></div>
            <p class="text-gray-600">{{ $t("client.common.loading") }}</p>
          </div>
        </div>
        <div v-else class="max-w-full text-gray-800 text-sm leading-7 md:leading-9">
          <div class="relative mb-4">
            <NuxtImg src="/client/images/bg-title-policy.png" alt="image-policy-bg" loading="lazy" class="w-full h-auto object-cover" />
            <div class="absolute inset-0 flex flex-col justify-center items-center gap-2">
              <div class="text-lg md:text-[24px] font-semibold uppercase">
                {{ isLoading ? "Đang tải..." : policy ? "Chính sách " + getPolicyTitle(policy.keyword as any) : "Không có dữ liệu!" }}
              </div>
              <p class="text-sm text-gray-600">Cập nhật lần cuối: {{ policy ? new Date(policy.updatedAt).toLocaleDateString("vi-VN") : "" }}</p>
            </div>
          </div>
          <div class="prose" v-if="isLoading">
            <div class="text-center text-gray-500">Đang tải nội dung...</div>
          </div>
          <div class="prose" v-else-if="policy && policy.content" v-html="policy.content"></div>
          <div class="prose" v-else-if="error">
            <div class="text-center text-red-500">{{ error }}</div>
          </div>
          <div class="prose" v-else>
            <div class="text-center text-gray-500">Nội dung đang được cập nhật...</div>
          </div>
        </div>
      </main>
    </section>
  </div>
</template>
<script setup lang="ts">
import type { ISystem } from "~/interfaces/system/system.interface";
import { ref, onMounted } from "vue";
import { systemService } from "~/services/system/system.service";
import { getPolicyTitle } from "~/utils/system/policy";
import SidebarPolicy from "~/components/client/navigation/SidebarPolicy.vue";

const route = useRoute();
const router = useRouter();

const policy = ref<ISystem | null>(null);
const isLoading = ref(true);
const error = ref<string | null>(null);

const loadPolicy = async (keyword: string) => {
  try {
    isLoading.value = true;
    error.value = null;
    const response = await systemService.findByKeyword(keyword);
    if (response.data) {
      policy.value = response.data;
    } else {
      error.value = "Không tìm thấy chính sách này";
    }
  } catch (err) {
    console.error("Error loading policy:", err);
    error.value = "Có lỗi xảy ra khi tải dữ liệu";
  } finally {
    isLoading.value = false;
  }
};

onMounted(async () => {
  const keyword = route.params.keyword as string;

  if (!keyword) {
    await router.replace("/policy/privacy_policy");
    return;
  }

  await loadPolicy(keyword);
});

watch(
  () => route.params.keyword,
  async (newKeyword) => {
    if (newKeyword) {
      await loadPolicy(newKeyword as string);
    }
  }
);

definePageMeta({
  layout: "client",
});
</script>
