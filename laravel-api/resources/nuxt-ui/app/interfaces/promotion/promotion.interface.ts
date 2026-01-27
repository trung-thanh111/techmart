import type { BasePayloadFilter } from "../base.interface";

export interface IPromotionRule {
    id?: number;
    promotionId?: number;
    ruleGroup?: number;
    logicOperator?: string;
    ruleType?: string;
    operator?: string;
    value?: any;
    description?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface IPromotionAction {
    id?: number;
    promotionId?: number;
    executionOrder?: number;
    actionType?: string;
    actionValue?: number | null;
    maxDiscount?: number | null;
    minDiscount?: number | null;
    applyTo?: string;
    config?: Record<string, any> | null;
    createdAt?: string;
    updatedAt?: string;
}

export interface IPromotionTarget {
    id?: number;
    promotionId?: number;
    targetType?: string;
    targetId?: number | null;
    isIncluded?: boolean;
    metadata?: Record<string, any> | null;
    createdAt?: string;
    updatedAt?: string;
}

export interface IPromotionUsage {
    id?: number;
    promotionId?: number;
    userId?: number;
    orderId?: number;
    discountAmount?: number;
    usedAt?: string;
    createdAt?: string;
    updatedAt?: string;
}

export interface IPromotion {
    id?: number;
    name?: string;
    code?: string | null;
    slug?: string;
    description?: string | null;
    image?: string | null;
    type?: string;
    priority?: number;
    promotionCatalogueId?: number | null;
    canCombine?: boolean;
    combinableWith?: number[] | null;
    usageLimit?: number | null;
    usagePerCustomer?: number | null;
    usedCount?: number;
    startDate?: string | null;
    endDate?: string | null;
    publish?: number;
    remainingUsage?: number | null;
    // Relations
    promotionCatalogue?: IPromotionCatalogue | null;
    promotionRules?: IPromotionRule[];
    promotionActions?: IPromotionAction[];
    promotionTargets?: IPromotionTarget[];
    promotionUsages?: IPromotionUsage[];
    createdAt?: string;
    updatedAt?: string;
}

// Catalogue interface
export interface IPromotionCatalogue {
    id?: number;
    name?: string;
    slug?: string;
    description?: string | null;
    parentId?: number | null;
    publish?: number;
    createdAt?: string;
    updatedAt?: string;
}

// Filter interface
export interface PromotionPayloadFilter extends BasePayloadFilter {
    promotion_catalogue_id?: number | null;
    type?: string | null;
    publish?: number | null;
    start_date?: string | null;
    end_date?: string | null;
    priority?: number | null;
}

// Form data interfaces for create/update
export interface PromotionRuleForm {
    ruleGroup: number;
    logicOperator: string;
    ruleType: string;
    operator: string;
    value: any;
    description?: string;
}

export interface PromotionActionForm {
    executionOrder: number;
    actionType: string;
    actionValue?: number | null;
    maxDiscount?: number | null;
    minDiscount?: number | null;
    applyTo: string;
    config?: Record<string, any> | null;
}

export interface PromotionTargetForm {
    targetType: string;
    targetId?: number | null;
    isIncluded: boolean;
    metadata?: Record<string, any> | null;
}

export interface PromotionForm {
    name: string;
    code?: string | null;
    slug: string;
    description?: string | null;
    image?: string | null;
    type: string;
    priority?: number;
    promotionCatalogueId?: number | null;
    canCombine?: boolean;
    combinableWith?: number[] | null;
    usageLimit?: number | null;
    usagePerCustomer?: number | null;
    startDate?: string | null;
    endDate?: string | null;
    publish: number;
    rules?: PromotionRuleForm[];
    actions?: PromotionActionForm[];
    targets?: PromotionTargetForm[];
}
