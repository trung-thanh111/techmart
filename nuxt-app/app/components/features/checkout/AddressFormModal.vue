<template>
  <Dialog :visible="visible" modal :header="isUpdate ? 'Cập nhật địa chỉ' : 'Thêm địa chỉ mới'" :style="{ width: '50rem' }" @update:visible="emit('update:visible', $event)">
    <div class="w-full">
      <Form :validation-schema="validationSchema" @submit="onSubmit" class="space-y-4">
        <Field name="street" v-model="form.street" type="hidden" readonly />
        <div class="flex justify-end mb-4">
          <button @click="handleToggleProfileInfo" type="button" :class="['px-2 py-1 text-xs border rounded transition-colors flex items-center gap-1', isUsingProfile ? 'text-red-600 bg-red-50 border-red-200 hover:bg-red-100 hover:text-red-700' : 'text-teal-600 bg-teal-50 border-teal-200 hover:bg-teal-100 hover:text-teal-700', isLoadingProfile ? 'opacity-70 cursor-wait' : '']" :disabled="isLoadingProfile">
            <i v-if="isLoadingProfile" class="pi pi-spinner pi-spin text-[10px]" />
            {{ isUsingProfile ? "Hủy" : "Sử dụng thông tin" }}
          </button>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">
            Tiêu đề
            <span class="text-red-500">*</span>
          </label>
          <Field name="title" v-model="form.title" type="text" placeholder="Nhà riêng / Văn phòng ..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="title" class="text-red-500 text-xs mt-1 block" />
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">
            Họ và tên
            <span class="text-red-500">*</span>
          </label>
          <Field name="fullName" v-model="form.fullName" type="text" placeholder="Nguyễn Văn A" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="fullName" class="text-red-500 text-xs mt-1 block" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Email</label>
            <Field name="email" v-model="form.email" type="email" placeholder="email@domain.com" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <ErrorMessage name="email" class="text-red-500 text-xs mt-1 block" />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              Số điện thoại
              <span class="text-red-500">*</span>
            </label>
            <Field name="phone" v-model="form.phone" type="text" placeholder="(+84) 123 456 789" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <ErrorMessage name="phone" class="text-red-500 text-xs mt-1 block" />
          </div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label for="city" class="block text-sm font-medium text-gray-700 mb-2">
              Tỉnh/Thành phố
              <span class="text-red-500">*</span>
            </label>
            <Field name="city" v-model="form.city">
              <template #default="{ field, errorMessage }">
                <Select
                  v-bind="field.value"
                  :model-value="field.value"
                  :options="cities"
                  option-label="name"
                  option-value="codename"
                  filter
                  checkmark
                  placeholder="Chọn Tỉnh/Thành phố"
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
              </template>
            </Field>
            <ErrorMessage name="city" class="text-red-500 text-xs mt-1 block" />
          </div>
          <div>
            <label for="ward" class="block text-sm font-medium text-gray-700 mb-2">
              Phường/Xã/Thị trấn
              <span class="text-red-500">*</span>
            </label>
            <Field name="ward" v-model="form.ward">
              <template #default="{ field, errorMessage }">
                <Select v-bind="field.value" :model-value="field.value" :options="wards" option-label="name" option-value="codename" filter checkmark placeholder="Chọn Phường/Xã/Thị trấn" class="w-full rounded py-[1px]" :class="{ 'p-invalid': errorMessage }" :disabled="!form.city" size="small" appendTo="body" overlayClass="!z-[99999]" @update:model-value="field.onChange" />
              </template>
            </Field>
            <ErrorMessage name="ward" class="text-red-500 text-xs mt-1 block" />
          </div>
        </div>
        <div>
          <label class="block text-sm text-gray-700 mb-1">
            Địa chỉ
            <span class="text-red-500">*</span>
          </label>
          <Field name="address" v-model="form.address" as="textarea" rows="3" placeholder="123 Đường ABC, Quận 1, TP.HCM" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          <ErrorMessage name="address" class="text-red-500 text-xs mt-1 block" />
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Mã quốc gia</label>
            <Field name="countryCode" v-model="form.countryCode" type="text" maxlength="2" placeholder="VN" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              Mã bưu chính
              <span class="text-red-500">*</span>
            </label>
            <Field name="postalCode" v-model="form.postalCode" type="text" placeholder="700000" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <ErrorMessage name="postalCode" class="text-red-500 text-xs mt-1 block" />
          </div>
        </div>
        <div class="flex items-center gap-2">
          <Field name="isDefault" v-model="form.isDefault" type="checkbox" :value="true" v-slot="{ field }">
            <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="defaultAddress" :class="'scale-125'" binary />
          </Field>
          <label for="defaultAddress" class="text-sm text-gray-700 select-none mt-1">Đặt làm mặc định</label>
        </div>
        <div class="flex justify-end gap-2 mt-4">
          <Button variant="cancel" @click="close" />
          <Button variant="success" type="submit" :loading="loading" />
        </div>
      </Form>
    </div>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, watch } from "vue";
import { Form, Field, ErrorMessage } from "vee-validate";
import { toTypedSchema } from "@vee-validate/zod";
import vietnamData from "../../../../data/address_vietnam.json";
import Button from "~/components/custom/Button.vue";
import AppCheckbox from "~/components/custom/AppCheckbox.vue";
import { useAuthStore } from "~/stores/auth/auth";
import { useAddressValidation } from "~/composables/user/useAddressOrder";
import { profileService } from "~/services/user/profile.service";

interface Props {
  visible: boolean;
  isUpdate?: boolean;
  loading?: boolean;
  initialData?: any;
}

const props = withDefaults(defineProps<Props>(), {
  visible: false,
  isUpdate: false,
  loading: false,
  initialData: () => ({}),
});

const emit = defineEmits<{
  (e: "update:visible", value: boolean): void;
  (e: "submit", data: any): void;
}>();

const authStore = useAuthStore();
const validationSchema = toTypedSchema(useAddressValidation());

const isUsingProfile = ref(false);
const isLoadingProfile = ref(false);

const handleToggleProfileInfo = async () => {
  if (isUsingProfile.value) {
    form.value = {
      ...form.value,
      fullName: "",
      email: "",
      phone: "",
      city: "",
      ward: "",
      street: "",
      address: "",
    };
    if (!form.value.title) form.value.title = "";
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
      let address = user.address || "";

      if (user.addressOrder && user.addressOrder.length > 0) {
        const defaultAddress = user.addressOrder.find((a: any) => a.isDefault) || user.addressOrder[0];
        if (defaultAddress) {
          city = defaultAddress.city || defaultAddress.shipping_city || "";
          ward = defaultAddress.ward || defaultAddress.shipping_ward || "";
          street = defaultAddress.street || defaultAddress.shipping_street || defaultAddress.address || "";
          address = street;
        }
      }

      form.value = {
        ...form.value,
        fullName: user.name || (user.firstName && user.lastName ? user.firstName + " " + user.lastName : "") || "",
        email: user.email || "",
        phone: user.phone || "",
        city: city,
        ward: ward,
        street: street,
        address: address,
      };
      if (!form.value.title) form.value.title = "Nhà riêng";
      isUsingProfile.value = true;
    }
  } catch (error) {
    console.error("Failed to fetch user info", error);
  } finally {
    isLoadingProfile.value = false;
  }
};

const form = ref({
  title: "",
  fullName: "",
  email: "",
  phone: "",
  city: "",
  ward: "",
  street: "",
  address: "",
  countryCode: "VN",
  postalCode: "",
  isDefault: false,
});

const resetForm = () => {
  form.value = {
    title: "",
    fullName: "",
    email: "",
    phone: "",
    city: "",
    ward: "",
    street: "",
    address: "",
    countryCode: "VN",
    postalCode: "",
    isDefault: false,
  };
};

watch(
  () => props.initialData,
  (val) => {
    if (val && Object.keys(val).length > 0) {
      form.value = {
        title: val.title || "",
        fullName: val.fullName || "",
        email: val.email || "",
        phone: val.phone || "",
        city: val.city || "",
        ward: val.ward || "",
        street: val.street || val.address || "",
        address: val.address || "",
        countryCode: val.countryCode || "VN",
        postalCode: val.postalCode || "",
        isDefault: val.isDefault || false,
      };
    } else {
      resetForm();
    }
  },
  { immediate: true },
);

watch(
  () => form.value.address,
  (val) => {
    form.value.street = val;
  },
);

const cities = computed(() => vietnamData);

const wards = computed(() => {
  if (!form.value.city) return [];
  const selectedCity = vietnamData.find((c: any) => c.codename === form.value.city);
  return selectedCity ? selectedCity.wards : [];
});

const onCityChange = (val: string) => {
  form.value.city = val;
  form.value.ward = "";
};

const close = () => {
  emit("update:visible", false);
};

const onSubmit = (values: any) => {
  emit("submit", { ...form.value, ...values });
};
</script>
