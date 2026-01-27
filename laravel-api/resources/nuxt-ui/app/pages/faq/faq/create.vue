<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.faq.breadcrumb'), to: FAQINDEX }, { label: $t('admin.common.create') }]" />
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
    <form @submit.prevent="() => handleStore(false)" class="row space-y-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="">
                        <label class="form-label" for="question">
                            {{ $t("admin.common.form.question") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="question" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="question" type="text" :placeholder="$t('admin.common.form.questionPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                        <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
                            <span class="mdi mdi-link-variant"></span>
                            <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="answer">
                            {{ $t("admin.common.form.answer") }}
                        </label>
                        <Field name="answer" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <div class="w-full">
                                <CKEditor v-model="field.value" @update:modelValue="field.onChange" height="h-64" />
                                <small v-if="errorMessage && meta.touched" class="text-danger">
                                    {{ $t(errorMessage) }}
                                </small>
                            </div>
                        </Field>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="FAQINDEX" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleStore(false)" />
                <Button type="button" variant="success" :loading="isLoading" @click="() => handleStore(true)" />
            </div>
        </div>
        <div class="col-lg-4 mt-0">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ $t("admin.common.relation") }}</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.faqCatalogue") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="faqCatalogueId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="faqCatalogueOptions" optionLabel="name" optionValue="id" filter display="chip" :placeholder="$t('admin.common.form.faqCataloguePlaceholder')" class="w-100" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
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
                        <label class="form-label" for="order">{{ $t("admin.common.form.order") }}:</label>
                        <Field name="order" v-slot="{ field, errorMessage, meta }">
                            <InputNumber :modelValue="field.value" @update:modelValue="field.onChange" :min="1" showButtons class="w-full" input-class="!rounded py-[7px] w-full" size="small" />
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
import { FAQINDEX } from "~/utils/constants";
import { useFaq } from "~/composables/faq/useFaq";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import CKEditor from "~/components/custom/CKEditor.vue";

const { isLoading, faqCatalogueOptions, config, slug, handleStore } = useFaq();

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
