<template>
  <div class="main-content max-w-7xl mx-auto">
    <!-- Breadcrumb  -->
    <Breadcrumb :crumbs="[{ label: $t('cart.breadcrumb'), href: TOCART }]" />
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
                    <CheckoutAddressInfo v-model="formData.address_info" :personal-address="formatAddress(formData.personal_info)" :can-proceed="canProceedToNext" @next="goToNextTab" @back="goToPreviousTab" @open-modal="openModal" />
                  </div>

                  <div v-if="activeTab === 'payment_method'">
                    <CheckoutPaymentMethod v-model="formData.payment_method" :payment-methods="paymentMethods" :can-proceed="canProceedToNext" @next="goToNextTab" @back="goToPreviousTab" />
                  </div>
                </div>
              </section>
            </div>

            <div class="col-span-12 lg:col-span-4">
              <OrderSummary
                :selected-items="selectedItems"
                :selected-count="selectedCount"
                :selected-total-price="selectedTotalPrice"
                :shipping-method="formData.address_info.shippingMethod || undefined"
                v-model:coupon-code="couponCode"
                :discount-amount="discountAmount"
                :applied-coupon="appliedCoupon"
                :is-applying-coupon="isApplyingCoupon"
                :loading="isSubmitting"
                :button-label="activeTab === 'payment_method' ? 'Đặt hàng' : undefined"
                @unselect-item="handleUnselectItem"
                @apply-coupon="applyCoupon"
                @remove-coupon="removeCoupon"
                @checkout="handleCheckout"
              />
            </div>
          </div>
        </div>
      </article>
    </section>

    <!-- Modals -->
    <CheckoutModals />
  </div>
</template>

<script setup lang="ts">
import { onMounted, onUnmounted } from "vue";
import { useCheckout } from "~/composables/cart/useCheckout";
import { useCartStore } from "~/stores/cart";
import { useCart } from "~/composables/cart/useCart";
import CheckoutProgress from "~/components/features/checkout/CheckoutProgress.vue";
import CheckoutPersonalInfo from "~/components/features/checkout/CheckoutPersonalInfo.vue";
import CheckoutAddressInfo from "~/components/features/checkout/CheckoutAddressInfo.vue";
import CheckoutPaymentMethod from "~/components/features/checkout/CheckoutPaymentMethod.vue";
import OrderSummary from "~/components/features/checkout/OrderSummary.vue";
import CheckoutModals from "~/components/features/checkout/CheckoutModals.vue";

const {
  activeTab,
  formData,
  paymentMethods,
  isTabValid,
  canProceedToNext,
  progressPercentage,
  isSubmitting,
  couponCode,
  discountAmount,
  appliedCoupon,
  isApplyingCoupon,
  selectedItems,
  selectedCount,
  selectedTotalPrice,
  goToNextTab,
  goToPreviousTab,
  handleTabClick,
  handleCheckout,
  applyCoupon,
  removeCoupon,
} = useCheckout();

const { loadCart } = useCart();
const cartStore = useCartStore();

const handleUnselectItem = (id: number) => {
  cartStore.toggleSelectItem(id);
};

// Modal handling
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
