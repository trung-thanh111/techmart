<?php

namespace App\Enums;

enum Promotion: string
{
    // Promotion Types
    public const TYPE_ORDER = 'order';
    public const TYPE_PRODUCT = 'product';
    public const TYPE_FLASH_SALE = 'flash_sale';
    public const TYPE_BUNDLE = 'bundle';
    public const TYPE_BXGY = 'bxgy';
    public const TYPE_FREESHIP = 'free_ship';
    public const TYPE_VOUCHER = 'voucher';

    // Publish Status
    public const PUBLISH_DRAFT = 0;
    public const PUBLISH_PRIVATE = 1;
    public const PUBLISH_PUBLIC = 2;

    // Logic Operators
    public const LOGIC_AND = 'AND';
    public const LOGIC_OR = 'OR';

    // Rule Types
    public const RULE_MIN_ORDER_TOTAL = 'min_order_total';
    public const RULE_PRODUCT_QTY = 'product_qty';
    public const RULE_PRODUCT_QTY_BY_CATEGORY = 'product_qty_by_category';
    public const RULE_CUSTOMER_GROUP = 'customer_group';
    public const RULE_TIME_SLOT = 'time_slot';

    // Rule Operators
    public const OPERATOR_EQUALS = '=';
    public const OPERATOR_NOT_EQUALS = '!=';
    public const OPERATOR_GREATER_THAN = '>';
    public const OPERATOR_GREATER_THAN_EQUALS = '>=';
    public const OPERATOR_LESS_THAN = '<';
    public const OPERATOR_LESS_THAN_EQUALS = '<=';
    public const OPERATOR_IN = 'IN';
    public const OPERATOR_NOT_IN = 'NOT_IN';
    public const OPERATOR_BETWEEN = 'BETWEEN';

    // Action Types
    public const ACTION_PERCENT = 'percent';
    public const ACTION_FIXED = 'fixed';
    public const ACTION_FREE_SHIPPING = 'free_shipping';
    public const ACTION_BXGY = 'bxgy';
    public const ACTION_TIERED = 'tiered';

    // Action Apply To
    public const APPLY_TO_ORDER = 'order';
    public const APPLY_TO_CART_SUBTOTAL = 'cart_subtotal';
    public const APPLY_TO_SHIPPING_FEE = 'shipping_fee';
    public const APPLY_TO_CHEAPEST_ITEM = 'cheapest_item';
    public const APPLY_TO_MOST_EXPENSIVE_ITEM = 'most_expensive_item';
    public const APPLY_TO_SPECIFIC_PRODUCTS = 'specific_products';

    // Target Types
    public const TARGET_ORDER = 'order';
    public const TARGET_PRODUCT = 'product';
    public const TARGET_CATEGORY = 'category';
    public const TARGET_BRAND = 'brand';
    public const TARGET_CUSTOMER_GROUP = 'customer_group';
    public const TARGET_LOCATION = 'location';

    // Available Types
    public const AVAILABLE_TYPES = [
        self::TYPE_ORDER,
        self::TYPE_PRODUCT,
        self::TYPE_FLASH_SALE,
        self::TYPE_BUNDLE,
        self::TYPE_BXGY,
        self::TYPE_FREESHIP,
        self::TYPE_VOUCHER,
    ];

    // Available Rule Types
    public const AVAILABLE_RULE_TYPES = [
        self::RULE_MIN_ORDER_TOTAL,
        self::RULE_PRODUCT_QTY,
        self::RULE_PRODUCT_QTY_BY_CATEGORY,
        self::RULE_CUSTOMER_GROUP,
        self::RULE_TIME_SLOT,
    ];

    // Available Rule Operators
    public const AVAILABLE_RULE_OPERATORS = [
        self::OPERATOR_EQUALS,
        self::OPERATOR_NOT_EQUALS,
        self::OPERATOR_GREATER_THAN,
        self::OPERATOR_GREATER_THAN_EQUALS,
        self::OPERATOR_LESS_THAN,
        self::OPERATOR_LESS_THAN_EQUALS,
        self::OPERATOR_IN,
        self::OPERATOR_NOT_IN,
        self::OPERATOR_BETWEEN,
    ];

    // Available Action Types
    public const AVAILABLE_ACTION_TYPES = [
        self::ACTION_PERCENT,
        self::ACTION_FIXED,
        self::ACTION_FREE_SHIPPING,
        self::ACTION_BXGY,
        self::ACTION_TIERED,
    ];

    // Available Apply To
    public const AVAILABLE_APPLY_TO = [
        self::APPLY_TO_ORDER,
        self::APPLY_TO_CART_SUBTOTAL,
        self::APPLY_TO_SHIPPING_FEE,
        self::APPLY_TO_CHEAPEST_ITEM,
        self::APPLY_TO_MOST_EXPENSIVE_ITEM,
        self::APPLY_TO_SPECIFIC_PRODUCTS,
    ];

    // Available Target Types
    public const AVAILABLE_TARGET_TYPES = [
        self::TARGET_ORDER,
        self::TARGET_PRODUCT,
        self::TARGET_CATEGORY,
        self::TARGET_BRAND,
        self::TARGET_CUSTOMER_GROUP,
        self::TARGET_LOCATION,
    ];

    // Available Publish Status
    public const AVAILABLE_PUBLISH_STATUS = [
        self::PUBLISH_DRAFT,
        self::PUBLISH_PRIVATE,
        self::PUBLISH_PUBLIC,
    ];

    // Available Logic Operators
    public const AVAILABLE_LOGIC_OPERATORS = [
        self::LOGIC_AND,
        self::LOGIC_OR,
    ];
}
