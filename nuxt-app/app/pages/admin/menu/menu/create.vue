<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.menu.breadcrumb'), to: MENUINDEX }, { label: $t('admin.common.create') }]" />
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="() => handleStore(false)" class="row space-y-4">
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.info") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }">
              <InputText v-bind="field" type="text" class="form-control" placeholder="Nhập tên menu (exp: main menu)" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.slug") }}:</label>
            <input :value="slug" class="form-control" readonly placeholder="Slug tự sinh từ name" />
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.position") }}:</label>
            <Field name="position" v-slot="{ field }">
              <Select v-model="field.value" @update:modelValue="field.onChange" :options="positionOptions" option-label="label" option-value="value" showClear class="!rounded w-full p-[1px]" size="small" />
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.status") }}</label>
            <Field name="publish" v-slot="{ field }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
            </Field>
          </div>
        </div>
      </div>
    </div>
    <div class="col-lg-8 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.page.menu.items") }}</h5>
        </div>
        <div class="card-body">
          <MenuItemEditor v-model:items="values.menuItems" />
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="MENUINDEX" />
        <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleStore(false)" />
        <Button type="button" variant="success" :loading="isLoading" @click="() => handleStore(true)" />
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { useMenu } from "~/composables/admin/menu/useMenu";
import MenuItemEditor from "~/components/admin/menu/MenuItemEditor.vue";
import { MENUINDEX } from "~/utils/constants";

const { isLoading, values, slug, positionOptions, handleStore } = useMenu();

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
