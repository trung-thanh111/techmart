<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.postTag.breadcrumb'), to: '/admin/post/tag' }, { label: $t('admin.common.create') }]" />
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="handleStore" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t("admin.page.postTag.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.postTag.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.page.postTag.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.page.postTag.form.description") }}</label>
            <Field name="description" v-slot="{ field }">
              <textarea v-bind="field" rows="4" class="form-control" :placeholder="$t('admin.page.postTag.form.descriptionPlaceholder')" />
            </Field>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="POSTTAGINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <div class="form-check d-flex align-items-center gap-2 ps-0">
              <div class="form-group">
                <label for="color">{{ $t("admin.page.postTag.form.color") }}</label>
                <Field name="color" v-slot="{ field, meta, errorMessage }">
                  <ColorPicker v-model="field.value" :format="'hex'" inputId="color" class="w-full" @update:modelValue="(val) => field.onChange(val?.startsWith('#') ? val : `#${val}`)" :showInput="true" default-color="#7dd3fc" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                  <small v-else class="text-muted">{{ $t("admin.page.postTag.form.colorNote") }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check d-flex align-items-center gap-2 ps-0">
              <Field name="isHot" v-slot="{ field }">
                <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="is_hot" class="scale-125" />
              </Field>
              <label for="is_hot" class="mb-0">{{ $t("admin.page.postTag.form.isHot") }}</label>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.status") }}</label>
            <Field name="publish" v-slot="{ field, errorMessage, meta }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import { POSTTAGINDEX } from "~/utils/constants";
import Button from "~/components/admin/custom/Button.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { usePostTag } from "~/composables/admin/post/usePostTag";

const { handleStore, isLoading, config, slug } = usePostTag();

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
