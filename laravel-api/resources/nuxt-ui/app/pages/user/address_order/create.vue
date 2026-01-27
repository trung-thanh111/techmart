<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.addressOrder.breadcrumb'), to: ADDRESSORDERINDEX }, { label: $t('admin.common.create') }]" />
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
                    <div class="mb-3">
                        <label class="form-label" for="title">
                            {{ $t("admin.common.form.title") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="title" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="title" type="text" :placeholder="$t('admin.common.form.titlePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                        <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
                            <span class="mdi mdi-link-variant"></span>
                            <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.common.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="fullName">
                            {{ $t("admin.common.form.fullName") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="fullName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="fullName" type="text" :placeholder="$t('admin.common.form.fullNamePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="email">
                                    {{ $t("admin.common.form.email") }}
                                </label>
                                <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                    <input v-bind="field" id="email" type="email" :placeholder="$t('admin.common.form.emailPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                </Field>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label" for="phone">
                                    {{ $t("admin.common.form.phone") }}:
                                    <span class="text-danger">*</span>
                                </label>
                                <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                    <input v-bind="field" id="phone" type="text" :placeholder="$t('admin.common.form.phonePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                </Field>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label" for="address">
                            {{ $t("admin.common.form.address") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="address" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <CKEditor :model-value="field.value" @update:model-value="field.onChange" :placeholder="$t('admin.common.form.addressPlaceholder')" height="h-32" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="countryCode">
                                    {{ $t("admin.common.form.countryCode") }}
                                </label>
                                <Field name="countryCode" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                    <input v-bind="field" id="countryCode" type="text" :placeholder="$t('admin.common.form.countryCodePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                </Field>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="state">
                                    {{ $t("admin.common.form.state") }}
                                </label>
                                <Field name="state" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                    <input v-bind="field" id="state" type="text" :placeholder="$t('admin.common.form.statePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                </Field>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label class="form-label" for="postalCode">
                                    {{ $t("admin.common.form.postalCode") }}:
                                    <span class="text-danger">*</span>
                                </label>
                                <Field name="postalCode" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                                    <input v-bind="field" id="postalCode" type="text" :placeholder="$t('admin.common.form.postalCodePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                                    <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                                </Field>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="ADDRESSORDERINDEX" />
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
                        <div class="form-check d-flex align-items-center gap-2 ps-0">
                            <Field name="isDefault" v-slot="{ field }">
                                <AppCheckbox v-model="field.value" inputId="id_default" class="scale-125" />
                            </Field>
                            <label for="id_default" class="mb-0">
                                {{ $t("admin.common.form.isDefault") }}
                            </label>
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
import { Field, useForm } from "vee-validate";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import Button from "~/components/custom/Button.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import { useAddressOrder } from "~/composables/user/useAddressOrder";
import { ADDRESSORDERINDEX } from "~/utils/constants";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import CKEditor from "~/components/custom/CKEditor.vue";

const { isLoading, config, slug, handleStore } = useAddressOrder();

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
