<template>
  <div class="main-content max-w-7xl mx-auto">
    <Breadcrumb :crumbs="[{ label: $t('cart.breadcrumb'), href: TOCART }]" />
    <section>
      <article>
        <div class="container mx-auto">
          <DataState :loading="isPageLoading" :error="false" :data="isEmpty ? [] : items" :show-empty-image="false">
            <template v-if="!isEmpty && items.length > 0">
              <div class="grid grid-cols-12 gap-6 mb-5">
                <div class="col-span-12 lg:col-span-8">
                  <div class="w-full overflow-x-auto scrollbar-hide">
                    <table class="min-w-[700px] w-full text-gray-700 table-fixed bg-white rounded shadow-sm">
                      <thead>
                        <tr class="border-b border-gray-200 text-sm text-left">
                          <th class="p-2 md:p-4 w-10 text-center">
                            <label class="relative flex items-center justify-center cursor-pointer">
                              <Checkbox :modelValue="isAllSelected" @update:modelValue="handleSelectAll" binary size="small" />
                              <span v-if="isAllSelected" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                              </span>
                            </label>
                          </th>
                          <th class="w-[52%] px-1 md:px-2">{{ $t("cart.product") }}</th>
                          <th class="w-[16%] px-1 md:px-2 text-center">{{ $t("cart.unitPrice") }}</th>
                          <th class="w-[14%] px-1 md:px-2 text-center">{{ $t("cart.quantity") }}</th>
                          <th class="w-[16%] px-1 md:px-2 text-center">{{ $t("cart.total") }}</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr v-for="item in items" :key="item.id" class="hover:bg-[#f1f1f1]">
                          <td class="p-2 md:p-4 text-center">
                            <label class="relative flex items-center justify-center cursor-pointer">
                              <Checkbox :modelValue="isItemSelected(item.id!)" @update:modelValue="toggleSelectItem(item.id!)" binary size="small" />
                              <span v-if="isItemSelected(item.id!)" class="absolute inset-0 flex items-center justify-center pointer-events-none">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 md:h-5 md:w-5 text-white" viewBox="0 0 20 20" fill="currentColor">
                                  <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                </svg>
                              </span>
                            </label>
                          </td>
                          <td class="py-2 md:py-4">
                            <div class="flex gap-2 md:gap-4 min-w-0">
                              <NuxtImg :src="getProductImage(item)" :alt="getProductName(item)" class="w-14 h-14 md:w-20 md:h-20 object-cover rounded max-w-full" loading="lazy" />
                              <div class="min-w-0 flex-1">
                                <NuxtLink :to="`${PRODUCT}/${item.product?.slug || ''}`" class="font-medium mb-1 text-xs md:text-sm hover:text-teal-600 line-clamp-custom-2">
                                  {{ getProductName(item) }}
                                </NuxtLink>
                                <p class="text-xs text-gray-500 truncate">{{ getVariantInfo(item) }}</p>
                                <Button @click.stop.prevent="handleRemoveItem(item.id!)" class="text-xs inline-flex items-center px-1 py-0.5 gap-1 rounded-md" outlined severity="danger" size="small">
                                  <i class="fa-solid fa-trash text-xs"></i>
                                  {{ $t("cart.removeItem") }}
                                </Button>
                              </div>
                            </div>
                          </td>
                          <td class="text-center align-middle whitespace-nowrap text-xs md:text-sm">
                            {{ formatPrice(item.price || 0) }}
                          </td>
                          <td class="text-center align-middle">
                            <div class="flex items-center justify-between rounded overflow-hidden border border-gray-200 p-1 shadow-sm min-w-0 gap-1">
                              <button type="button" :title="$t('cart.decreaseQuantity')" @click.stop.prevent="handleDecreaseQuantity(item.id!, item.quantity || 1)" :disabled="(item.quantity || 0) <= 1" class="bg-teal-500 text-white w-8 h-6 md:w-8 md:h-7 flex items-center justify-center hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                                  <path d="M3.75 9H14.25"></path>
                                </svg>
                              </button>
                              <div class="w-7 h-6 md:w-8 md:h-7 flex items-center justify-center bg-white">
                                <input type="text" :value="item.quantity || 1" readonly class="text-center text-gray-800 text-xs md:text-sm focus:outline-none max-w-full bg-white" inputmode="numeric" />
                              </div>
                              <button type="button" :title="$t('cart.increaseQuantity')" @click.stop.prevent="handleUpdateQuantity(item.id!, (item.quantity || 1) + 1)" :disabled="(item.quantity || 0) >= getStock(item)" class="bg-teal-500 text-white w-8 h-6 md:w-8 md:h-7 flex items-center justify-center hover:bg-teal-600 disabled:opacity-50 disabled:cursor-not-allowed">
                                <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" viewBox="0 0 18 18">
                                  <path d="M9 3.75V14.25"></path>
                                  <path d="M3.75 9H14.25"></path>
                                </svg>
                              </button>
                            </div>
                          </td>
                          <td class="text-center align-middle font-semibold whitespace-nowrap text-sm">
                            {{ formatPrice((item.price || 0) * (item.quantity || 0)) }}
                          </td>
                        </tr>
                      </tbody>
                    </table>
                  </div>

                  <!-- Cart Actions -->
                  <div class="mt-6 bg-white rounded-lg shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-4">
                      <div class="flex justify-between items-center gap-2 md:gap-4">
                        <Button :label="$t('cart.shopping')" outlined @click="navigateTo(TOSHOP)" class="!flex items-center gap-1 md:gap-2 px-2 md:px-4 py-1 md:py-2 text-xs md:text-sm font-medium text-gray-700 bg-gray-50 hover:bg-gray-100 rounded-sm transition-colors duration-200">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                          </svg>
                          <span>{{ $t("cart.shopping") }}</span>
                        </Button>
                        <Button :label="$t('cart.clearCart')" severity="danger" @click.stop.prevent="handleClearCart" class="!flex items-center gap-1 md:gap-2 px-2 md:px-4 py-1 md:py-2 text-xs md:text-sm font-medium text-white bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 rounded-sm transition-all duration-200 transform shadow-sm">
                          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                          </svg>
                          <span>{{ $t("cart.clearCart") }}</span>
                        </Button>
                      </div>
                      <div class="mt-2 md:mt-4 border-t border-gray-100">
                        <div class="flex items-center justify-center gap-1 md:gap-2 text-xs text-gray-500">
                          <svg class="w-3.5 h-3.5 md:w-4 md:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                          <span>{{ $t("cart.cartSaved") }}</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="col-span-12 lg:col-span-4">
                  <OrderSummary :selected-items="selectedItems" :selected-count="selectedCount" :selected-total-price="selectedTotalPrice" v-model:coupon-code="couponCode" @unselect-item="handleUnselectItem" @apply-coupon="applyCoupon" @checkout="handleCheckout" :to="TOCHECKOUT" />
                </div>
              </div>
            </template>
            <template #empty>
              <div class="flex flex-col items-center justify-center py-12 text-center text-gray-500">
                <NuxtImg :src="EMPTYBOX" :alt="$t('common.noData')" class="object-contain mb-4 h-32 md:h-40" loading="lazy" />
                <p class="text-gray-500 mb-6">{{ $t("cart.emptyDesc") }}</p>
                <Button :label="$t('cart.shopping')" icon="pi pi-arrow-left" @click="navigateTo(TOSHOP)" class="rounded py-1" />
              </div>
            </template>
          </DataState>
        </div>
      </article>
    </section>
  </div>

  <Dialog v-model:visible="showDeleteItemDialog" :header="t('cart.removeItem')" :modal="true" :style="{ width: '450px' }" :closable="true" @hide="selectedItemToDelete = null">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <i class="pi pi-exclamation-triangle text-2xl text-red-500"></i>
        <span class="text-base">{{ t("cart.removeItemConfirm") }}</span>
      </div>
      <div v-if="selectedItemToDelete" class="mt-2">
        <p class="text-sm text-gray-600 mb-2">{{ t("cart.product") }}:</p>
        <div class="p-2 bg-gray-50 border border-red-200 rounded">
          <p class="font-medium text-red-500 text-sm">{{ getProductName(selectedItemToDelete) }}</p>
        </div>
      </div>
    </div>
    <template #footer>
      <Button :label="t('cart.cancel')" severity="secondary" outlined @click="showDeleteItemDialog = false" class="px-3 py-1.5 text-xs rounded" />
      <Button :label="t('cart.removeItem')" severity="danger" :loading="isLoading" @click="handleConfirmDeleteItem" class="px-3 py-1.5 text-xs rounded" />
    </template>
  </Dialog>

  <Dialog v-model:visible="showClearCartDialog" :header="t('cart.clearCart')" :modal="true" :style="{ width: '450px' }" :closable="true" @hide="showClearCartDialog = false">
    <div class="flex flex-col gap-4">
      <div class="flex items-center gap-3">
        <i class="pi pi-exclamation-triangle text-2xl text-red-500"></i>
        <span class="text-base">{{ t("cart.clearCartConfirm") }}</span>
      </div>
      <div class="mt-2">
        <p class="text-sm text-gray-600">{{ t("cart.clearCartWarning") }}</p>
      </div>
    </div>
    <template #footer>
      <Button :label="t('cart.cancel')" severity="secondary" outlined @click="showClearCartDialog = false" class="px-3 py-1.5 text-xs rounded" />
      <Button :label="t('cart.clearCart')" severity="danger" :loading="isLoading" @click="handleConfirmClearCart" class="px-3 py-1.5 text-xs rounded" />
    </template>
  </Dialog>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { definePageMeta, navigateTo } from "#imports";
import { useToast } from "primevue/usetoast";
import { useI18n } from "vue-i18n";
import Breadcrumb from "~/components/navigation/Breadcrumb.vue";
import DataState from "~/components/ui/DataState.vue";
import { useCart } from "~/composables/cart/useCart";
import { formatPrice, resolveAlbum } from "~/utils/helpers";
import { IMAGEDEFAULT, TOSHOP, PRODUCT, EMPTYBOX, TOCHECKOUT } from "~/utils/constants";
import type { ICartItem } from "~/interfaces/cart/cartItem.interface";
import OrderSummary from "~/components/features/checkout/OrderSummary.vue";

const isPageLoading = ref(true);

const { t } = useI18n();
const toast = useToast();

const { items, isEmpty, isLoading, selectedItems, selectedCount, selectedTotalPrice, toggleSelectItem, selectAll, deselectAll, isItemSelected, updateItemQuantity, removeItem, clearCart, loadCart } = useCart();

const couponCode = ref("");
const showDeleteItemDialog = ref(false);
const showClearCartDialog = ref(false);
const selectedItemToDelete = ref<ICartItem | null>(null);

const isAllSelected = computed(() => {
  if (items.value.length === 0) return false;
  const itemsWithId = items.value.filter((item) => item.id);
  if (itemsWithId.length === 0) return false;
  return itemsWithId.every((item) => isItemSelected(item.id!));
});

const handleSelectAll = (checked: boolean) => {
  if (checked) {
    selectAll();
  } else {
    deselectAll();
  }
};

const getProductImage = (item: ICartItem): string => {
  if (item.variant && "products" in item.variant && (item.variant as any).products?.album) {
    const images = resolveAlbum((item.variant as any).products.album);
    return images[0] || IMAGEDEFAULT;
  }
  if (item.product?.album) {
    const images = resolveAlbum(item.product.album);
    return images[0] || IMAGEDEFAULT;
  }
  return IMAGEDEFAULT;
};

const getProductName = (item: ICartItem): string => {
  const productName = item.product?.name || "";
  const variantName = item.variant?.name || "";
  if (variantName) {
    return `${productName} - ${variantName}`;
  }
  return productName;
};

const getVariantInfo = (item: ICartItem): string => {
  if (item.variant?.attributeData) {
    return Object.values(item.variant.attributeData)
      .map((attr: any) => attr.name)
      .join(", ");
  }
  return "";
};

const getStock = (item: ICartItem): number => {
  return item.variant?.stock || 999;
};

const handleDecreaseQuantity = async (itemId: number, currentQty: number) => {
  if (currentQty > 1) {
    await updateItemQuantity(itemId, currentQty - 1);
  }
};

const handleUpdateQuantity = async (itemId: number, quantity: number) => {
  if (quantity >= 1) {
    await updateItemQuantity(itemId, quantity);
  }
};

const handleRemoveItem = (itemId: number) => {
  const item = items.value.find((i) => i.id === itemId);
  if (item) {
    selectedItemToDelete.value = item;
    showDeleteItemDialog.value = true;
  }
};

const handleConfirmDeleteItem = async () => {
  if (selectedItemToDelete.value?.id) {
    await removeItem(selectedItemToDelete.value.id);
    showDeleteItemDialog.value = false;
    selectedItemToDelete.value = null;
  }
};

const handleUnselectItem = (itemId: number) => {
  toggleSelectItem(itemId);
};

const handleClearCart = () => {
  showClearCartDialog.value = true;
};

const handleConfirmClearCart = async () => {
  await clearCart();
  showClearCartDialog.value = false;
};

const handleCheckout = () => {
  if (selectedCount.value === 0) {
    toast.add({
      severity: "warn",
      summary: t("cart.error"),
      detail: t("cart.selectAtLeastOne"),
      life: 3000,
    });
    return;
  }
};

const applyCoupon = () => {
  if (selectedCount.value === 0) {
    return;
  }
  toast.add({
    severity: "info",
    summary: t("cart.notification"),
    detail: t("cart.featureDeveloping"),
    life: 3000,
  });
};

onMounted(async () => {
  await loadCart();
  isPageLoading.value = false;
});

definePageMeta({
  layout: "client",
});
</script>
<style scoped>
.line-clamp-custom-2 {
  display: -webkit-box;
  -webkit-box-orient: vertical;
  overflow: hidden;
  text-overflow: ellipsis;
  -webkit-line-clamp: 2;
}
</style>
