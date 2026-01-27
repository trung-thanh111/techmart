import type { BasePayloadFilter } from "./../base.interface";

export interface IMenuItem {
    id?: number;
    parentId?: number | null;
    lft?: number | null;
    rgt?: number | null;
    level?: number | null;
    name: string;
    slug?: string;
    icon?: string | null;
    order?: number | null;
    menuId?: number | null;
    userId?: number | null;
    publish?: number;
    children?: IMenuItem[];
}

export interface IMenu {
    id?: number;
    name: string;
    slug?: string;
    position?: string;
    publish?: number;
    menuItems?: IMenuItem[];
}

export type MenuPayloadFilter = BasePayloadFilter;
