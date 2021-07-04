<?php

namespace ADP\BaseVersion\Includes\Rule\CartAdjustments;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CouponCart;
use ADP\BaseVersion\Includes\Rule\CartAdjustmentsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\CartAdjUsingCollection;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\CouponCartAdj;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class DiscountAmountRepeatableSetsCount extends AbstractCartAdjustment implements CouponCartAdj, CartAdjustment, CartAdjUsingCollection
{
    /**
     * @var float
     */
    protected $couponValue;

    /**
     * @var string
     */
    protected $couponCode;

    public static function getType()
    {
        return 'discount_repeatable_sets_count__amount';
    }

    public static function getLabel()
    {
        return __('Add fixed discount to each item line affected by rule', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public static function getTemplatePath()
    {
        return WC_ADP_PLUGIN_VIEWS_PATH . 'cart_adjustments/discount.php';
    }

    public static function getGroup()
    {
        return CartAdjustmentsLoader::GROUP_DISCOUNT;
    }

    public function __construct()
    {
        $this->amountIndexes = array('couponValue');
    }

    /**
     * @param float|string $couponValue
     */
    public function setCouponValue($couponValue)
    {
        $this->couponValue = $couponValue;
    }

    /**
     * @param string $couponCode
     */
    public function setCouponCode($couponCode)
    {
        $this->couponCode = $couponCode;
    }

    public function getCouponValue()
    {
        return $this->couponValue;
    }

    public function getCouponCode()
    {
        return $this->couponCode;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return isset($this->couponValue) or isset($this->couponCode);
    }

    /**
     * @param Rule $rule
     * @param Cart $cart
     */
    public function applyToCart($rule, $cart)
    {
    }

    /**
     * @param Rule $rule
     * @param Cart $cart
     * @param CartItemsCollection $itemsCollection
     */
    public function applyToCartWithItems($rule, $cart, $itemsCollection)
    {
        $context = $cart->getContext()->getGlobalContext();

        for ($i = 0; $i < $itemsCollection->getCount(); $i++) {
            $cart->addCoupon(new CouponCart($context, CouponCart::TYPE_FIXED_VALUE, $this->couponCode,
                $this->couponValue, $rule->getId()));
        }
    }

    /**
     * @param Rule $rule
     * @param Cart $cart
     * @param CartSetCollection $setCollection
     */
    public function applyToCartWithSets($rule, $cart, $setCollection)
    {
        $context = $cart->getContext()->getGlobalContext();

        for ($i = 0; $i < count($setCollection->getSets()); $i++) {
            $cart->addCoupon(new CouponCart($context, CouponCart::TYPE_FIXED_VALUE, $this->couponCode,
                $this->couponValue, $rule->getId()));
        }
    }
}
