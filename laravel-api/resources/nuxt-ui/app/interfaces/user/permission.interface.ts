import type { BasePayloadFilter } from "../base.interface";

export interface IPermission {
    id: number;
    name?: string;
    module?: string;
    value?: number;
    title?: string;
    userId?: number | null;
    publish?: number;
}

export interface PermissionPayloadFilter extends BasePayloadFilter {
    module?: string | null;
}
