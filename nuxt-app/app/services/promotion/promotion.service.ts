import type { IApiOk } from "~/interfaces/base.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  applyDiscount: `${API_VERSION.version1}/promotion/apply_discount`,
} as const;

export interface IApplyDiscountPayload {
  code: string;
  subtotal: number;
  items: Array<{ id: number; qty: number; category_id?: number; brand_id?: number }>;
}

export interface IApplyDiscountResult {
  code: string;
  subtotal: number;
  total_discount: number;
  final_total: number;
  applied_promotions: Array<{
    id: number;
    code: string;
    name: string;
    type: string;
  }>;
  discounts: Array<{
    type: string;
    value: number;
    amount: number;
    action_id: number;
  }>;
}

export const promotionService = {
  applyDiscount: async (payload: IApplyDiscountPayload): Promise<IApiOk<IApplyDiscountResult>> => {
    const { privateFetch } = useApi();
    return await privateFetch<IApiOk<IApplyDiscountResult>>(endpoint.applyDiscount, {
      method: "POST",
      body: payload,
    });
  },
};
