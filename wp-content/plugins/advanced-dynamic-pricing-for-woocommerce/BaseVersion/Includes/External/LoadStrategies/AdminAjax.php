<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Admin\Ajax;
use ADP\BaseVersion\Includes\Admin\Settings;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\AdminPage;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\DiscountMessage;
use ADP\BaseVersion\Includes\External\Engine;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\BaseVersion\Includes\External\PriceAjax;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTableAjax;
use ADP\BaseVersion\Includes\External\Reporter\ReporterAjax;
use ADP\BaseVersion\Includes\External\Shortcodes\CategoryRangeDiscountTableShortcode;
use ADP\BaseVersion\Includes\External\Shortcodes\ProductRangeDiscountTableShortcode;
use ADP\BaseVersion\Includes\External\WC\WcProductCustomAttributesCache;
use ADP\BaseVersion\Includes\External\WcCartStatsCollector;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AdminAjax implements LoadStrategy
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

    public function start()
    {
        /**
         * @var Customizer $customizer
         * @var DiscountMessage $discountMessage
         * @var AdminPage $adminPage
         * @var Engine $engine
         */
        $customizer      = Factory::get("External_Customizer_Customizer", $this->context);
        $discountMessage = Factory::get("External_DiscountMessage", $this->context);
        $adminPage       = Factory::get('External_AdminPage_AdminPage', $this->context);
        $engine          = Factory::get("External_Engine", $this->context, WC()->cart);

        /**
         * @var PriceAjax $priceAjax
         */
        $priceAjax = new PriceAjax($this->context, $engine);

        $adminPage->registerAjax();

        /**
         * @var $ajax Ajax
         */
        $ajax = Factory::get('Admin_Ajax', $this->context);
        $ajax->register();

        /**
         * @var $tableAjax RangeDiscountTableAjax
         */
        $tableAjax = new RangeDiscountTableAjax($this->context, $customizer);
        $tableAjax->register();
        if ( ! $this->context->is(Context::CUSTOMIZER)) {
            $discountMessage->setThemeOptions($customizer);
        }
        new Settings($this->context);

        $engine->installCartProcessAction();
        if (is_super_admin($this->context->getCurrentUser()->ID)) {
            $profiler     = $engine->getProfiler();
            $profilerAjax = new ReporterAjax($profiler);
            $profilerAjax->register();
            if ($this->context->getOption("show_debug_bar")) {
                $profiler->installActionCollectReport();
            }
        }

        $priceAjax->register();

        $wcCartStatsCollector = new WcCartStatsCollector($this->context);
        $wcCartStatsCollector->setActionCheckoutOrderProcessed();

        if ($this->context->getOption('update_cross_sells')) {
            add_filter('woocommerce_add_to_cart_fragments', array($this, 'woocommerceAddToCartFragments'), 10, 2);
        }

        /** Register shortcodes for quick view */
        ProductRangeDiscountTableShortcode::register($this->context, $customizer);
        CategoryRangeDiscountTableShortcode::register($this->context, $customizer);

        /** @see Functions::install() */
        Factory::callStaticMethod("Functions", 'install', $this->context, $engine);

        /** @var WcProductCustomAttributesCache $productAttributesCache */
        $productAttributesCache  = Factory::get("External_WC_WcProductCustomAttributesCache");
        $productAttributesCache->installHooks();
    }

    public function woocommerceAddToCartFragments($fragments)
    {
        /**
         * Fix incorrect add-to-cart url in cross sells elements.
         * We need to remove "wc-ajax" argument because WC_Product childs in method add_to_cart_url() use
         * add_query_arg() with current url.
         * Do not forget to set current url to cart_url.
         */
        $_SERVER['REQUEST_URI'] = remove_query_arg('wc-ajax', wc_get_cart_url());

        ob_start();
        woocommerce_cross_sell_display();
        $text = trim(ob_get_clean());
        if (empty($text)) {
            $text = '<div class="cross-sells"></div>';
        }
        $fragments['div.cross-sells'] = $text;

        return $fragments;
    }
}
