<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">{{ $t("profile.addAddress") }}</h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">{{ $t("profile.addAddressDesc") }}</p>

    <form @submit.prevent="onSubmit" class="space-y-4">
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
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t("profile.city") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="city" v-slot="{ field, errorMessage }">
              <Select
                v-bind="field.value"
                :model-value="field.value"
                :options="cities"
                option-label="name"
                option-value="codename"
                filter
                checkmark
                :placeholder="$t('profile.addressForm.cityPlaceholder')"
                class="w-full rounded py-[1px]"
                :class="{ 'p-invalid': errorMessage }"
                size="small"
                appendTo="body"
                overlayClass="!z-[99999]"
                @update:model-value="
                  (val) => {
                    field.onChange(val);
                    onCityChange(val);
                  }
                "
              />
              <small v-if="errorMessage" class="text-red-500 text-xs mt-1 block">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label for="ward" class="block text-sm font-medium text-gray-700 mb-2">
              {{ $t("profile.ward") }}
              <span class="text-red-500">*</span>
            </label>
            <Field name="ward" v-slot="{ field, errorMessage }">
              <Select v-bind="field.value" :model-value="field.value" :options="wards" option-label="name" option-value="codename" filter checkmark :placeholder="$t('profile.addressForm.wardPlaceholder')" class="w-full rounded py-[1px]" :class="{ 'p-invalid': errorMessage }" :disabled="!values.city" size="small" appendTo="body" overlayClass="!z-[99999]" @update:model-value="field.onChange" />
              <small v-if="errorMessage" class="text-red-500 text-xs mt-1 block">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>

        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            {{ $t("profile.street") }}
            <span class="text-red-500">*</span>
          </label>
          <Field name="street" v-slot="{ field, errorMessage, meta }">
            <input v-bind="field" type="text" :placeholder="$t('profile.addressForm.streetPlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">{{ $t("profile.countryCode") }}</label>
            <Field name="countryCode" v-slot="{ field, errorMessage, meta }">
              <input v-bind="field" type="text" maxlength="2" :placeholder="$t('profile.addressForm.countryCodePlaceholder')" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
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
import { PROFILEADDRESSORDERINDEX } from "~/utils/constants";
import { Field } from "vee-validate";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import Button from "~/components/custom/Button.vue";
import { useAddressOrder } from "~/composables/user/useAddressOrder";
import { useAuthStore } from "~/stores/auth/auth";
import vietnamData from "../../../../../../data/address_vietnam.json";

const cities = computed(() => vietnamData);

const wards = computed(() => {
  if (!values.city) return [];
  const selectedCity = vietnamData.find((c: any) => c.codename === values.city);
  return selectedCity ? selectedCity.wards : [];
});

const onCityChange = (val: string) => {
  setValues({
    ...values,
    city: val,
    ward: "",
  });
};

const { t } = useI18n();

const { isLoading, handleStore, getAddressesByUserId, data, values, setValues } = useAddressOrder(undefined, "create", {
  redirectPath: PROFILEADDRESSORDERINDEX,
});

const authStore = useAuthStore();
const userId = authStore.user?.id || 0;

const onSubmit = async () => {
  try {
    await handleStore();
  } catch (error) {
    console.error("Error creating address:", error);
  }
};

onMounted(async () => {
  if (!userId) return;

  try {
    await getAddressesByUserId(userId);

    const total = Array.isArray(data.value?.data) ? data.value?.data.length : 0;

    if (total && total >= 3) {
      return navigateTo(PROFILEADDRESSORDERINDEX);
    }
  } catch (error) {
    console.error("Error checking address limit:", error);
  }
});
definePageMeta({
  middleware: ["auth-required"],
});
</script>
