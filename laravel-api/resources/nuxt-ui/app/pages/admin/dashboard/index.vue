<template>
    <div class="row">
        <div class="col">
            <div class="h-100">
                <div class="row mb-3 pb-1">
                    <div class="col-12">
                        <div class="d-flex align-items-lg-center flex-lg-row flex-column">
                            <div class="flex-grow-1">
                                <h4 class="fs-16 mb-1">{{ $t("admin.page.dashboard.welcome") }}</h4>
                                <p class="text-muted mb-0">{{ $t("admin.page.dashboard.subtitle") }}</p>
                            </div>
                            <div class="mt-3 mt-lg-0">
                                <form action="javascript:void(0);">
                                    <div class="row g-3 mb-0 align-items-center">
                                        <div class="col-sm-auto">
                                            <div class="input-group">
                                                <input type="text" class="form-control border-0 minimal-border dash-filter-picker shadow" data-provider="flatpickr" data-range-date="true" data-date-format="d M, Y" data-deafult-date="01 Jan 2022 to 31 Jan 2022" :placeholder="$t('admin.page.dashboard.dateRange')" />
                                                <div class="input-group-text bg-primary border-primary text-white">
                                                    <i class="ri-calendar-2-line"></i>
                                                </div>
                                            </div>
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-soft-success material-shadow-none">
                                                <i class="ri-add-circle-line align-middle me-1"></i>
                                                {{ $t("admin.page.dashboard.addProduct") }}
                                            </button>
                                        </div>
                                        <!--end col-->
                                        <div class="col-auto">
                                            <button type="button" class="btn btn-soft-info btn-icon waves-effect material-shadow-none waves-light layout-rightside-btn"><i class="ri-pulse-line"></i></button>
                                        </div>
                                        <!--end col-->
                                    </div>
                                    <!--end row-->
                                </form>
                            </div>
                        </div>
                        <!-- end card header -->
                    </div>
                    <!--end col-->
                </div>
                <!--end row-->

                <div class="row">
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">{{ $t("admin.page.dashboard.totalEarnings") }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 :class="[(stats?.overview?.earnings?.growth_percentage ?? 0) >= 0 ? 'text-success' : 'text-danger', 'fs-14 mb-0']">
                                            <i :class="[(stats?.overview?.earnings?.growth_percentage ?? 0) >= 0 ? 'ri-arrow-right-up-line' : 'ri-arrow-right-down-line', 'fs-13 align-middle']"></i>
                                            {{ (stats?.overview?.earnings?.growth_percentage ?? 0) >= 0 ? '+' : '' }}{{ stats?.overview?.earnings?.growth_percentage ?? 0 }} %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            {{ formatPrice(stats?.overview?.earnings?.total || 0) }}
                                        </h4>
                                        <NuxtLink to="/admin/order/order" class="text-decoration-underline">{{ $t("admin.page.dashboard.viewNetEarnings") }}</NuxtLink>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-success-subtle rounded fs-3">
                                            <i class="bx bx-dollar-circle text-success"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">{{ $t("admin.page.dashboard.orders") }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 :class="[(stats?.overview?.orders?.growth_percentage ?? 0) >= 0 ? 'text-success' : 'text-danger', 'fs-14 mb-0']">
                                            <i :class="[(stats?.overview?.orders?.growth_percentage ?? 0) >= 0 ? 'ri-arrow-right-up-line' : 'ri-arrow-right-down-line', 'fs-13 align-middle']"></i>
                                            {{ (stats?.overview?.orders?.growth_percentage ?? 0) >= 0 ? '+' : '' }}{{ stats?.overview?.orders?.growth_percentage ?? 0 }} %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">{{ (stats?.overview?.orders?.total || 0).toLocaleString() }}</h4>
                                        <NuxtLink to="/admin/order/order" class="text-decoration-underline">{{ $t("admin.page.dashboard.viewAllOrders") }}</NuxtLink>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-info-subtle rounded fs-3">
                                            <i class="bx bx-shopping-bag text-info"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">{{ $t("admin.page.dashboard.customers") }}</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 :class="[(stats?.overview?.customers?.growth_percentage ?? 0) >= 0 ? 'text-success' : 'text-danger', 'fs-14 mb-0']">
                                            <i :class="[(stats?.overview?.customers?.growth_percentage ?? 0) >= 0 ? 'ri-arrow-right-up-line' : 'ri-arrow-right-down-line', 'fs-13 align-middle']"></i>
                                            {{ (stats?.overview?.customers?.growth_percentage ?? 0) >= 0 ? '+' : '' }}{{ stats?.overview?.customers?.growth_percentage ?? 0 }} %
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            {{ (stats?.overview?.customers?.total || 0).toLocaleString() }}
                                        </h4>
                                        <NuxtLink to="/admin/user/user" class="text-decoration-underline">{{ $t("admin.page.dashboard.viewDetails") }}</NuxtLink>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-warning-subtle rounded fs-3">
                                            <i class="bx bx-user-circle text-warning"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                    <div class="col-xl-3 col-md-6">
                        <!-- card -->
                        <div class="card card-animate">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1 overflow-hidden">
                                        <p class="text-uppercase fw-medium text-muted text-truncate mb-0">Sản phẩm & Tồn kho</p>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h5 class="text-warning fs-14 mb-0">
                                            <i class="ri-alert-line fs-13 align-middle me-1"></i>
                                            {{ stats?.overview?.products?.low_stock_count ?? 0 }} báo động
                                        </h5>
                                    </div>
                                </div>
                                <div class="d-flex align-items-end justify-content-between mt-4">
                                    <div>
                                        <h4 class="fs-22 fw-semibold ff-secondary mb-4">
                                            {{ (stats?.overview?.products?.total || 0).toLocaleString() }} SP
                                        </h4>
                                        <NuxtLink to="/admin/product/product" class="text-decoration-underline">Quản lý kho hàng</NuxtLink>
                                    </div>
                                    <div class="avatar-sm flex-shrink-0">
                                        <span class="avatar-title bg-primary-subtle rounded fs-3">
                                            <i class="bx bx-box text-primary"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>
                <!-- end row-->

                <div class="row">
                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header border-0 align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.revenue") }}</h4>
                                <div>
                                    <button type="button" class="btn btn-soft-secondary material-shadow-none btn-sm">ALL</button>
                                    <button type="button" class="btn btn-soft-secondary material-shadow-none btn-sm">1M</button>
                                    <button type="button" class="btn btn-soft-secondary material-shadow-none btn-sm">6M</button>
                                    <button type="button" class="btn btn-soft-primary material-shadow-none btn-sm">1Y</button>
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-header p-0 border-0 bg-light-subtle">
                                <div class="row g-0 text-center">
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1"><span>{{ (stats?.overview?.orders?.total || 0).toLocaleString() }}</span></h5>
                                            <p class="text-muted mb-0">{{ $t("admin.page.dashboard.ordersCount") }}</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1 text-success">
                                                <span>{{ formatPrice(stats?.overview?.earnings?.this_month || 0) }}</span>
                                            </h5>
                                            <p class="text-muted mb-0">{{ $t("admin.page.dashboard.income") }} (Tháng này)</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0">
                                            <h5 class="mb-1 text-danger"><span>{{ (stats?.overview?.orders?.cancelled || 0).toLocaleString() }}</span></h5>
                                            <p class="text-muted mb-0">Đơn đã hủy</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-6 col-sm-3">
                                        <div class="p-3 border border-dashed border-start-0 border-end-0">
                                            <h5 class="mb-1 text-primary">
                                                <span>{{ stats?.overview?.orders?.total ? Math.round(((stats?.overview?.orders?.completed || 0) / stats.overview.orders.total) * 100) : 0 }} %</span>
                                            </h5>
                                            <p class="text-muted mb-0">Tỷ lệ hoàn tất</p>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-body p-0 pb-2">
                                <div class="w-100">
                                    <div id="customer_impression_charts" data-colors='["--vz-primary", "--vz-success", "--vz-danger"]' data-colors-minimal='["--vz-light", "--vz-primary", "--vz-info"]' data-colors-saas='["--vz-success", "--vz-info", "--vz-danger"]' data-colors-modern='["--vz-warning", "--vz-primary", "--vz-success"]' data-colors-interactive='["--vz-info", "--vz-primary", "--vz-danger"]' data-colors-creative='["--vz-warning", "--vz-primary", "--vz-danger"]' data-colors-corporate='["--vz-light", "--vz-primary", "--vz-secondary"]' data-colors-galaxy='["--vz-secondary", "--vz-primary", "--vz-primary-rgb, 0.50"]' data-colors-classic='["--vz-light", "--vz-primary", "--vz-secondary"]' data-colors-vintage='["--vz-success", "--vz-primary", "--vz-secondary"]' class="apex-charts" dir="ltr"></div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                    <div class="col-xl-4">
                        <!-- card -->
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.salesByRegion") }}</h4>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-soft-primary material-shadow-none btn-sm">{{ $t("admin.page.dashboard.exportReport") }}</button>
                                </div>
                            </div>
                            <!-- end card header -->

                            <!-- card body -->
                            <div class="card-body">
                                <div id="sales-by-locations" data-colors='["--vz-light", "--vz-success", "--vz-primary"]' data-colors-interactive='["--vz-light", "--vz-info", "--vz-primary"]' style="height: 269px" dir="ltr"></div>

                                <div class="px-2 py-2 mt-1">
                                    <p class="mb-1">
                                        Canada
                                        <span class="float-end">75%</span>
                                    </p>
                                    <div class="progress mt-2" style="height: 6px">
                                        <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 75%" aria-valuenow="75" aria-valuemin="0" aria-valuemax="75"></div>
                                    </div>

                                    <p class="mt-3 mb-1">
                                        Greenland
                                        <span class="float-end">47%</span>
                                    </p>
                                    <div class="progress mt-2" style="height: 6px">
                                        <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 47%" aria-valuenow="47" aria-valuemin="0" aria-valuemax="47"></div>
                                    </div>

                                    <p class="mt-3 mb-1">
                                        Nga
                                        <span class="float-end">82%</span>
                                    </p>
                                    <div class="progress mt-2" style="height: 6px">
                                        <div class="progress-bar progress-bar-striped bg-primary" role="progressbar" style="width: 82%" aria-valuenow="82" aria-valuemin="0" aria-valuemax="82"></div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card body -->
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->
                </div>

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.bestSellingProducts") }}</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="fw-semibold text-uppercase fs-12">{{ $t("admin.page.dashboard.sortBy") }}</span>
                                            <span class="text-muted">
                                                {{ $t("admin.page.dashboard.today") }}
                                                <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.today") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.yesterday") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.last7Days") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.last30Days") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.thisMonth") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.lastMonth") }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-hover table-centered align-middle table-nowrap mb-0">
                                        <tbody>
                                            <tr v-if="loading && topProducts.length === 0">
                                                <td colspan="5" class="text-center py-4 text-muted">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                    Đang tải dữ liệu sản phẩm...
                                                </td>
                                            </tr>
                                            <tr v-else-if="topProducts.length === 0">
                                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu sản phẩm bán chạy</td>
                                            </tr>
                                            <tr v-for="product in topProducts" :key="product.id">
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-sm bg-light rounded p-1 me-2">
                                                            <NuxtImg :src="product.image || '/images/products/img-1.png'" :alt="product.name" class="img-fluid d-block" width="40" height="40" loading="lazy" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1">
                                                                <NuxtLink :to="`/admin/product/product/${product.id}`" class="text-reset">{{ product.name }}</NuxtLink>
                                                            </h5>
                                                            <span class="text-muted">Mã SP: #{{ product.id }}</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ formatPrice(product.price) }}</h5>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.price") }}</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ product.total_sold }}</h5>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.ordersCount") }}</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">
                                                        <span class="badge bg-success-subtle text-success">Đang bán</span>
                                                    </h5>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 my-1 fw-normal">{{ formatPrice(product.total_revenue) }}</h5>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.total") }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            {{ $t("admin.page.dashboard.showing") }}
                                            <span class="fw-semibold">5</span>
                                            {{ $t("admin.page.dashboard.of") }}
                                            <span class="fw-semibold">25</span>
                                            {{ $t("admin.page.dashboard.results") }}
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                                            <li class="page-item disabled">
                                                <a href="#" class="page-link">←</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">1</a>
                                            </li>
                                            <li class="page-item active">
                                                <a href="#" class="page-link">2</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">3</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">→</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-6">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.topSellers") }}</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">
                                                {{ $t("admin.page.dashboard.report") }}
                                                <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.downloadReport") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.export") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.import") }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-centered table-hover align-middle table-nowrap mb-0">
                                        <tbody>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <NuxtImg src="/images/companies/img-1.png" alt="" class="avatar-sm p-2" width="48" height="48" loading="lazy" />
                                                        </div>
                                                        <div>
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a href="apps-ecommerce-seller-details.html" class="text-reset">iTest Factory</a>
                                                            </h5>
                                                            <span class="text-muted">Oliver Tyler</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.category") }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">8547</p>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">$541200</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 mb-0">
                                                        32%
                                                        <i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i>
                                                    </h5>
                                                </td>
                                            </tr>
                                            <!-- end -->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <NuxtImg src="/images/companies/img-2.png" alt="" class="avatar-sm p-2" width="48" height="48" loading="lazy" />
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Digitech Galaxy</a></h5>
                                                            <span class="text-muted">John Roberts</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.watches") }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">895</p>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">$75030</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 mb-0">
                                                        79%
                                                        <i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i>
                                                    </h5>
                                                </td>
                                            </tr>
                                            <!-- end -->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <NuxtImg src="/images/companies/img-3.png" alt="" class="avatar-sm p-2" width="48" height="48" loading="lazy" />
                                                        </div>
                                                        <div class="flex-gow-1">
                                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Nesta Technologies</a></h5>
                                                            <span class="text-muted">Harley Fuller</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.carAccessories") }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">3470</p>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">$45600</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 mb-0">
                                                        90%
                                                        <i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i>
                                                    </h5>
                                                </td>
                                            </tr>
                                            <!-- end -->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <NuxtImg src="/images/companies/img-8.png" alt="" class="avatar-sm p-2" width="48" height="48" loading="lazy" />
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-14 my-1 fw-medium"><a href="apps-ecommerce-seller-details.html" class="text-reset">Zoetic Fashion</a></h5>
                                                            <span class="text-muted">James Bowen</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.clothing") }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">5488</p>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">$29456</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 mb-0">
                                                        40%
                                                        <i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i>
                                                    </h5>
                                                </td>
                                            </tr>
                                            <!-- end -->
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <NuxtImg src="/images/companies/img-5.png" alt="" class="avatar-sm p-2" width="48" height="48" loading="lazy" />
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h5 class="fs-14 my-1 fw-medium">
                                                                <a href="apps-ecommerce-seller-details.html" class="text-reset">Meta4Systems</a>
                                                            </h5>
                                                            <span class="text-muted">Zoe Dennis</span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.furniture") }}</span>
                                                </td>
                                                <td>
                                                    <p class="mb-0">4100</p>
                                                    <span class="text-muted">{{ $t("admin.page.dashboard.stock") }}</span>
                                                </td>
                                                <td>
                                                    <span class="text-muted">$11260</span>
                                                </td>
                                                <td>
                                                    <h5 class="fs-14 mb-0">
                                                        57%
                                                        <i class="ri-bar-chart-fill text-success fs-16 align-middle ms-2"></i>
                                                    </h5>
                                                </td>
                                            </tr>
                                            <!-- end -->
                                        </tbody>
                                    </table>
                                    <!-- end table -->
                                </div>

                                <div class="align-items-center mt-4 pt-2 justify-content-between row text-center text-sm-start">
                                    <div class="col-sm">
                                        <div class="text-muted">
                                            {{ $t("admin.page.dashboard.showing") }}
                                            <span class="fw-semibold">5</span>
                                            {{ $t("admin.page.dashboard.of") }}
                                            <span class="fw-semibold">25</span>
                                            {{ $t("admin.page.dashboard.results") }}
                                        </div>
                                    </div>
                                    <div class="col-sm-auto mt-3 mt-sm-0">
                                        <ul class="pagination pagination-separated pagination-sm mb-0 justify-content-center">
                                            <li class="page-item disabled">
                                                <a href="#" class="page-link">←</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">1</a>
                                            </li>
                                            <li class="page-item active">
                                                <a href="#" class="page-link">2</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">3</a>
                                            </li>
                                            <li class="page-item">
                                                <a href="#" class="page-link">→</a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <!-- .card-body-->
                        </div>
                        <!-- .card-->
                    </div>
                    <!-- .col-->
                </div>
                <!-- end row-->

                <div class="row">
                    <div class="col-xl-4">
                        <div class="card card-height-100">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.visitsBySource") }}</h4>
                                <div class="flex-shrink-0">
                                    <div class="dropdown card-header-dropdown">
                                        <a class="text-reset dropdown-btn" href="#" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                            <span class="text-muted">
                                                {{ $t("admin.page.dashboard.report") }}
                                                <i class="mdi mdi-chevron-down ms-1"></i>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.downloadReport") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.export") }}</a>
                                            <a class="dropdown-item" href="#">{{ $t("admin.page.dashboard.import") }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-body">
                                <div id="store-visits-source" data-colors='["--vz-primary", "--vz-success", "--vz-warning", "--vz-danger", "--vz-info"]' data-colors-minimal='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.70", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.45"]' data-colors-interactive='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.70", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.45"]' data-colors-galaxy='["--vz-primary", "--vz-primary-rgb, 0.85", "--vz-primary-rgb, 0.70", "--vz-primary-rgb, 0.60", "--vz-primary-rgb, 0.45"]' class="apex-charts" dir="ltr"></div>
                            </div>
                        </div>
                        <!-- .card-->
                    </div>
                    <!-- .col-->

                    <div class="col-xl-8">
                        <div class="card">
                            <div class="card-header align-items-center d-flex">
                                <h4 class="card-title mb-0 flex-grow-1">{{ $t("admin.page.dashboard.recentOrders") }}</h4>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-soft-info btn-sm material-shadow-none">
                                        <i class="ri-file-list-3-line align-middle"></i>
                                        {{ $t("admin.page.dashboard.createReport") }}
                                    </button>
                                </div>
                            </div>
                            <!-- end card header -->

                            <div class="card-body">
                                <div class="table-responsive table-card">
                                    <table class="table table-borderless table-centered align-middle table-nowrap mb-0">
                                        <thead class="text-muted table-light">
                                            <tr>
                                                <th scope="col">{{ $t("admin.page.dashboard.orderCode") }}</th>
                                                <th scope="col">{{ $t("admin.page.dashboard.customer") }}</th>
                                                <th scope="col">Số món</th>
                                                <th scope="col">{{ $t("admin.page.dashboard.amount") }}</th>
                                                <th scope="col">Phương thức</th>
                                                <th scope="col">{{ $t("admin.page.dashboard.status") }}</th>
                                                <th scope="col">Thời gian</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-if="loading && recentOrders.length === 0">
                                                <td colspan="7" class="text-center py-4 text-muted">
                                                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                    Đang tải danh sách đơn hàng...
                                                </td>
                                            </tr>
                                            <tr v-else-if="recentOrders.length === 0">
                                                <td colspan="7" class="text-center py-4 text-muted">Chưa có đơn hàng gần đây</td>
                                            </tr>
                                            <tr v-for="order in recentOrders" :key="order.id">
                                                <td>
                                                    <NuxtLink :to="`/admin/order/order/${order.id}`" class="fw-medium link-primary">
                                                        #{{ order.code }}
                                                    </NuxtLink>
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="avatar-xs me-2">
                                                            <span class="avatar-title bg-light text-primary rounded-circle">
                                                                <i class="ri-user-3-line"></i>
                                                            </span>
                                                        </div>
                                                        <div>
                                                            <div class="fw-medium">{{ order.customer_name }}</div>
                                                            <small class="text-muted">{{ order.customer_phone || '' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ order.items_count }} SP</td>
                                                <td>
                                                    <span class="text-success fw-medium">{{ formatPrice(order.total_amount) }}</span>
                                                </td>
                                                <td>
                                                    <span class="badge bg-light text-body border text-uppercase">{{ order.payment_method }}</span>
                                                </td>
                                                <td>
                                                    <OrderStatusBadge :status="order.status" size="sm" />
                                                </td>
                                                <td>
                                                    <span class="text-muted fs-12">{{ new Date(order.created_at).toLocaleDateString('vi-VN') }}</span>
                                                </td>
                                            </tr>
                                        </tbody>
                                        <!-- end tbody -->
                                    </table>
                                    <!-- end table -->
                                </div>
                            </div>
                        </div>
                        <!-- .card-->
                    </div>
                    <!-- .col-->
                </div>
                <!-- end row-->
            </div>
            <!-- end .h-100-->
        </div>
        <!-- end col -->

        <div class="col-auto layout-rightside-col">
            <div class="overlay"></div>
            <div class="layout-rightside">
                <div class="card h-100 rounded-0">
                    <div class="card-body p-0">
                        <div class="p-3">
                            <h6 class="text-muted mb-0 text-uppercase fw-semibold">{{ $t("admin.page.dashboard.recentActivity") }}</h6>
                        </div>
                        <div data-simplebar style="max-height: 410px" class="p-3 pt-0">
                            <div class="acitivity-timeline acitivity-main">
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                        <div class="avatar-title bg-success-subtle text-success rounded-circle material-shadow">
                                            <i class="ri-shopping-cart-2-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">{{ $t("admin.page.dashboard.purchaseBy") }} James Price</h6>
                                        <p class="text-muted mb-1">{{ $t("admin.page.dashboard.productName") }}</p>
                                        <small class="mb-0 text-muted">02:14 PM {{ $t("admin.page.dashboard.today") }}</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0 avatar-xs acitivity-avatar">
                                        <div class="avatar-title bg-danger-subtle text-danger rounded-circle material-shadow">
                                            <i class="ri-stack-fill"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">
                                            {{ $t("admin.page.dashboard.addedNew") }}
                                            <span class="fw-semibold">{{ $t("admin.page.dashboard.fashionCollection") }}</span>
                                        </h6>
                                        <p class="text-muted mb-1">{{ $t("admin.page.dashboard.by") }} Nesta Technologies</p>
                                        <div class="d-inline-flex gap-2 border border-dashed p-2 mb-2">
                                            <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                <NuxtImg src="/images/products/img-8.png" alt="" class="img-fluid d-block" />
                                            </a>
                                            <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                <NuxtImg src="/images/products/img-2.png" alt="" class="img-fluid d-block" />
                                            </a>
                                            <a href="apps-ecommerce-product-details.html" class="bg-light rounded p-1">
                                                <NuxtImg src="/images/products/img-10.png" alt="" class="img-fluid d-block" />
                                            </a>
                                        </div>
                                        <p class="mb-0 text-muted">
                                            <small>9:47 PM {{ $t("admin.page.dashboard.yesterday") }}</small>
                                        </p>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <NuxtImg src="/images/users/avatar-2.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar material-shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">Natasha Carey {{ $t("admin.page.dashboard.likedProduct") }}</h6>
                                        <p class="text-muted mb-1">{{ $t("admin.page.dashboard.allowUsersLike") }}</p>
                                        <small class="mb-0 text-muted">25 Thg 12, 2021</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-secondary material-shadow">
                                                <i class="mdi mdi-sale fs-14"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">
                                            {{ $t("admin.page.dashboard.todayOffer") }}
                                            <a href="apps-ecommerce-seller-details.html" class="link-secondary">Digitech Galaxy</a>
                                        </h6>
                                        <p class="text-muted mb-2">{{ $t("admin.page.dashboard.offerDescription") }}</p>
                                        <small class="mb-0 text-muted">12 Thg 12, 2021</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-danger-subtle text-danger material-shadow">
                                                <i class="ri-bookmark-fill"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">{{ $t("admin.page.dashboard.favoriteProduct") }}</h6>
                                        <p class="text-muted mb-2">Esther James {{ $t("admin.page.dashboard.likedProductBy") }}</p>
                                        <small class="mb-0 text-muted">25 Thg 11, 2021</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-secondary material-shadow">
                                                <i class="mdi mdi-sale fs-14"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">
                                            {{ $t("admin.page.dashboard.flashSale") }}
                                            <span class="text-primary">{{ $t("admin.page.dashboard.tomorrow") }}</span>
                                        </h6>
                                        <p class="text-muted mb-0">
                                            {{ $t("admin.page.dashboard.flashSaleBy") }}
                                            <a href="javascript:void(0);" class="link-secondary fw-medium">Zoetic Fashion</a>
                                        </p>
                                        <small class="mb-0 text-muted">22 Thg 10, 2021</small>
                                    </div>
                                </div>
                                <div class="acitivity-item py-3 d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs acitivity-avatar">
                                            <div class="avatar-title rounded-circle bg-info-subtle text-info material-shadow">
                                                <i class="ri-line-chart-line"></i>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">{{ $t("admin.page.dashboard.monthlySalesReport") }}</h6>
                                        <p class="text-muted mb-2">
                                            <span class="text-danger">{{ $t("admin.page.dashboard.daysLeft") }}</span>
                                            {{ $t("admin.page.dashboard.toSubmit") }}
                                            <a href="javascript:void(0);" class="link-warning text-decoration-underline">{{ $t("admin.page.dashboard.reportGenerator") }}</a>
                                        </p>
                                        <small class="mb-0 text-muted">15 Thg 10</small>
                                    </div>
                                </div>
                                <div class="acitivity-item d-flex">
                                    <div class="flex-shrink-0">
                                        <NuxtImg src="/images/users/avatar-3.jpg" alt="" class="avatar-xs rounded-circle acitivity-avatar material-shadow" />
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="mb-1 lh-base">Frank Hook {{ $t("admin.page.dashboard.commented") }}</h6>
                                        <p class="text-muted mb-2 fst-italic">" {{ $t("admin.page.dashboard.comment") }} "</p>
                                        <small class="mb-0 text-muted">26 Thg 8, 2021</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-3 mt-2">
                            <h6 class="text-muted mb-3 text-uppercase fw-semibold">{{ $t("admin.page.dashboard.top10Categories") }}</h6>

                            <ol class="ps-3 text-muted">
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.mobileAccessories") }}
                                        <span class="float-end">(10,294)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.desktop") }}
                                        <span class="float-end">(6,256)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.electronics") }}
                                        <span class="float-end">(3,479)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.homeFurniture") }}
                                        <span class="float-end">(2,275)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.groceries") }}
                                        <span class="float-end">(1,950)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.fashion") }}
                                        <span class="float-end">(1,582)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.homeAppliances") }}
                                        <span class="float-end">(1,037)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.beautyToysOthers") }}
                                        <span class="float-end">(924)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.foodDrinks") }}
                                        <span class="float-end">(701)</span>
                                    </a>
                                </li>
                                <li class="py-1">
                                    <a href="#" class="text-muted">
                                        {{ $t("admin.page.dashboard.toysGames") }}
                                        <span class="float-end">(239)</span>
                                    </a>
                                </li>
                            </ol>
                            <div class="mt-3 text-center">
                                <a href="javascript:void(0);" class="text-muted text-decoration-underline">{{ $t("admin.page.dashboard.viewAllCategories") }}</a>
                            </div>
                        </div>
                        <div class="p-3">
                            <h6 class="text-muted mb-3 text-uppercase fw-semibold">{{ $t("admin.page.dashboard.productReviews") }}</h6>
                            <!-- Swiper
                    <div class="swiper vertical-swiper" style="height: 250px">
                      <div class="swiper-wrapper">
                        <div class="swiper-slide">
                          <div class="card border border-dashed shadow-none">
                            <div class="card-body">
                              <div class="d-flex">
                                <div class="flex-shrink-0 avatar-sm">
                                  <div class="avatar-title bg-light rounded material-shadow">
                                    <NuxtImg src="/images/companies/img-1.png" alt="" height="30" />
                                  </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <div>
                                    <p class="text-muted mb-1 fst-italic text-truncate-two-lines">" Great product and looks great, lots of features. "</p>
                                    <div class="fs-11 align-middle text-warning">
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                    </div>
                                  </div>
                                  <div class="text-end mb-0 text-muted">
                                    - by
                                    <cite title="Source Title">Force Medicines</cite>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="swiper-slide">
                          <div class="card border border-dashed shadow-none">
                            <div class="card-body">
                              <div class="d-flex">
                                <div class="flex-shrink-0">
                                  <NuxtImg src="/images/users/avatar-3.jpg" alt="" class="avatar-sm rounded material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <div>
                                    <p class="text-muted mb-1 fst-italic text-truncate-two-lines">" Amazing template, very easy to understand and manipulate. "</p>
                                    <div class="fs-11 align-middle text-warning">
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-half-fill"></i>
                                    </div>
                                  </div>
                                  <div class="text-end mb-0 text-muted">
                                    - by
                                    <cite title="Source Title">Henry Baird</cite>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="swiper-slide">
                          <div class="card border border-dashed shadow-none">
                            <div class="card-body">
                              <div class="d-flex">
                                <div class="flex-shrink-0 avatar-sm">
                                  <div class="avatar-title bg-light rounded">
                                    <NuxtImg src="/images/companies/img-8.png" alt="" height="30" />
                                  </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <div>
                                    <p class="text-muted mb-1 fst-italic text-truncate-two-lines">"Very beautiful product and Very helpful customer service."</p>
                                    <div class="fs-11 align-middle text-warning">
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-line"></i>
                                      <i class="ri-star-line"></i>
                                    </div>
                                  </div>
                                  <div class="text-end mb-0 text-muted">
                                    - by
                                    <cite title="Source Title">Zoetic Fashion</cite>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                        <div class="swiper-slide">
                          <div class="card border border-dashed shadow-none">
                            <div class="card-body">
                              <div class="d-flex">
                                <div class="flex-shrink-0">
                                  <NuxtImg src="/images/users/avatar-2.jpg" alt="" class="avatar-sm rounded material-shadow" />
                                </div>
                                <div class="flex-grow-1 ms-3">
                                  <div>
                                    <p class="text-muted mb-1 fst-italic text-truncate-two-lines">" The product is very beautiful. I like it. "</p>
                                    <div class="fs-11 align-middle text-warning">
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-fill"></i>
                                      <i class="ri-star-half-fill"></i>
                                      <i class="ri-star-line"></i>
                                    </div>
                                  </div>
                                  <div class="text-end mb-0 text-muted">
                                    - by
                                    <cite title="Source Title">Nancy Martino</cite>
                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div> -->
                        </div>

                        <div class="p-3">
                            <h6 class="text-muted mb-3 text-uppercase fw-semibold">{{ $t("admin.page.dashboard.customerReviews") }}</h6>
                            <div class="bg-light px-3 py-2 rounded-2 mb-2">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="fs-16 align-middle text-warning">
                                            <i class="ri-star-fill"></i>
                                            <i class="ri-star-fill"></i>
                                            <i class="ri-star-fill"></i>
                                            <i class="ri-star-fill"></i>
                                            <i class="ri-star-half-fill"></i>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">
                                        <h6 class="mb-0">4.5 out of 5</h6>
                                    </div>
                                </div>
                            </div>
                            <div class="text-center">
                                <div class="text-muted">
                                    {{ $t("admin.page.dashboard.totalReviews") }}
                                    <span class="fw-medium">5.50k</span>
                                    {{ $t("admin.page.dashboard.reviews") }}
                                </div>
                            </div>

                            <div class="mt-3">
                                <div class="row align-items-center g-2">
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0">5 {{ $t("admin.page.dashboard.stars") }}</h6>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-1">
                                            <div class="progress animated-progress progress-sm">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 50.16%" aria-valuenow="50.16" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0 text-muted">2758</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row align-items-center g-2">
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0">4 {{ $t("admin.page.dashboard.stars") }}</h6>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-1">
                                            <div class="progress animated-progress progress-sm">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 29.32%" aria-valuenow="29.32" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0 text-muted">1063</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row align-items-center g-2">
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0">3 {{ $t("admin.page.dashboard.stars") }}</h6>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-1">
                                            <div class="progress animated-progress progress-sm">
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: 18.12%" aria-valuenow="18.12" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0 text-muted">997</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row align-items-center g-2">
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0">2 {{ $t("admin.page.dashboard.stars") }}</h6>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-1">
                                            <div class="progress animated-progress progress-sm">
                                                <div class="progress-bar bg-success" role="progressbar" style="width: 4.98%" aria-valuenow="4.98" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0 text-muted">227</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->

                                <div class="row align-items-center g-2">
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0">1 {{ $t("admin.page.dashboard.stars") }}</h6>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="p-1">
                                            <div class="progress animated-progress progress-sm">
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: 7.42%" aria-valuenow="7.42" aria-valuemin="0" aria-valuemax="100"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <div class="p-1">
                                            <h6 class="mb-0 text-muted">408</h6>
                                        </div>
                                    </div>
                                </div>
                                <!-- end row -->
                            </div>
                        </div>

                        <div class="card sidebar-alert bg-light border-0 text-center mx-4 mb-0 mt-3">
                            <div class="card-body">
                                <NuxtImg src="/images/giftbox.png" alt="Hộp quà" />
                                <div class="mt-4">
                                    <h5>{{ $t("admin.page.dashboard.inviteNewSeller") }}</h5>
                                    <p class="text-muted lh-base">{{ $t("admin.page.dashboard.inviteDescription") }}</p>
                                    <button type="button" class="btn btn-primary btn-label rounded-pill">
                                        <i class="ri-mail-fill label-icon align-middle rounded-pill fs-16 me-2"></i>
                                        {{ $t("admin.page.dashboard.inviteNow") }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card-->
            </div>
            <!-- end .rightbar-->
        </div>
        <!-- end col -->
    </div>
</template>
<script setup lang="ts">
import { dashboardService, type IDashboardStatistics, type IRecentOrder, type ITopProduct } from "~/services/dashboard/dashboard.service";
import { formatPrice } from "~/utils/helpers";
import OrderStatusBadge from "~/components/order/OrderStatusBadge.vue";

definePageMeta({
    layout: "admin",
    middleware: ["auth-admin"],
});

const loading = ref(true);
const stats = ref<IDashboardStatistics | null>(null);
const recentOrders = ref<IRecentOrder[]>([]);
const topProducts = ref<ITopProduct[]>([]);

const loadDashboardData = async () => {
    loading.value = true;
    try {
        const [statsRes, ordersRes, productsRes] = await Promise.allSettled([
            dashboardService.statistics(),
            dashboardService.recentOrders(5),
            dashboardService.topProducts(5),
        ]);

        if (statsRes.status === "fulfilled" && statsRes.value?.data) {
            stats.value = statsRes.value.data;
        }
        if (ordersRes.status === "fulfilled" && ordersRes.value?.data) {
            recentOrders.value = ordersRes.value.data;
        }
        if (productsRes.status === "fulfilled" && productsRes.value?.data) {
            topProducts.value = productsRes.value.data;
        }
    } catch (e) {
        console.error("Failed to load dashboard data:", e);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadDashboardData();
});
</script>
