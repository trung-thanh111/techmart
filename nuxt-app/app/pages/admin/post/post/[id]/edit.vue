<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.post.breadcrumb'), to: '/admin/post/post' }, { label: $t('admin.common.edit') }]" />
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
          <div class="">
            <label class="form-label" for="name">
              {{ $t("admin.page.post.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.post.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.page.postCatalogue.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="content">
              {{ $t("admin.page.post.form.content") }}
            </label>
            <Field name="content" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <div class="w-full">
                <CKEditor v-model="field.value" @update:modelValue="field.onChange" class="h-64" />
                <small v-if="errorMessage && meta.touched" class="text-danger">
                  {{ $t(errorMessage) }}
                </small>
              </div>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label" for="description">
              {{ $t("admin.page.post.form.description") }}
            </label>
            <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <textarea v-bind="field" id="description" rows="3" :placeholder="$t('admin.page.post.form.descriptionPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="order">{{ $t("admin.page.post.form.order") }}:</label>
                <Field name="order" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                  </small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="viewCount">
                  {{ $t("admin.page.post.form.viewCount") }}
                </label>
                <Field name="viewCount" v-slot="{ field, errorMessage, meta }">
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
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.seo") }}</h5>
        </div>
        <div class="card-body">
          <SeoPreview :metaTitle="values.metaTitle" :name="values.name" :metaDescription="values.metaDescription" :description="values.description" />
          <div class="mb-3">
            <label class="form-label" for="metaTitle">{{ $t("admin.common.metaTitle") }}</label>
            <Field name="metaTitle" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" id="metaTitle" type="text" class="form-control" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.metaKeyword") }}</label>
            <Field name="metaKeyword" v-slot="{ field, errorMessage, meta }">
              <textarea v-bind="field" rows="2" class="form-control" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.metaDescription") }}</label>
            <Field name="metaDescription" v-slot="{ field, errorMessage, meta }">
              <textarea v-bind="field" rows="3" class="form-control" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button variant="cancel" :loading="false" :to="POSTINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.image") }}</h5>
        </div>
        <div class="card-body p-4">
          <div class="text-center">
            <SingleImageUpload :initial-url="currentImageUrl" :note="$t('admin.common.noteUploadImage')" @selected="(f)=> setValues({ ...(values as any), image: f } as any)" />
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.relation") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.page.post.form.postCatalogue") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="postCatalogues" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="postCatalogueOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.page.post.form.postCataloguePlaceholder')" class="w-100" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.page.post.form.tag") }}
            </label>
            <Field name="postTags" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="tagOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.page.post.form.tagPlaceholder')" class="rounded py-[1px] w-100" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.page.post.form.related") }}
            </label>
            <Field name="relateds" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="postOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.page.post.form.relatedPlaceholder')" class="rounded py-[1px] w-100" size="small" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="publishedAt">
              {{ $t("admin.page.post.form.publishedAt") }}
            </label>
            <Field name="publishedAt" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="publishedAt" type="datetime-local" class="form-control rounded py-[1px] w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div class="mb-3">
            <div class="form-check d-flex align-items-center gap-2 ps-0">
              <Field name="isHot" v-slot="{ field }">
                <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="is_hot" class="scale-125" />
              </Field>
              <label for="is_hot" class="mb-0">{{ $t("admin.page.post.form.isHot") }}</label>
            </div>
          </div>
          <div class="mb-3">
            <div class="form-check d-flex align-items-center gap-2 ps-0">
              <Field name="tableOfContent" v-slot="{ field }">
                <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="table_of_content" class="scale-125" />
              </Field>
              <label for="table_of_content" class="mb-0">{{ $t("admin.page.post.form.tableOfContent") }}</label>
            </div>
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
import { usePost } from "~/composables/admin/post/usePost";
import { POSTINDEX } from "~/utils/constants";
import SingleImageUpload from "~/components/admin/custom/SingleImageUpload.vue";
import CKEditor from "~/components/admin/custom/CKEditor.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import SeoPreview from "~/components/admin/custom/SeoPreview.vue";

const { isLoading, tagOptions, postCatalogueOptions, postOptions, config, slug, show, handleUpdate, setValues, values } = usePost(undefined, "update");
const id = Number(useRoute().params.id);
const { data } = await show(id);
const currentImageUrl = ref<string | null>(null);
currentImageUrl.value = data?.image?.path ? data?.image?.url || null : null;

setValues({
  name: data.name,
  description: data.description,
  content: data.content,
  tableOfContent: data.tableOfContent,
  order: data.order,
  isHot: data.isHot,
  viewCount: data.viewCount,
  // ép kiểu để load data & phù hợp với Interface
  postCatalogues: (data?.postCatalogues as { id: number; name: string }[] | undefined)?.map((c) => c.id) ?? [],
  postTags: (data?.tags as { id: number; name: string }[] | undefined)?.map((t) => t.id) ?? [],
  relateds: (data?.relateds as { id: number; name: string }[] | undefined)?.map((r) => r.id) ?? [],
  publish: data.publish || 0,
  metaTitle: data.metaTitle,
  metaKeyword: data.metaKeyword,
  metaDescription: data.metaDescription,
  publishedAt: data.publishedAt,
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
