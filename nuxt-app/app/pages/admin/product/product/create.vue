<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.product.breadcrumb'), to: '/admin/product/product' }, { label: $t('admin.common.create') }]" />
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
              <p class="fst-italic">{{ $t("admin.common.required") }}</p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="() => handleStore(false)" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="">
            <label class="form-label" for="name">
              {{ $t("admin.common.form.title") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.common.form.titlePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label" for="description">{{ $t("admin.common.form.description") }}</label>
            <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <div class="w-full">
                <CKEditor v-model="field.value" @update:modelValue="field.onChange" class="h-64" />
                <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
              </div>
            </Field>
          </div>
          <div class="card">
            <div class="card-header">
              <h5 class="card-title mb-0">{{ $t("admin.common.image") }}</h5>
            </div>
            <div class="card-body">
              <MultipleImageUpload v-model="albumData" field-name="album" :max-files="15" @change="onAlbumChange" />
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="order">{{ $t("admin.common.form.order") }}:</label>
                <Field name="sortOrder" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="likeCount">{{ $t("admin.common.form.likeCount") }}</label>
                <Field name="likeCount" v-slot="{ field, errorMessage, meta }">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <Field name="productVariants" v-slot="{ field, errorMessage, meta }">
        <VariantManager :product-sku="values.sku" :product-slug="slug" :product-price="values.priceOrigin" :model-value="field.value" @update:model-value="field.onChange" />
        <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
      </Field>
      <div class="card mt-4">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.form.faq") }}</h5>
        </div>
        <div class="card-body">
          <Field name="productFaqs" v-slot="{ field, errorMessage, meta }">
            <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="faqOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.faqPlaceholder')" class="w-100" />
            <small v-if="errorMessage && meta.touched" class="text-danger">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
      </div>
      <SEO :values="values" />
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="PRODUCTINDEX" />
        <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleStore(false)" />
        <Button type="button" variant="success" :loading="isLoading" @click="() => handleStore(true)" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.customize") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.price") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="priceOrigin" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" :maxFractionDigits="2" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" mode="currency" currency="VND" locale="vi-VN" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.common.form.sku") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="sku" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="sku" type="text" :placeholder="$t('admin.common.form.skuPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" disabled />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.sold") }}:</label>
            <Field name="sold" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" disabled />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.season") }}:</label>
            <Field name="season" v-slot="{ field }" :validate-on-input="true">
              <Select v-model="field.value" @update:modelValue="field.onChange" :options="seasonOptions" option-label="name" option-value="value" showClear :placeholder="$t('admin.common.form.selectSeason')" class="w-full text-sm" checkmark />
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.warranty") }}:</label>
            <Field name="warranty" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" :placeholder="$t('admin.common.form.warrantyPlaceholder')" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
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
              {{ $t("admin.common.form.productCatalogue") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="productCatalogues" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="productCatalogueOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.productCataloguePlaceholder')" class="w-100">
                <template #header>
                  <div class="inline-flex items-center justify-center p-2">
                    <span class="font-medium whitespace-nowrap">{{ $t("admin.common.form.productCatalogue") }}</span>
                  </div>
                </template>
                <template #footer>
                  <div class="p-3 cursor-pointer flex items-center justify-end font-light text-xs text-emerald-600 gap-1">
                    <i class="ri-add-line text-emerald-600"></i>
                    <span @click="showCreateModal = true">Thêm mới</span>
                  </div>
                </template>
              </MultiSelect>
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.tag") }}</label>
            <Field name="productTags" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="tagOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.tagPlaceholder')" class="rounded py-[1px] w-100">
                <template #header>
                  <div class="inline-flex items-center justify-center p-2">
                    <span class="font-medium whitespace-nowrap">{{ $t("admin.common.form.tag") }}</span>
                  </div>
                </template>
                <template #footer>
                  <div class="p-3 cursor-pointer flex items-center justify-end font-light text-xs text-emerald-600 gap-1">
                    <i class="ri-add-line text-emerald-600"></i>
                    <span @click="showCreateTagModal = true">Thêm mới</span>
                  </div>
                </template>
              </MultiSelect>
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.brand") }}</label>
            <Field name="brandId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="brandOptions" optionLabel="name" optionValue="id" filter :placeholder="$t('admin.common.form.brandPlaceholder')" class="w-100 text-sm" showClear checkmark />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t("admin.common.form.related") }}</label>
            <Field name="relateds" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="productOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.relatedPlaceholder')" class="rounded py-[1px] w-100" size="small" />
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
            <div class="form-check d-flex align-items-center gap-2 ps-0">
              <Field name="isHot" v-slot="{ field }">
                <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="is_hot" class="scale-125" />
              </Field>
              <label for="is_hot" class="mb-0">{{ $t("admin.common.form.isHot") }}</label>
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
  <Dialog v-model:visible="showCreateModal" modal maximizable :header="$t('admin.common.create')" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" append-to="body">
    <form @submit.prevent="handleCreateCatalogue">
      <div class="mb-3">
        <label class="form-label" for="catalogueName">
          {{ $t("admin.common.form.name") }}:
          <span class="text-danger">*</span>
        </label>
        <input v-model="catalogueForm.name" id="catalogueName" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.form.order") }}</label>
        <InputNumber v-model="catalogueForm.order" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.form.description") }}</label>
        <textarea v-model="catalogueForm.description" rows="4" class="form-control" :placeholder="$t('admin.common.form.descriptionPlaceholder')" />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.status") }}</label>
        <PublishSelect v-model="catalogueForm.publish" />
      </div>
      <div class="flex justify-end gap-3 mt-4">
        <Button type="button" variant="cancel" @click="showCreateModal = false" />
        <Button type="submit" variant="success" :loading="catalogueForm.isLoading" />
      </div>
    </form>
  </Dialog>

  <!-- Modal tạo Tag -->
  <Dialog v-model:visible="showCreateTagModal" modal maximizable :header="$t('admin.common.create')" :style="{ width: '40rem' }" :breakpoints="{ '1199px': '75vw', '575px': '90vw' }" append-to="body">
    <form @submit.prevent="handleCreateTag">
      <div class="mb-3">
        <label class="form-label" for="tagName">
          {{ $t("admin.common.form.name") }}:
          <span class="text-danger">*</span>
        </label>
        <input v-model="tagForm.name" id="tagName" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" required />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.form.order") }}</label>
        <InputNumber v-model="tagForm.order" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.form.description") }}</label>
        <textarea v-model="tagForm.description" rows="4" class="form-control" :placeholder="$t('admin.common.form.descriptionPlaceholder')" />
      </div>
      <div class="mb-3">
        <label class="form-label">{{ $t("admin.common.status") }}</label>
        <PublishSelect v-model="tagForm.publish" />
      </div>
      <div class="flex justify-end gap-3 mt-4">
        <Button type="button" variant="cancel" @click="showCreateTagModal = false" />
        <Button type="submit" variant="success" :loading="tagForm.isLoading" />
      </div>
    </form>
  </Dialog>
</template>
<script setup lang="ts">
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import { PRODUCTINDEX } from "~/utils/constants";
import { useProduct } from "~/composables/admin/product/useProduct";
import CKEditor from "~/components/admin/custom/CKEditor.vue";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import SEO from "~/components/admin/custom/SEO.vue";
import MultipleImageUpload from "~/components/admin/custom/MultipleImageUpload.vue";
import type { AlbumItem } from "~/interfaces/upload/image.interface";
import { generateSKU } from "~/utils/helpers";
import VariantManager from "~/components/admin/custom/VariantManager.vue";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { productTagService } from "~/services/product/tag.service";

const { isLoading, values, tagOptions, productCatalogueOptions, productOptions, brandOptions, faqOptions, seasonOptions, config, slug, setValues, handleStore, loadProductCatalogue, loadTag } = useProduct();

const catalogueForm = ref({
  name: "",
  order: 0,
  description: "",
  publish: 2,
  isLoading: false,
});

const tagForm = ref({
  name: "",
  order: 0,
  description: "",
  publish: 2,
  isLoading: false,
});

const showCreateTagModal = ref(false);

const handleCreateCatalogue = async () => {
  try {
    catalogueForm.value.isLoading = true;
    const catalogueData = {
      name: catalogueForm.value.name,
      order: catalogueForm.value.order || 1,
      description: catalogueForm.value.description || "",
      publish: catalogueForm.value.publish || 2,
    };
    const response = await productCatalogueService.store(catalogueData);
    if (response && typeof response === "object" && "data" in response && response.data?.id) {
      await loadProductCatalogue();
      const currentCatalogues = values.productCatalogues || [];
      setValues({ ...values, productCatalogues: [...currentCatalogues, response.data.id] });
      showCreateModal.value = false;
      catalogueForm.value = {
        name: "",
        order: 0,
        description: "",
        publish: 2,
        isLoading: false,
      };
    }
  } catch (error) {
    console.error("Error creating catalogue:", error);
  } finally {
    catalogueForm.value.isLoading = false;
  }
};

const handleCreateTag = async () => {
  try {
    tagForm.value.isLoading = true;
    const tagData = {
      name: tagForm.value.name,
      order: tagForm.value.order || 1,
      description: tagForm.value.description || "",
      publish: tagForm.value.publish || 2,
    };
    const response = await productTagService.store(tagData);
    if (response && typeof response === "object" && "data" in response && response.data?.id) {
      await loadTag();
      showCreateTagModal.value = false;
      tagForm.value = {
        name: "",
        order: 0,
        description: "",
        publish: 2,
        isLoading: false,
      };
    }
  } catch (error) {
    console.error("Error creating tag:", error);
  } finally {
    tagForm.value.isLoading = false;
  }
};

const albumData = ref<AlbumItem[] | null>(null);
const showCreateModal = ref(false);

const onAlbumChange = () => {
  setValues({ ...values, album: albumData.value });
};

onMounted(() => {
  if (!values.sku) {
    const sku = generateSKU();
    setValues({ ...values, sku });
  }
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
