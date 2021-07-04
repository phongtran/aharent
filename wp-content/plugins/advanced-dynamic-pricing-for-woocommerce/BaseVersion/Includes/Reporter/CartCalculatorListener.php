<?php

namespace ADP\BaseVersion\Includes\Reporter;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CouponInterface;
use ADP\BaseVersion\Includes\Cart\Structures\CouponCart;
use ADP\BaseVersion\Includes\Cart\Structures\Coupon;
use ADP\BaseVersion\Includes\Cart\Structures\Fee;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\External\WC\WcShippingRateFacade;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use ADP\BaseVersion\Includes\Reporter\Interfaces\Listener;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleProcessor;
use WC_Cart;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartCalculatorListener implements Listener
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $totals;

    /**
     * @var array
     */
    protected $currentTotals;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context       = $context;
        $this->totals        = array();
        $this->currentTotals = array();
    }

    /**
     * @param WC_Cart|null $wcCart
     */
    public function processStarted($wcCart)
    {
        $this->currentTotals['processStarted'] = $this->fetchWcCartData($wcCart);
    }

    /**
     * @param Cart $cart
     */
    public function cartCreated($cart)
    {

    }

    /**
     * @param Cart $cart
     */
    public function cartCompleted($cart)
    {

    }

    public function calcProcessStarted()
    {

    }

    /**
     * @param RuleProcessor $proc
     */
    public function ruleCalculated($proc)
    {
        if ( ! isset($this->currentTotals['rules'])) {
            $this->currentTotals['rules'] = array();
        }

        $this->currentTotals['rules'][] = array(
            'id'        => $proc->getRule()->getId(),
            'status'    => $proc->getStatus(),
            'exec_time' => $proc->getLastExecTime(),
        );
    }

    /**
     * @param bool $result
     */
    public function processResult($result)
    {
        $this->currentTotals['processResult'] = $result;
    }

    /**
     * @param WC_Cart $wcCart
     */
    public function processFinished($wcCart)
    {
        $this->currentTotals['processFinished'] = $this->fetchWcCartData($wcCart);
        $this->totals[]                         = $this->currentTotals;
        $this->currentTotals                    = array();
    }

    /**
     * @param WC_Cart|null $wcCart
     *
     * @return array
     */
    protected function fetchWcCartData($wcCart)
    {
        if ( ! ($wcCart instanceof WC_Cart)) {
            return array();
        }

        $cartContentsData = array();
        $totalsFacade     = new WcTotalsFacade($this->context, $wcCart);

        $couponReplacedItemRuleId = array();
        $replacedFreeItemRuleId   = array();
        $feeReplacedItemRuleId    = array();
        foreach ($totalsFacade->getSingleCoupons() as $coupon) {
            if ($coupon instanceof Coupon) {
                if ($coupon->isType($coupon::TYPE_ITEM_DISCOUNT)) {
                    $couponReplacedItemRuleId[$coupon->getRuleId()] = $coupon;
                }

                if ($coupon->isType($coupon::TYPE_FREE_ITEM)) {
                    $replacedFreeItemRuleId[$coupon->getRuleId()] = $coupon;
                }
            }
        }

        foreach ($totalsFacade->getGroupedCoupons() as $code => $coupons) {
            foreach ($coupons as $coupon) {
                if ($coupon instanceof Coupon) {
                    if ($coupon->isType($coupon::TYPE_ITEM_DISCOUNT)) {
                        $couponReplacedItemRuleId[$coupon->getRuleId()] = $coupon;
                    }

                    if ($coupon->isType($coupon::TYPE_FREE_ITEM)) {
                        $replacedFreeItemRuleId[$coupon->getRuleId()] = $coupon;
                    }
                }
            }
        }

        foreach ($totalsFacade->getFees() as $fee) {
            if ($fee->isType($fee::TYPE_ITEM_OVERPRICE)) {
                $feeReplacedItemRuleId[$fee->getRuleId()] = $fee;
            }
        }

        foreach ($wcCart->cart_contents as $key => $cart_content) {
            $cartItem = new WcCartItemFacade($this->context, $cart_content, $key);

            $clearData = $cartItem->getClearData();
            /** @var WC_Product $product */
            $product                           = $clearData[$cartItem::KEY_PRODUCT];
            $clearData[$cartItem::KEY_PRODUCT] = array(
                'id'         => $product->get_id(),
                'parent_id'  => $product->get_parent_id(),
                'name'       => $product->get_name(),
                'changes'    => $product->get_changes(),
                'price_edit' => $product->get_price(''),
                'price_view' => $product->get_price(),
            );

            $couponRepl = array();
            $feeRepl    = array();
            if ($cartItem->getHistory()) {
                $historyKeys = array_keys($cartItem->getHistory());

                if ($cartItem->isFreeItem()) {
                    foreach (array_intersect($historyKeys, array_keys($replacedFreeItemRuleId)) as $ruleId) {
                        $coupon       = $replacedFreeItemRuleId[$ruleId];
                        $couponRepl[] = array(
                            'code'   => $coupon->getCode(),
                            'type'   => $coupon->getType(),
                            'value'  => $coupon->getValue(),
                            'amount' => $wcCart->get_coupon_discount_amount($coupon->getCode(),
                                $wcCart->display_cart_ex_tax),
                            'ruleId' => $coupon->getRuleId(),
                        );
                    }
                } else {
                    foreach (array_intersect($historyKeys, array_keys($couponReplacedItemRuleId)) as $ruleId) {
                        $coupon       = $couponReplacedItemRuleId[$ruleId];
                        $couponRepl[] = array(
                            'code'   => $coupon->getCode(),
                            'type'   => $coupon->getType(),
                            'value'  => $coupon->getValue(),
                            'amount' => $wcCart->get_coupon_discount_amount($coupon->getCode(),
                                $wcCart->display_cart_ex_tax),
                            'ruleId' => $coupon->getRuleId(),
                        );
                    }
                }

                if ($cartItem->getHistory()) {
                    foreach (array_intersect($historyKeys, array_keys($feeReplacedItemRuleId)) as $ruleId) {
                        $fee       = $feeReplacedItemRuleId[$ruleId];
                        $feeRepl[] = array(
                            'name'     => $fee->getName(),
                            'type'     => $fee->getType(),
                            'value'    => $fee->getValue(),
                            'amount'   => $fee->getAmount(),
                            'taxable'  => $fee->isTaxAble(),
                            'taxClass' => $fee->getTaxClass(),
                            'ruleId'   => $fee->getRuleId(),
                        );
                    }
                }
            }

            $cartContentsData[$key] = array(
                'clear'               => $clearData,
                'third_party'         => $cartItem->getThirdPartyData(),
                'our_data'            => $cartItem->getOurData(),
                'coupon_replacements' => $couponRepl,
                'fee_replacements'    => $feeRepl,
            );
        }

        $groupedCoupons = array();
        foreach ($totalsFacade->getGroupedCoupons() as $code => $coupons) {
            $groupedCoupons[$code] = array();

            foreach ($coupons as $coupon) {
                $data = array();

                if ($coupon instanceof CouponCart) {
                    $data = array(
                        'code'   => $coupon->getCode(),
                        'type'   => $coupon->getType(),
                        'value'  => $coupon->getValue(),
                        'amount' => $wcCart->get_coupon_discount_amount(
                            $coupon->getCode(), $wcCart->display_cart_ex_tax
                        ),
                        'ruleId' => $coupon->getRuleId(),
                    );
                } elseif ($coupon instanceof Coupon) {
                    $data = array(
                        'code'         => $coupon->getCode(),
                        'type'         => $coupon->getType(),
                        'value'        => $coupon->getValue(),
                        'amount'       => $wcCart->get_coupon_discount_amount(
                            $coupon->getCode(), $wcCart->display_cart_ex_tax
                        ),
                        'ruleId'       => $coupon->getRuleId(),
                        'affectedItem' => $coupon->getAffectedCartItemKey(),
                        'affectedQty'  => $coupon->getAffectedCartItemQty(),
                    );
                }

                if ($data) {
                    $groupedCoupons[$code][] = $data;
                }
            }
        }

        if ($this->context->isDisableShippingCalculationDuringProcess()) {
            $reflection = new \ReflectionClass($wcCart);
            $property   = $reflection->getProperty('shipping_methods');
            $property->setAccessible(true);
            if ( ! ($shippingRates = $property->getValue($wcCart))) {
                $shippingRates = array();
            }
        } else {
            if ( $wcCart->show_shipping() ) {
                $shippingRates = $wcCart->calculate_shipping();
            } else {
                $shippingRates = array();
            }
        }

        return array(
            'items'    => $cartContentsData,
            'coupons'  => array(
                'applied' => $wcCart->get_applied_coupons(),
                'adp'     => array(
                    'single'  => array_filter(
                        array_map(
                            function ($coupon) use ($wcCart) {
                                /** @var CouponInterface $coupon */

                                if ($coupon instanceof CouponCart) {
                                    return array(
                                        'code'   => $coupon->getCode(),
                                        'type'   => $coupon->getType(),
                                        'value'  => $coupon->getValue(),
                                        'amount' => $wcCart->get_coupon_discount_amount(
                                            $coupon->getCode(), $wcCart->display_cart_ex_tax
                                        ),
                                        'ruleId' => $coupon->getRuleId(),
                                    );
                                } elseif ($coupon instanceof Coupon) {
                                    return array(
                                        'code'         => $coupon->getCode(),
                                        'type'         => $coupon->getType(),
                                        'value'        => $coupon->getValue(),
                                        'amount'       => $wcCart->get_coupon_discount_amount(
                                            $coupon->getCode(), $wcCart->display_cart_ex_tax
                                        ),
                                        'ruleId'       => $coupon->getRuleId(),
                                        'affectedItem' => $coupon->getAffectedCartItemKey(),
                                        'affectedQty'  => $coupon->getAffectedCartItemQty(),
                                    );
                                }

                                return null;
                            }, $totalsFacade->getSingleCoupons())),
                    'grouped' => $groupedCoupons,
                ),
            ),
            'fees'     => array(
                'applied' => json_decode(json_encode($wcCart->get_fees())),
                'adp'     => array_map(function ($fee) {
                    /** @var Fee $fee */
                    return array(
                        'name'     => $fee->getName(),
                        'type'     => $fee->getType(),
                        'value'    => $fee->getValue(),
                        'amount'   => $fee->getAmount(),
                        'taxable'  => $fee->isTaxAble(),
                        'taxClass' => $fee->getTaxClass(),
                        'ruleId'   => $fee->getRuleId(),
                    );
                }, $totalsFacade->getFees()),
            ),
            'shipping' => array(
                'packages' => $wcCart->get_shipping_packages(),
                'methods'  => array_map(function ($rate) {
                    if ( ! $rate) {
                        return null;
                    }

                    $shipping_rate = new WcShippingRateFacade($rate);
                    $cost          = (float)$shipping_rate->getRate()->get_cost();
                    $meta          = $rate->get_meta_data();
                    $original_cost = $cost;
                    $is_on_sale    = false;
                    $rules         = array();
                    $is_free       = false;

                    if ($shipping_rate->getInitialPrice()) {
                        $original_cost = $shipping_rate->getInitialPrice();
                        $is_on_sale    = true;
                    }

                    if ($shipping_rate->getAdjustments()) {
                        $rules = array_map(function ($adj) {
                            return array(
                                'ruleId' => $adj->getRuleId(),
                                'type'   => $adj->getType(),
                                'value'  => $adj->getValue(),
                                'amount' => $adj->getAmount(),
                            );
                        }, $shipping_rate->getAdjustments());
                    }

                    if ($shipping_rate->getType() === "free") {
                        $is_free = true;
                    }

                    return array(
                        'label'          => $shipping_rate->getRate()->get_label(),
                        'cost'           => $cost,
                        'original_cost'  => $original_cost,
                        'is_on_adp_sale' => $is_on_sale,
                        'rules'          => $rules,
                        'is_adp_free'    => $is_free,
                    );
                }, $shippingRates),
            ),
        );
    }

    /**
     * @param Cart $cart
     *
     * @return array
     */
    protected function fetchCartData($cart)
    {
        return array();
    }

    /**
     * @return array
     */
    public function getTotals()
    {
        return $this->totals;
    }
}
