<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">Thêm mới địa chỉ</h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">Vui lòng nhập thông tin địa chỉ nhận hàng của bạn.</p>

    <form @submit.prevent="handleStore" class="space-y-4">
      <h2 class="text-sm font-semibold text-gray-700">Thông tin</h2>

      <div class="bg-white rounded-md">
        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            Tiêu đề
            <span class="text-red-500">*</span>
          </label>
          <Field name="title" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" placeholder="Nhà riêng / Văn phòng ..." class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>

        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            Họ và tên
            <span class="text-red-500">*</span>
          </label>
          <Field name="fullName" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <input v-bind="field" type="text" placeholder="Nguyễn Văn A" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Email</label>
            <Field name="email" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="email" placeholder="email@domain.com" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              Số điện thoại
              <span class="text-red-500">*</span>
            </label>
            <Field name="phone" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" placeholder="(+84) 123 456 789" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>

        <div class="mb-3">
          <label class="block text-sm text-gray-700 mb-1">
            Địa chỉ
            <span class="text-red-500">*</span>
          </label>
          <Field name="address" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
            <textarea v-bind="field" rows="3" placeholder="123 Đường ABC, Quận 1, TP.HCM" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
          </Field>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-3">
          <div>
            <label class="block text-sm text-gray-700 mb-1">Mã quốc gia</label>
            <Field name="countryCode" v-slot="{ field }">
              <input v-bind="field" type="text" maxlength="2" placeholder="VN" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">Tỉnh/Thành phố</label>
            <Field name="state" v-slot="{ field }">
              <input v-bind="field" type="text" placeholder="TP. Hồ Chí Minh" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
            </Field>
          </div>
          <div>
            <label class="block text-sm text-gray-700 mb-1">
              Mã bưu chính
              <span class="text-red-500">*</span>
            </label>
            <Field name="postalCode" v-slot="{ field, errorMessage, meta }" :validate-on-input="true">
              <input v-bind="field" type="text" placeholder="700000" class="w-full border border-gray-300 rounded px-3 py-2 text-sm focus:outline-none focus:border-sky-500" />
              <small v-if="errorMessage && meta.touched" class="text-red-500 text-xs">{{ $t(errorMessage) }}</small>
            </Field>
          </div>
        </div>

        <div class="flex items-end gap-2 mb-3">
          <Field name="isDefault" v-slot="{ field }">
            <AppCheckbox :modelValue="field.value" @update:modelValue="field.onChange" inputId="defaultAddress" :class="'scale-125'" binary />
          </Field>
          <label for="defaultAddress" class="text-sm text-gray-700 select-none">Địa chỉ mặc định</label>
        </div>
      </div>

      <div class="flex justify-end gap-3 sticky bottom-5 z-50">
        <Button type="button" variant="cancel" :to="PROFILEADRESSORDERINDEX" />
        <Button variant="success" :loading="isLoading" />
      </div>
    </form>
  </div>
</template>

<script setup lang="ts">
import { Field } from "vee-validate";
import AppCheckbox from "~/components/admin/custom/AppCheckbox.vue";
import Button from "~/components/admin/custom/Button.vue";
import { useAddressOrder } from "~/composables/admin/user/useAddressOrder";
import { useAuthStore } from "~/stores/auth/auth";

const { isLoading, handleStore, addressUserById, data } = useAddressOrder(undefined, "create", { redirectPath: "/profile/address_order" });
const authStore = useAuthStore();
const userId = authStore.user?.id || 0;

// Chặn truy cập trang create nếu đã có >= 3 địa chỉ
onMounted(async () => {
  if (!userId) return;
  await addressUserById(userId);
  const total = Array.isArray(data.value?.data) ? data.value?.data.length : 0;
  if (total >= 3) {
    return navigateTo("/profile/address_order");
  }
});

definePageMeta({
  middleware: ["auth-required"],
});
</script>

<style scoped></style>
