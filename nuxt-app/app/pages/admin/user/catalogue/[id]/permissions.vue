<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.page.userCatalogue.permissions.title") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.userCatalogue.breadcrumb'), to: USERCATALOGUEINDEX }, { label: $t('admin.page.userCatalogue.permissions.title') }]" />
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm" v-if="!isLoading && permissionsData">
    <div class="card-header">
      <h5 class="card-title mb-0">{{ $t("admin.page.userCatalogue.permissions.subtitle", { name: permissionsData.userCatalogue?.name }) }}</h5>
    </div>
    <div class="card-body">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="d-flex align-items-center gap-2">
          <AppCheckbox :model-value="isAllSelected" @update:modelValue="handleSelectAll as any" size="large" />
          <span class="text-muted">{{ $t("admin.page.userCatalogue.permissions.selectAll") }}</span>
        </div>
        <div class="d-flex align-items-center gap-2">
          <span class="text-muted">{{ $t("admin.page.userCatalogue.permissions.selectedCount", { count: selectedPermissionIds.length }) }}</span>
        </div>
      </div>

      <div v-for="(permissions, module) in permissionsData.permissionsByModule" :key="module" class="mb-4">
        <div class="card border">
          <div class="card-header bg-light">
            <div class="d-flex justify-content-between align-items-center">
              <h6 class="mb-0 text-uppercase">{{ module }}</h6>
              <div class="d-flex align-items-center gap-2">
                <AppCheckbox :model-value="isModuleSelected(module as any)" @update:modelValue="(val) => handleSelectModule(module as any, val as any)" size="large" />
                <span class="text-muted small">{{ $t("admin.page.userCatalogue.permissions.selectModule") }}</span>
              </div>
            </div>
          </div>
          <div class="card-body">
            <div class="row g-3">
              <div v-for="permission in permissions" :key="permission.id" class="col-md-6 col-lg-4">
                <div class="form-check d-flex align-items-start gap-2">
                  <AppCheckbox :model-value="selectedPermissionIds" :value="permission.id" @update:modelValue="handlePermissionIdsChange as any" size="large" class="mt-1" />
                  <label class="form-check-label flex-grow-1" :for="`permission-${permission.id}`">
                    <div class="text-base">{{ permission.title }}</div>
                    <small class="text-muted d-block">{{ permission.name }}</small>
                  </label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div v-if="Object.keys(permissionsData.permissionsByModule || {}).length === 0" class="text-center text-muted py-5">
        {{ $t("admin.page.userCatalogue.permissions.noPermissions") }}
      </div>

      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="USERCATALOGUEINDEX" />
        <Button variant="success" :loading="isSaving" @click="handleSave">
          {{ $t("admin.common.save") }}
        </Button>
      </div>
    </div>
  </div>

  <div v-else-if="isLoading" class="text-center py-5">
    <div class="spinner-border text-primary" role="status">
      <span class="visually-hidden">{{ $t("admin.common.loading") }}</span>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useI18n } from "vue-i18n";
import { useRoute } from "vue-router";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import Button from "~/components/admin/custom/Button.vue";
import { userCatalogueService } from "~/services/user/catalogue.service";
import { useAppToast } from "~/composables/useAppToast";
import { USERCATALOGUEINDEX } from "~/utils/constants";
import { permissionService } from "~/services/user/permission.service";

const { t } = useI18n();
const route = useRoute();
const toast = useAppToast();

const id = Number(route.params.id);
const isLoading = ref(false);
const isSaving = ref(false);
const permissionsData = ref<any>(null);
const selectedPermissionIds = ref<number[]>([]);

const isAllSelected = computed(() => {
  if (!permissionsData.value?.permissionsByModule) return false;
  const allPermissionIds: number[] = [];
  Object.values(permissionsData.value.permissionsByModule).forEach((permissions: any) => {
    permissions.forEach((p: any) => allPermissionIds.push(p.id));
  });
  return allPermissionIds.length > 0 && allPermissionIds.every((id) => selectedPermissionIds.value.includes(id));
});

const isModuleSelected = (module: string) => {
  if (!permissionsData.value?.permissionsByModule[module]) return false;
  const modulePermissions = permissionsData.value.permissionsByModule[module];
  return modulePermissions.length > 0 && modulePermissions.every((p: any) => selectedPermissionIds.value.includes(p.id));
};

const handleSelectAll = (checked: boolean) => {
  if (!permissionsData.value?.permissionsByModule) return;
  const allPermissionIds: number[] = [];
  Object.values(permissionsData.value.permissionsByModule).forEach((permissions: any) => {
    permissions.forEach((p: any) => allPermissionIds.push(p.id));
  });
  selectedPermissionIds.value = checked ? allPermissionIds : [];
};

const handleSelectModule = (module: string, checked: boolean) => {
  if (!permissionsData.value?.permissionsByModule[module]) return;
  const modulePermissions = permissionsData.value.permissionsByModule[module];
  const modulePermissionIds = modulePermissions.map((p: any) => p.id);

  if (checked) {
    selectedPermissionIds.value = [...new Set([...selectedPermissionIds.value, ...modulePermissionIds])];
  } else {
    selectedPermissionIds.value = selectedPermissionIds.value.filter((id) => !modulePermissionIds.includes(id));
  }
};

const handlePermissionIdsChange = (ids: number[]) => {
  selectedPermissionIds.value = ids;
};

const fetchPermissions = async () => {
  isLoading.value = true;
  try {
    const response = await userCatalogueService.getPermissions(id);
    permissionsData.value = response.data;
    selectedPermissionIds.value = response.data.assignedPermissionIds || [];
  } catch (error: any) {
    toast.error(error?.response?.data?.message || t("admin.messages.loadDataError"));
    console.error("Fetch permissions error:", error);
  } finally {
    isLoading.value = false;
  }
};

const handleSave = async () => {
  isSaving.value = true;
  try {
    const currentPermissionIds = permissionsData.value?.assignedPermissionIds || [];
    const newPermissionIds = selectedPermissionIds.value;

    const permissionsToDetach = currentPermissionIds.filter((id: number) => !newPermissionIds.includes(id));
    const permissionsToAttach = newPermissionIds.filter((id: number) => !currentPermissionIds.includes(id));

    // Kiểm tra xem có thay đổi không
    if (permissionsToDetach.length === 0 && permissionsToAttach.length === 0) {
      toast.info(t("admin.messages.noChanges"));
      return;
    }

    // Thực hiện detach trước (xóa permissions không còn được chọn)
    if (permissionsToDetach.length > 0) {
      await userCatalogueService.detach(id, permissionsToDetach, "permissions");
    }

    // Thực hiện attach sau (thêm permissions mới)
    if (permissionsToAttach.length > 0) {
      await userCatalogueService.attach(id, permissionsToAttach, "permissions");
    }

    toast.success(t("admin.messages.updateSuccess"));
    await fetchPermissions();
  } catch (error: any) {
    toast.error(error?.response?.data?.message || t("admin.messages.updateFailed"));
    console.error("Update permissions error:", error);
  } finally {
    isSaving.value = false;
  }
};

onMounted(() => {
  fetchPermissions();
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
