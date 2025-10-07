export interface IProductVariant {
  id?: number;
  album: string[];
  code: string;
  stock: number;
  sku: string;
  price: number | null;
  barcode: string | null;
  attributeData: Record<string, { id: number; name: string }>;
  attributes: number[];
  publish: number;
  productId?: number;
  userId?: number;
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
