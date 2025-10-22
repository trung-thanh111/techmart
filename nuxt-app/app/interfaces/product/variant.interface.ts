export interface IProductVariant {
  id?: number;
  name?: string;
  url?: string | null;
  code?: string | null;
  stock?: number | null;
  sku?: string | null;
  price?: number | null;
  barcode?: string | null;
  attributeData?: Record<string, any> | null;
  attributes?: number[];
  productId?: number | null;
  publish?: number | null;
}

export interface IAttribute {
  id: number;
  name: string;
  slug?: string;
  attribute_catalogue_id?: number;
}

export interface IAttributeCatalogue {
  id: number;
  name: string;
  slug: string;
  attributes?: IAttribute[];
}

export interface IVariantGroup {
  catalogueId: number;
  catalogueName: string;
  attributes: IAttribute[];
}
