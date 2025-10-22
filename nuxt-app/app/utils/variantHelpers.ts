import type { IAttribute, IAttributeCatalogue, IVariantGroup, IProductVariant } from "~/interfaces/product/variant.interface";

export type Attribute = IAttribute;
export type AttributeCatalogue = IAttributeCatalogue;
export type VariantGroup = IVariantGroup;
export type ProductVariant = IProductVariant;

/**
 * Generate cartesian product of all attribute combinations
 */
export function cartesianProduct(groups: VariantGroup[]): ProductVariant[] {
  if (groups.length === 0) return [];

  // Get all combinations using cartesian product
  const combinations = groups.reduce((acc, group) => {
    if (acc.length === 0) {
      return group.attributes.map((attr) => [attr]);
    }
    const result: Attribute[][] = [];
    acc.forEach((combination) => {
      group.attributes.forEach((attr) => {
        result.push([...combination, attr]);
      });
    });
    return result;
  }, [] as Attribute[][]);

  // Convert to variant objects
  return combinations.map((combination) => {
    const attributeData: Record<string, { id: number; name: string }> = {};
    const attributeIds: number[] = [];

    // Group attributes by catalogue
    groups.forEach((group) => {
      const attr = combination.find((a) => group.attributes.some((ga) => ga.id === a.id));
      if (attr) {
        attributeData[group.catalogueName] = {
          id: attr.id,
          name: attr.name,
        };
        attributeIds.push(attr.id);
      }
    });

    const code = generateCodeFromIds(attributeIds);

    return {
      code,
      stock: 0,
      sku: "",
      price: 0,
      barcode: null,
      attributeData,
      attributes: attributeIds,
      publish: 2,
    };
  });
}

/**
 * Generate code from attribute IDs
 */
export function generateCodeFromIds(ids: number[]): string {
  return ids.sort((a, b) => a - b).join("_");
}

/**
 * Generate default SKU for variant
 */
export function generateDefaultSku(productSku: string, code: string): string {
  return `${productSku}-${code}`;
}

/**
 * Validate variant combinations limit
 */
export function validateCombinationLimit(
  groups: VariantGroup[],
  maxVariants = 300
): {
  isValid: boolean;
  totalCombinations: number;
  message?: string;
} {
  let totalCombinations = 1;

  for (const group of groups) {
    totalCombinations *= group.attributes.length;
    if (totalCombinations > maxVariants) {
      return {
        isValid: false,
        totalCombinations,
        message: `Số lượng biến thể vượt quá giới hạn (${maxVariants}). Hiện tại: ${totalCombinations} biến thể. Vui lòng giảm số nhóm thuộc tính hoặc số giá trị trong mỗi nhóm.`,
      };
    }
  }

  return {
    isValid: true,
    totalCombinations,
  };
}

/**
 * Generate preview text for attribute data
 */
export function generateAttributeDataPreview(attributeData: Record<string, { id: number; name: string }>): string {
  return Object.entries(attributeData)
    .map(([groupName, attr]) => `${groupName}: ${attr.name}`)
    .join(", ");
}

/**
 * Check if variant has been modified from default
 */
export function isVariantModified(variant: ProductVariant, originalVariant?: ProductVariant): boolean {
  if (!originalVariant) return false;

  return variant.sku !== originalVariant.sku || variant.price !== originalVariant.price || variant.stock !== originalVariant.stock || variant.barcode !== originalVariant.barcode;
}

/**
 * Generate unique code for variant
 */
export function generateUniqueVariantCode(existingCodes: string[], baseCode: string): string {
  let counter = 1;
  let uniqueCode = baseCode;

  while (existingCodes.includes(uniqueCode)) {
    uniqueCode = `${baseCode}_${counter}`;
    counter++;
  }

  return uniqueCode;
}
