import { ref, reactive, computed } from "vue";
import { z } from "zod";
import type { CheckoutFormData, ICheckoutTab, IPaymentMethod } from "~/interfaces/cart/checkout.interface";

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
  const activePaymentMethod = ref<string>("vnpay");

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
      shippingMethod: null,
    },
    payment_method: {
      selectedMethod: "vnpay",
    },
  });

  const paymentMethods: IPaymentMethod[] = [
    {
      id: "vnpay",
      name: "VNPay",
      icon: "/images/logo/vnpay.webp",
    },
    {
      id: "momo",
      name: "MoMo",
      icon: "/images/logo/momo.svg",
    },
    {
      id: "cod",
      name: "Khi nhận hàng",
      icon: "/images/cash-on-delivery.webp",
    },
  ];

  const TABS: ICheckoutTab[] = ["personal_info", "address_info", "payment_method"];

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

  return {
    activeTab,
    activePaymentMethod,
    formData,
    paymentMethods,
    isTabValid,
    canProceedToNext,
    progressPercentage,
    goToNextTab,
    goToPreviousTab,
    handleTabClick,
  };
};
