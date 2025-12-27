import { ref, computed } from "vue";
import { useDebounceFn } from "@vueuse/core";
import { productService } from "~/services/product/product.service";
import { productCatalogueService } from "~/services/product/catalogue.service";
import { brandService } from "~/services/brand/brand.service";
import { userCatalogueService } from "~/services/user/catalogue.service";
import type { IProduct } from "~/interfaces/product/product.interface";
import type { IProductCatalogue } from "~/interfaces/product/catalogue.interface";
import type { IBrand } from "~/interfaces/brand/brand.interface";
import type { IUserCatalogue } from "~/interfaces/user/catalogue.interface";
import { resolveAlbum } from "~/utils/helpers";

// Entity types
export type EntityType = "product" | "category" | "brand" | "customer_group";

// Search result interface
export interface SearchResult {
  id: number;
  name: string;
  image?: string;
  price?: number;
  type: EntityType;
}

// Search composable
export const useEntitySearch = (entityType: EntityType) => {
  const keyword = ref("");
  const suggestions = ref<SearchResult[]>([]);
  const isLoading = ref(false);
  const selectedEntity = ref<SearchResult | null>(null);

  // Get display name for product
  const getProductDisplayName = (product: IProduct) => {
    const v = product.productVariants?.[0] || product.variant;
    return v?.name ? `${product.name} ${v.name}`.trim() : product.name || "";
  };

  // Get product price
  const getProductPrice = (product: IProduct) => {
    const v = product.productVariants?.[0] || product.variant;
    return v?.price || product.priceOrigin || 0;
  };

  // Get product image
  const getProductImage = (product: IProduct) => {
    return resolveAlbum(product.album || [])[0] || "";
  };

  // Search products
  const searchProducts = async (query: string): Promise<SearchResult[]> => {
    try {
      const params: any = {
        perpage: 20,
        sort: "created_at,desc",
      };

      if (query.trim()) {
        params.keyword = query;
      } else {
        params.type = "all";
      }

      const response = await productService.pagination(params);

      // response may be either { data: [...] } or { data: { data: [...] } }
      const products = Array.isArray(response?.data) ? response.data : response?.data?.data || [];
      return products.map((product: IProduct) => ({
        id: product.id!,
        name: getProductDisplayName(product),
        image: getProductImage(product),
        price: getProductPrice(product),
        type: "product" as EntityType,
      }));
    } catch (error) {
      console.error("Error searching products:", error);
      return [];
    }
  };

  // Search categories
  const searchCategories = async (query: string): Promise<SearchResult[]> => {
    try {
      const params: any = {
        perpage: 20,
        sort: "created_at,desc",
      };

      if (query.trim()) {
        params.keyword = query;
      } else {
        params.type = "all";
      }

      const response = await productCatalogueService.pagination(params);
      const categories = Array.isArray(response?.data) ? response.data : response?.data?.data || [];
      return categories.map((category: IProductCatalogue) => ({
        id: category.id,
        name: category.name || "",
        image: category.image?.url || undefined,
        type: "category" as EntityType,
      }));
    } catch (error) {
      console.error("Error searching categories:", error);
      return [];
    }
  };

  // Search brands
  const searchBrands = async (query: string): Promise<SearchResult[]> => {
    try {
      const params: any = {
        perpage: 20,
        sort: "created_at,desc",
      };

      if (query.trim()) {
        params.keyword = query;
      } else {
        params.type = "all";
      }

      const response = await brandService.pagination(params);
      const brands = Array.isArray(response?.data) ? response.data : response?.data?.data || [];
      return brands.map((brand: IBrand) => ({
        id: brand.id,
        name: brand.name || "",
        image: brand.logo?.url || undefined,
        type: "brand" as EntityType,
      }));
    } catch (error) {
      console.error("Error searching brands:", error);
      return [];
    }
  };

  // Search customer groups
  const searchCustomerGroups = async (query: string): Promise<SearchResult[]> => {
    try {
      const params: any = {
        perpage: 20,
        sort: "created_at,desc",
      };

      if (query.trim()) {
        params.keyword = query;
      } else {
        params.type = "all";
      }

      const response = await userCatalogueService.pagination(params);
      const groups = Array.isArray(response?.data) ? response.data : response?.data?.data || [];
      return groups.map((group: IUserCatalogue) => ({
        id: group.id,
        name: group.name,
        type: "customer_group" as EntityType,
      }));
    } catch (error) {
      console.error("Error searching customer groups:", error);
      return [];
    }
  };

  // Main search function
  const performSearch = async (query: string): Promise<SearchResult[]> => {
    switch (entityType) {
      case "product":
        return await searchProducts(query);
      case "category":
        return await searchCategories(query);
      case "brand":
        return await searchBrands(query);
      case "customer_group":
        return await searchCustomerGroups(query);
      default:
        return [];
    }
  };

  // Debounced search
  const debouncedSearch = useDebounceFn(async (query: string) => {
    isLoading.value = true;
    try {
      const results = await performSearch(query);
      console.log(`Search results for ${entityType} with query "${query}":`, results);
      suggestions.value = results;
    } catch (error) {
      console.error(`Error searching ${entityType}:`, error);
      suggestions.value = [];
    } finally {
      isLoading.value = false;
    }
  }, 300);

  // Watch keyword changes
  const search = (query: string) => {
    keyword.value = query;
    debouncedSearch(query);
  };

  // Select entity
  const selectEntity = (entity: SearchResult) => {
    selectedEntity.value = entity;
    keyword.value = entity.name;
    suggestions.value = [];
  };

  // Clear selection
  const clearSelection = () => {
    selectedEntity.value = null;
    keyword.value = "";
    suggestions.value = [];
  };

  // Get entity by ID
  const getEntityById = async (id: number): Promise<SearchResult | null> => {
    try {
      let entity: any = null;

      switch (entityType) {
        case "product":
          const productResponse = await productService.show(id);
          entity = productResponse?.data;
          if (entity) {
            return {
              id: entity.id,
              name: getProductDisplayName(entity),
              image: getProductImage(entity),
              price: getProductPrice(entity),
              type: "product",
            };
          }
          break;

        case "category":
          const categoryResponse = await productCatalogueService.show(id);
          entity = categoryResponse?.data;
          if (entity) {
            return {
              id: entity.id,
              name: entity.name || "",
              image: entity.image?.url,
              type: "category",
            };
          }
          break;

        case "brand":
          const brandResponse = await brandService.show(id);
          entity = brandResponse?.data;
          if (entity) {
            return {
              id: entity.id,
              name: entity.name || "",
              image: entity.logo?.url,
              type: "brand",
            };
          }
          break;

        case "customer_group":
          const groupResponse = await userCatalogueService.show(id);
          entity = groupResponse?.data;
          if (entity) {
            return {
              id: entity.id,
              name: entity.name,
              type: "customer_group",
            };
          }
          break;
      }

      return null;
    } catch (error) {
      console.error(`Error getting ${entityType} by ID:`, error);
      return null;
    }
  };

  return {
    keyword,
    suggestions,
    isLoading,
    selectedEntity,
    search,
    selectEntity,
    clearSelection,
    getEntityById,
  };
};
