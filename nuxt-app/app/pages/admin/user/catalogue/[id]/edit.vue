<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t('admin.page.userCatalogue.edit') }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.userCatalogue.breadcrumb'), to: '/admin/user/catalogue' }, { label: $t('admin.page.userCatalogue.edit') }]" />
        </div>
      </div>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-12">
      <div class="card">
        <div class="card-body">
          <div class="align-items-center">
            <h5>{{ $t('admin.page.userCatalogue.form.title') }}</h5>
            <span>
              {{ $t('admin.page.userCatalogue.form.note') }}
              <p class="fst-italic">
                {{ $t('admin.page.userCatalogue.form.required') }}
              </p>
            </span>
          </div>
        </div>
      </div>
    </div>
  </div>
  <form @submit.prevent="handleUpdate" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t('admin.page.userCatalogue.form.name') }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.userCatalogue.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ errorMessage }}</small>
            </Field>
            <div class="d-flex gap-2 text-info mt-1 underline cursor-pointer">
              <span class="mdi mdi-link-variant"></span>
              <p class="genrate_slug_to_name w-full pr-2" v-tooltip.bottom="$t('admin.page.userCatalogue.form.urlTooltip')">{{ config.public.baseUrl }}/{{ slug }}</p>
            </div>
          </div>
          <div class="mb-3">
            <label class="form-label">{{ $t('admin.page.userCatalogue.form.description') }}</label>
            <Field name="description" v-slot="{ field }">
              <textarea v-bind="field" rows="4" class="form-control" :placeholder="$t('admin.page.userCatalogue.form.descriptionPlaceholder')" />
            </Field>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4">
        <Button variant="cancel" :loading="false" :to="USERCATALOGUEINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4">
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t('admin.page.userCatalogue.status') }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">{{ $t('admin.page.userCatalogue.status') }}</label>
            <Field name="publish" v-slot="{ field, errorMessage, meta }">
              <PublishSelect :modelValue="field.value" @update:modelValue="field.onChange" />
              <small v-if="errorMessage && meta.touched" class="text-danger">
                {{ errorMessage }}
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
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import { useUserCatalogue } from "~/composables/admin/user/useUserCatalogue";
import { slugify } from "~/utils/helpers";

const { config, isLoading, show, handleUpdate, values, setValues } = useUserCatalogue(undefined, "update");

const id = Number(useRoute().params.id);
const { data } = await show(id);

const slug = ref(data.slug);
watch(
  () => values.name,
  (valName) => {
    if (valName) {
      slug.value = slugify(valName);
    }
  }
);
setValues({
  name: data.name,
  description: data.description,
  publish: data.publish,
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
