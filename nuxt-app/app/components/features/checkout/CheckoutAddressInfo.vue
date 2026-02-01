<template>
  <div class="text-gray-600 text-base">
    <div class="max-w-full w-full mx-auto p-4">
      <div>
        <h5 class="mb-1 font-medium text-lg">Địa chỉ nhận hàng</h5>
        <p class="text-sm mb-6 text-gray-500">Hãy điền thông tin hoặc chọn thông tin đã lưu để chúng tôi có thể giao hàng đến bạn.</p>
      </div>
      <div class="mt-6 max-w-4xl mx-auto">
        <div class="flex items-center justify-start mb-4">
          <h5 class="text-base font-semibold text-gray-800">Địa chỉ đã lưu</h5>
        </div>
        <AddressCard :model-value="modelValue.selectedAddress" :addresses="userAddresses" @update:model-value="(val: string | number) => $emit('update:modelValue', { ...modelValue, selectedAddress: Number(val) })" @create="handleOpenCreateContext" @update="handleOpenUpdateContext" @delete="handleDelete" />
        <div class="mt-8">
          <h5 class="text-base font-semibold text-gray-800 mb-4">Phương thức vận chuyển</h5>
          <div class="grid grid-cols-1 md:grid-cols-2 md:gap-4 gap-2">
            <label class="border border-gray-200 rounded-md shadow p-4 flex items-center justify-between cursor-pointer">
              <input type="radio" name="shippingMethod" value="standard" v-model="modelValue.shippingMethod" class="mr-3 w-5 h-5 accent-teal-500" />
              <div class="flex-1">
                <span class="block text-base font-semibold text-gray-800 mb-1">Giao hàng tiêu chuẩn</span>
                <span class="block text-xs text-gray-500 mb-1">Dự kiến giao từ 3 đến 5 ngày</span>
              </div>
              <span class="text-lg font-medium text-gray-800 ml-4">Miễn phí</span>
            </label>
            <label class="border border-gray-200 rounded-md shadow p-4 flex items-center justify-between cursor-pointer">
              <input type="radio" name="shippingMethod" value="express" v-model="modelValue.shippingMethod" class="mr-3 w-5 h-5 accent-teal-500" />
              <div class="flex-1">
                <span class="block text-base font-semibold text-gray-800 mb-1">Giao hàng nhanh</span>
                <span class="block text-xs text-gray-500 mb-1">Giao trong vòng 24 giờ</span>
              </div>
              <span class="text-lg font-medium text-gray-800 ml-4">50.000đ</span>
            </label>
          </div>
        </div>
      </div>
      <BackButton @click="$emit('back')" />
      <NextButton @click="$emit('next')" :disabled="!canProceed" />
      <AddressFormModal v-model:visible="showModal" :is-update="isUpdate" :initial-data="currentAddress" @submit="handleSubmit" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import type { IAddressInfo, IAddress } from "~/interfaces/cart/checkout.interface";
import AddressCard from "~/components/features/checkout/AddressCard.vue";
import AddressFormModal from "~/components/features/checkout/AddressFormModal.vue";
import NextButton from "~/components/features/checkout/NextButton.vue";
import BackButton from "~/components/features/checkout/BackButton.vue";
import { useAuthStore } from "~/stores/auth/auth";
import { profileService } from "~/services/user/profile.service";

const props = defineProps<{
  modelValue: IAddressInfo;
  canProceed: boolean;
}>();

const emit = defineEmits<{
  (e: "update:modelValue", value: IAddressInfo): void;
  (e: "next"): void;
  (e: "back"): void;
}>();

import { useAppToast } from "~/composables/useAppToast";
import { addressOrderService } from "~/services/user/address_order.service";
import type { IAddressOrder } from "~/interfaces/user/address_order.interface";

const toast = useAppToast();
const authStore = useAuthStore();
const userAddresses = ref<IAddress[]>([]);
const showModal = ref(false);
const isUpdate = ref(false);
const currentAddress = ref<any>({});
const isLoading = ref(false);

const mapAddress = (addr: any): IAddress => ({
  id: addr.id,
  label: addr.title || "Địa chỉ",
  customerName: addr.fullName || addr.name,
  customerPhone: addr.phone,
  shippingAddress: addr.address,
  shippingCity: addr.city,
  shippingWard: addr.ward,
  shippingStreet: addr.street || addr.address,
});

const fetchAddresses = async () => {
  if (authStore.user?.id) {
    try {
      const res = await profileService.getProfile();
      if (res.data && res.data.addressOrder) {
        userAddresses.value = res.data.addressOrder.slice(0, 3).map(mapAddress);
        // Optionally update auth store if needed, but primarily update local view
        if (authStore.user) {
          authStore.user.addressOrder = res.data.addressOrder;
        }
      }
    } catch (e) {
      console.error(e);
    }
  }
};

onMounted(() => {
  fetchAddresses();
});

const handleOpenCreateContext = () => {
  isUpdate.value = false;
  currentAddress.value = {};
  showModal.value = true;
};

const handleOpenUpdateContext = (id: string | number) => {
  const addr = authStore.user?.addressOrder?.find((a: any) => a.id === id);
  if (addr) {
    isUpdate.value = true;
    currentAddress.value = {
      ...addr,
      shippingCity: addr.city,
      shippingWard: addr.ward,
      countryCode: addr.countryCode || "VN",
      postalCode: addr.postalCode,
      street: addr.street || addr.address,
    };
    showModal.value = true;
  }
};

const handleSubmit = async (payload: Partial<IAddressOrder>) => {
  isLoading.value = true;
  try {
    let res;
    if (isUpdate.value && currentAddress.value.id) {
      res = await addressOrderService.update(currentAddress.value.id, payload);
    } else {
      res = await addressOrderService.store(payload);
    }

    if (res) {
      toast.success(isUpdate.value ? "Cập nhật địa chỉ thành công" : "Thêm địa chỉ thành công");
      await fetchAddresses();
      showModal.value = false;
    }
  } catch (error: any) {
    toast.error(error?.response?.data?.message || "Có lỗi xảy ra");
  } finally {
    isLoading.value = false;
  }
};

const handleDelete = async (id: string | number) => {
  if (!confirm("Bạn có chắc chắn muốn xóa địa chỉ này?")) return;
  try {
    await addressOrderService.delete(Number(id));
    toast.success("Xóa địa chỉ thành công");
    await fetchAddresses();
  } catch (error: any) {
    toast.error("Xóa địa chỉ thất bại");
  }
};
</script>
