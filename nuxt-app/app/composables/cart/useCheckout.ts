import { ref, reactive, computed } from "vue";
import { z } from "zod";
import type { CheckoutFormData, ICheckoutTab, IPaymentMethod } from "~/interfaces/cart/checkout.interface";
import type { IOrder, IOrderItem } from "~/interfaces/order/order.interface";
import { SHIPPING_METHODS, SHIPPING_FEES, PAYMENT_METHODS } from "~/utils/order.constants";
import { orderService } from "~/services/order/order.service";
import { cartItemService } from "~/services/cart/cartItem.service";
import { useCartStore } from "~/stores/cart";
import { useAuthStore } from "~/stores/auth/auth";
import { useAppToast } from "~/composables/useAppToast";
import { storeToRefs } from "pinia";

export const zodPersonalSchema = z.object({
  fullName: z.string({ required_error: "Họ và tên là bắt buộc" }).min(1, "Họ và tên là bắt buộc"),
  email: z.string({ required_error: "Email là bắt buộc" }).email("Email không hợp lệ"),
  phone: z.string({ required_error: "Số điện thoại là bắt buộc" }).regex(/(84|0[3|5|7|8|9])+([0-9]{8})\b/, "Số điện thoại không hợp lệ"),
  city: z.string({ required_error: "Tỉnh/Thành phố là bắt buộc" }).min(1, "Tỉnh/Thành phố là bắt buộc"),
  ward: z.string({ required_error: "Phường/Xã là bắt buộc" }).min(1, "Phường/Xã là bắt buộc"),
  street: z.string({ required_error: "Địa chỉ cụ thể là bắt buộc" }),
  customerNote: z.string().optional(),
});

export const useCheckout = () => {
  const activeTab = ref<ICheckoutTab>("personal_info");
  const activePaymentMethod = ref<string>(PAYMENT_METHODS.COD);
  const isSubmitting = ref(false);
  const couponCode = ref("");

  const cartStore = useCartStore();
  const authStore = useAuthStore();
  const toast = useAppToast();
  const { selectedItemsData: selectedItems, selectedCount, selectedTotalPrice } = storeToRefs(cartStore);

  const formData = reactive<CheckoutFormData>({
    personal_info: {
      fullName: "",
      email: "",
      phone: "",
      city: "",
      ward: "",
      street: "",
      customerNote: "",
    },
    address_info: {
      selectedAddress: null,
      shippingMethod: SHIPPING_METHODS.STANDARD,
    },
    payment_method: {
      selectedMethod: PAYMENT_METHODS.COD,
    },
  });

  const paymentMethods: IPaymentMethod[] = [
    {
      id: PAYMENT_METHODS.COD,
      name: "Khi nhận hàng",
      icon: "/images/cash-on-delivery.webp",
    },
    {
      id: PAYMENT_METHODS.VNPAY,
      name: "VNPay",
      icon: "/images/logo/vnpay.webp",
    },
    {
      id: PAYMENT_METHODS.MOMO,
      name: "MoMo",
      icon: "/images/logo/momo.svg",
    },
  ];

  const TABS: ICheckoutTab[] = ["personal_info", "address_info", "payment_method"];

  const selectedAddress = computed(() => {
    if (!formData.address_info.selectedAddress) return null;
    return authStore.user?.addressOrder?.find((addr: any) => addr.id === formData.address_info.selectedAddress);
  });

  const shippingFee = computed(() => {
    if (formData.address_info.shippingMethod === SHIPPING_METHODS.EXPRESS) {
      return SHIPPING_FEES[SHIPPING_METHODS.EXPRESS];
    }
    return SHIPPING_FEES[SHIPPING_METHODS.STANDARD];
  });

  // --- Validation ---
  const isPersonalInfoValid = computed(() => {
    return zodPersonalSchema.safeParse(formData.personal_info).success;
  });

  const isAddressInfoValid = computed(() => {
    return formData.address_info.selectedAddress !== null && formData.address_info.shippingMethod !== null;
  });

  const isTabValid = computed<Record<ICheckoutTab, boolean>>(() => ({
    personal_info: isPersonalInfoValid.value,
    address_info: isAddressInfoValid.value,
    payment_method: true,
  }));

  const canProceedToNext = computed(() => {
    return isTabValid.value[activeTab.value];
  });

  const progressPercentage = computed(() => {
    const tabProgress: Record<ICheckoutTab, number> = {
      personal_info: 33.33,
      address_info: 66.66,
      payment_method: 100,
    };
    return tabProgress[activeTab.value] || 0;
  });

  // --- Actions ---
  const goToNextTab = () => {
    if (!canProceedToNext.value) return;
    const currentIndex = TABS.indexOf(activeTab.value);
    const nextTab = TABS[currentIndex + 1];
    if (nextTab) {
      activeTab.value = nextTab;
    }
  };

  const goToPreviousTab = () => {
    const currentIndex = TABS.indexOf(activeTab.value);
    const prevTab = TABS[currentIndex - 1];
    if (prevTab) {
      activeTab.value = prevTab;
    }
  };

  const handleTabClick = (tabName: ICheckoutTab) => {
    const currentIndex = TABS.indexOf(activeTab.value);
    const targetIndex = TABS.indexOf(tabName);

    if (targetIndex <= currentIndex || isTabValid.value[activeTab.value]) {
      activeTab.value = tabName;
    }
  };

  const formatAddress = (addr: any) => {
    const parts = [addr.street, addr.ward, addr.city].filter(Boolean);
    return parts.join(", ");
  };

  const applyCoupon = () => {
    console.log("Apply coupon", couponCode.value);
    // TODO: Logic apply coupon
  };

  const handleCheckout = async () => {
    // Chỉ cho phép submit ở step cuối
    if (activeTab.value !== "payment_method") {
      goToNextTab();
      return;
    }

    if (!selectedAddress.value) {
      toast.error("Vui lòng chọn địa chỉ giao hàng");
      return;
    }

    if (selectedItems.value.length === 0) {
      toast.error("Vui lòng chọn sản phẩm để thanh toán");
      return;
    }

    // Setup orderItems payload
    const items: IOrderItem[] = selectedItems.value.map((item) => {
      const isVariant = item.purchasableType === "App\\Models\\ProductVariant";
      return {
        productId: isVariant ? (item.variant as any)?.products?.id || item.product?.id : item.purchasableId!,
        productVariantId: isVariant ? item.purchasableId : null,
        quantity: item.quantity || 1,
      };
    });

    // Setup order payload
    const orderPayload: Partial<IOrder> = {
      customerName: formData.personal_info.fullName,
      customerEmail: formData.personal_info.email,
      customerPhone: formData.personal_info.phone,
      shippingAddress: selectedAddress.value.address || formatAddress(selectedAddress.value),
      shippingCity: selectedAddress.value.city,
      shippingWard: selectedAddress.value.ward,
      shippingStreet: selectedAddress.value.street,
      shippingMethod: formData.address_info.shippingMethod || SHIPPING_METHODS.STANDARD,
      paymentMethod: formData.payment_method.selectedMethod,
      customerNote: formData.personal_info.customerNote,
      shippingFee: shippingFee.value,
      items,
    };

    isSubmitting.value = true;
    try {
      const res = await orderService.store(orderPayload);
      if (res.data) {
        toast.success("Đặt hàng thành công!");

        // Remove ordered items from cart (both backend and local store)
        const itemsToRemove = [...selectedItems.value];
        await Promise.all(
          itemsToRemove.map(async (item) => {
            if (item.id) {
              try {
                await cartItemService.delete(item.id);
              } catch {
                return 0;
              }
              cartStore.removeItem(item.id);
            }
          }),
        );

        navigateTo(`${TOORDERSUCCESS}?code=${res.data.code}`);
      }
    } catch (error: any) {
      toast.error(error?.response?.data?.message || "Đặt hàng thất bại. Vui lòng thử lại.");
    } finally {
      isSubmitting.value = false;
    }
  };

  return {
    activeTab,
    activePaymentMethod,
    formData,
    paymentMethods,
    isSubmitting,
    couponCode,
    selectedAddress,
    shippingFee,
    selectedItems,
    selectedCount,
    selectedTotalPrice,
    isTabValid,
    canProceedToNext,
    progressPercentage,
    goToNextTab,
    goToPreviousTab,
    handleTabClick,
    handleCheckout,
    applyCoupon,
    formatAddress,
  };
};
