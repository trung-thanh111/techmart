import type { IApiOk } from "~/interfaces/base.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

export interface CreatePaymentResponse {
  pay_url: string;
  deeplink?: string | null;
  qr_code_url?: string | null;
  request_id: string;
}

export interface VNPayReturnResponse {
  success: boolean;
  response_code: string;
  order_id: string | null;
  order_code: string | null;
  amount: number | null;
  message: string;
}

export const paymentService = {
  create: async (orderId: number, gateway: string): Promise<IApiOk<CreatePaymentResponse>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<CreatePaymentResponse>>(`${API_VERSION.version1}/payment/create`, {
      method: "POST",
      body: {
        order_id: orderId,
        gateway,
      },
    });
  },

  show: async (orderId: number): Promise<IApiOk<any>> => {
    const { publicFetch } = useApi();
    return await publicFetch<IApiOk<any>>(`${API_VERSION.version1}/payment/${orderId}`, {
      method: "GET",
    });
  },

  vnpayReturn: async (params: Record<string, any>): Promise<VNPayReturnResponse> => {
    const { publicFetch } = useApi();
    return await publicFetch<VNPayReturnResponse>(`${API_VERSION.version1}/payment/vnpay-return`, {
      method: "GET",
      params,
    });
  },
};
