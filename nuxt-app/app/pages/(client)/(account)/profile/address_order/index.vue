<template>
  <div class="w-100">
    <h1 class="text-xl md:text-2xl font-semibold text-gray-800 mb-1">
      {{ $t("profile.addressList") }}
    </h1>
    <p class="text-xs text-gray-500 mb-5 md:mb-10">
      {{ $t("profile.addressListDesc") }}
    </p>

    <form class="mb-10">
      <div class="flex justify-between items-center mb-4 gap-2">
        <h2 class="text-xs md:text-sm font-semibold text-gray-700">
          {{ $t("profile.list") }}
        </h2>
      </div>
      <div v-if="isLoading && !addresses.length" class="text-xs text-center text-gray-500 py-5">
        {{ $t("profile.loadingAddress") }}
      </div>
      <div v-else-if="!addresses.length" class="text-xs text-center text-gray-500 py-5">
        {{ $t("profile.noAddress") }}
      </div>

      <TransitionGroup name="address" tag="div">
        <div v-for="item in addresses" :key="item.id" class="voucher-item relative flex flex-col bg-sky-50 md:ps-3 ps-2 md:mb-3 mb-2 rounded-lg shadow-sm transition-all duration-300">
          <div v-if="item.isDefault" class="absolute top-0 right-0 bg-teal-500 text-white md:text-[10px] text-[8px] py-1 md:py-1.5 uppercase font-semibold px-2 rounded-bl-md">
            {{ $t("profile.default") }}
          </div>
          <div class="flex items-center">
            <div class="w-20 h-20 rounded-lg bg-white p-0 hidden md:flex items-center justify-center flex-shrink-0 overflow-hidden">
              <div v-if="buildMapEmbedSrc(item)">
                <iframe :src="buildMapEmbedSrc(item)" class="w-full h-full" style="border: 0" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
              </div>
              <div v-else>
                <NuxtImg src="/images/defaults/record.jpg" alt="Map preview" class="w-full h-full object-cover" loading="lazy" />
              </div>
            </div>

            <div class="hidden md:block w-[2px] h-[90px] border-l border-1 border-gray-300 mx-3"></div>
            <div class="flex-1 w-full text-gray-800 rounded-md md:p-4 p-2">
              <div>
                <h3 class="voucher-title font-semibold text-base md:text-lg mb-1">
                  {{ item.title || item.fullName }}
                </h3>
                <div class="flex items-center justify-between mb-2">
                  <div class="flex items-center">
                    <svg class="md:w-4 md:h-4 w-3 h-3 mr-2 text-sky-500" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512" fill="currentColor">
                      <path d="M16 144a144 144 0 1 1 288 0A144 144 0 1 1 16 144zM160 80c8.8 0 16-7.2 16-16s-7.2-16-16-16c-53 0-96 43-96 96c0 8.8 7.2 16 16 16s16-7.2 16-16c0-35.3 28.7-64 64-64zM128 480l0-162.9c10.4 1.9 21.1 2.9 32 2.9s21.6-1 32-2.9L192 480c0 17.7-14.3 32-32 32s-32-14.3-32-32z" />
                    </svg>
                    <span class="md:text-sm text-xs">{{ item.address }}</span>
                  </div>
                  <label class="relative inline-block w-8 h-4 cursor-pointer md:mb-3 mb-2">
                    <ToggleSwitch v-model="item.isDefault" @update:modelValue="() => handleToggleDefault(item)" class="scale-75" :disabled="isTogglingDefault" />
                  </label>
                </div>
              </div>

              <div class="flex justify-between items-center text-xs">
                <a class="hover:underline" :href="item.phone ? `tel:${item.phone}` : '#'">(+84) {{ item.phone?.replace(/^0/, "") || "N/A" }}</a>
                <div class="flex items-center gap-3">
                  <NuxtLink :to="`/profile/address_order/${item.id}/update`" class="text-sky-500 font-medium underline flex items-center cursor-pointer hover:text-sky-600 transition-colors">
                    {{ $t("profile.customize") }}
                  </NuxtLink>
                  <button type="button" @click="() => openDeleteModal(item)" class="text-red-500 font-medium underline flex items-center cursor-pointer hover:text-red-600 transition-colors" :disabled="isLoading">
                    {{ $t("profile.delete") }}
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </TransitionGroup>

      <div v-if="addresses.length < 3" class="md:mb-4 mb-2">
        <NuxtLink :to="PROFILEADDRESSORDERCREATE" class="block border-1 border-dashed border-gray-300 rounded-lg p-3 text-center hover:border-sky-400 hover:bg-sky-50 transition-colors">
          <div class="flex items-center justify-center gap-2 text-sky-600">
            <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 448 512" fill="currentColor">
              <path d="M256 80c0-17.7-14.3-32-32-32s-32 14.3-32 32l0 144L48 224c-17.7 0-32 14.3-32 32s14.3 32 32 32l144 0 0 144c0 17.7 14.3 32 32 32s32-14.3 32-32l0-144 144 0c17.7 0 32-14.3 32-32s-14.3-32-32-32l-144 0 0-144z" />
            </svg>
            <span class="text-sm">{{ $t("profile.addAddress") }}</span>
          </div>
        </NuxtLink>
      </div>
    </form>

    <Teleport to="body">
      <Transition name="modal">
        <div v-if="showDeleteModal" class="fixed inset-0 z-50 flex items-center justify-center p-4">
          <div class="absolute inset-0 bg-black/50 transition-opacity" @click="closeDeleteModal"></div>
          <div class="relative bg-white rounded-lg shadow-lg w-full max-w-xl animate-modal-in">
            <div class="grid grid-cols-[48px_1fr] gap-4 p-5">
              <div class="flex items-start justify-center">
                <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center">
                  <svg class="w-5 h-5 text-red-500" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                    <path d="M12 2a10 10 0 1 0 10 10A10.011 10.011 0 0 0 12 2Zm1 15h-2v-2h2Zm0-4h-2V7h2Z" />
                  </svg>
                </div>
              </div>

              <div>
                <h3 class="text-base font-semibold text-gray-900">
                  {{ $t("profile.deleteRecord") }}
                </h3>
                <p class="mt-2 text-sm text-gray-600">
                  {{ $t("profile.deleteConfirm") }}
                </p>

                <div v-if="selectedItem" class="mt-4">
                  <p class="text-[12px] text-gray-500 mb-1">
                    {{ $t("profile.record") }}
                  </p>
                  <input :value="selectedItem.title" type="text" class="w-full border border-red-300 rounded px-3 py-2 text-sm text-red-600 bg-red-50" readonly />
                </div>

                <div class="mt-5 flex justify-end gap-3">
                  <button type="button" class="px-4 py-1 rounded border border-gray-300 bg-white text-gray-700 text-xs hover:bg-gray-50 transition-colors" @click="closeDeleteModal" :disabled="isLoading">
                    {{ $t("profile.cancel") }}
                  </button>
                  <button type="button" class="px-4 py-1 rounded bg-red-600 text-white text-xs hover:bg-red-700 disabled:opacity-50 transition-colors" :disabled="isLoading || !selectedItem" @click="handleConfirmDelete">
                    <span v-if="isLoading">{{ $t("common.loading") }}...</span>
                    <span v-else>{{ $t("profile.delete") }}</span>
                  </button>
                </div>
              </div>
            </div>
          </div>
        </div>
      </Transition>
    </Teleport>
  </div>
</template>

<script setup lang="ts">
import { useAddressOrder } from "~/composables/user/useAddressOrder";
import { PROFILEADDRESSORDERCREATE } from "~/utils/constants";
import { useAuthStore } from "~/stores/auth/auth";
import type { IAddressOrder } from "~/interfaces/user/address_order.interface";

const { data, isLoading, getAddressesByUserId, handleToggleIsDefault, selectedItem, setSelectedItem, confirmDelete } = useAddressOrder();

const authStore = useAuthStore();
const id = authStore.user?.id || 0;
const showDeleteModal = ref(false);
const isTogglingDefault = ref(false);

const addresses = computed((): IAddressOrder[] => {
  const rawData = data.value?.data as any;
  if (Array.isArray(rawData)) return rawData;
  if (rawData && Array.isArray(rawData.data)) return rawData.data;
  return [];
});

const composeAddress = (item: IAddressOrder): string => {
  const parts = [item.address, item.state, item.postalCode, item.countryCode].map((v) => (v || "").trim()).filter((v) => v.length > 0);

  return parts.join(", ");
};

const buildMapEmbedSrc = (item: IAddressOrder): string => {
  const addr = composeAddress(item);
  if (!addr) return "";

  const q = encodeURIComponent(addr);
  return `https://www.google.com/maps?q=${q}&output=embed`;
};

const openDeleteModal = (item: IAddressOrder) => {
  setSelectedItem(item);
  showDeleteModal.value = true;
};

const closeDeleteModal = () => {
  showDeleteModal.value = false;
  setSelectedItem(null);
};

const handleConfirmDelete = async () => {
  try {
    await confirmDelete();
    closeDeleteModal();
  } catch (error) {
    console.error("Error in handleConfirmDelete:", error);
  }
};

const handleToggleDefault = async (item: IAddressOrder) => {
  if (isTogglingDefault.value) return;

  isTogglingDefault.value = true;
  try {
    await handleToggleIsDefault(item);
  } catch (error) {
    console.error("Error in handleToggleDefault:", error);
  } finally {
    isTogglingDefault.value = false;
  }
};

onMounted(async () => {
  try {
    await getAddressesByUserId(id);
  } catch (error) {
    console.error("Error loading addresses on mount:", error);
  }
});
</script>

<style scoped>
.address-enter-active,
.address-leave-active {
  transition: all 0.3s ease;
}

.address-enter-from {
  opacity: 0;
  transform: translateY(-10px);
}

.address-leave-to {
  opacity: 0;
  transform: translateY(10px);
}

.address-move {
  transition: transform 0.3s ease;
}
.modal-enter-active,
.modal-leave-active {
  transition: opacity 0.3s ease;
}

.modal-enter-from,
.modal-leave-to {
  opacity: 0;
}

@keyframes modal-in {
  from {
    opacity: 0;
    transform: scale(0.95) translateY(-10px);
  }
  to {
    opacity: 1;
    transform: scale(1) translateY(0);
  }
}

.animate-modal-in {
  animation: modal-in 0.3s ease-out;
}
.transition-all {
  transition-property: all;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}

.transition-colors {
  transition-property: color, background-color, border-color;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 200ms;
}

.transition-opacity {
  transition-property: opacity;
  transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
  transition-duration: 300ms;
}
</style>
