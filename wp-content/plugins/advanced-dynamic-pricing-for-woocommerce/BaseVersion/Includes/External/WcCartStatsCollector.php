<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Cart\Structures\ShippingAdjustment;
use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use \ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use WC_Cart;
use WC_Shipping_Rate;
use WooCommerce;
use function WC;

if ( ! defined('ABSPATH')) {
    exit;
}

class WcCartStatsCollector
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function setActionCheckoutOrderProcessed()
    {
        add_action('woocommerce_checkout_order_processed', array($this, 'checkoutOrderProcessed'), 10, 3);
    }

    public function setActionCheckoutOrderProcessedDuringRestApi() {
        add_action( 'woocommerce_order_after_calculate_totals', array($this, 'afterOrderCalculateTotalsDuringRestApi'), 10, 2 );
    }

    public function unsetActionCheckoutOrderProcessed()
    {
        remove_action('woocommerce_checkout_order_processed', array($this, 'checkoutOrderProcessed'), 10);
    }

    public function unsetActionCheckoutOrderProcessedDuringRestApi() {
        remove_action('woocommerce_order_after_calculate_totals', array($this, 'afterOrderCalculateTotalsDuringRestApi'), 10);
    }

    public function afterOrderCalculateTotalsDuringRestApi($andTaxes, \WC_Order $order) {
        if ( ! isset(WC()->cart)) {
            return;
        }

        $orderId = $order->get_id();

        list($orderStats, $productStats) = $this->collectWcCartStats(WC());

        $order_date = current_time('mysql');

        foreach ($orderStats as $ruleId => $statsItem) {
            $statsItem = array_merge(array(
                'order_id'         => $orderId,
                'rule_id'          => $ruleId,
                'amount'           => 0,
                'extra'            => 0,
                'shipping'         => 0,
                'is_free_shipping' => 0,
                'gifted_amount'    => 0,
                'gifted_qty'       => 0,
                'date'             => $order_date,
            ), $statsItem);
            Database::addOrderStats($statsItem);
        }

        foreach ($productStats as $product_id => $by_rule) {
            foreach ($by_rule as $ruleId => $statsItem) {
                $statsItem = array_merge(array(
                    'order_id'      => $orderId,
                    'product_id'    => $product_id,
                    'rule_id'       => $ruleId,
                    'qty'           => 0,
                    'amount'        => 0,
                    'gifted_amount' => 0,
                    'gifted_qty'    => 0,
                    'date'          => $order_date,
                ), $statsItem);

                Database::addProductStats($statsItem);
            }
        }
    }

    public function checkoutOrderProcessed($orderId, $postedData, \WC_Order $order)
    {
        if ( ! isset(WC()->cart)) {
            return;
        }

        list($orderStats, $productStats) = $this->collectWcCartStats(WC());

        $order_date = current_time('mysql');

        foreach ($orderStats as $ruleId => $statsItem) {
            $statsItem = array_merge(array(
                'order_id'         => $orderId,
                'rule_id'          => $ruleId,
                'amount'           => 0,
                'extra'            => 0,
                'shipping'         => 0,
                'is_free_shipping' => 0,
                'gifted_amount'    => 0,
                'gifted_qty'       => 0,
                'date'             => $order_date,
            ), $statsItem);
            Database::addOrderStats($statsItem);
        }

        foreach ($productStats as $product_id => $by_rule) {
            foreach ($by_rule as $ruleId => $statsItem) {
                $statsItem = array_merge(array(
                    'order_id'      => $orderId,
                    'product_id'    => $product_id,
                    'rule_id'       => $ruleId,
                    'qty'           => 0,
                    'amount'        => 0,
                    'gifted_amount' => 0,
                    'gifted_qty'    => 0,
                    'date'          => $order_date,
                ), $statsItem);

                Database::addProductStats($statsItem);
            }
        }
    }

    /**
     * @param WooCommerce $wc
     *
     * @return array
     */
    private function collectWcCartStats(WooCommerce $wc)
    {
        $orderStats   = array();
        $productStats = array();

        $wcCart = $wc->cart;

        $cartItems = $wcCart->get_cart();
        foreach ($cartItems as $cartKey => $cartItem) {
            $itemFacade = new WcCartItemFacade($this->context, $cartItem, $cartKey);
            $rules      = $itemFacade->getDiscounts();

            if (empty($rules)) {
                continue;
            }

            $productId = $itemFacade->getProductId();
            foreach ($rules as $ruleId => $amounts) {
                $amount = is_array($amounts) ? array_sum($amounts) : $amounts;
                //add stat rows
                if ( ! isset($orderStats[$ruleId])) {
                    $orderStats[$ruleId] = array(
                        'amount'           => 0,
                        'qty'              => 0,
                        'gifted_qty'       => 0,
                        'gifted_amount'    => 0,
                        'shipping'         => 0,
                        'is_free_shipping' => 0,
                        'extra'            => 0
                    );
                }
                if ( ! isset($productStats[$productId][$ruleId])) {
                    $productStats[$productId][$ruleId] = array(
                        'amount'        => 0,
                        'qty'           => 0,
                        'gifted_qty'    => 0,
                        'gifted_amount' => 0
                    );
                }

                if ($itemFacade->isFreeItem()) {
                    $prefix = 'gifted_';
                } else {
                    $prefix = '';
                }
                // order
                $orderStats[$ruleId][$prefix . 'qty']    += $itemFacade->getQty();
                $orderStats[$ruleId][$prefix . 'amount'] += $amount * $itemFacade->getQty();
                // product
                $productStats[$productId][$ruleId][$prefix . 'qty']    += $itemFacade->getQty();
                $productStats[$productId][$ruleId][$prefix . 'amount'] += $amount * $itemFacade->getQty();
            }
        }

        $this->injectWcCartCouponStats($wcCart, $orderStats);
        $this->injectWcCartFeeStats($wcCart, $orderStats);
        $this->injectWcCartShippingStats($wc, $orderStats);

        return array($orderStats, $productStats);
    }

    /**
     * @param WC_Cart $wcCart
     * @param array $orderStats
     */
    private function injectWcCartCouponStats($wcCart, array &$orderStats)
    {
        $totalsFacade = new WcTotalsFacade($this->context, $wcCart);

        $singleCoupons  = $totalsFacade->getSingleCoupons();
        $groupedCoupons = $totalsFacade->getGroupedCoupons();

        if ( ! $singleCoupons && ! $groupedCoupons) {
            return;
        }

        foreach ($wcCart->get_coupon_discount_totals() as $couponCode => $amount) {
            if (isset($groupedCoupons[$couponCode])) {
                foreach ($groupedCoupons[$couponCode] as $coupon) {
                    $ruleId = $coupon->getRuleId();
                    $value  = $coupon->getValue();

                    if ( ! isset($orderStats[$ruleId])) {
                        $orderStats[$ruleId] = array();
                    }

                    if ( ! isset($orderStats[$ruleId]['extra'])) {
                        $orderStats[$ruleId]['extra'] = 0.0;
                    }

                    $orderStats[$ruleId]['extra'] += $value;
                }
            } elseif (isset($singleCoupons[$couponCode])) {
                $coupon = $singleCoupons[$couponCode];
                $ruleId = $coupon->getRuleId();

                if ( ! isset($orderStats[$ruleId])) {
                    $orderStats[$ruleId] = array();
                }

                if ( ! isset($orderStats[$ruleId]['extra'])) {
                    $orderStats[$ruleId]['extra'] = 0.0;
                }

                $orderStats[$ruleId]['extra'] += $amount;
            }
        }
    }

    /**
     * @param WC_Cart $wcCart
     * @param array $orderStats
     */
    private function injectWcCartFeeStats($wcCart, array &$orderStats)
    {
        $totalsFacade = new WcTotalsFacade($this->context, $wcCart);

        $fees = $totalsFacade->getFees();

        if ( ! $fees) {
            return;
        }

        foreach ($fees as $fee) {
            $ruleId = $fee->getRuleId();

            if ( ! isset($orderStats[$ruleId])) {
                $orderStats[$ruleId] = array();
            }

            if ( ! isset($orderStats[$ruleId]['extra'])) {
                $orderStats[$ruleId]['extra'] = 0.0;
            }

            $orderStats[$ruleId]['extra'] -= $fee->getAmount();
        }
    }

    /**
     * @param WooCommerce $wc
     * @param array $orderStats
     */
    private function injectWcCartShippingStats(WooCommerce $wc, array &$orderStats)
    {
        $shippings = $wc->session->get('chosen_shipping_methods');
        if (empty($shippings)) {
            return;
        }

        $appliedRulesKey = 'adp_adjustments';

        foreach ($shippings as $packageId => $shippingRateKey) {
            $packages = $wc->shipping()->get_packages();
            if (isset($packages[$packageId]['rates'][$shippingRateKey])) {
                /** @var WC_Shipping_Rate $shRate */
                $shRate     = $packages[$packageId]['rates'][$shippingRateKey];
                $shRateMeta = $shRate->get_meta_data();

                $isFreeShipping = isset($shRateMeta['adp_type']) && $shRateMeta['adp_type'] === "free"; //notice
                $adpRules       = isset($shRateMeta[$appliedRulesKey]) ? $shRateMeta[$appliedRulesKey] : false;

                if ( ! empty($adpRules) && is_array($adpRules)) {
                    foreach ($adpRules as $rule) {
                        /**
                         * @var ShippingAdjustment $rule
                         */
                        $ruleId = $rule->getRuleId();
                        $amount = $rule->getAmount();
                        if ( ! isset($orderStats[$ruleId])) {
                            $orderStats[$ruleId] = array();
                        }

                        if ( ! isset($orderStats[$ruleId]['shipping'])) {
                            $orderStats[$ruleId]['shipping'] = 0.0;
                        }

                        $orderStats[$ruleId]['shipping']         += $amount;
                        $orderStats[$ruleId]['is_free_shipping'] = $isFreeShipping;
                    }
                }

            }
        }
    }
}
