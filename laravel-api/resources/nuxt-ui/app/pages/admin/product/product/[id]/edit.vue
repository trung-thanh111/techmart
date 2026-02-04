<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.product.breadcrumb'), to: '/product/product' }, { label: $t('admin.common.edit') }]" />
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
    <form @submit.prevent="() => handleUpdate(false)" class="row space-y-4">
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
                        <label class="form-label" for="description">
                            {{ $t("admin.common.form.description") }}
                        </label>
                        <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <div class="w-full">
                                <CKEditor :model-value="field.value" @update:model-value="field.onChange" height="h-64" />
                                <small v-if="errorMessage && meta.touched" class="text-danger">
                                    {{ $t(errorMessage) }}
                                </small>
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
                                <label class="form-label" for="likeCount">
                                    {{ $t("admin.common.form.likeCount") }}
                                </label>
                                <Field name="likeCount" v-slot="{ field, errorMessage, meta }">
                                    <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">
                                        {{ $t(errorMessage) }}
                                    </small>
                                </Field>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <Field name="productVariants" v-slot="{ field, errorMessage, meta }">
                <VariantManager :product-sku="values.sku" :product-slug="slug" :model-value="field.value" @update:model-value="field.onChange" />
                <small v-if="errorMessage && meta.touched" class="text-danger">
                    {{ $t(errorMessage) }}
                </small>
            </Field>
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.common.form.faq") }}</h5>
                </div>
                <div class="card-body">
                    <Field name="productFaqs" v-slot="{ field, errorMessage, meta }">
                        <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="faqOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.faqPlaceholder')" class="w-100" />
                        <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                    </Field>
                </div>
            </div>
            <SEO :values="values" />
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="PRODUCTINDEX" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
                <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
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
                            <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" :maxFractionDigits="2" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.sku") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="sku" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="sku" type="text" :placeholder="$t('admin.common.form.skuPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
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
                            <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
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
                            <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="productCatalogueOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.productCataloguePlaceholder')" class="w-100" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.tag") }}
                        </label>
                        <Field name="productTags" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <MultiSelect :modelValue="field.value" @update:modelValue="field.onChange" :options="tagOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.tagPlaceholder')" class="rounded py-[1px] w-100" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.brand") }}
                        </label>
                        <Field name="brandId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="brandOptions" optionLabel="name" optionValue="id" filter :placeholder="$t('admin.common.form.brandPlaceholder')" class="w-100" showClear checkmark />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.related") }}
                        </label>
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
import Button from "~/components/custom/Button.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import { PRODUCTINDEX } from "~/utils/constants";
import { useProduct } from "~/composables/product/useProduct";
import CKEditor from "~/components/custom/CKEditor.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import SEO from "~/components/custom/SEO.vue";
import MultipleImageUpload from "~/components/custom/MultipleImageUpload.vue";
import type { AlbumItem } from "~/interfaces/upload/image.interface";
import { generateSKU } from "~/utils/helpers";
import VariantManager from "~/components/custom/VariantManager.vue";

const { isLoading, values, tagOptions, productCatalogueOptions, productOptions, brandOptions, faqOptions, seasonOptions, config, slug, show, setValues, handleUpdate } = useProduct(undefined, "update");
const id = Number(useRoute().params.id);
const albumData = ref<AlbumItem[] | null>(null);

const onAlbumChange = () => {
    setValues({ ...values, album: albumData.value });
};

onMounted(async () => {
    const response = await show(id);
    const data = response?.data;

    if (data) {
        albumData.value = data?.album ?? null;

        setValues({
            name: data?.name,
            description: data?.description,
            content: data?.content,
            album: data?.album ?? null,
            season: data?.season ?? null,
            warranty: Number(data?.warranty ?? 0),
            sold: Number(data?.sold ?? 0),
            priceOrigin: Number(data?.priceOrigin ?? 0),
            sku: data?.sku,
            sortOrder: data?.sortOrder ?? null,
            isHot: Boolean(data?.isHot ?? false),
            likeCount: Number(data?.likeCount ?? 0),
            productCatalogues: (data?.productCatalogues as Array<{ id: number; name: string }> | undefined)?.map((c) => c.id) ?? [],
            productTags: (data?.productTags as Array<{ id: number; name: string }> | undefined)?.map((t) => t.id) ?? [],
            productFaqs: (data?.faqs as Array<{ id: number; question: string }> | undefined)?.map((f) => f.id) ?? [],
            productVariants: (data?.productVariants as Array<any> | undefined) ?? [],
            brandId: (data as any)?.brandId ?? (data as any)?.brand?.id ?? null,
            relateds: (data?.relateds as Array<{ id: number; name: string }> | undefined)?.map((r) => r.id) ?? [],
            publish: (data as any)?.publish ?? 0,
            metaTitle: (data as any)?.metaTitle ?? null,
            metaKeyword: (data as any)?.metaKeyword ?? null,
            metaDescription: (data as any)?.metaDescription ?? null,
        });
    }

    if (!values.sku) {
        const sku = generateSKU();
        setValues({ ...values, sku });
    }
});

watch(
    albumData,
    (val) => {
        setValues({ ...values, album: val } as any);
    },
    { immediate: false, deep: true },
);

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
