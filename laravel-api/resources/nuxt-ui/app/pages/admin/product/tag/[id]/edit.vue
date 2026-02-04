<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.productTag.breadcrumb'), to: PRODUCTTAGINDEX }, { label: $t('admin.common.edit') }]" />
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
                    <div class="mb-3">
                        <label class="form-label" for="name">
                            {{ $t("admin.common.form.name") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="name" type="text" :placeholder="$t('admin.common.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                        <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
                            <span class="mdi mdi-link-variant"></span>
                            <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.form.description") }}</label>
                        <Field name="description" v-slot="{ field }">
                            <CKEditor :model-value="field.value" @update:model-value="field.onChange" :placeholder="$t('admin.common.form.descriptionPlaceholder')" height="h-32" />
                        </Field>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="PRODUCTTAGINDEX" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleUpdate(false)" />
                <Button type="button" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
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
                                <label for="color">{{ $t("admin.common.form.color") }}</label>
                                <Field name="color" v-slot="{ field, meta, errorMessage }">
                                    <ColorPicker v-model="field.value" :format="'hex'" inputId="color" class="w-full" @update:modelValue="(val) => field.onChange(val?.startsWith('#') ? val : `#${val}`)" :showInput="true" default-color="#7dd3fc" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                    <small v-else class="text-muted">{{ $t("admin.common.form.colorNote") }}</small>
                                </Field>
                            </div>
                        </div>
                    </div>
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
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import Button from "~/components/custom/Button.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import { useProductTag } from "~/composables/product/useProductTag";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import CKEditor from "~/components/custom/CKEditor.vue";

const { config, isLoading, slug, show, handleUpdate, setValues } = useProductTag(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);

setValues({
    name: data.name,
    description: data.description,
    publish: data.publish,
    isHot: data.isHot,
    color: data.color,
});

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
