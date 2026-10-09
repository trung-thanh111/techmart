<?php

declare(strict_types=1);

namespace App\Services\Interfaces\Dashboard;

interface DashboardServiceInterface
{
    /**
     * Lấy thống kê tổng quan KPI (doanh thu, đơn hàng, khách hàng, sản phẩm).
     *
     * @param string|null $period
     * @return array
     */
    public function getOverviewStatistics(?string $period = null): array;

    /**
     * Lấy dữ liệu biểu đồ doanh thu và đơn hàng theo thời gian.
     *
     * @param string $range
     * @return array
     */
    public function getRevenueChart(string $range = 'monthly'): array;

    /**
     * Lấy danh sách các đơn hàng gần đây.
     *
     * @param int $limit
     * @return array
     */
    public function getRecentOrders(int $limit = 5): array;

    /**
     * Lấy top sản phẩm bán chạy nhất.
     *
     * @param int $limit
     * @return array
     */
    public function getTopProducts(int $limit = 5): array;

    /**
     * Lấy phân bố trạng thái đơn hàng.
     *
     * @return array
     */
    public function getOrderStatusDistribution(): array;
}
