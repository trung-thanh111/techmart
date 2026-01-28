import type { IApiOk } from "~/interfaces/base.interface";
import { attributeCatalogueService } from "~/services/attribute/catalogue.service";
import { attributeService } from "~/services/attribute/attribute.service";
import { productService } from "~/services/product/product.service";

export const productVariantService = {
  getAttributeCatalogues: async (): Promise<IApiOk<any>> => {
    return await attributeCatalogueService.all();
  },

  getAttributesByCatalogue: async (catalogueId: number): Promise<IApiOk<any>> => {
    return await attributeService.getAttributesByCatalogue(catalogueId);
  },

  updateProductWithVariants: async (productId: number, payload: any): Promise<IApiOk<any>> => {
    return await productService.update(productId, payload);
  },
};
