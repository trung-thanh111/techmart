import type { BasePayloadFilter } from "../base.interface";

export interface IUserCatalogue {
    id: number;
    name: string;
    slug: string;
    description: string | null;
    publish: number;
}

export interface Option {
    name: string;
    value: string | number | null;
}

export type CataloguePayloadFilter = BasePayloadFilter;
