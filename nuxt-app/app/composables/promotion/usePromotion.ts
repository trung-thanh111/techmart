import { ref } from "vue";
import { useRouter } from "vue-router";
import { promotionService, type IPromotion } from "~/services/promotion/promotion.service";
import { useAppToast } from "~/composables/useAppToast";
import { formatPrice } from "~/utils/helpers";
import { TOCHECKOUT, TOSHOP } from "~/utils/constants";
import { useCartStore } from "~/stores/cart";

export const usePromotion = () => {
  const router = useRouter();
  const cartStore = useCartStore();
  const toast = useAppToast();
  const promotions = ref<IPromotion[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  const fetchPromotions = async (params: Record<string, any> = {}) => {
    loading.value = true;
    error.value = null;
    try {
      const res = await promotionService.pagination(params);
      if (res.data?.data) {
        promotions.value = res.data.data;
      } else {
        promotions.value = [];
      }
    } catch (err: any) {
      error.value = err?.data?.message || err?.message || "Lỗi tải danh sách voucher";
      promotions.value = [];
    } finally {
      loading.value = false;
    }
  };

  const copyCode = async (code: string) => {
    try {
      if (typeof navigator !== "undefined" && navigator?.clipboard?.writeText) {
        await navigator.clipboard.writeText(code);
      } else if (typeof document !== "undefined") {
        const input = document.createElement("input");
        input.value = code;
        document.body.appendChild(input);
        input.select();
        document.execCommand("copy");
        document.body.removeChild(input);
      }
      toast.success(`Đã sao chép mã ${code}`);
    } catch {
      toast.error("Không thể sao chép mã");
    }
  };

  const useVoucher = (code: string) => {
    if (cartStore.totalItems > 0) {
      router.push({ path: TOCHECKOUT, query: { coupon: code } });
    } else {
      copyCode(code);
      toast.info("Giỏ hàng đang trống. Vui lòng chọn sản phẩm để sử dụng mã!");
      router.push(TOSHOP);
    }
  };

  const getDiscountBadge = (promo: IPromotion): string => {
    const action = promo.actions?.[0];
    if (!action) return "Khuyến mãi";
    if (action.actionType === "percent") {
      return `Giảm ${action.actionValue}%`;
    }
    return `Giảm ${formatPrice(action.actionValue)}`;
  };

  const getConditionText = (promo: IPromotion): string => {
    const minOrderRule = promo.rules?.find((r) => r.ruleType === "min_order_total");
    if (minOrderRule) {
      const minAmount = minOrderRule.parsedValue?.amount || Number(minOrderRule.value) || 0;
      if (minAmount > 0) {
        return `Đơn tối thiểu ${formatPrice(minAmount)}`;
      }
    }
    return "Không giới hạn đơn tối thiểu";
  };

  const getMaxDiscountText = (promo: IPromotion): string | null => {
    const action = promo.actions?.[0];
    if (action && action.actionType === "percent" && action.maxDiscount) {
      return `Tối đa ${formatPrice(action.maxDiscount)}`;
    }
    return null;
  };

  const getExpiryText = (promo: IPromotion): string => {
    if (!promo.endDate) return "Không thời hạn";
    try {
      const d = new Date(promo.endDate);
      const day = String(d.getDate()).padStart(2, "0");
      const month = String(d.getMonth() + 1).padStart(2, "0");
      const year = d.getFullYear();
      return `HSD: ${day}/${month}/${year}`;
    } catch {
      return `HSD: ${promo.endDate}`;
    }
  };

  return {
    promotions,
    loading,
    error,
    fetchPromotions,
    copyCode,
    useVoucher,
    getDiscountBadge,
    getConditionText,
    getMaxDiscountText,
    getExpiryText,
  };
};
