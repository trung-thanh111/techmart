<template>
  <div class="text-gray-600 text-base">
    <div class="max-w-full w-full mx-auto p-4">
      <div class="flex flex-col sm:flex-row sm:items-end justify-between gap-3 mb-6">
        <div>
          <h5 class="mb-1 font-medium md:text-lg text-base">Thông tin người nhận</h5>
          <p class="text-sm text-gray-500">
            Điền thông tin bên dưới hoặc
            <span class="text-sky-600">sử dụng thông tin từ tài khoản của bạn.</span>
          </p>
        </div>
        <button @click="handleToggleProfileInfo" type="button" :class="['px-2 py-1 text-xs border rounded transition-colors flex items-center gap-1', isUsingProfile ? 'text-red-600 bg-red-50 border-red-200 hover:bg-red-100 hover:text-red-700' : 'text-teal-600 bg-teal-50 border-teal-200 hover:bg-teal-100 hover:text-teal-700', isLoadingProfile ? 'opacity-70 cursor-wait' : '']" :disabled="isLoadingProfile">
          <i v-if="isLoadingProfile" class="pi pi-spinner pi-spin text-[10px]" />
          {{ isUsingProfile ? "hủy" : "sử dụng thông tin" }}
        </button>
      </div>
      <Form :validation-schema="validationSchema" class="md:space-y-4 space-y-2">
        <div>
          <label for="fullName" class="block text-sm font-medium text-gray-700 mb-2">
            Họ và tên
            <span class="text-red-500">*</span>
          </label>
          <Field id="fullName" name="fullName" type="text" placeholder="Nguyễn Văn A" v-model="modelValue.fullName" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="fullName" class="text-red-500 text-xs mt-1 block" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
              Email
              <span class="text-red-500">*</span>
            </label>
            <Field id="email" name="email" type="email" placeholder="email@domain.com" v-model="modelValue.email" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <ErrorMessage name="email" class="text-red-500 text-xs mt-1 block" />
          </div>
          <div>
            <label for="phone" class="block text-sm font-medium text-gray-700 mb-2">
              Số điện thoại
              <span class="text-red-500">*</span>
            </label>
            <Field id="phone" name="phone" type="tel" placeholder="0123 456 789" v-model="modelValue.phone" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <ErrorMessage name="phone" class="text-red-500 text-xs mt-1 block" />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
              Tỉnh/Thành phố
              <span class="text-red-500">*</span>
            </label>
            <Field name="city" v-model="modelValue.city">
              <template #default="{ field, errorMessage, handleChange }">
                <Select
                  :id="field.name"
                  :model-value="modelValue.city"
                  :options="cities"
                  option-label="name"
                  option-value="name"
                  filter
                  checkmark
                  placeholder="Chọn Tỉnh/Thành phố"
                  class="w-full rounded py-[1px]"
                  :class="{ 'p-invalid': errorMessage }"
                  size="small"
                  @update:model-value="
                    (val) => {
                      handleChange(val);
                      modelValue.city = val;
                      modelValue.ward = '';
                    }
                  "
                />
              </template>
            </Field>
            <ErrorMessage name="city" class="text-red-500 text-xs mt-1 block" />
          </div>
          <div>
            <label for="ward" class="block text-sm font-medium text-gray-700 mb-2">
              Phường/Xã/Thị trấn
              <span class="text-red-500">*</span>
            </label>
            <Field name="ward" v-model="modelValue.ward">
              <template #default="{ field, errorMessage, handleChange }">
                <Select
                  :id="field.name"
                  :model-value="modelValue.ward"
                  :options="wards"
                  option-label="name"
                  option-value="name"
                  filter
                  checkmark
                  placeholder="Chọn Phường/Xã/Thị trấn"
                  class="w-full rounded py-[1px]"
                  :class="{ 'p-invalid': errorMessage }"
                  :disabled="!modelValue.city"
                  @update:model-value="
                    (val) => {
                      handleChange(val);
                      modelValue.ward = val;
                    }
                  "
                  size="small"
                />
              </template>
            </Field>
            <ErrorMessage name="ward" class="text-red-500 text-xs mt-1 block" />
          </div>
        </div>
        <div>
          <label for="street" class="block text-sm font-medium text-gray-700 mb-2">
            Số nhà/ đường/ phố
            <span class="text-red-500">*</span>
          </label>
          <Field id="street" name="street" type="text" placeholder="Số nhà, tên đường..." v-model="modelValue.street" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="street" class="text-red-500 text-xs mt-1 block" />
        </div>
        <div>
          <label for="customerNote" class="block text-sm font-medium text-gray-700 mb-2">Ghi chú</label>
          <Field as="textarea" id="customerNote" name="customerNote" rows="3" placeholder="Ví dụ: Giao giờ hành chính" v-model="modelValue.customerNote" class="w-full border border-gray-300 rounded-sm px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="customerNote" class="text-red-500 text-xs mt-1 block" />
        </div>
        <NextButton @click="$emit('next')" :disabled="!canProceed" />
      </Form>
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Form, Field, ErrorMessage } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import { zodPersonalSchema } from "~/composables/cart/useCheckout";
import type { IPersonalInfo } from "~/interfaces/cart/checkout.interface";
import vietnamData from "../../../../data/address_vietnam.json";
import NextButton from "~/components/features/checkout/NextButton.vue";
import { useAuthStore } from "~/stores/auth/auth";
import { profileService } from "~/services/user/profile.service";

const props = defineProps<{
  modelValue: IPersonalInfo;
  canProceed: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: IPersonalInfo): void;
  (e: "next"): void;
}>();

const validationSchema = toTypedSchema(zodPersonalSchema);
const authStore = useAuthStore();
const isUsingProfile = ref(false);
const isLoadingProfile = ref(false);

const handleToggleProfileInfo = async () => {
  if (isUsingProfile.value) {
    emit("update:modelValue", {
      ...props.modelValue,
      fullName: "",
      email: "",
      phone: "",
      city: "",
      ward: "",
      street: "",
      customerNote: "",
    });
    isUsingProfile.value = false;
    return;
  }

  isLoadingProfile.value = true;
  try {
    const res = await profileService.getProfile();
    if (res && res.data) {
      const user = res.data;
      let city = "";
      let ward = "";
      let street = user.address || "";

      if (user.addressOrder && user.addressOrder.length > 0) {
        const defaultAddress = user.addressOrder.find((a: any) => a.isDefault) || user.addressOrder[0];
        if (defaultAddress) {
          city = defaultAddress.city || defaultAddress.shipping_city || "";
          ward = defaultAddress.ward || defaultAddress.shipping_ward || "";
          street = defaultAddress.street || defaultAddress.shipping_street || defaultAddress.address || "";
        }
      }

      emit("update:modelValue", {
        ...props.modelValue,
        fullName: user.name || (user.firstName && user.lastName ? user.firstName + " " + user.lastName : "") || "",
        email: user.email || "",
        phone: user.phone || "",
        city: city,
        ward: ward,
        street: street,
      });
      isUsingProfile.value = true;
    }
  } catch (error) {
    console.error("Failed to fetch user info", error);
  } finally {
    isLoadingProfile.value = false;
  }
};

const cities = computed(() => {
  return vietnamData;
});

const wards = computed(() => {
  if (!props.modelValue.city) return [];
  const selectedCity = vietnamData.find((c: any) => c.name === props.modelValue.city);
  return selectedCity ? selectedCity.wards : [];
});
</script>
