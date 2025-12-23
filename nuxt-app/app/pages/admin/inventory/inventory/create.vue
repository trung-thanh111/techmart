<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.inventory.breadcrumb'), to: INVENTORYINDEX }, { label: $t('admin.common.create') }]" />
        </div>
      </div>
    </div>
  </div>

  <form @submit.prevent="() => handleStore(false)" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t("admin.common.form.agency") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="agencyId" v-slot="{ field }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="agencyOptions" optionLabel="name" optionValue="id" filter showClear :placeholder="$t('admin.common.form.agencyPlaceholder')" class="rounded py-[1px] w-100" size="small" />
            </Field>
          </div>
          <div class="row">
            <div class="col-md-4">
              <div class="mb-3">
                <label class="form-label">
                  {{ $t("admin.common.form.product") }}:
                  <span class="text-danger">*</span>
                </label>
                <Select v-model="selectedProductId" @update:modelValue="handleProductChange" :options="productOptions" optionLabel="name" optionValue="id" filter showClear :placeholder="$t('admin.common.form.productPlaceholder')" class="rounded py-[1px] w-100" size="small" />
              </div>
            </div>
            <div class="col-md-8">
              <label class="form-label">
                {{ $t("admin.common.form.variant") }}:
                <span class="text-danger">*</span>
              </label>
              <Field name="productVariantId" v-slot="{ field }" :validate-on-input="true">
                <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="productVariantOptions" optionLabel="name" optionValue="id" filter showClear :placeholder="$t('admin.common.form.productVariantPlaceholder')" class="rounded py-[1px] w-100" size="small" :disabled="disableProductVariant" />
              </Field>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="stock">
                  {{ $t("admin.common.form.stock") }}
                </label>
                <Field name="stock" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" mode="decimal" input-class="!h-[38px] text-sm" showButtons :min="0" fluid class="w-full" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="priceOverride">
                  {{ $t("admin.common.form.priceOverride") }}
                </label>
                <Field name="priceOverride" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" :maxFractionDigits="2" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" mode="currency" currency="VND" locale="vi-VN" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="INVENTORYINDEX" />
        <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleStore(false)" />
        <Button type="button" variant="success" :loading="isLoading" @click="() => handleStore(true)" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
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
import { ref } from "vue";
import { Field } from "vee-validate";
import { INVENTORYINDEX } from "~/utils/constants";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import { useInventory } from "~/composables/admin/inventory/useInventory";

const { handleStore, setValues, agencyOptions, productOptions, values, isLoading, handleChangeProduct } = useInventory();
const productVariantOptions = ref<{ id: number; name: string }[]>([]);
const disableProductVariant = ref<boolean>(true);
const priceOverrideRef = ref<number>(0);
const selectedProductId = ref<number | null>(null);

const handleProductChange = (val: number | null) => {
  selectedProductId.value = val;
  handleChangeProduct(val, setValues, productVariantOptions, disableProductVariant, priceOverrideRef);
};

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
