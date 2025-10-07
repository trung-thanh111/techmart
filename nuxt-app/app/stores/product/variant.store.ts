/**
 * Pinia store for product variant management
 */

import { defineStore } from "pinia";
import { ref, computed } from "vue";
import { productVariantService } from "~/services/product/variant.service";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";
import { attributeService } from "~/services/attribute/attribute.service";
import type { IAttributeCatalogue, IAttribute, IVariantGroup, IProductVariant } from "~/interfaces/product/variant.interface";
import { cartesianProduct, generateDefaultSku, validateCombinationLimit, generateUniqueVariantCode } from "~/utils/variantHelpers";

export const useProductVariantStore = defineStore("productVariant", () => {
  // State
  const attributeCatalogues = ref<IAttributeCatalogue[]>([]);
  const selectedCatalogueId = ref<number | null>(null);
  const selectedAttributes = ref<number[]>([]);
  const variantGroups = ref<IVariantGroup[]>([]);
  const variants = ref<IProductVariant[]>([]);
  const loading = ref(false);
  const error = ref<string | null>(null);

  // Computed
  const selectedCatalogue = computed(() => {
    return attributeCatalogues.value.find((c) => c.id === selectedCatalogueId.value);
  });

  const canAddGroup = computed(() => {
    return selectedCatalogueId.value && selectedAttributes.value.length > 0;
  });

  const totalVariants = computed(() => variants.value.length);

  // Actions
  const loadAttributeCatalogues = async () => {
    loading.value = true;
    error.value = null;

    try {
      const response = await productVariantService.getAttributeCatalogues();
      attributeCatalogues.value = response.data || [];
    } catch (err: any) {
      error.value = err.message || "Không thể tải danh sách nhóm thuộc tính";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const loadAttributesByCatalogue = async (catalogueId: number) => {
    try {
      const response = await attributeService.getAttributesByCatalogue(catalogueId);
      const payload = response.data as any;
      const list = Array.isArray(payload) ? payload : payload?.data || [];
      const attributes = list.filter((attr: any) => (attr.attributeCatalogueId == null ? true : attr.attributeCatalogueId === catalogueId));

      const catalogue = attributeCatalogues.value.find((c) => c.id === catalogueId);
      if (catalogue) {
        catalogue.attributes = attributes;
      }
      return attributes;
    } catch (err: any) {
      error.value = err.message || "Không thể tải thuộc tính";
      throw err;
    }
  };

  const selectCatalogue = async (catalogue: IAttributeCatalogue) => {
    if (isCatalogueDisabled(catalogue.id)) return;

    selectedCatalogueId.value = catalogue.id;
    selectedAttributes.value = [];

    if (!catalogue.attributes) {
      await loadAttributesByCatalogue(catalogue.id);
    }
  };

  const isCatalogueDisabled = (catalogueId: number) => {
    return variantGroups.value.some((g) => g.catalogueId === catalogueId);
  };

  const toggleAttribute = (attribute: IAttribute) => {
    const index = selectedAttributes.value.indexOf(attribute.id);
    if (index > -1) {
      selectedAttributes.value.splice(index, 1);
    } else {
      selectedAttributes.value.push(attribute.id);
    }
  };

  const addGroupToVariants = () => {
    if (!selectedCatalogue.value || selectedAttributes.value.length === 0) return;

    const selectedAttrs = selectedCatalogue.value.attributes?.filter((attr) => selectedAttributes.value.includes(attr.id)) || [];

    const group: IVariantGroup = {
      catalogueId: selectedCatalogue.value.id,
      catalogueName: selectedCatalogue.value.name,
      attributes: selectedAttrs,
    };

    variantGroups.value.push(group);

    // Clear selection
    selectedCatalogueId.value = null;
    selectedAttributes.value = [];
  };

  const removeGroup = (catalogueId: number) => {
    const index = variantGroups.value.findIndex((g) => g.catalogueId === catalogueId);
    if (index > -1) {
      variantGroups.value.splice(index, 1);
    }
  };

  const setVariants = (newVariants: IProductVariant[]) => {
    variants.value = newVariants;
  };

  const addVariant = (variant: IProductVariant) => {
    variants.value.push(variant);
  };

  const removeVariant = (index: number) => {
    variants.value.splice(index, 1);
  };

  const updateVariant = (index: number, variant: IProductVariant) => {
    variants.value[index] = variant;
  };

  const saveVariants = async (productId: number, productSku: string) => {
    loading.value = true;
    error.value = null;

    try {
      const payload = {
        productVariants: variants.value.map((variant) => ({
          ...variant,
          productId,
        })),
      };

      const response = await productVariantService.updateProductWithVariants(productId, payload);
      return response;
    } catch (err: any) {
      error.value = err.message || "Không thể lưu biến thể";
      throw err;
    } finally {
      loading.value = false;
    }
  };

  const generateVariants = () => {
    if (variantGroups.value.length === 0) {
      variants.value = [];
      return;
    }

    // Validate combination limit
    const validation = validateCombinationLimit(variantGroups.value);
    if (!validation.isValid) {
      throw new Error(validation.message);
    }

    const newVariants = cartesianProduct(variantGroups.value);

    // Generate SKUs - will be handled by parent component
    // newVariants.forEach(variant => {
    //   if (!variant.sku && productSku) {
    //     variant.sku = generateDefaultSku(productSku, variant.code)
    //   }
    // })

    variants.value = newVariants;
  };

  const autoGenerateSkus = (productSku: string) => {
    const existingSkus = variants.value.map((v) => v.sku).filter(Boolean);

    variants.value.forEach((variant) => {
      if (!variant.sku) {
        variant.sku = generateUniqueVariantCode(existingSkus, generateDefaultSku(productSku, variant.code));
        existingSkus.push(variant.sku);
      }
    });
  };

  const clearSelection = () => {
    selectedCatalogueId.value = null;
    selectedAttributes.value = [];
  };

  const reset = () => {
    attributeCatalogues.value = [];
    selectedCatalogueId.value = null;
    selectedAttributes.value = [];
    variantGroups.value = [];
    variants.value = [];
    loading.value = false;
    error.value = null;
  };

  return {
    // State
    attributeCatalogues,
    selectedCatalogueId,
    selectedAttributes,
    variantGroups,
    variants,
    loading,
    error,

    // Computed
    selectedCatalogue,
    canAddGroup,
    totalVariants,

    // Actions
    loadAttributeCatalogues,
    loadAttributesByCatalogue,
    selectCatalogue,
    isCatalogueDisabled,
    toggleAttribute,
    addGroupToVariants,
    removeGroup,
    setVariants,
    addVariant,
    removeVariant,
    updateVariant,
    saveVariants,
    generateVariants,
    autoGenerateSkus,
    clearSelection,
    reset,
  };
});
