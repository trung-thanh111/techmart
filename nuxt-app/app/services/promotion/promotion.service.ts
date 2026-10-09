import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
  collection: `${API_VERSION.version1}/promotions`,
  applyDiscount: `${API_VERSION.version1}/promotion/apply_discount`,
} as const;

export interface IPromotionRule {
  id: number;
  ruleGroup?: number;
  logicOperator?: string;
  ruleType: string;
  operator: string;
  value: any;
  parsedValue?: any;
  description?: string;
}

export interface IPromotionAction {
  id: number;
  executionOrder?: number;
  actionType: string;
  actionValue: number;
  maxDiscount?: number | null;
  minDiscount?: number | null;
  applyTo?: string;
  config?: any;
}

export interface IPromotion {
  id: number;
  code: string;
  name: string;
  slug?: string;
  description?: string;
  image?: string | null;
  type: string;
  priority?: number;
  promotionCatalogueId?: number | null;
  canCombine?: boolean;
  combinableWith?: any;
  usageLimit?: number | null;
  usagePerCustomer?: number | null;
  usedCount?: number;
  remainingUsage?: number | null;
  startDate?: string;
  endDate?: string;
  publish: number;
  isActive?: boolean;
  rules?: IPromotionRule[];
  actions?: IPromotionAction[];
  targets?: any[];
  createdAt?: string;
  updatedAt?: string;
}

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
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IApplyDiscountResult>>(endpoint.applyDiscount, {
      method: "POST",
      body: payload,
    });
  },

  pagination: async (params?: Record<string, any>): Promise<IApiOk<IPaginate<IPromotion>>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPaginate<IPromotion>>>(endpoint.collection, {
      method: "GET",
      params: {
        sort: "priority,desc",
        publish: 2,
        ...params,
      },
    });
  },

  all: async (params?: Record<string, any>): Promise<IApiOk<IPromotion[]>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion[]>>(endpoint.collection, {
      method: "GET",
      params: {
        type: "all",
        sort: "priority,desc",
        publish: 2,
        ...params,
      },
    });
  },

  show: async (id: number | string): Promise<IApiOk<IPromotion>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<IPromotion>>(`${endpoint.collection}/${id}`, {
      method: "GET",
    });
  },
};
