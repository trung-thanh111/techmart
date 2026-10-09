<?php

declare(strict_types=1);

namespace App\Services\Impl\Dashboard;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use App\Services\Interfaces\Dashboard\DashboardServiceInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardService implements DashboardServiceInterface
{
    /**
     * Lấy thống kê tổng quan KPI.
     */
    public function getOverviewStatistics(?string $period = null): array
    {
        $now = Carbon::now();
        $startOfMonth = $now->copy()->startOfMonth();
        $startOfLastMonth = $now->copy()->subMonth()->startOfMonth();
        $endOfLastMonth = $now->copy()->subMonth()->endOfMonth();

        // 1. Doanh thu (loại trừ các đơn huỷ/thất bại)
        $validStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'delivered'];

        $totalEarnings = (float) Order::whereIn('status', $validStatuses)->sum('total_amount');
        $thisMonthEarnings = (float) Order::whereIn('status', $validStatuses)
            ->where('created_at', '>=', $startOfMonth)
            ->sum('total_amount');
        $lastMonthEarnings = (float) Order::whereIn('status', $validStatuses)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->sum('total_amount');

        $earningsGrowth = $this->calculateGrowthPercentage($thisMonthEarnings, $lastMonthEarnings);

        // 2. Đơn hàng
        $totalOrders = Order::count();
        $thisMonthOrders = Order::where('created_at', '>=', $startOfMonth)->count();
        $lastMonthOrders = Order::whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();
        $ordersGrowth = $this->calculateGrowthPercentage($thisMonthOrders, $lastMonthOrders);

        $pendingOrders = Order::where('status', 'pending')->count();
        $completedOrders = Order::whereIn('status', ['completed', 'delivered'])->count();
        $cancelledOrders = Order::where('status', 'cancelled')->count();

        // 3. Khách hàng
        $totalCustomers = User::where('user_catalogue_id', 1)->count();
        $thisMonthCustomers = User::where('user_catalogue_id', 1)
            ->where('created_at', '>=', $startOfMonth)
            ->count();
        $lastMonthCustomers = User::where('user_catalogue_id', 1)
            ->whereBetween('created_at', [$startOfLastMonth, $endOfLastMonth])
            ->count();
        $customersGrowth = $this->calculateGrowthPercentage($thisMonthCustomers, $lastMonthCustomers);

        // 4. Sản phẩm & Cảnh báo tồn kho
        $totalProducts = Product::where('publish', 2)->count();
        $lowStockVariants = ProductVariant::where('stock', '<', 10)->count();

        return [
            'earnings' => [
                'total' => $totalEarnings,
                'this_month' => $thisMonthEarnings,
                'last_month' => $lastMonthEarnings,
                'growth_percentage' => $earningsGrowth,
            ],
            'orders' => [
                'total' => $totalOrders,
                'this_month' => $thisMonthOrders,
                'last_month' => $lastMonthOrders,
                'growth_percentage' => $ordersGrowth,
                'pending' => $pendingOrders,
                'completed' => $completedOrders,
                'cancelled' => $cancelledOrders,
            ],
            'customers' => [
                'total' => $totalCustomers,
                'this_month' => $thisMonthCustomers,
                'last_month' => $lastMonthCustomers,
                'growth_percentage' => $customersGrowth,
            ],
            'products' => [
                'total' => $totalProducts,
                'low_stock_count' => $lowStockVariants,
            ],
        ];
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu và đơn hàng theo thời gian.
     */
    public function getRevenueChart(string $range = 'monthly'): array
    {
        $months = [];
        $revenueData = [];
        $ordersData = [];

        $validStatuses = ['pending', 'confirmed', 'shipping', 'completed', 'delivered'];

        // Lấy 12 tháng gần nhất
        for ($i = 11; $i >= 0; $i--) {
            $monthStart = Carbon::now()->subMonths($i)->startOfMonth();
            $monthEnd = Carbon::now()->subMonths($i)->endOfMonth();
            $monthLabel = $monthStart->format('m/Y');

            $months[] = $monthLabel;

            $rev = (float) Order::whereIn('status', $validStatuses)
                ->whereBetween('created_at', [$monthStart, $monthEnd])
                ->sum('total_amount');

            $cnt = Order::whereBetween('created_at', [$monthStart, $monthEnd])
                ->count();

            $revenueData[] = $rev;
            $ordersData[] = $cnt;
        }

        return [
            'categories' => $months,
            'series' => [
                [
                    'name' => 'Doanh thu (VND)',
                    'data' => $revenueData,
                ],
                [
                    'name' => 'Số đơn hàng',
                    'data' => $ordersData,
                ],
            ],
        ];
    }

    /**
     * Lấy danh sách các đơn hàng gần đây.
     */
    public function getRecentOrders(int $limit = 5): array
    {
        return Order::with(['order_items.products', 'order_items.product_variants'])
            ->latest('id')
            ->limit($limit)
            ->get()
            ->map(function ($order) {
                return [
                    'id' => $order->id,
                    'code' => $order->code,
                    'customer_name' => $order->customer_name,
                    'customer_phone' => $order->customer_phone,
                    'total_amount' => (float) $order->total_amount,
                    'status' => $order->status,
                    'payment_status' => $order->payment_status,
                    'payment_method' => $order->payment_method,
                    'items_count' => $order->order_items->count(),
                    'created_at' => $order->created_at?->toISOString(),
                ];
            })
            ->toArray();
    }

    /**
     * Lấy top sản phẩm bán chạy nhất.
     */
    public function getTopProducts(int $limit = 5): array
    {
        $topFromOrderItems = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total) as total_revenue'))
            ->groupBy('product_id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->with('products')
            ->get();

        if ($topFromOrderItems->isNotEmpty()) {
            return $topFromOrderItems->map(function ($item) {
                $product = $item->products;
                return [
                    'id' => $product?->id ?? $item->product_id,
                    'name' => $product?->name ?? 'Sản phẩm',
                    'image' => $product?->image,
                    'price' => (float) ($product?->price ?? 0),
                    'total_sold' => (int) $item->total_sold,
                    'total_revenue' => (float) $item->total_revenue,
                ];
            })->toArray();
        }

        // Fallback: Lấy top sản phẩm theo field sold trên Product
        return Product::where('publish', 2)
            ->orderByDesc('sold')
            ->limit($limit)
            ->get()
            ->map(function ($p) {
                return [
                    'id' => $p->id,
                    'name' => $p->name,
                    'image' => $p->image,
                    'price' => (float) ($p->price ?? $p->price_origin ?? 0),
                    'total_sold' => (int) ($p->sold ?? 0),
                    'total_revenue' => (float) (($p->price ?? $p->price_origin ?? 0) * ($p->sold ?? 0)),
                ];
            })
            ->toArray();
    }

    /**
     * Phân bố trạng thái đơn hàng.
     */
    public function getOrderStatusDistribution(): array
    {
        $statuses = [
            'pending' => 'Chờ xác nhận',
            'confirmed' => 'Đã xác nhận',
            'shipping' => 'Đang giao',
            'completed' => 'Hoàn thành',
            'cancelled' => 'Đã hủy',
        ];

        $result = [];
        foreach ($statuses as $status => $label) {
            $count = Order::where('status', $status)->count();
            $result[] = [
                'status' => $status,
                'label' => $label,
                'count' => $count,
            ];
        }

        return $result;
    }

    private function calculateGrowthPercentage(float $current, float $previous): float
    {
        if ($previous <= 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        $growth = (($current - $previous) / $previous) * 100;
        return round($growth, 2);
    }
}
