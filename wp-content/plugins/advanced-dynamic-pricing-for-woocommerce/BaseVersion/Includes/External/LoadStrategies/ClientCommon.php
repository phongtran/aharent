<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\DiscountMessage;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\Engine;
use ADP\BaseVersion\Includes\External\ExternalHooksSuppressor;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTableDisplay;
use ADP\BaseVersion\Includes\External\Reporter\AdminBounceBack;
use ADP\BaseVersion\Includes\External\Reporter\DebugBar;
use ADP\BaseVersion\Includes\External\WC\StructuredData;
use ADP\BaseVersion\Includes\External\WC\WcProductCustomAttributesCache;
use ADP\BaseVersion\Includes\External\WcCartStatsCollector;
use ADP\BaseVersion\Includes\External\Shortcodes\CategoryRangeDiscountTableShortcode;
use ADP\BaseVersion\Includes\External\Shortcodes\ProductRangeDiscountTableShortcode;
use ADP\BaseVersion\Includes\Frontend;
use ADP\BaseVersion\Includes\External\Shortcodes\BogoProducts as BogoProductsShortCode;
use ADP\BaseVersion\Includes\External\Shortcodes\OnSaleProducts as OnSaleProductsShortCode;
use ADP\BaseVersion\Includes\Functions;
use ADP\BaseVersion\Includes\Reporter\CalculationProfiler;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ClientCommon implements LoadStrategy
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
         * @var Customizer $customizer
         * @var DiscountMessage $discountMessage
         * @var Engine $engine
         * @var ExternalHooksSuppressor $hookSuppressor
         * @var StructuredData $structuredData
         */
        $customizer      = Factory::get("External_Customizer_Customizer", $this->context);
        $discountMessage = Factory::get("External_DiscountMessage", $this->context);
        $engine          = Factory::get("External_Engine", $this->context, WC()->cart);
        $structuredData  = Factory::get("External_WC_StructuredData", $this->context, $engine);
        $structuredData->install();
        $hookSuppressor  = new ExternalHooksSuppressor($this->context);

        /**
         * @var RangeDiscountTableDisplay $discountTable
         */
        $discountTable = new RangeDiscountTableDisplay($this->context, $customizer);
        ProductRangeDiscountTableShortcode::register($this->context, $customizer);
        CategoryRangeDiscountTableShortcode::register($this->context, $customizer);

        $customizer->runStyleCustomize();
        $customizer->customizeRegister();

        if ($this->context->getOption('support_shortcode_products_on_sale')) {
            /** @see OnSaleProductsShortCode::register() */
            Factory::callStaticMethod("External_Shortcodes_OnSaleProducts", 'register', $this->context);
        }

        if ($this->context->getOption('support_shortcode_products_bogo')) {
            /** @see BogoProductsShortCode::register() */
            Factory::callStaticMethod("External_Shortcodes_BogoProducts", 'register', $this->context);
        }

        if ($this->context->getOption('suppress_other_pricing_plugins')) {
            $hookSuppressor->registerHookSuppressor();
        }

        $wcCartStatsCollector = new WcCartStatsCollector($this->context);
        $wcCartStatsCollector->setActionCheckoutOrderProcessed();

        $engine->installCartProcessAction();
        if (is_super_admin($this->context->getCurrentUser()->ID)) {
            $profiler = $engine->getProfiler();
            $this->installReportAdminBounceBackAction($profiler);
            if ($this->context->getOption("show_debug_bar")) {
                $profiler->installActionCollectReport();
                $this->installDebugBar($profiler);
            }
        }

        $discountMessage->setThemeOptions($customizer);
        $discountTable->installRenderHooks();
        $non_admin_side = new Frontend($this->context);

        /** @see Functions::install() */
        Factory::callStaticMethod("Functions", 'install', $this->context, $engine);

        /** @var WcProductCustomAttributesCache $productAttributesCache */
        $productAttributesCache  = Factory::get("External_WC_WcProductCustomAttributesCache");
        $productAttributesCache->installHooks();
    }

    /**
     * @param CalculationProfiler $profiler
     */
    public function installDebugBar(CalculationProfiler $profiler)
    {
        /** @var DebugBar $debugBar */
        $debugBar = Factory::get("External_Reporter_DebugBar", $profiler);

        $debugBar->register_assets();
        $debugBar->install_action_to_render_bar_templates();
        $debugBar->installActionToAddIframe();
        $debugBar->installActionToRenderBar();
    }

    public function installReportAdminBounceBackAction(CalculationProfiler $profiler)
    {
        /** @var AdminBounceBack $adminBounceBack */
        $adminBounceBack = Factory::get("External_Reporter_AdminBounceBack", $profiler);
        $adminBounceBack->catchBounceEvent();
    }
}
