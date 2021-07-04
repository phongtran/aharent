<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\Reporter\Interfaces\Listener;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleProcessor;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartCalculator
{
    /**
     * @var RulesCollection
     */
    protected $ruleCollection;
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Listener
     */
    public $listener;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     * @param RulesCollection $ruleCollection
     * @param Listener|null $listener
     */
    public function __construct($context, $ruleCollection, $listener = null)
    {
        $this->context        = $context;
        $this->ruleCollection = $ruleCollection;
        $this->listener       = $listener;
    }

    /**
     * @param Context $context
     * @param Listener|null $listener
     *
     * @return self
     */
    public static function make($context, $listener = null)
    {
        return new static($context, CacheHelper::loadActiveRules($context), $listener);
    }

    public function getRulesCollection()
    {
        return $this->ruleCollection;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function processCart(&$cart)
    {
        if ($cart->isEmpty()) {
            return false;
        }

        if ($this->listener) {
            $this->listener->calcProcessStarted();
        }

        $appliedRules = 0;

        foreach ($this->ruleCollection->getRules() as $rule) {
            $proc = $rule->buildProcessor($this->context);
            if ($proc->applyToCart($cart)) {
                $appliedRules++;
            }

            $this->announceRuleCalculated($proc);
        }

        $result = boolval($appliedRules);

        if ($result) {
            if ('compare_discounted_and_sale' === $this->context->getOption('discount_for_onsale')) {
                $newItems = array();
                foreach ($cart->getItems() as $item) {
                    $productPrice = $item->getOriginalPrice();
                    foreach ($item->getDiscounts() as $ruleId => $amounts) {
                        $productPrice -= array_sum($amounts);
                    }
                    if ($this->context->getOption('is_calculate_based_on_wc_precision')) {
                        $productPrice = round($productPrice, wc_get_price_decimals());
                    }

                    $product     = $item->getWcItem()->getProduct();
                    $wcSalePrice = null;

                    /** Always remember about scheduled WC sales */
                    if ($product->is_on_sale('edit') && $product->get_sale_price('edit') !== '') {
                        $wcSalePrice = floatval($product->get_sale_price('edit'));
                    }

                    if ( ! is_null($wcSalePrice) && $wcSalePrice < $productPrice) {
                        $newItem = new CartItem($item->getWcItem(), $wcSalePrice, $item->getQty(), $item->getPos());

                        foreach ($item->getAttrs() as $attr) {
                            $newItem->addAttr($attr);
                        }

                        foreach ($item->getMarks() as $mark) {
                            $newItem->addMark($mark);
                        }

                        $minDiscountRangePrice = $item->getMinDiscountRangePrice();
                        if ($minDiscountRangePrice !== null ) {
                            $minDiscountRangePrice = $minDiscountRangePrice < $wcSalePrice ? $minDiscountRangePrice : $wcSalePrice;
                            $newItem->setMinDiscountRangePrice($minDiscountRangePrice);
                        }

                        $item = $newItem;
                    }

                    $newItems[] = $item;
                }

                $cart->setItems($newItems);
            } elseif ('discount_regular' === $this->context->getOption('discount_for_onsale')) {
                $newItems = array();
                foreach ($cart->getItems() as $item) {
                    $product     = $item->getWcItem()->getProduct();
                    $wcSalePrice = null;

                    /** Always remember about scheduled WC sales */
                    if ($product->is_on_sale('edit') && $product->get_sale_price('edit') !== '') {
                        $wcSalePrice = floatval($product->get_sale_price('edit'));
                    }

                    if ( ! is_null($wcSalePrice) && count($item->getHistory()) == 0) {
                        $newItem = new CartItem($item->getWcItem(), $wcSalePrice, $item->getQty(), $item->getPos());

                        foreach ($item->getAttrs() as $attr) {
                            $newItem->addAttr($attr);
                        }

                        foreach ($item->getMarks() as $mark) {
                            $newItem->addMark($mark);
                        }

                        $minDiscountRangePrice = $item->getMinDiscountRangePrice();
                        if ($minDiscountRangePrice !== null ) {
                            $newItem->setMinDiscountRangePrice($minDiscountRangePrice);
                        }

                        $item = $newItem;
                    }

                    $newItems[] = $item;
                }

                $cart->setItems($newItems);
            }
        }

        if ($this->listener) {
            $this->listener->processResult($result);
        }

        return $result;
    }

    /**
     * @param RuleProcessor $proc
     */
    protected function announceRuleCalculated($proc)
    {
        if ($this->listener) {
            $this->listener->ruleCalculated($proc);
        }
    }
}
