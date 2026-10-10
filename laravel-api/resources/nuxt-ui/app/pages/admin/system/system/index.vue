<script setup lang="ts">
import { ref, reactive, computed, onMounted } from "vue";
import FilterBar from "~/components/navigation/FilterBar.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Pagination from "~/components/navigation/AppPagination.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import { useSystem } from "~/composables/system/useSystem";
import { systemService } from "~/services/system/system.service";
import { uploadService } from "~/services/upload/image.service";
import { useAppToast } from "~/composables/useAppToast";
import { SYSTEMCREATE, SYSTEMUPDATE } from "~/utils/constants";

const toast = useAppToast();
const { data, isLoading, filters, search, setPage, selectedIds, selectedItem, setSelectedItem, handleDeleteMultiple, confirmDelete, updateSelectedIds, getTotalItems } = useSystem();

// Main View Switcher
const mainTab = ref<"dynamic" | "table">("dynamic");

// Dynamic Configs State
const structure = ref<Record<string, any>>({});
const activeGroup = ref<string>("general");
const loadingStructure = ref<boolean>(false);
const savingSettings = ref<boolean>(false);
const formValues = reactive<Record<string, any>>({});
let originalValues: Record<string, any> = {};

// Image Upload State
const uploadingNuxtUiKeyword = ref<string | null>(null);
const nuxtUiFileRefs: Record<string, HTMLInputElement | null> = {};

const setNuxtUiFileRef = (el: any, keyword: string) => {
    nuxtUiFileRefs[keyword] = el;
};

const triggerNuxtUiImageSelect = (keyword: string) => {
    const el = nuxtUiFileRefs[keyword];
    if (el) el.click();
};

const handleNuxtUiImageUpload = async (event: Event, keyword: string) => {
    const input = event.target as HTMLInputElement;
    const file = input.files?.[0];
    if (!file) return;

    uploadingNuxtUiKeyword.value = keyword;
    try {
        const res = await uploadService.temp([file], uploadService.generateSessionId());
        if (res?.files?.[0]?.url) {
            formValues[keyword] = res.files[0].url;
            toast?.success?.("Tải ảnh lên thành công!");
        } else {
            toast?.error?.("Không nhận được URL ảnh từ máy chủ.");
        }
    } catch (err: any) {
        console.error("Upload error:", err);
        toast?.error?.(err?.message || "Không thể tải ảnh lên.");
    } finally {
        uploadingNuxtUiKeyword.value = null;
        input.value = "";
    }
};

const getGroupIcon = (key: string): string => {
    switch (key) {
        case "general":
            return "ri-building-line";
        case "contact":
            return "ri-phone-line";
        case "seo":
            return "ri-global-line";
        case "social":
            return "ri-share-line";
        case "script":
            return "ri-code-line";
        default:
            return "ri-settings-line";
    }
};

const fetchStructure = async () => {
    loadingStructure.value = true;
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
            if (!activeGroup.value || !res.data[activeGroup.value]) {
                activeGroup.value = Object.keys(res.data)[0] || "general";
            }
        }
    } catch (err: any) {
        console.error("Error fetching system structure:", err);
        toast?.error?.("Không thể tải cấu trúc cấu hình hệ thống");
    } finally {
        loadingStructure.value = false;
    }
};

const handleSaveSettings = async () => {
    savingSettings.value = true;
    try {
        await systemService.saveSettings(formValues);
        originalValues = { ...formValues };
        toast?.success?.("Cập nhật cấu hình hệ thống thành công!");
    } catch (err: any) {
        console.error("Save system settings failed:", err);
        toast?.error?.(err?.message || "Lỗi lưu cấu hình hệ thống.");
    } finally {
        savingSettings.value = false;
    }
};

const resetSettings = () => {
    Object.keys(originalValues).forEach((k) => {
        formValues[k] = originalValues[k];
    });
    toast?.info?.("Đã hoàn tác thay đổi.");
};

onMounted(() => {
    fetchStructure();
});

const handleSelectedIdsChange = (value: boolean | number[]) => {
    if (Array.isArray(value)) updateSelectedIds(value);
};

const handleDeleteSingle = (item: any) => setSelectedItem(item);

const handleConfirmDeleteSingle = async () => {
    await confirmDelete();
    closeModal("deleteRecord");
};

const handleConfirmDeleteMultiple = async () => {
    await handleDeleteMultiple();
    closeModal("deleteMultipleRecord");
};

const isBulkSelecting = computed(() => selectedIds.value.length >= 1);
const hasData = computed(() => Boolean(data.value?.data?.data?.length));
const hasSelectedItems = computed(() => selectedIds.value.length > 0);

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="mb-0 text-uppercase fw-bold">{{ $t("admin.page.system.title") }}</h5>
            <p class="text-muted small mb-0 mt-1">Cấu hình thông tin hệ thống trong website</p>
        </div>
        <Breadcrumb :crumbs="[{ label: $t('admin.page.system.breadcrumb') }]" />
    </div>

    <!-- Main Navigation Mode Switcher -->
    <ul class="nav nav-tabs nav-tabs-custom border-bottom-0 mb-4">
        <li class="nav-item">
            <button
                type="button"
                :class="['nav-link', { active: mainTab === 'dynamic' }]"
                @click="mainTab = 'dynamic'"
            >
                <i class="ri-equalizer-line me-1"></i>
                Cấu hình theo nhóm
            </button>
        </li>
        <li class="nav-item">
            <button
                type="button"
                :class="['nav-link', { active: mainTab === 'table' }]"
                @click="mainTab = 'table'"
            >
                <i class="ri-table-line me-1"></i>
                Danh sách key
            </button>
        </li>
    </ul>

    <div v-show="mainTab === 'dynamic'" class="card shadow-sm">
        <div class="card-header border-bottom d-flex justify-content-between align-items-center bg-light-subtle py-3">
            <div class="d-flex align-items-center gap-2">
                <i class="ri-settings-4-line text-success fs-18"></i>
                <span class="fw-semibold">Cấu hình hệ thống</span>
            </div>
            <div class="d-flex gap-2">
                <button
                    type="button"
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="loadingStructure || savingSettings"
                    @click="fetchStructure"
                >
                    <i class="ri-refresh-line me-1" :class="{ 'spin-infinite': loadingStructure }"></i>
                    Tải lại
                </button>
                <button
                    type="button"
                    class="btn btn-sm btn-success"
                    :disabled="loadingStructure || savingSettings"
                    @click="handleSaveSettings"
                >
                    <i class="ri-save-line me-1"></i>
                    {{ savingSettings ? "Đang lưu..." : "Lưu tất cả cấu hình" }}
                </button>
            </div>
        </div>

        <div v-if="loadingStructure" class="card-body text-center py-5">
            <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Đang tải...</span>
            </div>
            <p class="text-muted small mt-2">Đang tải cấu hình hệ thống...</p>
        </div>

        <div v-else class="card-body p-0">
            <div class="row g-0">
                <!-- Group Tabs Sidebar -->
                <div class="col-md-3 border-end bg-light-subtle p-3">
                    <div class="nav flex-column nav-pills" role="tablist">
                        <button
                            v-for="(group, key) in structure"
                            :key="key"
                            type="button"
                            :class="['nav-link text-start py-2.5 px-3 mb-2 rounded d-flex align-items-center justify-content-between', activeGroup === key ? 'active bg-success text-white' : 'text-dark hover:bg-light']"
                            @click="activeGroup = key"
                        >
                            <span>
                                <i :class="[getGroupIcon(key), 'me-2']"></i>
                                {{ group.label }}
                            </span>
                            <span :class="['badge rounded-pill', activeGroup === key ? 'bg-white text-success' : 'bg-secondary-subtle text-secondary']">
                                {{ Object.keys(group.fields || {}).length }}
                            </span>
                        </button>
                    </div>
                </div>

                <!-- Group Fields Panel -->
                <div class="col-md-9 p-4">
                    <div v-for="(group, groupKey) in structure" :key="groupKey" v-show="activeGroup === groupKey">
                        <div class="d-flex justify-content-between align-items-center pb-3 mb-4 border-bottom">
                            <div>
                                <h6 class="fw-bold mb-1 text-uppercase text-success">{{ group.label }}</h6>
                                <p v-if="group.description" class="text-muted small mb-0">{{ group.description }}</p>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div
                                v-for="(field, fieldKey) in group.fields"
                                :key="fieldKey"
                                :class="field.type === 'textarea' ? 'col-12' : 'col-md-6'"
                            >
                                <label class="form-label fw-semibold small text-uppercase">
                                    {{ field.label }}
                                </label>

                                <!-- Textarea -->
                                <textarea
                                    v-if="field.type === 'textarea'"
                                    v-model="formValues[field.keyword]"
                                    :placeholder="field.placeholder || ''"
                                    rows="4"
                                    class="form-control font-monospace"
                                ></textarea>

                                <!-- Image / URL input with file upload and preview -->
                                <div v-else-if="field.type === 'image'" class="space-y-2">
                                    <input
                                        type="file"
                                        accept="image/png,image/jpeg,image/webp,image/svg+xml,image/jpg,image/x-icon,image/vnd.microsoft.icon"
                                        class="d-none"
                                        :ref="(el) => setNuxtUiFileRef(el, field.keyword)"
                                        @change="handleNuxtUiImageUpload($event, field.keyword)"
                                    />
                                    <div class="input-group">
                                        <button
                                            type="button"
                                            class="btn btn-outline-primary"
                                            :disabled="uploadingNuxtUiKeyword === field.keyword"
                                            @click="triggerNuxtUiImageSelect(field.keyword)"
                                        >
                                            <i :class="uploadingNuxtUiKeyword === field.keyword ? 'spinner-border spinner-border-sm' : 'ri-upload-2-line'" class="me-1"></i>
                                            {{ uploadingNuxtUiKeyword === field.keyword ? 'Đang tải...' : 'Chọn ảnh' }}
                                        </button>
                                        <input
                                            type="text"
                                            v-model="formValues[field.keyword]"
                                            :placeholder="field.placeholder || '/images/... hoặc https://...'"
                                            class="form-control"
                                        />
                                        <button
                                            v-if="formValues[field.keyword]"
                                            type="button"
                                            class="btn btn-outline-danger"
                                            @click="formValues[field.keyword] = ''"
                                            title="Xóa"
                                        >
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                    <div v-if="formValues[field.keyword]" class="border rounded p-2 text-center bg-light mt-1" style="max-width: 200px;">
                                        <img :src="formValues[field.keyword]" :alt="field.label" loading="lazy" class="img-fluid" style="max-height: 80px;" />
                                    </div>
                                </div>

                                <!-- Email -->
                                <div v-else-if="field.type === 'email'" class="input-group">
                                    <span class="input-group-text"><i class="ri-mail-line"></i></span>
                                    <input
                                        type="email"
                                        v-model="formValues[field.keyword]"
                                        :placeholder="field.placeholder || 'example@email.com'"
                                        class="form-control"
                                    />
                                </div>

                                <!-- URL -->
                                <div v-else-if="field.type === 'url'" class="input-group">
                                    <span class="input-group-text"><i class="ri-link"></i></span>
                                    <input
                                        type="url"
                                        v-model="formValues[field.keyword]"
                                        :placeholder="field.placeholder || 'https://...'"
                                        class="form-control"
                                    />
                                </div>

                                <!-- Text -->
                                <input
                                    v-else
                                    type="text"
                                    v-model="formValues[field.keyword]"
                                    :placeholder="field.placeholder || ''"
                                    class="form-control"
                                />
                            </div>
                        </div>

                        <!-- Panel Bottom Buttons -->
                        <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                            <button
                                type="button"
                                class="btn btn-outline-secondary"
                                :disabled="savingSettings"
                                @click="resetSettings"
                            >
                                Hủy thay đổi
                            </button>
                            <button
                                type="button"
                                class="btn btn-success"
                                :disabled="savingSettings"
                                @click="handleSaveSettings"
                            >
                                <i class="ri-save-line me-1"></i>
                                {{ savingSettings ? "Đang lưu..." : "Lưu cấu hình" }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- RAW TABLE VIEW -->
    <div v-show="mainTab === 'table'" class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center gap-3">
                    <h5 class="card-title mb-0 text-uppercase fw-bold fs-15">QUẢN LÝ CẤU HÌNH HỆ THỐNG</h5>
                    <div v-if="hasSelectedItems" class="d-flex align-items-center fz-[12px] gap-2">
                        <Button variant="delete" type="button" :label="`${$t('admin.common.delete')} (${selectedIds.length})`" :loading="isLoading" :disabled="!hasSelectedItems" data-bs-toggle="modal" data-bs-target="#deleteMultipleRecord" />
                        <Button variant="cancel" :loading="isLoading" :disabled="!hasSelectedItems" @click="updateSelectedIds([])" />
                    </div>
                </div>
                <NuxtLink :to="SYSTEMCREATE" class="btn btn-success">
                    <i class="ri-add-line me-1"></i>
                    {{ $t("admin.common.create") }}
                </NuxtLink>
            </div>
            <FilterBar @search="search"></FilterBar>
            <div class="table-responsive">
                <table class="table table-hover align-middle" style="table-layout: fixed">
                    <colgroup>
                        <col style="width: 40px" />
                        <col />
                        <col style="width: 200px" />
                        <col style="width: 150px" />
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">
                                <AppCheckbox :model-value="selectedIds" :is-select-all="true" @update:model-value="handleSelectedIdsChange" />
                            </th>
                            <th>{{ $t("admin.page.system.groupInfo") }}</th>
                            <th class="text-center">{{ $t("admin.common.status") }}</th>
                            <th class="text-end">{{ $t("admin.common.actions") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="4" class="text-center text-muted">{{ $t("admin.common.loading") }}</td>
                        </tr>
                        <template v-else>
                            <tr v-for="item in data?.data?.data ?? []" :key="item.id" class="cursor-pointer" :class="{ 'table-active': selectedIds.includes(item.id!) }">
                                <td>
                                    <AppCheckbox :model-value="selectedIds" :value="item.id!" @update:model-value="handleSelectedIdsChange" />
                                </td>
                                <td>
                                    <NuxtLink class="hover:text-teal-500" :to="SYSTEMUPDATE(item.id!)">
                                        <div class="text-truncate fw-semibold">{{ item.keyword }}</div>
                                        <div class="text-muted small">
                                            <p class="line-clamp-2 mb-0" v-html="item.content"></p>
                                        </div>
                                    </NuxtLink>
                                </td>
                                <td class="text-center">
                                    <span :class="['badge', item.publish === 2 ? 'bg-success-subtle text-success' : item.publish === 1 ? 'bg-secondary-subtle text-secondary' : 'bg-danger-subtle text-danger']">
                                        {{ item.publish === 2 ? $t("admin.common.published") : item.publish === 1 ? $t("admin.common.draft") : $t("admin.common.hidden") }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light" type="button" :class="{ disabled: isBulkSelecting }" :data-bs-toggle="isBulkSelecting ? null : 'dropdown'" @click.stop.prevent>
                                            <i class="ri-more-2-fill"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li>
                                                <NuxtLink class="dropdown-item text-info" :to="SYSTEMUPDATE(item.id!)">
                                                    <i class="ri-edit-box-line me-1"></i>
                                                    {{ $t("admin.common.edit") }}
                                                </NuxtLink>
                                            </li>
                                            <li>
                                                <button type="button" class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#deleteRecord" @click.stop="handleDeleteSingle(item)">
                                                    <i class="ri-delete-bin-line me-1"></i>
                                                    {{ $t("admin.common.delete") }}
                                                </button>
                                            </li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="(data?.data?.data?.length ?? 0) === 0">
                                <td colspan="4" class="text-center text-muted">{{ $t("admin.common.noData") }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <Pagination v-if="!isLoading && hasData && data?.data?.links" :pagination="data.data as any" @update:page="setPage" class="mt-3" />
        </div>
    </div>

    <!-- Delete Single Modal -->
    <div id="deleteRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteRecordLabel" aria-hidden="true">
        <div class="modal-dialog zoomIn">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger uppercase" id="deleteRecordLabel">{{ $t("admin.common.deleteRecord") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">{{ $t("admin.common.confirmDelete") }}</h5>
                    <div v-if="selectedItem" class="mt-3">
                        <p class="text-muted fz-[12px] mt-3 mb-1">{{ $t("admin.common.record") }}</p>
                        <input :value="selectedItem.keyword" type="text" class="form-control border-danger border-1 text-danger" readonly />
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" />
                    <Button variant="delete" :loading="isLoading" :label="$t('admin.common.delete')" :disabled="!selectedItem" @click="handleConfirmDeleteSingle" />
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Multiple Modal -->
    <div id="deleteMultipleRecord" class="modal fade" tabindex="-1" aria-labelledby="deleteMultipleRecordLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg zoomIn">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title text-danger uppercase" id="deleteMultipleRecordLabel">{{ $t("admin.common.deleteMultiple") }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body py-4">
                    <h5 class="fs-15">{{ $t("admin.common.confirmDeleteMultiple") }}</h5>
                    <div class="mt-3">
                        <p class="text-muted fz-[12px] mb-2">{{ $t("admin.common.selectedRecords") }} ({{ selectedIds.length }}):</p>
                        <div class="border rounded p-3 bg-light max-h-[300px] overflow-y-auto">
                            <ul class="list-unstyled mb-0">
                                <li v-for="item in data?.data?.data?.filter((i) => selectedIds.includes(i.id as any))" :key="item.id" class="py-1">
                                    <div class="d-flex align-items-center">
                                        <AppCheckbox :model-value="selectedIds" :value="item.id" @update:model-value="handleSelectedIdsChange" class="me-2" />
                                        <span class="text-danger">{{ item.keyword }}</span>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" />
                    <Button variant="delete" :loading="isLoading" :label="$t('admin.common.deleteAll')" :disabled="selectedIds.length === 0" @click="handleConfirmDeleteMultiple" />
                </div>
            </div>
        </div>
    </div>
</template>
