import { useI18n } from "vue-i18n";
import { agencyService } from "~/services/agency/agency.service";
import { resolveImageUrl, formatPhoneVN } from "~/utils/helpers";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IAgency, AgencyPayloadFilter } from "~/interfaces/agency/agency.interface";
import type { BasePayloadFilter } from "~/interfaces/base.interface";

export const useAgency = (initialFilters?: AgencyPayloadFilter) => {
  const { t } = useI18n();
  const data = ref<IApiOk<IPaginate<IAgency>>>();
  const filters = ref<AgencyPayloadFilter>(initialFilters ?? { keyword: "", perpage: 10, publish: null });
  const page = ref(1);
  const isLoading = ref(false);
  const agency = ref<IAgency | null>(null);
  let requestSeq = 0;

  const fetchData = async () => {
    isLoading.value = true;
    try {
      const current = ++requestSeq;
      const response = await agencyService.paginationPublic({
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

  const fetchBySlug = async (slug: string): Promise<IAgency | null> => {
    if (!slug) return null;
    isLoading.value = true;
    try {
      const current = ++requestSeq;
      const res = await agencyService.showBySlug(slug);
      const next = (res as any)?.data ?? null;
      if (current === requestSeq) {
        agency.value = next;
      }
      return next;
    } catch (e: any) {
      agency.value = null;
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

  // Reusable helpers
  const getCoverUrl = (input?: Partial<IAgency> | null): string => resolveImageUrl(input || agency.value);

  const getSafeWebsite = (input?: Partial<IAgency> | null): string => {
    const website = (input?.website || agency.value?.website || "") as string;
    if (!website) return "";
    if (/^https?:\/\//i.test(website)) return website;
    return `https://${website}`;
  };

  const getWorkingHours = (input?: Partial<IAgency> | null): Array<{ id: number; dayOfWeek: string; openTime: string; closeTime: string }> => {
    const list = (input as any)?.agencyWorkingHours || (agency.value as any)?.agencyWorkingHours || [];
    if (!Array.isArray(list)) return [];
    return list
      .filter((x: any) => x && (x.publish === 2 || x.publish === true))
      .map((x: any) => ({ id: x.id, dayOfWeek: x.dayOfWeek, openTime: x.openTime, closeTime: x.closeTime }));
  };

  const getMapEmbedSrcFromAddress = (address?: string): string => {
    const addr = (address || agency.value?.address || "").trim();
    if (!addr) return "";
    const q = encodeURIComponent(addr);
    return `https://www.google.com/maps?q=${q}&output=embed`;
  };

  const getExternalMapLinkFromAddress = (address?: string): string => {
    const addr = (address || agency.value?.address || "").trim();
    if (!addr) return "";
    const q = encodeURIComponent(addr);
    return `https://www.google.com/maps?q=${q}`;
  };

  const formatPhone = (phone?: string | null): string => formatPhoneVN(phone || undefined);

  onMounted(() => {
    fetchData();
  });

  return {
    data,
    isLoading,
    filters,
    page,
    agency,
    fetchBySlug,
    fetchData,
    search,
    setPage,
    getTotalItems,
    getCoverUrl,
    getSafeWebsite,
    getWorkingHours,
    getMapEmbedSrcFromAddress,
    getExternalMapLinkFromAddress,
    formatPhone,
  };
};
