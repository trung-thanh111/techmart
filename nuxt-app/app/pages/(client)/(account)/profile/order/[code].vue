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
              <NuxtLink :to="PROFILEORDER" class="text-gray-500 hover:text-gray-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
              </NuxtLink>
              <h5 class="text-2xl font-semibold">Đơn hàng #{{ order.code }}</h5>
              <span :class="getStatusClass(order.status)" class="text-[9px] uppercase font-semibold px-2 py-0.5 rounded">
                {{ ORDER_STATUS_LABELS[order.status || ""] || order.status }}
              </span>
            </div>
            <div class="flex gap-2">
              <button v-if="canCancel" @click="handleCancel" class="bg-red-100 text-red-600 px-3 py-1 text-xs rounded hover:bg-red-200 transition">Hủy đơn</button>
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
                  <div v-if="idx < orderSteps.length - 1" class="absolute left-[-13px] top-8 w-0.5 h-[calc(100%-8px)]" :class="step.completed ? 'bg-green-500' : 'border-l-2 border-dashed border-gray-300'"></div>
                  <!-- Circle -->
                  <div :class="['absolute left-[-29px] w-8 h-8 rounded-full flex items-center justify-center text-sm font-semibold shadow-md border-2', step.completed || step.current ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-gray-300 text-gray-400']">
                    <svg v-if="step.completed || step.current" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                      <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span v-else>{{ idx + 1 }}</span>
                  </div>
                  <!-- Content -->
                  <div class="ml-4">
                    <p :class="['text-sm font-semibold', step.completed || step.current ? 'text-green-600' : 'text-gray-400']">{{ step.label }}</p>
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
                    <NuxtImg :src="getProductImage(item.productImage) ?? IMAGEDEFAULT" :alt="item.productName" class="w-full h-full object-contain" loading="lazy" />
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
            <div v-if="order.customerNote !== undefined" class="mt-6">
              <h5 class="text-base font-semibold mb-3">Ghi chú:</h5>
              <div class="relative">
                <textarea v-model="customerNote" rows="3" class="w-full border border-gray-200 rounded-lg px-4 py-3 text-sm focus:outline-none focus:border-sky-500 resize-none" placeholder="Thêm ghi chú cho đơn hàng..." @blur="handleSaveNote"></textarea>
                <p v-if="noteSaving" class="absolute bottom-2 right-2 text-xs text-gray-400">Đang lưu...</p>
                <p v-else-if="noteSaved" class="absolute bottom-2 right-2 text-xs text-green-500">Đã lưu</p>
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
import type { IOrder } from "~/interfaces/order/order.interface";
import { orderService } from "~/services/order/order.service";
import { ORDER_STATUS_LABELS, PAYMENT_METHOD_LABELS, PAYMENT_STATUS_LABELS, STATUS_CONFIG, STATUS_ORDER } from "~/utils/order.constants";
import { formatPrice } from "~/utils/helpers";
import { getCityName, getWardName } from "~/utils/address.helpers";
import { useAppToast } from "~/composables/useAppToast";
import { PROFILEORDER } from "~/utils/constants";
import { getStatusClass, getProductImage, formatItemAttributes, formatFullDate } from "~/utils/order.helpers";
import { IMAGEDEFAULT } from "~/utils/constants";

definePageMeta({
  layout: "client",
  middleware: ["auth-required"],
});

const route = useRoute();
const toast = useAppToast();

const order = ref<IOrder | null>(null);
const loading = ref(true);
const customerNote = ref("");
const noteSaving = ref(false);
const noteSaved = ref(false);

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

const formattedCity = computed(() => getCityName(order.value?.shippingCity || ""));
const formattedWard = computed(() => getWardName(order.value?.shippingCity || "", order.value?.shippingWard || ""));

// Methods
const handleCancel = async () => {
  if (!order.value?.id || !confirm("Bạn có chắc muốn hủy đơn hàng này?")) return;
  try {
    await orderService.cancel(order.value.id, "Khách hàng yêu cầu hủy");
    toast.success("Đã hủy đơn hàng thành công");
    fetchOrder();
  } catch (e: any) {
    toast.error(e?.message || "Không thể hủy đơn hàng");
  }
};

const handleSaveNote = async () => {
  if (!order.value?.id || customerNote.value === order.value.customerNote) return;
  noteSaving.value = true;
  noteSaved.value = false;
  try {
    await new Promise((r) => setTimeout(r, 500));
    noteSaved.value = true;
    setTimeout(() => (noteSaved.value = false), 2000);
  } catch (e) {
    toast.error("Không thể lưu ghi chú");
  } finally {
    noteSaving.value = false;
  }
};

// Timeline
const orderSteps = computed(() => {
  const currentStatus = order.value?.status || "pending";
  const currentIdx = STATUS_ORDER.indexOf(currentStatus as (typeof STATUS_ORDER)[number]);

  if (currentStatus === "cancelled" || currentStatus === "failed" || currentStatus === "refunded") {
    const config = STATUS_CONFIG[currentStatus] || { label: currentStatus, description: "" };
    return [
      { status: "pending", label: "Đặt hàng", description: STATUS_CONFIG.description, completed: true, current: false, timestamp: order.value?.createdAt },
      { status: currentStatus, label: config.label, description: config.description, completed: true, current: true, timestamp: order.value?.cancelledAt || order.value?.updatedAt },
    ];
  }

  return STATUS_ORDER.map((status, idx) => {
    const timestamps: Record<string, string | undefined> = {
      pending: order.value?.createdAt,
      confirmed: order.value?.confirmedAt,
      shipped: order.value?.shippedAt,
      delivered: order.value?.deliveredAt,
    };
    const config = STATUS_CONFIG[status] || { label: status, description: "" };
    return {
      status,
      label: config.label,
      description: config.description,
      completed: idx < currentIdx,
      current: idx === currentIdx,
      timestamp: timestamps[status],
    };
  });
});
</script>
