<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api\V1\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApiResource;
use App\Services\Interfaces\Dashboard\DashboardServiceInterface as DashboardService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class DashboardController extends Controller
{
    protected DashboardService $dashboardService;

    public function __construct(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    /**
     * Lấy tổng quan thống kê dashboard.
     */
    public function statistics(Request $request): JsonResponse
    {
        $overview = $this->dashboardService->getOverviewStatistics($request->query('period'));
        $distribution = $this->dashboardService->getOrderStatusDistribution();

        return ApiResource::ok([
            'overview' => $overview,
            'status_distribution' => $distribution,
        ], Lang::get('message.successfully'));
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu & đơn hàng.
     */
    public function charts(Request $request): JsonResponse
    {
        $range = (string) $request->query('range', 'monthly');
        $chartData = $this->dashboardService->getRevenueChart($range);

        return ApiResource::ok($chartData, Lang::get('message.successfully'));
    }

    /**
     * Lấy danh sách đơn hàng gần đây.
     */
    public function recentOrders(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 5);
        $recentOrders = $this->dashboardService->getRecentOrders($limit);

        return ApiResource::ok($recentOrders, Lang::get('message.successfully'));
    }

    /**
     * Lấy danh sách top sản phẩm bán chạy.
     */
    public function topProducts(Request $request): JsonResponse
    {
        $limit = (int) $request->query('limit', 5);
        $topProducts = $this->dashboardService->getTopProducts($limit);

        return ApiResource::ok($topProducts, Lang::get('message.successfully'));
    }
}
