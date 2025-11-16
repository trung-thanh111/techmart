<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.promotion.breadcrumb'), to: PROMOTIONINDEX }, { label: $t('admin.common.edit') }]" />
        </div>
      </div>
    </div>
  </div>

  <form @submit.prevent="handleUpdate" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.page.promotion.basicInfo") }}</h5>
        </div>
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
          </div>

          <div class="mb-3">
            <label class="form-label" for="couponCode">
              {{ $t("admin.page.promotion.couponCode") }}
            </label>
            <Field name="couponCode" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" id="couponCode" type="text" :placeholder="$t('admin.page.promotion.couponCodePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>

          <div class="mb-3">
            <label class="form-label" for="description">
              {{ $t("admin.common.form.description") }}
            </label>
            <Field name="description" v-slot="{ field }">
              <textarea v-bind="field" id="description" rows="4" class="form-control" :placeholder="$t('admin.common.form.descriptionPlaceholder')" />
            </Field>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.page.promotion.discountInfo") }}</h5>
        </div>
        <div class="card-body">
          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="discountType">
                {{ $t("admin.page.promotion.discountType") }}:
                <span class="text-danger">*</span>
              </label>
              <Field name="discountType" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="discountTypeOptions" optionLabel="name" optionValue="value" :placeholder="$t('admin.page.promotion.selectDiscountType')" class="w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                <small v-if="errorMessage && meta.touched" class="text-danger">
                  {{ $t(errorMessage) }}
                </small>
              </Field>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label" for="discountValue">
                {{ $t("admin.page.promotion.discountValue") }}:
                <span class="text-danger">*</span>
              </label>
              <Field name="discountValue" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" :suffix="values.discountType === 'percent' ? '%' : ''" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                <small v-if="errorMessage && meta.touched" class="text-danger">
                  {{ $t(errorMessage) }}
                </small>
              </Field>
            </div>
          </div>

          <div class="mb-3" v-if="values.discountType === 'percent'">
            <label class="form-label" for="maxDiscountValue">
              {{ $t("admin.page.promotion.maxDiscountValue") }}
            </label>
            <Field name="maxDiscountValue" v-slot="{ field }">
              <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
            </Field>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="startDate">
                {{ $t("admin.page.promotion.startDate") }}
              </label>
              <Field name="startDate" v-slot="{ field }">
                <input v-bind="field" id="startDate" type="datetime-local" class="form-control" />
              </Field>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label" for="endDate">
                {{ $t("admin.page.promotion.endDate") }}
              </label>
              <Field name="endDate" v-slot="{ field }">
                <input v-bind="field" id="endDate" type="datetime-local" class="form-control" />
              </Field>
            </div>
          </div>

          <div class="row">
            <div class="col-md-6 mb-3">
              <label class="form-label" for="priority">
                {{ $t("admin.page.promotion.priority") }}
              </label>
              <Field name="priority" v-slot="{ field }">
                <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
              </Field>
            </div>

            <div class="col-md-6 mb-3">
              <label class="form-label d-flex align-items-center gap-2">
                {{ $t("admin.page.promotion.canCombine") }}
                <Field name="canCombine" v-slot="{ field }">
                  <InputSwitch :modelValue="field.value" @update:modelValue="field.onChange" />
                </Field>
              </label>
            </div>
          </div>
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header d-flex justify-content-between align-items-center">
          <h5 class="card-title mb-0">{{ $t("admin.page.promotion.promotionTargets") }}:</h5>
          <button type="button" class="btn btn-sm btn-success" @click="addPromotionTarget">
            <i class="ri-add-line me-1"></i>
            {{ $t("admin.common.add") }}
          </button>
        </div>
        <div class="card-body">
          <Field name="promotionTargets" v-slot="{ field, errorMessage, meta }">
            <div v-if="errorMessage && meta.touched" class="alert alert-danger mb-3">
              {{ $t(errorMessage) }}
            </div>
            <div v-for="(target, index) in promotionTargets" :key="target.uid" class="border rounded p-3 mb-3">
              <div class="d-flex justify-content-between align-items-start mb-3">
                <h6 class="mb-0">{{ $t("admin.page.promotion.target") }} #{{ index + 1 }}</h6>
                <button type="button" class="btn btn-sm btn-danger" @click="removePromotionTarget(index)" v-if="promotionTargets.length > 1">
                  <i class="ri-delete-bin-line"></i>
                </button>
              </div>

              <div class="mb-3">
                <label class="form-label mb-2">{{ $t("admin.page.promotion.target") }}</label>
                <div class="d-flex flex-column gap-2">
                  <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                    <label class="form-label mb-0">{{ $t("admin.page.promotion.product") }}</label>
                    <InputSwitch :modelValue="target.enableProduct" @update:modelValue="(val: boolean) => handleTargetToggle(index, 'product', val)" />
                  </div>
                  <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                    <label class="form-label mb-0">{{ $t("admin.page.promotion.productVariant") }}</label>
                    <InputSwitch :modelValue="target.enableVariant" @update:modelValue="(val: boolean) => handleTargetToggle(index, 'variant', val)" />
                  </div>
                  <div class="d-flex align-items-center justify-content-between p-2 border rounded">
                    <label class="form-label mb-0">{{ $t("admin.page.promotion.productCatalogue") }}</label>
                    <InputSwitch :modelValue="target.enableCatalogue" @update:modelValue="(val: boolean) => handleTargetToggle(index, 'catalogue', val)" />
                  </div>
                </div>
              </div>

              <div class="mb-3" v-if="target.enableProduct">
                <label class="form-label">{{ $t("admin.page.promotion.product") }}</label>
                <Select :modelValue="target.productId" @update:modelValue="(val) => handleTargetChange(index, 'productId', val ? Number(val) : null)" :options="productOptions" optionLabel="name" optionValue="value" filter showClear :placeholder="$t('admin.page.promotion.selectProduct')" class="w-100" />
              </div>

              <div v-if="target.enableVariant">
                <div class="mb-3">
                  <label class="form-label">{{ $t("admin.page.promotion.product") }}</label>
                  <Select :modelValue="target.selectedProductId" @update:modelValue="(val) => handleProductSelectForVariant(index, val ? Number(val) : null)" :options="productOptions" optionLabel="name" optionValue="value" filter showClear :placeholder="$t('admin.page.promotion.selectProduct')" class="w-100" />
                </div>
                <div class="mb-3" v-if="target.selectedProductId">
                  <label class="form-label">{{ $t("admin.page.promotion.productVariant") }}</label>
                  <Select :modelValue="target.productVariantId" @update:modelValue="(val) => handleTargetChange(index, 'productVariantId', val ? Number(val) : null)" :options="getVariantOptions(index)" optionLabel="name" optionValue="value" filter showClear :placeholder="$t('admin.page.promotion.selectProductVariant')" class="w-100" />
                </div>
              </div>

              <div class="mb-3" v-if="target.enableCatalogue">
                <label class="form-label">{{ $t("admin.page.promotion.productCatalogue") }}</label>
                <Select :modelValue="target.productCatalogueId" @update:modelValue="(val) => handleTargetChange(index, 'productCatalogueId', val ? Number(val) : null)" :options="productCatalogueOptions" optionLabel="name" optionValue="value" filter showClear :placeholder="$t('admin.page.promotion.selectProductCatalogue')" class="w-100" />
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label d-flex align-items-center gap-2">
                    {{ $t("admin.page.promotion.isGift") }}
                    <InputSwitch :modelValue="target.isGift" @update:modelValue="(val: boolean) => handleTargetChange(index, 'isGift', val)" />
                  </label>
                </div>

                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t("admin.page.promotion.minQuantity") }}:</label>
                  <InputNumber :modelValue="target.minQuantity" @update:modelValue="(val) => handleTargetChange(index, 'minQuantity', val)" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                </div>

                <div class="col-md-3 mb-3">
                  <label class="form-label">{{ $t("admin.page.promotion.maxQuantity") }}</label>
                  <InputNumber :modelValue="target.maxQuantity" @update:modelValue="(val) => handleTargetChange(index, 'maxQuantity', val)" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                </div>
              </div>

              <div class="row">
                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t("admin.page.promotion.minValue") }}</label>
                  <InputNumber :modelValue="target.minValue" @update:modelValue="(val) => handleTargetChange(index, 'minValue', val)" :min="0" :maxFractionDigits="2" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" mode="currency" currency="VND" locale="vi-VN" />
                </div>

                <div class="col-md-6 mb-3">
                  <label class="form-label">{{ $t("admin.page.promotion.maxValue") }}</label>
                  <InputNumber :modelValue="target.maxValue" @update:modelValue="(val) => handleTargetChange(index, 'maxValue', val)" :min="0" :maxFractionDigits="2" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" mode="currency" currency="VND" locale="vi-VN" />
                </div>
              </div>
            </div>
            <div v-if="promotionTargets.length === 0" class="text-center text-muted py-4">
              {{ $t("admin.page.promotion.noTargets") }}
            </div>
          </Field>
        </div>
      </div>

      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="PROMOTIONINDEX" />
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
            <label class="form-label" for="promotionCatalogueId">
              {{ $t("admin.page.promotion.promotionCatalogue") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="promotionCatalogueId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="promotionCatalogueOptions" optionLabel="name" optionValue="value" filter :placeholder="$t('admin.page.promotion.selectPromotionCatalogue')" class="w-100" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
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
import { PROMOTIONINDEX } from "~/utils/constants";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { usePromotion } from "~/composables/admin/promotion/usePromotion";
import type { IPromotionTarget } from "~/interfaces/promotion/promotion.interface";

const { config, isLoading, values, show, handleUpdate, setValues, promotionCatalogueOptions, productOptions, productCatalogueOptions, productVariantOptions, loadProductVariants } = usePromotion(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);

// Format dates for datetime-local input
const formatDateForInput = (dateString: string | null | undefined): string => {
  if (!dateString) return "";
  const date = new Date(dateString);
  const year = date.getFullYear();
  const month = String(date.getMonth() + 1).padStart(2, "0");
  const day = String(date.getDate()).padStart(2, "0");
  const hours = String(date.getHours()).padStart(2, "0");
  const minutes = String(date.getMinutes()).padStart(2, "0");
  return `${year}-${month}-${day}T${hours}:${minutes}`;
};

const discountTypeOptions = [
  { name: "Phần trăm (%)", value: "percent" },
  { name: "Số tiền", value: "amount" },
];

const promotionTargets = ref<(IPromotionTarget & { uid: string; selectedProductId?: number | null; enableProduct?: boolean; enableVariant?: boolean; enableCatalogue?: boolean })[]>([]);

// Load promotion targets from data
if (data.promotionTargets && data.promotionTargets.length > 0) {
  promotionTargets.value = data.promotionTargets.map((target: IPromotionTarget) => {
    // Determine which type is enabled based on which field has value
    const enableProduct = !!target.productId;
    const enableVariant = !!target.productVariantId;
    const enableCatalogue = !!target.productCatalogueId;

    // If variant is enabled, we need to find the product ID from the variant
    let selectedProductId = null;
    if (enableVariant && target.productVariantId) {
      // We'll need to load variants to find the product, but for now set it from the variant relationship if available
      selectedProductId = target.productVariant?.productId || null;
    }

    return {
      ...target,
      uid: target.id?.toString() || crypto.randomUUID(),
      selectedProductId,
      enableProduct,
      enableVariant,
      enableCatalogue,
      isGift: target.isGift ?? false,
      minQuantity: target.minQuantity ?? 1,
    };
  });

  // Load product variants for targets that have variants
  promotionTargets.value.forEach(async (target) => {
    if (target.enableVariant && target.selectedProductId) {
      await loadProductVariants(target.selectedProductId);
    }
  });
} else {
  promotionTargets.value = [
    {
      uid: crypto.randomUUID(),
      productId: null,
      productVariantId: null,
      productCatalogueId: null,
      selectedProductId: null,
      enableProduct: false,
      enableVariant: false,
      enableCatalogue: false,
      isGift: false,
      minQuantity: 1,
      maxQuantity: null,
      minValue: null,
      maxValue: null,
    },
  ];
}

setValues({
  name: data.name,
  couponCode: data.couponCode,
  description: data.description,
  promotionCatalogueId: data.promotionCatalogueId,
  discountValue: data.discountValue,
  discountType: data.discountType,
  maxDiscountValue: data.maxDiscountValue,
  startDate: formatDateForInput(data.startDate),
  endDate: formatDateForInput(data.endDate),
  priority: data.priority ?? 0,
  canCombine: data.canCombine ?? false,
  publish: data.publish,
  promotionTargets: promotionTargets.value.map(({ uid, selectedProductId, enableProduct, enableVariant, enableCatalogue, ...rest }) => ({
    ...rest,
    isGift: rest.isGift ?? false,
    minQuantity: rest.minQuantity ?? 1,
  })),
});

// Handle target toggle - only allow one to be enabled at a time
const handleTargetToggle = (index: number, type: "product" | "variant" | "catalogue", enabled: boolean) => {
  const target = promotionTargets.value[index];
  if (!target) return;

  if (enabled) {
    // Disable all other toggles
    target.enableProduct = type === "product";
    target.enableVariant = type === "variant";
    target.enableCatalogue = type === "catalogue";

    // Clear all target fields
    target.productId = null;
    target.productVariantId = null;
    target.productCatalogueId = null;
    target.selectedProductId = null;
  } else {
    // Disable the current toggle
    if (type === "product") {
      target.enableProduct = false;
      target.productId = null;
    } else if (type === "variant") {
      target.enableVariant = false;
      target.productVariantId = null;
      target.selectedProductId = null;
    } else if (type === "catalogue") {
      target.enableCatalogue = false;
      target.productCatalogueId = null;
    }
  }

  syncPromotionTargets();
};

// Handle product select for variant
const handleProductSelectForVariant = async (index: number, productId: number | null) => {
  const target = promotionTargets.value[index];
  if (!target) return;

  target.selectedProductId = productId;
  target.productVariantId = null; // Clear variant when product changes

  if (productId) {
    await loadProductVariants(productId);
  }

  syncPromotionTargets();
};

// Get variant options for a target
const getVariantOptions = (index: number) => {
  const target = promotionTargets.value[index];
  if (!target?.selectedProductId) return [];
  return productVariantOptions.value[target.selectedProductId] || [];
};

// Handle target field change
const handleTargetChange = (index: number, field: string, value: any) => {
  const target = promotionTargets.value[index];
  if (!target) return;

  // If setting a target field, clear others and update toggles
  if (field === "productId") {
    target.productVariantId = null;
    target.productCatalogueId = null;
    target.selectedProductId = null;
    target.enableProduct = value !== null && value !== undefined;
    target.enableVariant = false;
    target.enableCatalogue = false;
  } else if (field === "productVariantId") {
    target.productId = null;
    target.productCatalogueId = null;
    target.enableProduct = false;
    target.enableVariant = value !== null && value !== undefined;
    target.enableCatalogue = false;
  } else if (field === "productCatalogueId") {
    target.productId = null;
    target.productVariantId = null;
    target.selectedProductId = null;
    target.enableProduct = false;
    target.enableVariant = false;
    target.enableCatalogue = value !== null && value !== undefined;
  }

  (target as any)[field] = value;
  syncPromotionTargets();
};

const addPromotionTarget = () => {
  promotionTargets.value.push({
    uid: crypto.randomUUID(),
    productId: null,
    productVariantId: null,
    productCatalogueId: null,
    selectedProductId: null,
    enableProduct: false,
    enableVariant: false,
    enableCatalogue: false,
    isGift: false,
    minQuantity: 1,
    maxQuantity: null,
    minValue: null,
    maxValue: null,
  });
  syncPromotionTargets();
};

const removePromotionTarget = (index: number) => {
  promotionTargets.value.splice(index, 1);
  syncPromotionTargets();
};

const syncPromotionTargets = () => {
  const targets = promotionTargets.value.map(({ uid, selectedProductId, enableProduct, enableVariant, enableCatalogue, ...rest }) => rest);
  setValues({ ...values, promotionTargets: targets } as any);
};

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
