<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">{{ $t("profile.updateAddress") }}</h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">{{ $t("profile.updateAddressDesc") }}</p>

    <div v-if="isLoadingData" class="flex justify-center items-center py-10">
      <div class="inline-block animate-spin rounded-full h-8 w-8 border-4 border-sky-500 border-t-transparent"></div>
    </div>

    <form v-else @submit.prevent="onSubmit" class="space-y-4">
      <h2 class="text-sm font-semibold text-gray-700">{{ $t("profile.information") }}</h2>

      <div class="bg-white rounded-md">
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("profile.titleLabel") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="title" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.titlePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("profile.fullName") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="fullName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.fullNamePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("profile.email") }}</label>
            <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="email" :placeholder="$t('profile.addressForm.emailPlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              {{ $t("profile.phone") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.phonePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("profile.address") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="address" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <textarea v-bind="field" rows="3" :placeholder="$t('profile.addressForm.addressPlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("profile.countryCode") }}</label>
            <Field name="countryCode" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" type="text" maxlength="2" :placeholder="$t('profile.addressForm.countryCodePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("profile.state") }}</label>
            <Field name="state" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.statePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              {{ $t("profile.postalCode") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="postalCode" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.postalCodePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>

        <div class="flex items-end gap-2 mb-3">
          <Field name="isDefault" v-slot="{ field }">
            <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="defaultAddress" :class="'scale-125'" binary />
          </Field>
          <label for="defaultAddress" class="text-sm text-gray-700 select-none">{{ $t("profile.defaultAddress") }}</label>
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
import { Field } from "vee-validate";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import { useAddressOrder } from "~/composables/user/useAddressOrder";
import { PROFILEADDRESSORDERINDEX } from "~/utils/constants";

const { t } = useI18n();
const route = useRoute();
const id = Number(route.params.id);

const { isLoading, setValues, show, handleUpdate } = useAddressOrder(undefined, "update", {
  redirectPath: PROFILEADDRESSORDERINDEX,
});

const address = ref<any>(null);
const isLoadingData = ref(true);

const onSubmit = async () => {
  try {
    await handleUpdate(false);
    await navigateTo(PROFILEADDRESSORDERINDEX);
  } catch (error) {
    console.error("Error updating address:", error);
  }
};

onMounted(async () => {
  try {
    if (!id || isNaN(id)) {
      console.error("Invalid address ID");
      await navigateTo(PROFILEADDRESSORDERINDEX);
      return;
    }

    const res = await show(id);
    address.value = res?.data || null;

    if (address.value) {
      setValues({
        title: address.value.title || "",
        fullName: address.value.fullName || "",
        email: address.value.email || "",
        phone: address.value.phone || "",
        address: address.value.address || "",
        countryCode: address.value.countryCode || "",
        state: address.value.state || "",
        city: address.value.city || "",
        postalCode: address.value.postalCode || "",
        isDefault: address.value.isDefault || false,
      });
    } else {
      await navigateTo(PROFILEADDRESSORDERINDEX);
    }
  } catch (error) {
    console.error("Error loading address data:", error);
    await navigateTo(PROFILEADDRESSORDERINDEX);
  } finally {
    isLoadingData.value = false;
  }
});

definePageMeta({
  middleware: ["auth-required"],
});
</script>

<style scoped>
@keyframes spin {
  to {
    transform: rotate(360deg);
  }
}

.animate-spin {
  animation: spin 1s linear infinite;
}
</style>
