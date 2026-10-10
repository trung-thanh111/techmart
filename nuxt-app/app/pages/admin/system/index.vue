<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-6 rounded-xl border border-gray-200/80 shadow-sm">
      <div>
        <div class="flex items-center gap-2 text-xs text-gray-500 mb-1">
          <NuxtLink to="/" class="hover:underline">Trang chủ</NuxtLink>
          <i class="pi pi-angle-right text-[10px]"></i>
          <span class="text-gray-700 font-medium">Quản trị hệ thống</span>
          <i class="pi pi-angle-right text-[10px]"></i>
          <span class="text-[#007c8b] font-medium">Cấu hình động</span>
        </div>
        <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-2">
          <i class="pi pi-sliders-h text-[#00b88c]"></i>
          <span>Cấu hình Hệ Thống Động</span>
        </h1>
        <p class="text-sm text-gray-500 mt-1">
          Quản lý thông tin doanh nghiệp, hotline, logo, SEO và mã nhúng toàn hệ thống mà không cần sửa mã nguồn.
        </p>
      </div>

      <div class="flex items-center gap-3">
        <button
          type="button"
          @click="fetchStructure"
          :disabled="loading || saving"
          class="px-4 py-2 border border-gray-300 hover:bg-gray-50 text-gray-700 text-sm font-medium rounded-lg transition-colors flex items-center gap-2 disabled:opacity-50"
        >
          <i :class="['pi pi-refresh text-xs', { 'animate-spin': loading }]"></i>
          <span>Tải lại</span>
        </button>

        <button
          type="button"
          @click="handleSave"
          :disabled="loading || saving"
          class="px-5 py-2 bg-gradient-to-r from-[#007c8b] to-[#00b88c] hover:opacity-95 text-white text-sm font-semibold rounded-lg shadow-sm transition-all flex items-center gap-2 disabled:opacity-50 cursor-pointer"
        >
          <i :class="['pi', saving ? 'pi-spin pi-spinner' : 'pi-check']"></i>
          <span>{{ saving ? "Đang lưu..." : "Lưu cấu hình" }}</span>
        </button>
      </div>
    </div>

    <!-- Loading State -->
    <div v-if="loading" class="bg-white p-12 rounded-xl border border-gray-200 text-center space-y-3">
      <i class="pi pi-spin pi-spinner text-3xl text-[#00b88c]"></i>
      <p class="text-sm text-gray-500">Đang tải cấu trúc cấu hình hệ thống...</p>
    </div>

    <!-- Error State -->
    <div v-else-if="error" class="bg-red-50 p-6 rounded-xl border border-red-200 text-center space-y-3">
      <i class="pi pi-exclamation-triangle text-3xl text-red-500"></i>
      <p class="text-sm font-medium text-red-700">{{ error }}</p>
      <button
        @click="fetchStructure"
        class="px-4 py-2 bg-red-600 text-white rounded-lg text-xs font-semibold hover:bg-red-700"
      >
        Thử lại
      </button>
    </div>

    <!-- Main Settings Form with Tabs -->
    <div v-else class="bg-white rounded-xl border border-gray-200/80 shadow-sm overflow-hidden">
      <!-- Tabs Header -->
      <div class="border-b border-gray-200 bg-gray-50/60 px-4 flex flex-wrap gap-2">
        <button
          v-for="(group, key) in structure"
          :key="key"
          type="button"
          @click="activeTab = key"
          :class="[
            'px-5 py-3.5 text-sm font-medium transition-all border-b-2 flex items-center gap-2',
            activeTab === key
              ? 'border-[#00b88c] text-[#007c8b] bg-white font-semibold'
              : 'border-transparent text-gray-600 hover:text-gray-900 hover:border-gray-300',
          ]"
        >
          <i :class="getGroupIcon(key)"></i>
          <span>{{ group.label }}</span>
          <span class="text-[11px] bg-gray-200 text-gray-700 px-1.5 py-0.5 rounded-full font-mono">
            {{ Object.keys(group.fields || {}).length }}
          </span>
        </button>
      </div>

      <!-- Tab Content Panel -->
      <div class="p-6 sm:p-8">
        <div v-for="(group, groupKey) in structure" :key="groupKey" v-show="activeTab === groupKey">
          <div class="mb-6 pb-4 border-b border-gray-100">
            <h2 class="text-lg font-bold text-gray-800">{{ group.label }}</h2>
            <p v-if="group.description" class="text-xs text-gray-500 mt-0.5">
              {{ group.description }}
            </p>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div
              v-for="(field, fieldKey) in group.fields"
              :key="fieldKey"
              :class="[
                'space-y-1.5',
                field.type === 'textarea' ? 'md:col-span-2' : '',
              ]"
            >
              <label class="block text-xs font-semibold text-gray-700 uppercase tracking-wider">
                {{ field.label }}
              </label>

              <!-- Textarea field (e.g. script, intro, map iframe, meta description) -->
              <template v-if="field.type === 'textarea'">
                <textarea
                  v-model="formValues[field.keyword]"
                  :placeholder="field.placeholder || ''"
                  rows="4"
                  class="w-full text-sm font-mono p-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00b88c] focus:border-transparent outline-none transition-all placeholder:text-gray-400 bg-gray-50/30"
                ></textarea>
              </template>

              <!-- Image field with file upload and URL support -->
              <template v-else-if="field.type === 'image'">
                <div class="space-y-2">
                  <input
                    type="file"
                    accept="image/png,image/jpeg,image/webp,image/svg+xml,image/jpg,image/x-icon,image/vnd.microsoft.icon"
                    class="hidden"
                    :ref="(el) => setFileInputRef(el, field.keyword)"
                    @change="handleImageUpload($event, field.keyword)"
                  />
                  <div class="flex items-center gap-2">
                    <button
                      type="button"
                      @click="triggerImageSelect(field.keyword)"
                      :disabled="uploadingKeyword === field.keyword"
                      class="px-3 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 border border-gray-300 rounded-lg text-xs font-semibold flex items-center gap-1.5 transition-colors whitespace-nowrap cursor-pointer"
                    >
                      <i :class="['pi', uploadingKeyword === field.keyword ? 'pi-spin pi-spinner' : 'pi-upload']"></i>
                      <span>{{ uploadingKeyword === field.keyword ? 'Đang tải...' : 'Chọn ảnh' }}</span>
                    </button>
                    <input
                      type="text"
                      v-model="formValues[field.keyword]"
                      :placeholder="field.placeholder || '/images/... hoặc https://...'"
                      class="flex-1 text-sm p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00b88c] focus:border-transparent outline-none transition-all placeholder:text-gray-400"
                    />
                    <button
                      v-if="formValues[field.keyword]"
                      type="button"
                      @click="formValues[field.keyword] = ''"
                      class="p-2.5 text-gray-400 hover:text-red-500 text-xs border rounded-lg hover:border-red-300 transition-colors cursor-pointer"
                      title="Xóa URL"
                    >
                      <i class="pi pi-trash"></i>
                    </button>
                  </div>
                  <!-- Image Preview Box -->
                  <div
                    v-if="formValues[field.keyword]"
                    class="h-20 w-48 border border-dashed border-gray-300 rounded-lg p-2 bg-gray-50 flex items-center justify-center overflow-hidden"
                  >
                    <img
                      :src="formValues[field.keyword]"
                      :alt="field.label"
                      loading="lazy"
                      class="max-h-full max-w-full object-contain"
                      @error="handleImageError($event)"
                    />
                  </div>
                </div>
              </template>

              <!-- Email field -->
              <template v-else-if="field.type === 'email'">
                <div class="relative">
                  <i class="pi pi-envelope absolute left-3 top-3 text-gray-400 text-xs"></i>
                  <input
                    type="email"
                    v-model="formValues[field.keyword]"
                    :placeholder="field.placeholder || 'example@email.com'"
                    class="w-full text-sm pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00b88c] focus:border-transparent outline-none transition-all placeholder:text-gray-400"
                  />
                </div>
              </template>

              <!-- URL field -->
              <template v-else-if="field.type === 'url'">
                <div class="relative">
                  <i class="pi pi-link absolute left-3 top-3 text-gray-400 text-xs"></i>
                  <input
                    type="url"
                    v-model="formValues[field.keyword]"
                    :placeholder="field.placeholder || 'https://...'"
                    class="w-full text-sm pl-9 pr-3 py-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00b88c] focus:border-transparent outline-none transition-all placeholder:text-gray-400"
                  />
                </div>
              </template>

              <!-- Standard text input -->
              <template v-else>
                <input
                  type="text"
                  v-model="formValues[field.keyword]"
                  :placeholder="field.placeholder || ''"
                  class="w-full text-sm p-2.5 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#00b88c] focus:border-transparent outline-none transition-all placeholder:text-gray-400"
                />
              </template>
            </div>
          </div>
        </div>

        <!-- Footer Actions inside panel -->
        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center justify-end gap-3">
          <button
            type="button"
            @click="resetToOriginal"
            :disabled="saving"
            class="px-4 py-2 border border-gray-300 text-gray-700 rounded-lg text-sm font-medium hover:bg-gray-50 transition-colors"
          >
            Hủy thay đổi
          </button>
          <button
            type="button"
            @click="handleSave"
            :disabled="saving"
            class="px-6 py-2 bg-gradient-to-r from-[#007c8b] to-[#00b88c] text-white rounded-lg text-sm font-bold shadow hover:opacity-95 transition-all flex items-center gap-2"
          >
            <i :class="['pi', saving ? 'pi-spin pi-spinner' : 'pi-save']"></i>
            <span>{{ saving ? "Đang lưu cấu hình..." : "Lưu tất cả cấu hình" }}</span>
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, reactive, onMounted } from "vue";
import { systemService } from "~/services/system/system.service";
import { uploadService } from "~/services/upload/image.service";
import { useSystemConfig } from "~/composables/system/useSystemConfig";
import { useAppToast } from "~/composables/useAppToast";
import type { ISystemGroupSchema } from "~/interfaces/system/system.interface";

definePageMeta({
  layout: "admin",
  middleware: ["auth-admin"],
});

const toast = useAppToast();
const { fetchConfigs } = useSystemConfig();

const structure = ref<Record<string, ISystemGroupSchema>>({});
const activeTab = ref<string>("general");
const loading = ref<boolean>(true);
const saving = ref<boolean>(false);
const error = ref<string | null>(null);

const formValues = reactive<Record<string, any>>({});
let originalValues: Record<string, any> = {};

// Image Upload State
const uploadingKeyword = ref<string | null>(null);
const fileInputRefs: Record<string, HTMLInputElement | null> = {};

const setFileInputRef = (el: any, keyword: string) => {
  fileInputRefs[keyword] = el;
};

const triggerImageSelect = (keyword: string) => {
  const el = fileInputRefs[keyword];
  if (el) el.click();
};

const handleImageUpload = async (event: Event, keyword: string) => {
  const input = event.target as HTMLInputElement;
  const file = input.files?.[0];
  if (!file) return;

  const validation = uploadService.validateFile(file);
  if (!validation.valid) {
    toast.error("File không hợp lệ", { description: validation.error });
    input.value = "";
    return;
  }

  uploadingKeyword.value = keyword;
  try {
    const res = await uploadService.temp([file], uploadService.generateSessionId());
    if (res?.files?.[0]?.url) {
      formValues[keyword] = res.files[0].url;
      toast.success("Tải ảnh lên thành công!");
    } else {
      toast.error("Không nhận được URL ảnh từ máy chủ.");
    }
  } catch (err: any) {
    console.error("Upload image error:", err);
    toast.error("Lỗi tải ảnh", {
      description: err?.message || "Không thể tải ảnh lên. Vui lòng thử lại.",
    });
  } finally {
    uploadingKeyword.value = null;
    input.value = "";
  }
};

const getGroupIcon = (key: string): string => {
  switch (key) {
    case "general":
      return "pi pi-building";
    case "contact":
      return "pi pi-phone";
    case "seo":
      return "pi pi-globe";
    case "social":
      return "pi pi-share-alt";
    case "script":
      return "pi pi-code";
    default:
      return "pi pi-cog";
  }
};

const handleImageError = (event: Event) => {
  const target = event.target as HTMLImageElement;
  if (target) {
    target.style.display = "none";
  }
};

const fetchStructure = async () => {
  loading.value = true;
  error.value = null;
  try {
    const res = await systemService.getStructure();
    if (res?.data) {
      structure.value = res.data;
      const initial: Record<string, any> = {};
      Object.keys(res.data).forEach((groupKey) => {
        const group = res.data[groupKey];
        if (group?.fields) {
          Object.keys(group.fields).forEach((fieldKey) => {
            const field = group.fields[fieldKey];
            initial[field.keyword] = field.value ?? "";
            formValues[field.keyword] = field.value ?? "";
          });
        }
      });
      originalValues = { ...initial };

      if (!activeTab.value || !res.data[activeTab.value]) {
        activeTab.value = Object.keys(res.data)[0] || "general";
      }
    }
  } catch (err: any) {
    console.error("Error fetching system structure:", err);
    error.value = err?.message || "Không thể tải cấu trúc cấu hình hệ thống.";
  } finally {
    loading.value = false;
  }
};

const resetToOriginal = () => {
  Object.keys(originalValues).forEach((key) => {
    formValues[key] = originalValues[key];
  });
  toast.info("Đã khôi phục các giá trị ban đầu.");
};

const handleSave = async () => {
  saving.value = true;
  try {
    await systemService.saveSettings(formValues);
    originalValues = { ...formValues };
    toast.success("Thành công", {
      description: "Cấu hình hệ thống đã được cập nhật thành công!",
    });
    // Làm mới cache và reactive state cho toàn bộ client
    await fetchConfigs(true);
  } catch (err: any) {
    console.error("Save system settings failed:", err);
    toast.error("Lỗi cập nhật", {
      description: err?.message || "Không thể lưu cấu hình hệ thống. Vui lòng thử lại.",
    });
  } finally {
    saving.value = false;
  }
};

onMounted(() => {
  fetchStructure();
});
</script>
