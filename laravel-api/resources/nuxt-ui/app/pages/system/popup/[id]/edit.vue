<template>
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
                <div class="page-title-right">
                    <Breadcrumb :crumbs="[{ label: $t('admin.sidebar.popupList'), to: POPUPINDEX }, { label: $t('admin.common.edit') }]" />
                </div>
            </div>
        </div>
    </div>

    <form @submit.prevent="() => handleUpdate(true)" class="row space-y-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">
                            {{ $t("admin.common.form.title") }}:
                            <span class="text-danger">*</span>
                        </label>
                        <Field name="title" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                            <input v-bind="field" type="text" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.page.popup.form.type") }}</label>
                        <Field name="type" v-slot="{ field, errorMessage, meta }">
                            <Select v-model="field.value" @update:modelValue="field.onChange" :options="typeOptions" optionLabel="label" optionValue="value" placeholder="Chọn loại cho popup" class="rounded py-[1px] w-full" size="small" showClear checkmark />
                            <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                        </Field>
                    </div>

                    <div class="mb-3" v-if="values.type !== 'form'">
                        <label class="form-label">{{ $t("admin.common.form.content") }}</label>
                        <Field name="content" v-slot="{ field }">
                            <CKEditor v-model="field.value" @update:modelValue="field.onChange" class="h-64" />
                        </Field>
                    </div>

                    <div class="mb-3" v-if="values.type === 'promotion' || values.type === 'guide'">
                        <label class="form-label">{{ $t("admin.page.popup.form.link") }}</label>
                        <Field name="link" v-slot="{ field }">
                            <input v-bind="field" class="form-control" />
                        </Field>
                    </div>

                    <div class="mb-3" v-if="values.type === 'promotion' || values.type === 'confirm'">
                        <label class="form-label">{{ $t("admin.page.popup.form.buttonText") }}</label>
                        <Field name="buttonText" v-slot="{ field }">
                            <input v-bind="field" class="form-control" />
                        </Field>
                    </div>
                </div>
            </div>
            <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
                <Button type="button" variant="cancel" @click="() => navigateTo(POPUPINDEX)" />
                <Button type="button" variant="custom" severity="info" :label="$t('admin.components.buttons.confirm')" :loading="isLoading" @click="() => handleStore(false)" />
                <Button type="submit" variant="success" :loading="isLoading" @click="() => handleUpdate(true)" />
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.image") }}</label>
                        <SingleImageUpload :initial-url="currentImageUrl" :note="$t('admin.common.noteUploadImage')" @selected="(f) => setValues({ ...(values as any), image: f } as any)" class="w-full" />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.page.popup.form.showDelay") }}</label>
                        <Field name="showDelay" v-slot="{ field }">
                            <InputNumber v-model="field.value" @update:modelValue="field.onChange" :min="0" showButtons class="rounded py-[7px] w-full" size="small" />
                        </Field>
                    </div>
                    <div class="mb-3 flex items-center gap-2">
                        <AppCheckbox v-model="values.priority" />
                        <label class="form-label mb-0">{{ $t("admin.page.popup.form.priority") }}</label>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">{{ $t("admin.common.status") }}</label>
                        <Field name="publish" v-slot="{ field }">
                            <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
                        </Field>
                    </div>
                </div>
            </div>
        </div>
    </form>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import Button from "~/components/custom/Button.vue";
import SingleImageUpload from "~/components/custom/SingleImageUpload.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import PublishSelect from "~/components/custom/PublishSelect.vue";
import CKEditor from "~/components/custom/CKEditor.vue";
import { usePopup } from "~/composables/popup/usePopup";
import { POPUPINDEX } from "~/utils/constants";

const { config, isLoading, values, slug, show, handleUpdate, setValues, typeOptions } = usePopup(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);

setValues({
    title: data.title,
    type: data.type,
    content: data.content,
    publish: data.publish,
    showDelay: data.showDelay ?? data.showDelay ?? null,
    priority: data.priority ?? false,
    link: data.link ?? "",
    buttonText: data.buttonText ?? "",
});

const currentImageUrl = ref<string | null>(null);
currentImageUrl.value = data?.image?.path ? data?.image?.url || null : null;

definePageMeta({
    layout: "admin",
    middleware: ["auth-required", "auth-admin"],
});
</script>
