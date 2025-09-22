<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.brand.breadcrumb'), to: '/admin/brand/catalogue' }, { label: $t('admin.common.edit') }]" />
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="align-items-center">
            <h5>{{ $t("admin.common.titleNote") }}</h5>
            <span>
              {{ $t("admin.common.note") }}
              <p class="fst-italic">
                {{ $t("admin.common.required") }}
              </p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="handleUpdate" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t("admin.page.brand.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.brand.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.page.brand.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.page.brand.form.description") }}</label>
            <Field name="description" v-slot="{ field }">
              <textarea v-bind="field" rows="4" class="form-control" :placeholder="$t('admin.page.brand.form.descriptionPlaceholder')" />
            </Field>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="email">
                  {{ $t("admin.page.brand.form.email") }}:
                  <span class="text-danger">*</span>
                </label>
                <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="email" type="email" :placeholder="$t('admin.page.brand.form.emailPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="website">
                  {{ $t("admin.page.brand.form.website") }}
                </label>
                <Field name="website" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="website" type="text" :placeholder="$t('admin.page.brand.form.websitePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="country">
                  {{ $t("admin.page.brand.form.country") }}
                </label>
                <Field name="country" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="country" type="text" :placeholder="$t('admin.page.brand.form.countryPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="order">{{ $t("admin.page.brand.form.order") }}:</label>
                <Field name="order" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="BRANDINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-body p-4">
          <label class="form-label">{{ $t("admin.common.image") }}</label>
          <div class="text-center">
            <SingleImageUpload :initial-url="currentLogoUrl" :note="$t('admin.common.noteUploadImage')" @selected="(f)=> setValues({ ...(values as any), logo: f } as any)" />
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.status") }}</label>
            <Field name="publish" v-slot="{ field, errorMessage, meta }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
      </div>
    </div>
  </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import { useBrand } from "~/composables/admin/brand/useBrand";
import { BRANDINDEX } from "~/utils/constants";
import SingleImageUpload from "~/components/admin/custom/SingleImageUpload.vue";

const { config, isLoading, values, slug, show, handleUpdate, setValues } = useBrand(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);
setValues({
  name: data?.name,
  description: data?.description,
  email: data?.email,
  website: data?.website,
  country: data?.country,
  order: data?.order ?? 0,
  publish: data?.publish ?? 0,
});


const currentLogoUrl = ref<string | null>(null);
currentLogoUrl.value = data?.logo?.path ? data?.logo?.url || null : null;

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
