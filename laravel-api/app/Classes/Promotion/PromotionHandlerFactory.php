<?php

declare(strict_types=1);

namespace App\Classes\Promotion;

use App\Classes\Promotion\Actions\FixedAmountAction;
use App\Classes\Promotion\Actions\PercentDiscountAction;
use App\Classes\Promotion\Actions\FreeShippingAction;
use App\Classes\Promotion\Actions\PromotionActionHandler;
use App\Classes\Promotion\Rules\MinOrderTotalRule;
use App\Classes\Promotion\Rules\ProductQtyRule;
use App\Classes\Promotion\Rules\TimeSlotRule;
use App\Classes\Promotion\Rules\PromotionRuleHandler;
use App\Enums\Promotion as PromotionEnum;
use InvalidArgumentException;

class PromotionHandlerFactory
{
    /**
     * @var array
     */
    protected static array $ruleHandlers = [
        PromotionEnum::RULE_MIN_ORDER_TOTAL => MinOrderTotalRule::class,
        PromotionEnum::RULE_PRODUCT_QTY => ProductQtyRule::class,
        PromotionEnum::RULE_TIME_SLOT => TimeSlotRule::class,
    ];

    /**
     * @var array
     */
    protected static array $actionHandlers = [
        PromotionEnum::ACTION_PERCENT => PercentDiscountAction::class,
        PromotionEnum::ACTION_FIXED => FixedAmountAction::class,
        PromotionEnum::ACTION_FREE_SHIPPING => FreeShippingAction::class,
    ];

    /**
     * Get rule handler instance.
     *
     * @param string $type
     * @return PromotionRuleHandler
     */
    public function getRuleHandler(string $type): PromotionRuleHandler
    {
        if (!isset(self::$ruleHandlers[$type])) {
            throw new InvalidArgumentException("No handler registered for rule type: {$type}");
        }

        $handlerClass = self::$ruleHandlers[$type];
        return new $handlerClass();
    }

    /**
     * Get action handler instance.
     *
     * @param string $type
     * @return PromotionActionHandler
     */
    public function getActionHandler(string $type): PromotionActionHandler
    {
        if (!isset(self::$actionHandlers[$type])) {
            throw new InvalidArgumentException("No handler registered for action type: {$type}");
        }

        $handlerClass = self::$actionHandlers[$type];
        return new $handlerClass();
    }

    /**
     * Register a new rule handler.
     *
     * @param string $type
     * @param string $handlerClass
     */
    public static function registerRuleHandler(string $type, string $handlerClass): void
    {
        self::$ruleHandlers[$type] = $handlerClass;
    }

    /**
     * Register a new action handler.
     *
     * @param string $type
     * @param string $handlerClass
     */
    public static function registerActionHandler(string $type, string $handlerClass): void
    {
        self::$actionHandlers[$type] = $handlerClass;
    }
}
