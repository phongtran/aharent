<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * TODO force the option 'initial_price_context' value to 'view'
 */
class SomewhereWarmBundlesCmp
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    public function addFilters()
    {
        // type cast for "identical" comparison in "update_cart_action" method
        add_filter('woocommerce_stock_amount_cart_item', function ($qty) {
            return (float)$qty;
        }, 10, 2);
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return class_exists("WC_Bundles");
    }

    /**
     * @param WcCartItemFacade $facade
     *
     * @return bool
     */
    public function isBundled(WcCartItemFacade $facade)
    {
        return function_exists('wc_pb_maybe_is_bundled_cart_item') && wc_pb_maybe_is_bundled_cart_item($facade->getData());
    }
}
