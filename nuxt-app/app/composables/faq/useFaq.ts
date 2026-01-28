import { useI18n } from "vue-i18n";
import { faqCatalogueService } from "~/services/faq/catalogue.service";
import { resolveImageUrl } from "~/utils/helpers";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IFaqCatalogue } from "~/interfaces/faq/catalogue.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";

export const useFaq = (initialFilters?: BasePayloadFilter) => {
  const { t } = useI18n();
  const data = ref<IApiOk<IPaginate<IFaqCatalogue>>>();
  const filters = ref<BasePayloadFilter>(initialFilters ?? { keyword: "", perpage: 20, publish: 2 });
  const page = ref(1);
  const isLoading = ref(false);
  const faq = ref<IFaqCatalogue | null>(null);
  let requestSeq = 0;

  const fetchData = async () => {
    isLoading.value = true;
    try {
      const current = ++requestSeq;
      const response = await faqCatalogueService.pagination({
        ...filters.value,
        page: page.value,
      });
      if (current === requestSeq) {
        data.value = response;
      }
    } catch (err: any) {
      console.error("Lỗi! Không tải được dữ liệu:", err);
    } finally {
      isLoading.value = false;
    }
  };

  const fetchDetailBySlug = async (slug: string): Promise<IFaqCatalogue | null> => {
    if (!slug) return null;
    isLoading.value = true;
    try {
      const current = ++requestSeq;
      const res = await faqCatalogueService.show(slug);
      const next = (res as any)?.data ?? null;
      if (current === requestSeq) {
        faq.value = next;
      }
      return next;
    } catch (e: any) {
      faq.value = null;
      return null;
    } finally {
      isLoading.value = false;
    }
  };

  const search = async (newFilters: BasePayloadFilter) => {
    filters.value = { ...filters.value, ...newFilters };
    page.value = 1;
    await fetchData();
  };
  const setPage = async (newPage: number) => {
    page.value = newPage;
    await fetchData();
  };

  const getTotalItems = () => {
    return data.value?.data?.data?.length || 0;
  };

  const getCoverUrl = (input?: Partial<IFaqCatalogue> | null): string => resolveImageUrl(input || faq.value);

  onMounted(() => {
    fetchData();
  });

  return {
    data,
    isLoading,
    filters,
    page,
    faq,
    fetchDetailBySlug,
    fetchData,
    search,
    setPage,
    getTotalItems,
    getCoverUrl,
  };
};
