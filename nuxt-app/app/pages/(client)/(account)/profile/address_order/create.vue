<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">{{ $t("client.profile.addAddress") }}</h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">{{ $t("client.profile.addAddressDesc") }}</p>
    <form @submit.prevent="handleStore" class="space-y-4">
      <h2 class="text-sm font-semibold text-gray-700">{{ $t("client.profile.information") }}</h2>
      <div class="bg-white rounded-md">
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("client.profile.titleLabel") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="title" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" :placeholder="$t('client.profile.addressForm.titlePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("client.profile.fullName") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="fullName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" :placeholder="$t('client.profile.addressForm.fullNamePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("client.profile.email") }}</label>
            <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="email" :placeholder="$t('client.profile.addressForm.emailPlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              {{ $t("client.profile.phone") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" :placeholder="$t('client.profile.addressForm.phonePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("client.profile.address") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="address" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <textarea v-bind="field" rows="3" :placeholder="$t('client.profile.addressForm.addressPlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("client.profile.countryCode") }}</label>
            <Field name="countryCode" v-slot="{ field }">
              <input v-bind="field" type="text" maxlength="2" :placeholder="$t('client.profile.addressForm.countryCodePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("client.profile.state") }}</label>
            <Field name="state" v-slot="{ field }">
              <input v-bind="field" type="text" :placeholder="$t('client.profile.addressForm.statePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              {{ $t("client.profile.postalCode") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="postalCode" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" :placeholder="$t('client.profile.addressForm.postalCodePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
        <div class="flex items-end gap-2 mb-3">
          <Field name="isDefault" v-slot="{ field }">
            <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="defaultAddress" :class="'scale-125'" binary />
          </Field>
          <label for="defaultAddress" class="text-sm text-gray-700 select-none">{{ $t("client.profile.defaultAddress") }}</label>
        </div>
      </div>
      <div class="flex justify-end gap-3 sticky bottom-5 z-50">
        <Button type="button" variant="cancel" :to="PROFILEADDRESSORDERINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { PROFILEADDRESSORDERINDEX } from "~/utils/constants";
import { Field } from "vee-validate";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import Button from "~/components/admin/custom/Button.vue";
import { useAddressOrder } from "~/composables/admin/user/useAddressOrder";
import { useAuthStore } from "~/stores/auth/auth";

const { t } = useI18n();

const { isLoading, handleStore, addressUserById, data } = useAddressOrder(undefined, "create", { redirectPath: "/profile/address_order" });
const authStore = useAuthStore();
const userId = authStore.user?.id || 0;

onMounted(async () => {
  if (!userId) return;
  await addressUserById(userId);
  const total = Array.isArray(data.value?.data) ? data.value?.data.length : 0;
  if (total && total >= 3) {
    return navigateTo(PROFILEADDRESSORDERINDEX);
  }
});

definePageMeta({
  middleware: ["auth-required"],
});
</script>
