import { ref, onMounted } from "vue";
import { useAppToast } from "~/composables/useAppToast";
import { productReviewService } from "~/services/product/review.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IProductReview, IProductReviewFilter } from "~/interfaces/product/review.interface";

export const useProductReview = (initialFilters?: Partial<IProductReviewFilter>) => {
    const toast = useAppToast();
    const data = ref<IApiOk<IPaginate<IProductReview>>>();
    const filters = ref<IProductReviewFilter>({
        keyword: "",
        perpage: 15,
        publish: null,
        rating: null,
        ...initialFilters,
    });
    const page = ref(1);
    const isLoading = ref(false);
    const selectedIds = ref<number[]>([]);
    const selectedItem = ref<IProductReview | null>(null);

    const ratingOptions = [
        { label: "Tất cả số sao", value: null },
        { label: "5 sao (★★★★★)", value: 5 },
        { label: "4 sao (★★★★☆)", value: 4 },
        { label: "3 sao (★★★☆☆)", value: 3 },
        { label: "2 sao (★★☆☆☆)", value: 2 },
        { label: "1 sao (★☆☆☆☆)", value: 1 },
    ];

    const publishOptions = [
        { label: "Tất cả trạng thái", value: null },
        { label: "Đã duyệt (Hiển thị)", value: 2 },
        { label: "Chờ duyệt (Pending)", value: 1 },
        { label: "Đã ẩn / Vi phạm", value: 0 },
    ];

    const fetchData = async () => {
        isLoading.value = true;
        try {
            const cleanParams: Record<string, any> = {
                page: page.value,
                perpage: filters.value.perpage,
            };

            if (filters.value.keyword?.trim()) {
                cleanParams.keyword = filters.value.keyword.trim();
            }
            if (filters.value.publish !== null && filters.value.publish !== undefined && filters.value.publish !== "") {
                cleanParams.publish = filters.value.publish;
            }
            if (filters.value.rating !== null && filters.value.rating !== undefined && filters.value.rating !== "") {
                cleanParams.rating = filters.value.rating;
            }

            const response = await productReviewService.pagination(cleanParams);
            data.value = response;
        } catch (err: any) {
            toast.error(err?.response?.data?.message || "Không thể tải danh sách đánh giá");
            console.error("Lỗi khi tải reviews:", err);
        } finally {
            isLoading.value = false;
        }
    };

    const search = async (newFilters?: Partial<IProductReviewFilter>) => {
        if (newFilters) {
            filters.value = { ...filters.value, ...newFilters };
        }
        page.value = 1;
        selectedIds.value = [];
        await fetchData();
    };

    const setPage = async (newPage: number) => {
        page.value = newPage;
        selectedIds.value = [];
        await fetchData();
    };

    const updateStatus = async (id: number, publish: number) => {
        isLoading.value = true;
        try {
            await productReviewService.updateStatus(id, publish);
            const statusLabel = publish === 2 ? "Duyệt hiển thị" : publish === 1 ? "Chuyển chờ duyệt" : "Ẩn đánh giá";
            toast.success(`${statusLabel} thành công!`);
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || "Cập nhật trạng thái thất bại");
        } finally {
            isLoading.value = false;
        }
    };

    const handleBulkStatus = async (publish: number) => {
        if (selectedIds.value.length === 0) return;
        isLoading.value = true;
        try {
            await productReviewService.bulkStatus(selectedIds.value, publish);
            const statusLabel = publish === 2 ? "Duyệt" : publish === 1 ? "Chờ duyệt" : "Ẩn";
            toast.success(`Đã cập nhật trạng thái ${statusLabel} cho ${selectedIds.value.length} đánh giá!`);
            selectedIds.value = [];
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || "Cập nhật hàng loạt thất bại");
        } finally {
            isLoading.value = false;
        }
    };

    const handleDelete = async (id: number) => {
        isLoading.value = true;
        try {
            await productReviewService.delete(id);
            toast.success("Xóa đánh giá thành công!");
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || "Xóa đánh giá thất bại");
        } finally {
            isLoading.value = false;
        }
    };

    const handleDeleteMultiple = async () => {
        if (selectedIds.value.length === 0) return;
        isLoading.value = true;
        try {
            await productReviewService.deleteMultiple(selectedIds.value);
            toast.success(`Đã xóa ${selectedIds.value.length} đánh giá!`);
            selectedIds.value = [];
            await fetchData();
        } catch (error: any) {
            toast.error(error?.response?.data?.message || "Xóa hàng loạt thất bại");
        } finally {
            isLoading.value = false;
        }
    };

    const setSelectedItem = (item: IProductReview | null) => {
        selectedItem.value = item;
    };

    const confirmDelete = async () => {
        if (!selectedItem.value) return;
        await handleDelete(selectedItem.value.id);
        selectedItem.value = null;
    };

    const updateSelectedIds = (ids: number[]) => {
        selectedIds.value = ids;
    };

    const getTotalItems = () => {
        return data.value?.data?.data?.length || 0;
    };

    const getAllItemIds = () => {
        return data.value?.data?.data?.map((item) => item.id) || [];
    };

    onMounted(() => {
        fetchData();
    });

    return {
        data,
        isLoading,
        page,
        filters,
        selectedIds,
        selectedItem,
        ratingOptions,
        publishOptions,
        fetchData,
        search,
        setPage,
        updateStatus,
        handleBulkStatus,
        handleDelete,
        handleDeleteMultiple,
        setSelectedItem,
        confirmDelete,
        updateSelectedIds,
        getTotalItems,
        getAllItemIds,
    };
};
