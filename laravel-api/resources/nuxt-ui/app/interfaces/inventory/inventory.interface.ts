import type { BasePayloadFilter } from "../base.interface";

export interface IInventory {
    id: number;
    productVariantId: number;
    productVariant?: {
        id: number;
        name: string;
        productId: number;
    };
    agency?: {
        id: number;
        name: string;
    };
    agencyId: number;
    stock: number;
    priceOverride?: number;
    publish?: number;
}

export interface Option {
    name: string;
    value: string | null;
}

export type InventoryPayloadFilter = BasePayloadFilter;
