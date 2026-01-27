import type { BasePayloadFilter } from "../base.interface";

export interface IPostTag {
    id: number;
    name?: string;
    slug?: string;
    description?: string;
    color?: string;
    publish?: number;
    isHot?: boolean;
}

export interface Option {
    name: string;
    value: string | null;
}

export type TagPayloadFilter = BasePayloadFilter;
