import type { Ref } from "vue";
import { postService } from "~/services/post/post.service";
import type { IApiOk, IPaginate } from "~/interfaces/base.interface";
import type { IPost, PostPayloadFilter } from "~/interfaces/post/post.interface";
import type { IPostTag } from "~/interfaces/post/tag.interface";
import { postTagService } from "~/services/post/tag.service";
import type { IPostCatalogue } from "~/interfaces/post/catalogue.interface";
import { postCatalogueService } from "~/services/post/catalogue.service";
import { buildRelationParams } from "~/utils/helpers";

export const usePost = () => {
  const isLoading: Ref<boolean> = ref(false);
  const paginate: Ref<IApiOk<IPaginate<IPost>> | null> = ref(null);
  const list: Ref<IPost[]> = ref([]);
  const tagList: Ref<IPostTag[]> = ref([]);
  const postCatalogueList: Ref<IPostCatalogue[]> = ref([]);
  const hotCataloguePosts = useState<IPost[]>("hotCataloguePosts", () => [] as IPost[]);
  const byCatalogueCache = reactive<Record<number, IPost[]>>({});
  const byCatalogueLoading = reactive<Record<number, boolean>>({});
  const byCatalogueError = reactive<Record<number, string | null>>({});
  const latestItems = ref<IPost[]>([]);
  const latestPage = ref(1);
  const latestLastPage = ref(1);
  const latestLoading = ref(false);
  const hotQuickItems = ref<IPost[]>([]);
  const hotQuickPage = ref(1);
  const hotQuickLastPage = ref(1);
  const hotQuickLoading = ref(false);
  const postDetail = ref<IPost | null>(null);
  const postDetailLoading = ref(false);
  const postDetailError = ref<string | null>(null);

  // Cache flags for optimization using useState
  const isTagsLoaded = useState("post-tags-loaded", () => false);
  const isCataloguesLoaded = useState("post-catalogues-loaded", () => false);
  const isLatestLoaded = useState("post-latest-loaded", () => false);
  const isHotQuickLoaded = useState("post-hot-quick-loaded", () => false);

  /**
   * Lấy bài viết theo paginate
   */
  const fetchPaginate = async (filters: PostPayloadFilter, page = 1, perPage = 10) => {
    isLoading.value = true;
    try {
      const response = await postService.pagination({
        ...filters,
        page,
        perpage: perPage,
      });
      paginate.value = response;
      return response;
    } catch (error: any) {
      console.error("Lỗi khi load paginate:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  /**
   * Lấy danh sách bài viết (featured, latest...)
   */
  const fetchList = async (option: "featured" | "featuredLimit" | "latest"): Promise<IPost[]> => {
    isLoading.value = true;
    try {
      let response: IApiOk<IPost[]>;
      switch (option) {
        case "featured":
          response = await postService.getFeatured();
          break;
        case "featuredLimit":
          response = await postService.getFeaturedLimit();
          break;
        case "latest":
          response = await postService.getLatest();
          break;
        default:
          throw new Error(`Unknown type: ${option}`);
      }
      list.value = response.data ?? [];
      return list.value;
    } catch (error: any) {
      console.error("Lỗi khi load list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchTags = async (): Promise<IPostTag[]> => {
    if (isTagsLoaded.value) return tagList.value; // Cache hit
    isLoading.value = true;
    try {
      const response = await postTagService.all();
      tagList.value = response.data ?? [];
      isTagsLoaded.value = true;
      return tagList.value;
    } catch (error: any) {
      console.error("Lỗi khi load tag list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchPostCatalogues = async (): Promise<IPostCatalogue[]> => {
    if (isCataloguesLoaded.value) return postCatalogueList.value; // Cache hit
    isLoading.value = true;
    try {
      const response = await postCatalogueService.all();
      postCatalogueList.value = response.data ?? [];
      isCataloguesLoaded.value = true;
      return postCatalogueList.value;
    } catch (error: any) {
      console.error("Lỗi khi load post catalogue list:", error);
      throw error;
    } finally {
      isLoading.value = false;
    }
  };

  const fetchHotCataloguePosts = async (): Promise<IPost[]> => {
    try {
      const response = await postService.getByHotCatalogue();
      hotCataloguePosts.value = response.data ?? [];
      return hotCataloguePosts.value;
    } catch (error: any) {
      console.error("Lỗi khi load danh sách bài viết theo danh mục nổi bật:", error);
      hotCataloguePosts.value = [];
      return hotCataloguePosts.value;
    }
  };

  const fetchPostsByCatalogueId = async (catalogueId: number): Promise<IPost[]> => {
    if (!catalogueId || Number.isNaN(catalogueId)) return [];
    if (byCatalogueCache[catalogueId]?.length) return byCatalogueCache[catalogueId] ?? [];
    byCatalogueLoading[catalogueId] = true;
    byCatalogueError[catalogueId] = null;
    try {
      const response = await postService.getByCatalogueId(catalogueId);
      const data = response.data ?? [];
      byCatalogueCache[catalogueId] = data;
      return data;
    } catch (error: any) {
      console.error("Lỗi khi load bài theo catalogue id:", error);
      byCatalogueError[catalogueId] = error?.message ?? "Unknown error";
      return [];
    } finally {
      byCatalogueLoading[catalogueId] = false;
    }
  };

  const loadLatest = async () => {
    if (latestLoading.value) return;
    if (isLatestLoaded.value && latestPage.value === 1) return; // Cache hit for first page
    latestLoading.value = true;
    try {
      const res = await fetchPaginate({ keyword: "", perpage: 5, sort: "published_at,desc" }, latestPage.value, 5);
      const pg = res?.data;
      if (pg) {
        latestItems.value = [...latestItems.value, ...pg.data];
        latestLastPage.value = pg.last_page;
        latestPage.value += 1;
        if (latestPage.value === 2) isLatestLoaded.value = true; // Mark as loaded after first page
      }
    } finally {
      latestLoading.value = false;
    }
  };

  const loadHotQuick = async () => {
    if (hotQuickLoading.value) return;
    if (isHotQuickLoaded.value && hotQuickPage.value === 1) return; // Cache hit for first page
    hotQuickLoading.value = true;
    try {
      const relation = buildRelationParams({ post_catalogues: 1 }, "is_hot");
      const res = await postService.pagination({
        ...relation,
        page: hotQuickPage.value,
        perpage: 6,
      });
      const pg: any = res?.data;
      const pageItems: IPost[] = Array.isArray(pg?.data) ? pg.data : [];
      if (pageItems.length > 0) {
        hotQuickItems.value = [...hotQuickItems.value, ...pageItems];
        hotQuickLastPage.value = typeof pg?.last_page === "number" ? pg.last_page : hotQuickLastPage.value;
        hotQuickPage.value = hotQuickPage.value + 1;
        if (hotQuickPage.value === 2) isHotQuickLoaded.value = true; // Mark as loaded after first page
      }
    } finally {
      hotQuickLoading.value = false;
    }
  };

  const fetchById = async (slug: string): Promise<IPost | null> => {
    if (!slug) return null;
    postDetailLoading.value = true;
    postDetailError.value = null;
    try {
      const res = await postService.show(slug);
      postDetail.value = (res as any)?.data ?? null;
      return postDetail.value;
    } catch (e: any) {
      postDetailError.value = e?.message || "Không tải được bài viết";
      postDetail.value = null;
      return null;
    } finally {
      postDetailLoading.value = false;
    }
  };

  const trackView = async (postId: number): Promise<void> => {
    try {
      const currentViewCount = postDetail.value?.viewCount || 0;
      const newViewCount = currentViewCount + 1;
      await postService.incrementViewCount(postId, newViewCount);
      if (postDetail.value) {
        postDetail.value.viewCount = newViewCount;
      }
    } catch (error) {
      console.log("Error tracking view:", error);
    }
  };

  const mostLikedItems = ref<IPost[]>([]);
  const mostLikedLoading = ref(false);

  const fetchMostLiked = async (): Promise<IPost[]> => {
    mostLikedLoading.value = true;
    try {
      const response = await postService.getMostLiked();
      mostLikedItems.value = response.data ?? [];
      return mostLikedItems.value;
    } catch (error: any) {
      console.error("Lỗi khi load most liked posts:", error);
      throw error;
    } finally {
      mostLikedLoading.value = false;
    }
  };

  return {
    isLoading,
    paginate,
    list,
    tagList,
    postCatalogueList,
    hotCataloguePosts,
    byCatalogueCache,
    byCatalogueLoading,
    byCatalogueError,
    latestItems,
    latestPage,
    latestLastPage,
    latestLoading,
    hotQuickItems,
    hotQuickPage,
    hotQuickLastPage,
    hotQuickLoading,
    postDetail,
    postDetailLoading,
    postDetailError,
    mostLikedItems,
    mostLikedLoading,
    fetchPaginate,
    fetchList,
    fetchTags,
    loadLatest,
    loadHotQuick,
    fetchById,
    fetchPostCatalogues,
    fetchHotCataloguePosts,
    fetchPostsByCatalogueId,
    fetchMostLiked,
    trackView,
  };
};
