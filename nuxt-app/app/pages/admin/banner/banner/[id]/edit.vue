<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.banner.breadcrumb'), to: BANNERINDEX }, { label: $t('admin.common.edit') }]" />
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
              {{ $t("admin.common.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="order">{{ $t("admin.common.form.order") }}:</label>
                <Field name="order" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" :placeholder="$t('admin.common.form.orderPlaceholder')" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="linkUrl">
                  {{ $t("admin.common.form.linkUrl") }}
                </label>
                <Field name="linkUrl" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="linkUrl" type="text" :placeholder="$t('admin.common.form.linkUrlPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="startAt">
                  {{ $t("admin.common.form.startAt") }}
                </label>
                <Field name="startAt" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="startAt" type="datetime-local" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="endAt">
                  {{ $t("admin.common.form.endAt") }}
                </label>
                <Field name="endAt" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="startAt" type="datetime-local" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.image") }}</h5>
        </div>
        <div class="card-body">
          <MultipleImageUpload v-model="albumData" field-name="album" :max-files="15" @change="onAlbumChange" />
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="BANNERINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="form-check d-flex align-items-center gap-2 ps-0 mb-3">
            <Field name="isHot" v-slot="{ field }">
              <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="is_hot" class="scale-125" />
            </Field>
            <label for="is_hot" class="mb-0">
              {{ $t("admin.common.form.isHot") }}
            </label>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.type") }}</label>
            <Field name="type" v-slot="{ field, errorMessage, meta }">
              <Select v-model="field.value" :options="typeOptions" @update:modelValue="field.onChange" option-label="name" option-value="value" filter showClear :placeholder="$t('admin.common.form.type')" class="rounded py-[1px] w-100" size="small" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.position") }}</label>
            <Field name="position" v-slot="{ field, errorMessage, meta }">
              <Select v-model="field.value" :options="positionOptions" @update:modelValue="field.onChange" option-label="name" option-value="value" filter showClear :placeholder="$t('admin.common.form.position')" class="rounded py-[1px] w-100" size="small" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
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
import { useBanner } from "~/composables/admin/banner/useBanner";
import { BANNERINDEX } from "~/utils/constants";
import MultipleImageUpload from "~/components/admin/custom/MultipleImageUpload.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import type { AlbumItem } from "~/interfaces/upload/image.interface";

const { config, isLoading, values, slug, show, handleUpdate, setValues, positionOptions, typeOptions } = useBanner(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);

const albumData = ref<AlbumItem[] | null>(null);
const onAlbumChange = () => {
  setValues({ ...values, album: albumData.value });
};
albumData.value = data?.album ?? null;

setValues({
  name: data?.name,
  order: data?.order,
  linkUrl: data?.linkUrl,
  startAt: data?.startAt,
  endAt: data?.endAt,
  isHot: data?.isHot ?? false,
  type: data?.type,
  position: data?.position,
  publish: data?.publish,
  album: data?.album ?? null,
} as any);

watch(
  albumData,
  (val) => {
    setValues({ ...(values as any), album: val } as any);
  },
  { immediate: false, deep: true }
);

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
