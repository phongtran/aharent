<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Cart\CartProcessor;
use ADP\BaseVersion\Includes\External\Cmp\WcSubscriptionsCmp;
use ADP\BaseVersion\Includes\External\WC\WcCartItemDisplayExtensions;
use ADP\BaseVersion\Includes\Product\Processor;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Reporter\CalculationProfiler;
use ADP\Factory;
use WC_Cart;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Engine
{
    /**
     * @var CartProcessor
     */
    protected $cartProcessor;

    /**
     * @var Processor
     */
    protected $productProcessor;

    /**
     * @var PriceDisplay
     */
    protected $priceDisplay;

    /**
     * @var CalculationProfiler
     */
    protected $profiler;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var FreeItemsController
     */
    protected $freeItemsController;

    /**
     * @var WcCartItemDisplayExtensions
     */
    protected $cartItemDisplayExtensions;

    /**
     * @param Context $context
     * @param WC_Cart|null $wcCart Can be null! e.g. during REST API requests
     */
    public function __construct($context, $wcCart)
    {
        $this->context = $context;

        $this->cartProcessor    = Factory::get('Cart_CartProcessor', $context, $wcCart);
        $this->productProcessor = new Processor($context);

        $this->priceDisplay              = Factory::get('External_PriceDisplay', $context, $this->productProcessor);
        $this->cartItemDisplayExtensions = Factory::get('External_WC_WcCartItemDisplayExtensions', $context);
        $this->profiler                  = Factory::get("Reporter_CalculationProfiler", $this->context,
            $this->cartProcessor,
            $this->productProcessor);

        $this->freeItemsController = Factory::get('External_FreeItemsController', $this->context);
        $this->freeItemsController->installHooks();
    }

    /**
     * Install main hooks.
     *
     * We start processing the cart at 'wp_loaded'. It is obvious.
     *
     * 'Coupon, Fee and Shipping Rate' hooks are required, because we do not want to lost our adjustments,
     * after the 3rd party calls WC_Cart->calculate_totals().
     * @see \WC_Cart::calculate_totals()
     *
     * So, we always process Coupons, Fees and Shipping Rates, but the price change is controlled
     * by the internal adjustments, which are updated only after $this->process()
     * @see CartProcessor::process()
     * @see CartProcessor::applyTotals()
     *
     *
     * get_cart_from_session - 10
     * @see WC_Cart_Session::get_cart_from_session()
     *
     * wc form handle - 20
     * @see WC_Form_Handler
     */
    public function installCartProcessAction()
    {
        add_action('wp_loaded', array($this, 'firstTimeProcessCart'), 15);
        $this->cartProcessor->installActionFirstProcess();
    }

    public function installProductProcessorWithEmptyCart()
    {
        $this->process(true);
    }

    public function firstTimeProcessCart()
    {
        /**
         * Force "yes" value for option Woocommerce->Settings->Tax->Round tax at subtotal level, instead of rounding per line
         * Sometimes subtotal rounds up incorrectly
         *
         * e.g.
         * Rule: 3 items for 29 (fixed price)
         * Cart: 3 items with costs: 64, 45, 45
         */
        if ($this->context->getOption('is_calculate_based_on_wc_precision')) {
            add_filter("pre_option_woocommerce_tax_round_at_subtotal", function ($pre, $option, $default) {
                return 'yes';
            }, 10, 3);
        }

        $this->process(true);

        $hookPriority = intval(apply_filters('wdp_calculate_totals_hook_priority', PHP_INT_MAX));
        add_action('woocommerce_after_calculate_totals', array($this, 'afterCalculateTotals'), $hookPriority);

        /**
         * Force checkout page context
         */
        add_action('woocommerce_checkout_process', function () {
            $context = $this->context;
            $context->setProps(array($context::WC_CHECKOUT_PAGE => true));
            $this->process();
        }, PHP_INT_MAX);

        /**
         * During 'wc-ajax=update_order_review' we change context to CHECKOUT page.
         * Condition 'cart payment method' works only at checkout page.
         */
        add_action('woocommerce_checkout_update_order_review', function () {
            $context = $this->context;
            $context->setProps(array($context::WC_CHECKOUT_PAGE => true));
        }, PHP_INT_MAX);
    }

    public function process($first = false)
    {
        $cart = $this->cartProcessor->process($first);
        $this->productProcessor->withCart($cart);
        $this->priceDisplay->initHooks();
        $this->cartItemDisplayExtensions->register();
        $this->freeItemsController->withCart($cart);
    }

//	public function woocommerce_checkout_update_order_review() {
//		$this->price_display->remove_price_hooks();
//		$this->process_cart();
//		$this->price_display->restore_hooks();
//	}

    public function afterCalculateTotals()
    {
//		$this->priceDisplay->remove_price_hooks();
        if (WcSubscriptionsCmp::isRecurringCartCalculation()) {
            return;
        }

        $this->process(false);
//		$this->priceDisplay->restore_hooks();
    }

    /**
     * @return CartProcessor
     */
    public function getCartProcessor(): CartProcessor
    {
        return $this->cartProcessor;
    }

    /**
     * @return Processor
     */
    public function getProductProcessor(): Processor
    {
        return $this->productProcessor;
    }

    /**
     * @return CalculationProfiler
     */
    public function getProfiler(): CalculationProfiler
    {
        return $this->profiler;
    }

    /**
     * @return PriceDisplay
     */
    public function getPriceDisplay(): PriceDisplay
    {
        return $this->priceDisplay;
    }

}
