<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">{{ $t("client.profile.accountInfo") }}</h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">{{ $t("client.profile.accountInfoDesc") }}</p>
    <form @submit.prevent="handleUpdateProfile" class="grid grid-cols-1 md:grid-cols-12 gap-6 mb-6 md:mb-10" enctype="multipart/form-data">
      <div class="col-span-1 md:col-span-9 space-y-6">
        <h2 class="text-sm font-semibold text-gray-700">{{ $t("client.profile.profile") }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="firstName" class="block text-sm text-gray-600 mb-1">
              {{ $t("client.profile.form.firstName") }}
            </label>
            <Field name="firstName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="firstName" type="text" :placeholder="$t('client.profile.form.firstNamePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
          <div>
            <label for="lastName" class="block text-sm text-gray-600 mb-1">
              {{ $t("client.profile.form.lastName") }}
            </label>
            <Field name="lastName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" id="lastName" type="text" :placeholder="$t('client.profile.form.lastNamePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
        <div>
          <label for="name" class="block text-sm text-gray-600 mb-1">
            {{ $t("client.profile.form.fullName") }}
          </label>
          <Field name="name" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="name" type="text" :placeholder="$t('client.profile.form.fullNamePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div>
          <label for="email" class="block text-sm text-gray-600 mb-1">
            {{ $t("client.profile.form.email") }}
          </label>
          <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="email" type="email" :placeholder="$t('client.profile.form.emailPlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" readonly />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div>
          <label for="phone" class="block text-sm text-gray-600 mb-1">
            {{ $t("client.profile.form.phone") }}
          </label>
          <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" id="phone" type="text" :placeholder="$t('client.profile.form.phonePlaceholder')" class="base-input" :class="errorMessage && meta.touched ? 'border-red-500 focus:ring-red-500 focus:border-red-500' : ''" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="birthday" class="block text-sm text-gray-600 mb-1">
              {{ $t("client.profile.form.birthday") }}
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
              {{ $t("client.profile.form.gender") }}
            </label>
            <Field name="gender" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <Select :modelValue="field.value" @update:modelValue="field.onChange" :options="genderOptions" checkmark optionLabel="name" optionValue="value" :placeholder="$t('client.profile.form.genderPlaceholder')" class="w-full border border-gray-300 rounded text-sm h-[40px]" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
                {{ $t(errorMessage) }}
              </small>
            </Field>
          </div>
        </div>
        <div>
          <label for="description" class="block text-sm text-gray-600 mb-1">
            {{ $t("client.profile.description") }}
          </label>
          <Field name="description" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <Textarea v-bind="field" id="description" rows="4" :placeholder="$t('client.profile.descriptionPlaceholder')" class="base-input"></Textarea>
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">
              {{ $t(errorMessage) }}
            </small>
          </Field>
        </div>
        <div class="flex justify-end gap-3 mt-4 sticky bottom-6 z-50">
          <Button
            type="submit"
            :label="$t('admin.common.confirm')"
            :loading="isLoading"
            unstyled
            class="max-w-[160px] text-sm flex justify-center py-1.5 px-4 rounded shadow-sm text-white bg-teal-500 hover:bg-teal-600 focus:outline-none mt-5 disabled:opacity-50"
            :pt="{
              loadingicon: 'w-3 h-3 mr-2 mt-1',
            }"
          />
        </div>
      </div>
      <div class="col-span-1 md:col-span-3 text-center">
        <label for="image" class="block text-left text-sm text-gray-600 mb-2">{{ $t("client.profile.form.image") }}</label>
        <SingleImageUpload :initial-url="currentImageUrl" :note="$t('client.profile.form.imageNote')" @selected="(f) => setValues({ ...(values as any), image: f } as any)" />
        <small>{{ $t("client.profile.form.clickToUpload") }}</small>
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import SingleImageUpload from "~/components/admin/custom/SingleImageUpload.vue";
import { useProfile } from "~/composables/client/user/useProfile";
import { useAuthStore } from "~/stores/auth/auth";

const { isLoading, genderOptions, fetchProfile, handleUpdateProfile, setValues, values } = useProfile();
const authStore = useAuthStore();
const data = ref<any>(null);

// Fetch Profile on mount
const init = async () => {
  const user = await fetchProfile();
  data.value = user;
  if (user && user.image) {
    currentImageUrl.value = user.image.path ? user.image.url || null : null;
  }
};

onMounted(() => {
  init();
});

const currentImageUrl = ref<string | null>(null);

definePageMeta({
  middleware: "auth-required",
  layout: "client",
});
</script>

<style scoped>
.base-input {
  @apply w-full border border-gray-300 rounded px-3 py-2 text-sm
         focus:outline-none focus:ring-1 focus:ring-sky-500 focus:border-sky-500
         transition h-[40];
}
:deep(.p-button) {
  transform: none !important;
}

:deep(.p-button:hover) {
  transform: none !important;
}
</style>
