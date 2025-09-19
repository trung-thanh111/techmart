<script setup lang="ts">
import FilterBar from "~/components/admin/navigation/FilterBar.vue";
import { closeModal } from "#imports";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import Pagination from "~/components/admin/navigation/AppPagination.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import Button from "~/components/admin/custom/Button.vue";
import { useAgencyWorkingHour } from "~/composables/admin/agency/useWorkingHour";

const { data, isLoading, selectedIds, selectedItem, search, setPage, handleDeleteMultiple, setSelectedItem, confirmDelete, updateSelectedIds, getTotalItems, getAllItemIds } = useAgencyWorkingHour();

provide("getTotalItems", getTotalItems);
provide("getAllItemIds", getAllItemIds);

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
    <h5 class="mb-0 text-uppercase fw-bold">{{ $t("admin.page.agencyWorkingHour.title") }}</h5>
    <Breadcrumb :crumbs="[{ label: $t('admin.page.agencyWorkingHour.breadcrumb') }]" />
  </div>
  <div class="card shadow-sm">
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center fz-[12px] gap-2" :class="{ 'opacity-0': !hasSelectedItems }">
          <Button variant="delete" type="button" :label="`${$t('admin.common.delete')} (${selectedIds.length})`" :loading="isLoading" :disabled="!hasSelectedItems" data-bs-toggle="modal" data-bs-target="#deleteMultipleRecord" />
          <Button variant="cancel" :loading="isLoading" :disabled="!hasSelectedItems" @click="updateSelectedIds([])" />
        </div>
        <NuxtLink to="/admin/agency/working_hour/create" class="btn btn-success">
          <i class="ri-add-line me-1"></i>
          {{ $t("admin.common.create") }}
        </NuxtLink>
      </div>
      <FilterBar @search="search" />
      <div class="table-responsive">
        <table class="table table-hover align-middle" style="table-layout: fixed">
          <colgroup>
            <col style="width: 40px" />
            <col />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 150px" />
            <col style="width: 120px" />
          </colgroup>
          <thead class="table-light">
            <tr>
              <th style="width: 50px">
                <AppCheckbox :model-value="selectedIds" :is-select-all="true" @update:model-value="handleSelectedIdsChange" />
              </th>
              <th>{{ $t("admin.page.agencyWorkingHour.groupInfo") }}</th>
              <th class="text-center">{{ $t("admin.common.openTime") }}</th>
              <th class="text-center">{{ $t("admin.common.closeTime") }}</th>
              <th class="text-center">{{ $t("admin.common.status") }}</th>
              <th class="text-end">{{ $t("admin.common.actions") }}</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="isLoading">
              <td colspan="4" class="text-center text-muted">{{ $t("admin.common.loading") }}</td>
            </tr>
            <template v-else>
              <tr v-for="item in data?.data?.data ?? []" :key="item.id" class="cursor-pointer" :class="{ 'table-active': selectedIds.includes(item.id) }">
                <td>
                  <AppCheckbox :model-value="selectedIds" :value="item.id" @update:model-value="handleSelectedIdsChange" />
                </td>
                <td>
                  <NuxtLink class="hover:text-teal-500 flex items-center gap-2" :to="`/admin/agency/working_hour/${item.id}/edit`">
                    {{ item.dayOfWeek }}
                  </NuxtLink>
                </td>
                <td class="text-center">
                  <span>{{ item.openTime }}</span>
                </td>
                <td class="text-center">
                  <span>{{ item.closeTime }}</span>
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
                        <NuxtLink class="dropdown-item text-info" :to="`/admin/agency/working_hour/${item.id}/edit`">
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
  <!-- modal  -->
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
            <input :value="selectedItem.dayOfWeek" type="text" class="form-control border-danger border-1 text-danger" readonly />
          </div>
        </div>
        <div class="modal-footer">
          <Button variant="cancel" data-bs-dismiss="modal" />
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
                <li v-for="item in data?.data?.data?.filter((i) => selectedIds.includes(i.id))" :key="item.id" class="py-1">
                  <div class="d-flex align-items-center">
                    <AppCheckbox :model-value="selectedIds" :value="item.id" @update:model-value="handleSelectedIdsChange" class="me-2" />
                    <span class="text-danger">{{ item.dayOfWeek }}</span>
                  </div>
                </li>
              </ul>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <Button variant="cancel" data-bs-dismiss="modal" />
          <Button variant="delete" :loading="isLoading" :label="$t('admin.common.deleteAll')" :disabled="selectedIds.length === 0" @click="handleConfirmDeleteMultiple" />
        </div>
      </div>
    </div>
  </div>
</template>
