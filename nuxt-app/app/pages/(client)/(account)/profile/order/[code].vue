<template>
  <div class="main-content w-full mx-auto">
    <section class="flex min-h-screen">
      <main class="flex-1 w-full">
        <div v-if="loading" class="flex justify-center items-center py-20">
          <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-sky-500"></div>
        </div>

        <!-- Error/Empty State -->
        <div v-else-if="!order" class="text-center py-20">
          <svg class="w-20 h-20 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <p class="text-gray-500 mb-4">Không tìm thấy đơn hàng</p>
          <NuxtLink :to="PROFILEORDER" class="text-sky-500 hover:underline">← Quay lại danh sách đơn hàng</NuxtLink>
        </div>

        <!-- Order Detail -->
        <div v-else>
          <!-- Header -->
          <div class="mb-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
              <!-- <NuxtLink :to="PROFILEORDER" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </NuxtLink> -->
              <h5 class="text-2xl font-semibold">Đơn hàng #{{ order.code }}</h5>
              <span :class="getStatusClass(order.status)" class="text-[9px] uppercase font-semibold px-2 py-0.5 rounded">
                {{ ORDER_STATUS_LABELS[order.status || ""] || order.status }}
              </span>
            </div>
            <div class="flex gap-2">
              <button
                v-if="canReorder"
                @click="handleReorder"
                :disabled="reordering"
                class="bg-sky-500 text-white px-3 py-1 text-xs rounded hover:bg-sky-600 transition flex items-center gap-1 disabled:opacity-50"
              >
                <span v-if="reordering" class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></span>
                <span>{{ $t("order.reorder") }}</span>
              </button>
              <button v-if="canCancel" @click="handleCancel" class="bg-red-100 text-red-600 px-3 py-1 text-xs rounded hover:bg-red-200 transition">
                {{ $t("order.cancel") }}
              </button>
            </div>
          </div>

          <div class="py-4">
            <!-- Info Cards -->
            <div class="mb-8">
              <h5 class="text-base font-semibold mb-4">Thông tin</h5>
              <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Customer Info -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-4">
                  <h6 class="text-sm font-semibold mb-4 flex items-center text-gray-800 uppercase">Thông tin khách hàng</h6>
                  <div class="space-y-1.5 text-sm">
                    <p class="font-medium text-gray-900 italic">{{ order.customer?.name || "-" }}</p>
                    <p class="text-gray-600 italic">(+84) {{ order.customer?.phone?.slice(1) || "-" }}</p>
                    <p class="text-gray-600 italic">{{ order.customer?.email || "-" }}</p>
                  </div>
                </div>

                <!-- Shipping Address -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-4">
                  <h6 class="text-sm font-semibold mb-4 flex items-center text-gray-800 uppercase">Địa chỉ giao hàng</h6>
                  <div class="space-y-1.5 text-sm">
                    <p class="font-medium text-gray-900">{{ order.customer?.name || "-" }}</p>
                    <p class="text-gray-600">{{ order.shippingStreet || "-" }}</p>
                    <p class="text-gray-600">{{ formattedWard }}, {{ formattedCity }}</p>
                  </div>
                </div>

                <!-- Payment Info -->
                <div class="bg-white border border-gray-200 shadow-sm rounded-lg p-4">
                  <h6 class="text-sm font-semibold mb-4 flex items-center text-gray-800 uppercase">Chi tiết thanh toán</h6>
                  <div class="space-y-1.5 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Phương thức:</span>
                      <span class="font-medium text-gray-900">{{ PAYMENT_METHOD_LABELS[order.paymentMethod || ""] || order.paymentMethod }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Trạng thái:</span>
                      <span :class="order.paymentStatus === 'paid' ? 'text-green-600' : 'text-orange-600'" class="font-medium">
                        {{ PAYMENT_STATUS_LABELS[order.paymentStatus || ""] || order.paymentStatus }}
                      </span>
                    </div>
                    <div v-if="order.transactionId" class="flex justify-between">
                      <span class="text-gray-600">Mã GD:</span>
                      <span class="font-medium">#{{ order.transactionId }}</span>
                    </div>
                    <div class="flex justify-between font-semibold text-red-600 pt-2 border-t border-gray-200 mt-2">
                      <span>Tổng tiền:</span>
                      <span>{{ formatPrice(order.totalAmount || 0) }}</span>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <div class="mb-8">
              <h5 class="text-base font-semibold mb-4">Trạng thái đơn hàng</h5>
              <div class="relative pl-8">
                <div v-for="(step, idx) in orderSteps" :key="step.status" class="relative pb-8 last:pb-0">
                  <!-- Vertical Line -->
                  <div
                    v-if="idx < orderSteps.length - 1"
                    class="absolute left-[-13px] top-8 w-0.5 h-[calc(100%-8px)]"
                    :class="orderSteps[idx + 1].completed ? (orderSteps[idx + 1].variant === 'danger' ? 'bg-red-400' : 'bg-green-500') : 'border-l-2 border-dashed border-gray-300'"
                  ></div>
                  <!-- Circle -->
                  <div
                    :class="[
                      'absolute left-[-29px] w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shadow-md border-2',
                      step.variant === 'danger'
                        ? 'bg-red-500 border-red-500 text-white'
                        : step.variant === 'warning'
                        ? 'bg-orange-500 border-orange-500 text-white'
                        : step.completed || step.current
                        ? 'bg-green-500 border-green-500 text-white'
                        : 'bg-white border-gray-300 text-gray-400',
                    ]"
                  >
                    <!-- Danger/Cancelled icon -->
                    <svg v-if="step.variant === 'danger'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                    </svg>
                    <!-- Warning/Refund icon -->
                    <svg v-else-if="step.variant === 'warning'" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd" />
                    </svg>
                    <!-- Checkmark icon -->
                    <svg v-else-if="step.completed || step.current" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <!-- Number -->
                    <span v-else>{{ idx + 1 }}</span>
                  </div>
                  <!-- Content -->
                  <div class="ml-4">
                    <p
                      :class="[
                        'text-sm font-semibold',
                        step.variant === 'danger'
                          ? 'text-red-600'
                          : step.variant === 'warning'
                          ? 'text-orange-600'
                          : step.completed || step.current
                          ? 'text-green-600'
                          : 'text-gray-400',
                      ]"
                    >
                      {{ step.label }}
                    </p>
                    <p class="text-xs text-gray-500 mt-0.5">{{ step.description }}</p>
                    <p v-if="step.timestamp" class="text-xs text-gray-400 mt-1">{{ formatFullDate(step.timestamp) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Products -->
            <div class="mb-8">
              <h5 class="text-base font-semibold mb-4">Sản phẩm ({{ order.items?.length || 0 }})</h5>
              <div class="space-y-3">
                <div v-for="item in order.items" :key="item.id" class="flex gap-4 p-3 border border-gray-100 rounded-lg hover:border-sky-400 transition bg-white">
                  <!-- Image -->
                  <div class="w-20 h-20 flex-shrink-0 bg-gray-100 rounded overflow-hidden">
                    <NuxtImg :src="getProductImage(item.productImage) ?? IMAGEDEFAULT" :alt="item.productName" class="w-full h-full object-contain" loading="lazy" @error="handleImageError" />
                  </div>
                  <!-- Info -->
                  <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-cyan-600 line-clamp-2 mb-1">{{ item.productName }}</p>
                    <p v-if="item.productSku" class="text-xs text-gray-400 mb-1">SKU: {{ item.productSku }}</p>
                    <p v-if="formatItemAttributes(item.productAttributes)" class="text-xs text-gray-500 mb-1">
                      {{ formatItemAttributes(item.productAttributes) }}
                    </p>
                    <div class="flex items-center gap-3 mt-2">
                      <span class="text-sm font-semibold text-red-500">{{ formatPrice(item.unitPrice ?? 0) }}</span>
                      <span v-if="item.originalPrice && item.originalPrice > (item.unitPrice ?? 0)" class="text-xs text-gray-400 line-through">
                        {{ formatPrice(item.originalPrice) }}
                      </span>
                      <span class="text-xs text-gray-500">x{{ item.quantity ?? 1 }}</span>
                    </div>
                  </div>
                  <!-- Subtotal -->
                  <div class="text-right flex-shrink-0">
                    <p class="text-sm font-semibold text-gray-800">{{ formatPrice(item.subtotal ?? (item.unitPrice ?? 0) * (item.quantity ?? 1)) }}</p>
                  </div>
                </div>
              </div>
            </div>

            <!-- Order Summary -->
            <div class="mb-8">
              <div class="flex justify-end">
                <div class="w-full max-w-sm bg-gray-100 rounded-lg p-4">
                  <h6 class="text-sm font-semibold mb-4 text-gray-800">Tổng kết đơn hàng</h6>
                  <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                      <span class="text-gray-600">Tạm tính:</span>
                      <span class="font-medium">{{ formatPrice(order.subtotal || 0) }}</span>
                    </div>
                    <div v-if="order.discountAmount && order.discountAmount > 0" class="flex justify-between">
                      <span class="text-gray-600">Giảm giá:</span>
                      <span class="font-medium text-green-600">-{{ formatPrice(order.discountAmount) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Phí vận chuyển:</span>
                      <span class="font-medium">{{ formatPrice(order.shippingFee || 0) }}</span>
                    </div>
                    <div class="flex justify-between">
                      <span class="text-gray-600">Thuế (10%):</span>
                      <span class="font-medium">{{ formatPrice(order.taxAmount || 0) }}</span>
                    </div>
                    <div class="pt-3 mt-3 border-t border-gray-200">
                      <div class="flex justify-between text-base font-bold">
                        <span class="text-gray-800">Tổng thanh toán:</span>
                        <span class="text-red-500">{{ formatPrice(order.totalAmount || 0) }}</span>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div v-if="order" class="mt-6">
              <div class="flex items-center justify-between mb-3">
                <h5 class="text-base font-semibold">{{ $t("order.noteLabel") }}</h5>
                <button
                  v-if="order.status === 'pending' && customerNote !== (order.customerNote || '')"
                  @click="handleSaveNote"
                  :disabled="noteSaving"
                  class="text-xs bg-sky-500 text-white px-3 py-1 rounded hover:bg-sky-600 transition disabled:opacity-50 flex items-center gap-1"
                >
                  <span v-if="noteSaving" class="animate-spin rounded-full h-3 w-3 border-b-2 border-white"></span>
                  <span>{{ noteSaving ? $t("order.noteSaving") : ($t("common.save") || "Lưu") }}</span>
                </button>
              </div>
              <div class="relative">
                <textarea
                  v-model="customerNote"
                  rows="3"
                  class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-sky-500 resize-none disabled:bg-gray-50 disabled:text-gray-500"
                  :placeholder="$t('order.notePlaceholder')"
                  :disabled="order.status !== 'pending' || noteSaving"
                  @blur="handleSaveNote"
                ></textarea>
                <p v-if="noteSaving" class="absolute bottom-2 right-2 text-xs text-gray-400">{{ $t("order.noteSaving") }}</p>
                <p v-else-if="noteSaved" class="absolute bottom-2 right-2 text-xs text-green-500">{{ $t("order.noteSaved") }}</p>
              </div>
            </div>
          </div>
        </div>
      </main>
    </section>
  </div>
</template>

<script setup lang="ts">
import { ref, computed, onMounted } from "vue";
import { useRoute } from "vue-router";
import { useI18n } from "vue-i18n";
import type { IOrder } from "~/interfaces/order/order.interface";
import { orderService } from "~/services/order/order.service";
import { useCart } from "~/composables/cart/useCart";
import { ORDER_STATUS_LABELS, PAYMENT_METHOD_LABELS, PAYMENT_STATUS_LABELS, STATUS_CONFIG, STATUS_ORDER } from "~/utils/order.constants";
import { formatPrice } from "~/utils/helpers";
import { getCityName, getWardName } from "~/utils/address.helpers";
import { useAppToast } from "~/composables/useAppToast";
import { PROFILEORDER } from "~/utils/constants";
import { getStatusClass, getProductImage, formatItemAttributes, formatFullDate, handleImageError } from "~/utils/order.helpers";
import { IMAGEDEFAULT } from "~/utils/constants";

definePageMeta({
  layout: "client",
  middleware: ["auth-required"],
});

const route = useRoute();
const { t } = useI18n();
const toast = useAppToast();
const { reorder } = useCart();

const order = ref<IOrder | null>(null);
const loading = ref(true);
const customerNote = ref("");
const noteSaving = ref(false);
const noteSaved = ref(false);
const reordering = ref(false);

const fetchOrder = async () => {
  loading.value = true;
  try {
    const code = route.params.code as string;
    const res = await orderService.show(code, {
      field: "code",
      relations: "order_items,order_status_histories",
    });
    order.value = res.data;
    customerNote.value = res.data?.customerNote || "";
  } catch (e) {
    order.value = null;
  } finally {
    loading.value = false;
  }
};

onMounted(fetchOrder);

// Computed
const canCancel = computed(() => order.value?.status === "pending");
const canReorder = computed(() => !!order.value && !!order.value.items && order.value.items.length > 0);

const formattedCity = computed(() => getCityName(order.value?.shippingCity || ""));
const formattedWard = computed(() => getWardName(order.value?.shippingCity || "", order.value?.shippingWard || ""));

// Methods
const handleReorder = async () => {
  if (!order.value) return;
  reordering.value = true;
  try {
    await reorder(order.value);
  } finally {
    reordering.value = false;
  }
};

const handleCancel = async () => {
  if (!order.value?.id || !confirm(t("order.cancelConfirm"))) return;
  try {
    await orderService.cancel(order.value.id, "Khách hàng yêu cầu hủy");
    toast.success(t("order.cancelSuccess"));
    fetchOrder();
  } catch (e: any) {
    toast.error(e?.data?.message || e?.message || t("order.cancelError"));
  }
};

const handleSaveNote = async () => {
  if (!order.value?.id || customerNote.value === (order.value.customerNote || "")) return;
  if (order.value.status !== "pending") {
    toast.error(t("order.cannotUpdateNote"));
    return;
  }
  noteSaving.value = true;
  noteSaved.value = false;
  try {
    const res = await orderService.updateNote(order.value.id, customerNote.value);
    if (res?.data) {
      order.value.customerNote = res.data.customerNote;
    }
    noteSaved.value = true;
    toast.success(t("order.noteSaveSuccess"));
    setTimeout(() => (noteSaved.value = false), 2000);
  } catch (e: any) {
    toast.error(e?.data?.message || e?.message || t("order.noteSaveError"));
  } finally {
    noteSaving.value = false;
  }
};

// Dynamic Order Timeline
interface IOrderTimelineStep {
  status: string;
  label: string;
  description: string;
  completed: boolean;
  current: boolean;
  timestamp?: string;
  variant?: "standard" | "danger" | "warning";
}

const STANDARD_FLOW = ["pending", "confirmed", "processing", "shipped", "delivered"] as const;

const orderSteps = computed<IOrderTimelineStep[]>(() => {
  if (!order.value) return [];
  const currentStatus = order.value.status || "pending";

  // Build map of status histories
  const historyMap: Record<string, { timestamp?: string; note?: string }> = {};
  if (Array.isArray(order.value.statusHistories)) {
    for (const h of order.value.statusHistories) {
      if (h.newStatus) {
        historyMap[h.newStatus] = {
          timestamp: h.createdAt,
          note: h.note,
        };
      }
    }
  }

  const timestamps: Record<string, string | undefined> = {
    pending: order.value.createdAt,
    confirmed: order.value.confirmedAt || historyMap.confirmed?.timestamp,
    processing: historyMap.processing?.timestamp || (["processing", "shipped", "delivered"].includes(currentStatus) ? order.value.confirmedAt : undefined),
    shipped: order.value.shippedAt || historyMap.shipped?.timestamp,
    delivered: order.value.deliveredAt || historyMap.delivered?.timestamp,
    cancelled: order.value.cancelledAt || historyMap.cancelled?.timestamp || order.value.updatedAt,
    failed: historyMap.failed?.timestamp || order.value.updatedAt,
    refunded: (order.value as any).refundedAt || historyMap.refunded?.timestamp || order.value.updatedAt,
  };

  // Case 1: Cancelled
  if (currentStatus === "cancelled") {
    const steps: IOrderTimelineStep[] = [
      {
        status: "pending",
        label: STATUS_CONFIG.pending?.label || "Đặt hàng",
        description: STATUS_CONFIG.pending?.description || "Đơn hàng đã được tạo và đang chờ xác nhận",
        completed: true,
        current: false,
        timestamp: timestamps.pending,
        variant: "standard",
      },
    ];

    if (timestamps.confirmed) {
      steps.push({
        status: "confirmed",
        label: STATUS_CONFIG.confirmed?.label || "Xác nhận",
        description: STATUS_CONFIG.confirmed?.description || "Đơn hàng đã được xác nhận bởi người bán hoặc hệ thống",
        completed: true,
        current: false,
        timestamp: timestamps.confirmed,
        variant: "standard",
      });
    }

    if (historyMap.processing?.timestamp) {
      steps.push({
        status: "processing",
        label: STATUS_CONFIG.processing?.label || "Đang xử lý",
        description: STATUS_CONFIG.processing?.description || "Đơn hàng đang được đóng gói và chuẩn bị giao đến bạn",
        completed: true,
        current: false,
        timestamp: timestamps.processing,
        variant: "standard",
      });
    }

    const cancelConfig = STATUS_CONFIG.cancelled || { label: "Đã hủy", description: "Đơn hàng đã bị hủy!" };
    const cancelNote = historyMap.cancelled?.note || order.value.adminNote;
    steps.push({
      status: "cancelled",
      label: cancelConfig.label,
      description: cancelNote ? `Đơn hàng đã bị hủy (${cancelNote})` : cancelConfig.description,
      completed: true,
      current: true,
      timestamp: timestamps.cancelled,
      variant: "danger",
    });

    return steps;
  }

  // Case 2: Failed
  if (currentStatus === "failed") {
    const steps: IOrderTimelineStep[] = [
      {
        status: "pending",
        label: STATUS_CONFIG.pending?.label || "Đặt hàng",
        description: STATUS_CONFIG.pending?.description || "Đơn hàng đã được tạo và đang chờ xác nhận",
        completed: true,
        current: false,
        timestamp: timestamps.pending,
        variant: "standard",
      },
    ];

    if (timestamps.confirmed) {
      steps.push({
        status: "confirmed",
        label: STATUS_CONFIG.confirmed?.label || "Xác nhận",
        description: STATUS_CONFIG.confirmed?.description || "Đơn hàng đã được xác nhận",
        completed: true,
        current: false,
        timestamp: timestamps.confirmed,
        variant: "standard",
      });
    }

    if (timestamps.shipped) {
      steps.push({
        status: "shipped",
        label: STATUS_CONFIG.shipped?.label || "Đang giao",
        description: STATUS_CONFIG.shipped?.description || "Đơn hàng đã được giao cho đơn vị vận chuyển",
        completed: true,
        current: false,
        timestamp: timestamps.shipped,
        variant: "standard",
      });
    }

    const failConfig = STATUS_CONFIG.failed || { label: "Thất bại", description: "Đơn hàng giao không thành công!" };
    const failNote = historyMap.failed?.note || order.value.adminNote;
    steps.push({
      status: "failed",
      label: failConfig.label,
      description: failNote ? `Đơn hàng giao không thành công (${failNote})` : failConfig.description,
      completed: true,
      current: true,
      timestamp: timestamps.failed,
      variant: "danger",
    });

    return steps;
  }

  // Case 3: Refunded
  if (currentStatus === "refunded") {
    const steps: IOrderTimelineStep[] = [
      {
        status: "pending",
        label: STATUS_CONFIG.pending?.label || "Đặt hàng",
        description: STATUS_CONFIG.pending?.description || "Đơn hàng đã được tạo và đang chờ xác nhận",
        completed: true,
        current: false,
        timestamp: timestamps.pending,
        variant: "standard",
      },
    ];

    if (timestamps.confirmed) {
      steps.push({
        status: "confirmed",
        label: STATUS_CONFIG.confirmed?.label || "Xác nhận",
        description: STATUS_CONFIG.confirmed?.description || "Đơn hàng đã được xác nhận",
        completed: true,
        current: false,
        timestamp: timestamps.confirmed,
        variant: "standard",
      });
    }

    if (timestamps.delivered) {
      steps.push({
        status: "delivered",
        label: STATUS_CONFIG.delivered?.label || "Hoàn thành",
        description: STATUS_CONFIG.delivered?.description || "Đơn hàng đã được giao thành công!",
        completed: true,
        current: false,
        timestamp: timestamps.delivered,
        variant: "standard",
      });
    }

    const refundConfig = STATUS_CONFIG.refunded || { label: "Hoàn tiền", description: "Đơn hàng đã được hoàn tiền!" };
    const refundNote = historyMap.refunded?.note;
    steps.push({
      status: "refunded",
      label: refundConfig.label,
      description: refundNote ? `Đơn hàng đã được hoàn tiền (${refundNote})` : refundConfig.description,
      completed: true,
      current: true,
      timestamp: timestamps.refunded,
      variant: "warning",
    });

    return steps;
  }

  // Case 4: Standard flow (pending -> confirmed -> processing -> shipped -> delivered)
  const currentIdx = STANDARD_FLOW.indexOf(currentStatus as (typeof STANDARD_FLOW)[number]);

  return STANDARD_FLOW.map((status, idx) => {
    const config = STATUS_CONFIG[status] || { label: status, description: "" };
    const isCompleted = currentIdx !== -1 ? idx <= currentIdx : idx === 0;
    const isCurrent = currentIdx !== -1 ? idx === currentIdx : idx === 0;

    return {
      status,
      label: config.label,
      description: config.description,
      completed: isCompleted,
      current: isCurrent,
      timestamp: timestamps[status],
      variant: "standard",
    };
  });
});
</script>
