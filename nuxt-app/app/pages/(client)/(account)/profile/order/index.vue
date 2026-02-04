<template>
  <div class="main-content w-full mx-auto">
    <section class="flex min-h-screen">
      <main class="flex-1 w-full">
        <h1 class="text-2xl font-semibold text-gray-900 mb-1">Đơn hàng của bạn</h1>
        <p class="text-sm text-gray-500 mb-5">Quản lý, theo dõi đơn hàng của bạn tại đây</p>
        <section id="tabs-order" class="my-2">
          <div class="container pb-1 tabs">
            <div class="relative max-w-full overflow-x-auto scrollbar-hide hiddenScroll tabs-order-scroll">
              <div class="flex w-full items-center space-x-[2px]">
                <div v-for="tab in tabs" :key="tab.key" :class="['flex-1 text-sm flex-shrink-0 p-2 md:p-3 text-center whitespace-normal duration-75 cursor-pointer tabs_item', activeTab === tab.key ? 'bg-gradient-to-r from-sky-500 to-sky-600 text-white' : 'text-gray-700 bg-white hover:bg-gradient-to-r hover:from-sky-500 hover:to-sky-600 hover:text-white']" @click="activeTab = tab.key">
                  {{ tab.label }}
                </div>
              </div>
            </div>
          </div>

          <!-- Orders List -->
          <div class="container mt-4 overflow-y-auto scrollbar-hide max-h-[620px]">
            <div v-if="loading" class="flex justify-center items-center py-12">
              <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-sky-500"></div>
            </div>

            <!-- Empty State -->
            <div v-else-if="groupedOrders.length === 0" class="text-center py-12">
              <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
              </svg>
              <p class="text-gray-500">Chưa có đơn hàng nào</p>
            </div>

            <!-- Orders Grouped by Date -->
            <div v-else class="text-gray-600 text-base">
              <OrderDateGroup v-for="group in groupedOrders" :key="group.dateKey" :date-label="group.dateLabel" :orders="group.orders" @cancel="handleCancel" @review="handleReview" @reorder="handleReorder" />
            </div>
          </div>
        </section>
      </main>
    </section>
  </div>
</template>

<script setup lang="ts">
import { useOrders, type OrderTab } from "~/composables/order/useOrders";
import { orderService } from "~/services/order/order.service";
import { useAppToast } from "~/composables/useAppToast";
import OrderDateGroup from "~/components/features/order/OrderDateGroup.vue";

definePageMeta({
  layout: "client",
  middleware: ["auth-required"],
});

const toast = useAppToast();
const { loading, activeTab, groupedOrders, fetchOrders } = useOrders();

const tabs: { key: OrderTab; label: string }[] = [
  { key: "all-orders", label: "Tất cả đơn hàng" },
  { key: "pending", label: "Chờ xác nhận" },
  { key: "shipping", label: "Đang vận chuyển" },
  { key: "completed", label: "Hoàn thành" },
  { key: "canceled", label: "Đã hủy" },
  { key: "returned", label: "Trả/Hoàn tiền" },
];

const handleCancel = async (order: any) => {
  if (!order?.id || !confirm("Bạn có chắc muốn hủy đơn hàng này?")) return;
  try {
    await orderService.cancel(order.id, "Khách hàng yêu cầu hủy");
    toast.success("Đã hủy đơn hàng thành công");
    fetchOrders();
  } catch (e: any) {
    toast.error(e?.message || "Không thể hủy đơn hàng");
  }
};

const handleReview = (order: any) => {
  // TODO: Navigate to review page
  navigateTo(`${PROFILEORDER}/${order.code}?review=true`);
};

const handleReorder = (order: any) => {
  // TODO: Add items to cart and navigate to checkout
  toast.info("Tính năng đang phát triển");
};
</script>
