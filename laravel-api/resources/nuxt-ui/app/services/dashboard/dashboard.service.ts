import type { IApiOk } from "~/interfaces/base.interface";
import { useApi } from "~/composables/useApi";
import { API_VERSION } from "~/utils/apiConfig";

const endpoint = {
    statistics: `${API_VERSION.version1}/dashboard/statistics`,
    charts: `${API_VERSION.version1}/dashboard/charts`,
    recentOrders: `${API_VERSION.version1}/dashboard/recent-orders`,
    topProducts: `${API_VERSION.version1}/dashboard/top-products`,
} as const;

export interface IDashboardOverview {
    earnings: {
        total: number;
        this_month: number;
        last_month: number;
        growth_percentage: number;
    };
    orders: {
        total: number;
        this_month: number;
        last_month: number;
        growth_percentage: number;
        pending: number;
        completed: number;
        cancelled: number;
    };
    customers: {
        total: number;
        this_month: number;
        last_month: number;
        growth_percentage: number;
    };
    products: {
        total: number;
        low_stock_count: number;
    };
}

export interface IStatusDistribution {
    status: string;
    label: string;
    count: number;
}

export interface IDashboardStatistics {
    overview: IDashboardOverview;
    status_distribution: IStatusDistribution[];
}

export interface IDashboardChart {
    categories: string[];
    series: Array<{
        name: string;
        data: number[];
    }>;
}

export interface IRecentOrder {
    id: number;
    code: string;
    customer_name: string;
    customer_phone: string;
    total_amount: number;
    status: string;
    payment_status: string;
    payment_method: string;
    items_count: number;
    created_at: string;
}

export interface ITopProduct {
    id: number;
    name: string;
    image?: string;
    price: number;
    total_sold: number;
    total_revenue: number;
}

export const dashboardService = {
    statistics: async (): Promise<IApiOk<IDashboardStatistics>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IDashboardStatistics>>(endpoint.statistics);
    },

    charts: async (range = "monthly"): Promise<IApiOk<IDashboardChart>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IDashboardChart>>(endpoint.charts, {
            params: { range },
        });
    },

    recentOrders: async (limit = 5): Promise<IApiOk<IRecentOrder[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<IRecentOrder[]>>(endpoint.recentOrders, {
            params: { limit },
        });
    },

    topProducts: async (limit = 5): Promise<IApiOk<ITopProduct[]>> => {
        const { privateFetch } = useApi();
        return await privateFetch<IApiOk<ITopProduct[]>>(endpoint.topProducts, {
            params: { limit },
        });
    },
};
