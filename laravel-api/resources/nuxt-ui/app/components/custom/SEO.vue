<template>
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title mb-0">{{ $t("admin.common.seo") }}</h5>
        </div>
        <div class="card-body">
            <SeoPreview :metaTitle="values.metaTitle" :metaDescription="values.metaDescription" :metaKeyword="values.metaKeyword" />
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
                <Field name="metaKeyword" v-slot="{ field }">
                    <textarea v-bind="field" rows="2" class="form-control"></textarea>
                </Field>
            </div>
            <div class="mb-3">
                <label class="form-label">{{ $t("admin.common.metaDescription") }}</label>
                <Field name="metaDescription" v-slot="{ field }">
                    <textarea v-bind="field" rows="3" class="form-control"></textarea>
                </Field>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import SeoPreview from "./SeoPreview.vue";

interface SeoFields {
    metaTitle?: string | null;
    metaDescription?: string | null;
    metaKeyword?: string | null;
}

defineProps<{ values: SeoFields }>();
</script>
