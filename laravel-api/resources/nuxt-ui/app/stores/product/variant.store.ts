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

    const selectedCatalogue = computed(() => {
        return attributeCatalogues.value.find((c) => c.id === selectedCatalogueId.value);
    });
    const canAddGroup = computed(() => {
        return selectedCatalogueId.value && selectedAttributes.value.length > 0;
    });
    const totalVariants = computed(() => variants.value.length);

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
        const validation = validateCombinationLimit(variantGroups.value);
        if (!validation.isValid) {
            throw new Error(validation.message);
        }
        const existingVariants = JSON.parse(JSON.stringify(variants.value || []));
        const newVariants = cartesianProduct(variantGroups.value);
        const oldGroupNames = new Set<string>();

        existingVariants.forEach((v: any) => {
            if (v.attributeData) {
                Object.keys(v.attributeData).forEach((groupName) => oldGroupNames.add(groupName));
            }
        });

        const newGroupNames = new Set<string>();
        newVariants.forEach((v) => {
            if (v.attributeData) {
                Object.keys(v.attributeData).forEach((groupName) => newGroupNames.add(groupName));
            }
        });

        const commonGroupNames = new Set([...oldGroupNames].filter((n) => newGroupNames.has(n)));
        const addedGroups = Array.from(newGroupNames).filter((n) => !oldGroupNames.has(n));
        const removedGroups = Array.from(oldGroupNames).filter((n) => !newGroupNames.has(n));

        if (existingVariants.length > 0 && addedGroups.length > 0) {
            const expandedVariants: IProductVariant[] = [];
            existingVariants.forEach((oldVariant: any) => {
                if (!oldVariant.attributeData) {
                    expandedVariants.push(oldVariant);
                    return;
                }
                const matchingNewVariants = newVariants.filter((newV) => {
                    if (!newV.attributeData) return false;
                    for (const groupName of commonGroupNames) {
                        const oldAttr = oldVariant.attributeData[groupName];
                        const newAttr = newV.attributeData[groupName];
                        if (oldAttr?.id !== newAttr?.id) return false;
                    }
                    return true;
                });
                if (matchingNewVariants.length === 0) {
                    expandedVariants.push(oldVariant);
                    return;
                }
                matchingNewVariants.forEach((matchingNew) => {
                    expandedVariants.push({
                        ...matchingNew,
                        price: Number(oldVariant?.price ?? 0),
                        stock: Number(oldVariant?.stock ?? 0),
                        barcode: oldVariant.barcode || matchingNew.barcode,
                        id: undefined,
                    });
                });
            });
            variants.value = expandedVariants;
            return;
        }
        const mergedVariants: IProductVariant[] = [];
        newVariants.forEach((newVariant) => {
            const matchingOld = existingVariants.find((oldVariant: any) => {
                if (!oldVariant.attributeData || !newVariant.attributeData) return false;
                return JSON.stringify(oldVariant.attributeData) === JSON.stringify(newVariant.attributeData);
            });

            if (matchingOld) {
                mergedVariants.push({
                    ...newVariant,
                    price: Number(matchingOld?.price ?? 0),
                    stock: Number(matchingOld?.stock ?? 0),
                    barcode: matchingOld.barcode || newVariant.barcode,
                    id: matchingOld.id,
                });
            } else {
                const defaultPrice = Number(existingVariants[0]?.price ?? 0);
                const defaultStock = Number(existingVariants[0]?.stock ?? 0);

                mergedVariants.push({
                    ...newVariant,
                    price: defaultPrice,
                    stock: defaultStock,
                });
            }
        });
        variants.value = mergedVariants;
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
