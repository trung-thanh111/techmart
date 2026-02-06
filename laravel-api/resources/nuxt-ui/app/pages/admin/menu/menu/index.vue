<script setup lang="ts">
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Pagination from "~/components/navigation/AppPagination.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import { useMenu } from "~/composables/menu/useMenu";
import { MENUUPDATE } from "~/utils/constants";
import FilterBar from "~/components/navigation/FilterBar.vue";

const { data, isLoading, filters, selectedIds, selectedItem, fetchData, setPage, handleDeleteMultiple, handleDelete, setValues, search, getTotalItems, getAllItemIds } = useMenu();

provide("getTotalItems", getTotalItems);
provide("getAllItemIds", getAllItemIds);

const handleSelectedIdsChange = (value: boolean | number[]) => {
    if (Array.isArray(value)) selectedIds.value = value;
};

const handleDeleteSingle = (item: any) => {
    selectedItem.value = item;
};

const handleConfirmDeleteSingle = async () => {
    if (!selectedItem.value || !selectedItem.value.id) return;
    await handleDelete(selectedItem.value.id);
    // close modal via bootstrap
    const el = document.getElementById("deleteRecord");
    if (el) (window as any).bootstrap.Modal.getInstance(el)?.hide();
};

const handleConfirmDeleteMultiple = async () => {
    await handleDeleteMultiple();
    // close modal via bootstrap
    const el = document.getElementById("deleteMultipleRecord");
    if (el) (window as any).bootstrap.Modal.getInstance(el)?.hide();
};

const isBulkSelecting = computed(() => selectedIds.value.length >= 1);
const hasData = computed(() => Boolean(data.value?.data?.data?.length));

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>

<template>
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h5 class="mb-0 text-uppercase fw-bold">{{ $t("admin.page.menu.title") }}</h5>
        <Breadcrumb :crumbs="[{ label: $t('admin.page.menu.breadcrumb') }]" />
    </div>
    <div class="card shadow-sm">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div class="d-flex align-items-center fz-[12px] gap-2" :class="{ 'opacity-0': !selectedIds.length }">
                    <Button variant="delete" type="button" :label="`${$t('admin.common.delete')} (${selectedIds.length})`" :loading="isLoading" :disabled="!selectedIds.length" data-bs-toggle="modal" data-bs-target="#deleteMultipleRecord" />
                    <Button variant="cancel" :loading="isLoading" :disabled="!selectedIds.length" @click="selectedIds = []" />
                </div>
                <NuxtLink to="/menu/menu/create" class="btn btn-success">
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
                        <col style="width: 150px" />
                        <col style="width: 150px" />
                        <col style="width: 100px" />
                        <col style="width: 100px" />
                        <col style="width: 100px" />
                    </colgroup>
                    <thead class="table-light">
                        <tr>
                            <th style="width: 50px">
                                <AppCheckbox :model-value="selectedIds" :is-select-all="true" @update:model-value="handleSelectedIdsChange" />
                            </th>
                            <th>{{ $t("admin.page.menu.name") }}</th>
                            <th class="text-center">{{ $t("admin.page.menu.slug") }}</th>
                            <th class="text-center">{{ $t("admin.page.menu.position") }}</th>
                            <th class="text-center">{{ $t("admin.page.menu.items") }}</th>
                            <th class="text-center">{{ $t("admin.common.status") }}</th>
                            <th class="text-end">{{ $t("admin.common.actions") }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-if="isLoading">
                            <td colspan="7" class="text-center text-muted">{{ $t("admin.common.loading") }}</td>
                        </tr>
                        <template v-else>
                            <tr v-for="item in data?.data?.data ?? []" :key="item.id" class="cursor-pointer">
                                <td>
                                    <AppCheckbox :model-value="selectedIds" :value="item.id!" @update:model-value="handleSelectedIdsChange" />
                                </td>
                                <td>
                                    <NuxtLink class="hover:text-teal-500" :to="MENUUPDATE(item.id!)">{{ item.name }}</NuxtLink>
                                </td>
                                <td class="text-center">
                                    <code>{{ item.slug }}</code>
                                </td>
                                <td class="text-center">
                                    <span class="badge badge-label bg-info">
                                        <i class="mdi mdi-circle-medium"></i>
                                        {{ item.position }}
                                    </span>
                                </td>
                                <td class="text-center">{{ (item.menuItems || []).length }}</td>
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
                                                <NuxtLink class="dropdown-item text-info" :to="MENUUPDATE(item.id!)">
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
                                <td colspan="7" class="text-center text-muted">{{ $t("admin.common.noData") }}</td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
            <Pagination v-if="!isLoading && hasData && data?.data?.links" :pagination="data.data as any" @update:page="setPage" class="mt-3" />
        </div>
    </div>

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
                        <input :value="selectedItem.name" type="text" class="form-control border-danger border-1 text-danger" readonly />
                    </div>
                </div>
                <div class="modal-footer">
                    <Button type="button" variant="cancel" data-bs-dismiss="modal" />
                    <Button variant="delete" :loading="isLoading" :label="$t('admin.common.delete')" :disabled="!selectedItem" @click="handleConfirmDeleteSingle" />
                </div>
            </div>
        </div>
    </div>
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
                                <li v-for="item in data?.data?.data?.filter((i: any) => selectedIds.includes(i.id))" :key="item.id" class="py-1">
                                    <div class="d-flex align-items-center">
                                        <AppCheckbox :model-value="selectedIds" :value="item.id" @update:model-value="handleSelectedIdsChange" class="me-2" />
                                        <span class="text-danger">{{ item.name }}</span>
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
