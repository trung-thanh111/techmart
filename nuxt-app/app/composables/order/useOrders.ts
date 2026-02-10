import { ref, computed, watch, onMounted } from "vue";
import type { IOrder, IOrderGroup, OrderTab } from "~/interfaces/order/order.interface";
import { orderService } from "~/services/order/order.service";

const TAB_STATUS_MAP: Record<OrderTab, string | undefined> = {
  "all-orders": undefined,
  pending: "pending",
  shipping: "shipped",
  completed: "delivered",
  canceled: "cancelled",
  returned: "refunded",
};

export const useOrders = () => {
  const orders = ref<IOrder[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);
  const activeTab = ref<OrderTab>("all-orders");

  const fetchOrders = async (status?: string) => {
    loading.value = true;
    error.value = null;
    try {
      const params: Record<string, any> = {
        perpage: 50,
        relations: "order_items",
        orderby: "created_at",
        order: "desc",
      };
      if (status) {
        params.status = status;
      }
      const res = await orderService.pagination(params);
      orders.value = res.data?.data || [];
    } catch (e: any) {
      error.value = e?.message || "Không thể tải đơn hàng";
      orders.value = [];
    } finally {
      loading.value = false;
    }
  };

  watch(
    activeTab,
    (newTab) => {
      const status = TAB_STATUS_MAP[newTab];
      fetchOrders(status);
    },
    { immediate: true },
  );

  const groupedOrders = computed<IOrderGroup[]>(() => {
    const groups: Record<string, IOrder[]> = {};

    orders.value.forEach((order) => {
      const dateKey: string = order.createdAt?.split("T")[0] ?? "unknown";
      if (!groups[dateKey]) {
        groups[dateKey] = [];
      }
      groups[dateKey]!.push(order);
    });

    const sortedKeys = Object.keys(groups).sort((a, b) => b.localeCompare(a));

    return sortedKeys.map((dateKey) => ({
      dateKey,
      dateLabel: formatDateLabel(dateKey),
      orders: groups[dateKey] || [],
    }));
  });

  const formatDateLabel = (dateKey: string): string => {
    if (dateKey === "unknown") return "Không xác định";
    const date = new Date(dateKey);
    const today = new Date();
    const yesterday = new Date(today);
    yesterday.setDate(yesterday.getDate() - 1);

    if (dateKey === today.toISOString().split("T")[0]) return "Hôm nay";
    if (dateKey === yesterday.toISOString().split("T")[0]) return "Hôm qua";

    return date.toLocaleDateString("vi-VN", {
      weekday: "long",
      day: "2-digit",
      month: "2-digit",
      year: "numeric",
    });
  };

  return {
    orders,
    loading,
    error,
    activeTab,
    groupedOrders,
    fetchOrders,
  };
};

export const useOrderDetail = () => {
  const order = ref<IOrder | null>(null);
  const loading = ref(false);
  const route = useRoute();

  const fetchOrder = async () => {
    const code = route.query.code as string;
    if (!code) return;

    loading.value = true;
    try {
      const res = await orderService.show(code, { relations: "order_items" });
      order.value = res.data ?? null;
    } catch {
      order.value = null;
    } finally {
      loading.value = false;
    }
  };

  onMounted(fetchOrder);

  return { order, loading };
};

export type { OrderTab };
