<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.create") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.page.permission.breadcrumb'), to: PERMISSIONINDEX }, { label: $t('admin.common.create') }]" />
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
                            {{ $t("admin.page.permission.name") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.permission.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="module">
                            {{ $t("admin.page.permission.module") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="module" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="module" type="text" :placeholder="$t('admin.page.permission.modulePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="value">
                            {{ $t("admin.page.permission.value") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="value" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" id="value" type="number" min="0" step="1" :placeholder="$t('admin.page.permission.valuePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label" for="title">{{ $t("admin.page.permission.title") }}:</label>
                        <Field name="title" v-slot="{ field, errorMessage, meta }">
                            <input v-bind="field" id="title" type="text" :placeholder="$t('admin.page.permission.titlePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">
                                {{ $t(errorMessage) }}
                            </small>
                        </Field>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" :to="PERMISSIONINDEX" />
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
import { Field } from "vee-validate";
import { PERMISSIONINDEX } from "~/utils/constants";
import Button from "~/components/custom/Button.vue";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import { usePermission } from "~/composables/user/usePermission";

const { handleStore, isLoading } = usePermission();

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
