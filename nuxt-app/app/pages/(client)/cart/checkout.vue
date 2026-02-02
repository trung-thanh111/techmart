<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- Breadcrumb  -->
    <Breadcrumb :crumbs="[{ label: $t('cart.breadcrumb'), href: '/cart' }]" />
    <!-- bnner slide  -->
    <section>
      <article>
        <div class="container mx-auto">
          <div class="grid grid-cols-12 gap-6 mb-5">
            <div class="col-span-12 lg:col-span-8">
              <section id="tabs-information" class="bg-white checkout-description max-md:pt-3 p-3 shadow-md">
                <CheckoutProgress :active-tab="activeTab" :progress="progressPercentage" :is-valid="isTabValid" @change-tab="handleTabClick" />

                <div class="container">
                  <div v-if="activeTab === 'personal_info'">
                    <CheckoutPersonalInfo v-model="formData.personal_info" :can-proceed="canProceedToNext" @next="goToNextTab" />
                  </div>

                  <div v-if="activeTab === 'address_info'">
                    <CheckoutAddressInfo v-model="formData.address_info" :can-proceed="canProceedToNext" @next="goToNextTab" @back="goToPreviousTab" @open-modal="openModal" />
                  </div>

                  <div v-if="activeTab === 'payment_method'">
                    <CheckoutPaymentMethod v-model="formData.payment_method" :payment-methods="paymentMethods" :can-proceed="canProceedToNext" @next="goToNextTab" @back="goToPreviousTab" />
                  </div>
                </div>
              </section>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <OrderSummary :selected-items="selectedItems" :selected-count="selectedCount" :selected-total-price="selectedTotalPrice" v-model:coupon-code="couponCode" @unselect-item="handleUnselectItem" @apply-coupon="applyCoupon" @checkout="handleCheckout" />
            </div>
          </div>
        </div>
      </article>
    </section>

    <!-- Modals -->
    <CheckoutModals @create="console.log('Create address')" @update="console.log('Update address')" @delete="console.log('Delete address')" />
  </div>
</template>

<script setup lang="ts">
import { ref, onMounted, onUnmounted } from "vue";
import { useCheckout } from "~/composables/cart/useCheckout";
import { useCartStore } from "~/stores/cart";
import { storeToRefs } from "pinia";
import CheckoutProgress from "~/components/features/checkout/CheckoutProgress.vue";
import CheckoutPersonalInfo from "~/components/features/checkout/CheckoutPersonalInfo.vue";
import CheckoutAddressInfo from "~/components/features/checkout/CheckoutAddressInfo.vue";
import CheckoutPaymentMethod from "~/components/features/checkout/CheckoutPaymentMethod.vue";
import OrderSummary from "~/components/features/checkout/OrderSummary.vue";
import CheckoutModals from "~/components/features/checkout/CheckoutModals.vue";
import { useCart } from "~/composables/cart/useCart";

const { activeTab, formData, paymentMethods, isTabValid, canProceedToNext, progressPercentage, goToNextTab, goToPreviousTab, handleTabClick } = useCheckout();

const { loadCart } = useCart();
const cartStore = useCartStore();
const { selectedItemsData: selectedItems, selectedCount, selectedTotalPrice } = storeToRefs(cartStore);

const couponCode = ref("");

const applyCoupon = () => {
  console.log("Apply coupon", couponCode.value);
  // Logic apply coupon
};

const handleCheckout = () => {
  console.log("Checkout", formData);
  // Logic checkout
};

const handleUnselectItem = (id: number) => {
  cartStore.toggleSelectItem(id);
};

const openModal = (modalId: string) => {
  const modal = document.getElementById(modalId);
  if (modal) {
    modal.classList.remove("hidden");
    const scrollbarWidth = window.innerWidth - document.documentElement.clientWidth;
    document.body.style.paddingRight = `${scrollbarWidth}px`;
    document.body.style.overflow = "hidden";
  }
};

const closeModal = (modal: Element | null) => {
  if (modal) {
    modal.classList.add("hidden");
    document.body.style.overflow = "";
    document.body.style.paddingRight = "";
  }
};

const handleModalClick = (e: MouseEvent) => {
  const target = e.target as HTMLElement;

  const openTrigger = target.closest("[data-open-modal]");
  if (openTrigger) {
    const modalId = openTrigger.getAttribute("data-open-modal");
    if (modalId) openModal(modalId);
  }

  if (target.closest(".btn-close-modal") || target.closest("[data-modal-hide]")) {
    const modal = target.closest(".modal");
    closeModal(modal);
  }

  if (target.classList.contains("modal")) {
    closeModal(target);
  }
};

onMounted(async () => {
  document.addEventListener("click", handleModalClick);
  await loadCart();
});

onUnmounted(() => {
  document.removeEventListener("click", handleModalClick);
});

definePageMeta({
  layout: "client",
});
</script>
