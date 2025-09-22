<template>
  <div class="row">
    <div class="col-12">
      <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
        <h4 class="mb-sm-0">{{ $t("admin.common.edit") }}</h4>
        <div class="page-title-right">
          <Breadcrumb :crumbs="[{ label: $t('admin.page.user.breadcrumb'), to: '/admin/user/user' }, { label: $t('admin.common.edit') }]" />
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
  <form @submit.prevent="handleUpdate" class="row space-y-4">
    <div class="col-lg-8">
      <div class="card">
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label" for="name">
              {{ $t("admin.page.user.form.name") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.user.form.namePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="firstName">
                  {{ $t("admin.page.user.form.firstName") }}
                </label>
                <Field name="firstName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="firstName" type="text" :placeholder="$t('admin.page.user.form.firstNamePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="lastName">
                  {{ $t("admin.page.user.form.lastName") }}
                </label>
                <Field name="lastName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="lastName" type="text" :placeholder="$t('admin.page.user.form.lastNamePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="email">
                  {{ $t("admin.page.user.form.email") }}:
                  <span class="text-danger">*</span>
                </label>
                <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="email" type="email" :placeholder="$t('admin.page.user.form.emailPlaceholder')" class="form-control" disabled />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="phone">
                  {{ $t("admin.page.user.form.phone") }}
                </label>
                <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="phone" type="text" :placeholder="$t('admin.page.user.form.phonePlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <div class="mb-3">
                <label class="form-label" for="birthday">
                  {{ $t("admin.page.user.form.birthday") }}
                </label>
                <Field name="birthday" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <input v-bind="field" id="birthday" type="date" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
            <div class="col-md-6">
              <div class="mb-3">
                <Field name="gender" filter v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
                  <label class="form-label" for="gender">
                    {{ $t("admin.page.user.form.gender") }}
                  </label>
                  <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="genderOptions" optionLabel="name" optionValue="value" :placeholder="$t('admin.page.user.form.genderPlaceholder')" checkmark class="rounded py-[1px] w-100 md:w-56" size="small" />
                  <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
                </Field>
              </div>
            </div>
          </div>
          <!-- <div class="mb-3">
            <label class="form-label" for="password">
              {{ $t("admin.page.user.form.password") }}:
              <span class="text-danger">*</span>
            </label>
            <Field v-slot="{ field, errorMessage, meta }" name="password" :validate-on-input="true">
              <div class="relative">
                <input v-bind="field" id="password" :type="showPassword ? 'text' : 'password'" :placeholder="$t('admin.page.user.form.passwordPlaceholder')" :class="['w-full border rounded px-3 py-2 text-sm focus:outline-none pr-10 transition-colors', errorMessage && meta.touched ? 'border-red-500 focus:border-red-500' : 'border-gray-300 focus:border-sky-500']" />
                <button type="button" class="absolute right-3 top-2 text-sm text-gray-500 hover:text-gray-700" @click="showPassword = !showPassword" tabindex="-1">
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'" class="w-4 h-4"></i>
                </button>
              </div>
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div> -->
          <div class="mb-3">
            <label class="form-label" for="address">
              {{ $t("admin.page.user.form.address") }}
            </label>
            <Field name="address" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <textarea v-bind="field" id="address" rows="3" :placeholder="$t('admin.page.user.form.addressPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div class="mb-3">
            <label class="form-label" for="description">
              {{ $t("admin.page.user.form.description") }}
            </label>
            <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <textarea v-bind="field" id="description" rows="3" :placeholder="$t('admin.page.user.form.descriptionPlaceholder')" class="form-control" :class="errorMessage && meta.touched ? 'border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
      </div>
      <div class="flex justify-end gap-3 mt-4 position-sticky bottom-5 z-20">
        <Button type="button" variant="cancel" :to="USERINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </div>
    <div class="col-lg-4 mt-0">
      <div class="card">
        <div class="card-body p-4">
          <div class="text-center">
            <SingleImageUpload :initial-url="currentImageUrl" :note="$t('admin.common.noteUploadImage')" @selected="(f)=> setValues({ ...(values as any), image: f } as any)" />
          </div>
        </div>
      </div>
      <div class="card">
        <div class="card-header">
          <h5 class="card-title mb-0">{{ $t("admin.common.setting") }}</h5>
        </div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">
              {{ $t("admin.page.user.form.userCatalogue") }}:
              <span class="text-danger">*</span>
            </label>
            <Field name="userCatalogueId" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="userCatalogueOptions" optionLabel="name" optionValue="value" filter :placeholder="$t('admin.page.user.form.userCataloguePlaceholder')" class="rounded py-[1px] w-100" size="small" />
              <small v-if="errorMessage && meta.touched" class="text-danger">{{ $t(errorMessage) }}</small>
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
import PublishSelect from "~/components/admin/custom/PublishSelect.vue";
import Button from "~/components/admin/custom/Button.vue";
import Breadcrumb from "~/components/admin/navigation/Breadcrumb.vue";
import { useUser } from "~/composables/admin/user/useUser";
import { USERINDEX } from "~/utils/constants";
import SingleImageUpload from "~/components/admin/custom/SingleImageUpload.vue";

const { isLoading, userCatalogueOptions, genderOptions, show, handleUpdate, setValues, values } = useUser(undefined, "update");
const currentImageUrl = ref<string | null>(null);
const id = Number(useRoute().params.id);
const { data } = await show(id);
currentImageUrl.value = data?.image?.path ? data?.image?.url || null : null;

setValues({
  name: data.name,
  email: data.email,
  lastName: data.lastName,
  firstName: data.firstName,
  description: data.description,
  birthday: data.birthday,
  gender: data.gender,
  phone: data.phone,
  address: data.address,
  publish: data.publish || 0,
  userCatalogueId: data.userCatalogueId,
});

definePageMeta({
  layout: "admin",
  middleware: ["auth-required", "auth-admin"],
});
</script>
