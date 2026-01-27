import type { IApiOk } from "~/interfaces/base.interface";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";
import { attributeService } from "~/services/attribute/attribute.service";
import { productService } from "~/services/product/product.service";

export const productVariantService = {
    /**
     * Get attribute catalogues with attributes
     */
    getAttributeCatalogues: async (): Promise<IApiOk<any>> => {
        return await attributeCatalogueService.all();
    },

    /**
     * Get attributes by catalogue ID
     */
    getAttributesByCatalogue: async (catalogueId: number): Promise<IApiOk<any>> => {
        // Gọi API với filter theo attribute_catalogue_id để BE lọc sẵn
        return await attributeService.pagination({ type: "all", attribute_catalogue_id: catalogueId });
    },

    /**
     * Update product with variants
     */
    updateProductWithVariants: async (productId: number, payload: any): Promise<IApiOk<any>> => {
        return await productService.update(productId, payload);
    },
};
