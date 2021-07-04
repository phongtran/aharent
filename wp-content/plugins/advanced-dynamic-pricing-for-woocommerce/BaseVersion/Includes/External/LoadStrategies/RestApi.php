<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Engine;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\BaseVersion\Includes\External\WC\WcProductCustomAttributesCache;
use ADP\BaseVersion\Includes\External\WcCartStatsCollector;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RestApi implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function start()
    {
        /**
         * We do not need this if "WooCommerce Blocks" < 2.6.0 is installed.
         * In future versions method "maybe_init_cart_session" has been removed.
         * @see https://github.com/woocommerce/woocommerce-gutenberg-products-block/commit/5a195cf105133e5b3ac232cfb469ed5c53a3d4bc#diff-17c1ab7a1ea1f97171811713b2a886c1
         *
         * @see WpCron::start() explanation here!
         */
        add_filter('woocommerce_apply_base_tax_for_local_pickup', "__return_false");

        /**
         * @var Engine $engine
         */
        $engine = Factory::get("External_Engine", $this->context, WC()->cart);

        // Should we install all price display hooks?
        $engine->installProductProcessorWithEmptyCart();

        $wcCartStatsCollector = new WcCartStatsCollector($this->context);
        $wcCartStatsCollector->setActionCheckoutOrderProcessedDuringRestApi();

        add_action('woocommerce_before_calculate_totals', array($this, 'initProcessActionIfCartWasLoaded'), 10, 1);

        /** @see Functions::install() */
        Factory::callStaticMethod("Functions", 'install', $this->context);

        /** @var WcProductCustomAttributesCache $productAttributesCache */
        $productAttributesCache  = Factory::get("External_WC_WcProductCustomAttributesCache");
        $productAttributesCache->installHooks();
    }

    public function initProcessActionIfCartWasLoaded($wcCart)
    {
        /** @var Engine $engine */
        $engine = Factory::get("External_Engine", $this->context, $wcCart);
        $engine->getCartProcessor()->installActionFirstProcess();
        $engine->firstTimeProcessCart();

        remove_action('woocommerce_before_calculate_totals', array($this, 'initProcessActionIfCartWasLoaded'), 10);
    }
}
