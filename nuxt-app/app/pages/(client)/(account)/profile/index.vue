<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-900 mb-1">Thông tin tài khoản</h1>
    <p class="text-sm text-gray-500 mb-5 md:mb-10">Cập nhật hồ sơ và chi tiết cá nhân của bạn tại đây</p>
    <form @submit.prevent="handleUpdateProfile" class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6 md:mb-10" enctype="multipart/form-data">
      <div class="col-span-1 md:col-span-9 space-y-6">
        <h2 class="text-sm font-semibold text-gray-700">Hồ sơ</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="firstName" class="block text-sm text-gray-600 mb-1">
              {{ $t("admin.page.user.form.firstName") }}
            </label>
            <Field name="firstName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="firstName" type="text" :placeholder="$t('admin.page.user.form.firstNamePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div>
            <label for="lastName" class="block text-sm text-gray-600 mb-1">
              {{ $t("admin.page.user.form.lastName") }}
            </label>
            <Field name="lastName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="lastName" type="text" :placeholder="$t('admin.page.user.form.lastNamePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
        <div>
          <label for="name" class="block text-sm text-gray-600 mb-1">
            {{ $t("admin.page.user.form.name") }}
          </label>
          <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="name" type="text" :placeholder="$t('admin.page.user.form.namePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div>
          <label for="email" class="block text-sm text-gray-600 mb-1">
            {{ $t("admin.page.user.form.email") }}
          </label>
          <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="email" type="email" :placeholder="$t('admin.page.user.form.emailPlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" readonly />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div>
          <label for="phone" class="block text-sm text-gray-600 mb-1">
            {{ $t("admin.page.user.form.phone") }}
          </label>
          <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="phone" type="text" :placeholder="$t('admin.page.user.form.phonePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="birthday" class="block text-sm text-gray-600 mb-1">
              {{ $t("admin.page.user.form.birthday") }}
            </label>
            <Field name="birthday" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="birthday" type="date" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div>
            <label for="gender" class="block text-sm text-gray-600 mb-1">
              {{ $t("admin.page.user.form.gender") }}
            </label>
            <Field name="gender" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="genderOptions" checkmark optionLabel="name" optionValue="value" :placeholder="$t('admin.page.user.form.genderPlaceholder')" class="w-full border border-gray-300 rounded text-sm h-[40px]" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
        <div class="flex justify-end gap-3 mt-4 sticky bottom-5 z-20">
          <Button variant="success" :loading="isLoading"  />
        </div>
      </div>
      <div class="col-span-1 md:col-span-3 text-center">
        <label for="image" class="block text-left text-sm text-gray-600 mb-2">{{ $t("admin.common.image") }}</label>
        <SingleImageUpload :initial-url="currentImageUrl" :note="$t('admin.common.noteUploadImage')" @selected="(f)=> setValues({ ...(values as any), image: f } as any)" />
        <small>{{ $t("admin.common.clickToUpload") }}</small>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import Button from "~/components/admin/custom/Button.vue";
import SingleImageUpload from "~/components/admin/custom/SingleImageUpload.vue";
import { useUser } from "~/composables/admin/user/useUser";
import { useAuthStore } from "~/stores/auth/auth";

// kế thừa layout + middleware từ parent /profile

const { isLoading, genderOptions, show, handleUpdateProfile, setValues, values } = useUser(undefined, "update");
const authStore = useAuthStore();

const id = authStore.user?.id || 0;
const { data } = await show(id);

const currentImageUrl = ref<string | null>(null);
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
</script>

<style scoped>
.base-input {
  @apply w-full border border-gray-300 rounded px-3 py-2 text-sm
         focus:outline-none focus:ring-2 focus:ring-sky-500 focus:border-sky-500
         transition h-[40];
}
:deep(.p-button) {
  transform: none !important;
}

:deep(.p-button:hover) {
  transform: none !important;
}
</style>


