<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\DiscountMessage;
use ADP\BaseVersion\Includes\External\LoadStrategies\CustomizePreview;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\BaseVersion\Includes\External\LoadStrategies\AdminAjax;
use ADP\BaseVersion\Includes\External\LoadStrategies\AdminCommon;
use ADP\BaseVersion\Includes\External\LoadStrategies\ClientCommon;
use ADP\BaseVersion\Includes\External\LoadStrategies\PhpUnit;
use ADP\BaseVersion\Includes\External\LoadStrategies\RestApi;
use ADP\BaseVersion\Includes\External\LoadStrategies\WpCron;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Loader
{
    public function __construct()
    {
        $this->define();
        add_action('init', array($this, 'initPlugin'));
    }

    protected function define()
    {
        define('WC_ADP_PLUGIN_TEMPLATES_PATH', WC_ADP_PLUGIN_PATH . 'BaseVersion/templates/');
        define('WC_ADP_PLUGIN_VIEWS_PATH', WC_ADP_PLUGIN_PATH . 'BaseVersion/views/');
    }

    public function initPlugin()
    {
        if ( ! $this->checkRequirements()) {
            return;
        }

        load_plugin_textdomain('advanced-dynamic-pricing-for-woocommerce', false,
            basename(dirname(dirname(__FILE__))) . '/languages/');

        $context = apply_filters("adp_context_created", new Context());
        $this->load($context);
    }

    /**
     * @param Context $context
     */
    protected function load($context)
    {
        $strategy = $this->selectLoadStrategy($context);
        $strategy->start();

        $customizer      = Factory::get("External_Customizer_Customizer", $context);
        $discountMessage = Factory::get("External_DiscountMessage", $context);
        /**
         * @var Customizer $customizer
         * @var DiscountMessage $discountMessage
         */
        $discountMessage->setThemeOptionsEmail($customizer);
    }

    public function checkRequirements()
    {
        $state = true;
        if (version_compare(phpversion(), WC_ADP_MIN_PHP_VERSION, '<')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>' . sprintf(__('<strong>Advanced Dynamic Pricing for WooCommerce</strong> requires PHP version %s or later.',
                        'advanced-dynamic-pricing-for-woocommerce'), WC_ADP_MIN_PHP_VERSION) . '</p></div>';
            });
            $state = false;
        } elseif ( ! class_exists('WooCommerce')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>' . __('<strong>Advanced Dynamic Pricing for WooCommerce</strong> requires active WooCommerce!',
                        'advanced-dynamic-pricing-for-woocommerce') . '</p></div>';
            });
            $state = false;
        } elseif (version_compare(WC_VERSION, WC_ADP_MIN_WC_VERSION, '<')) {
            add_action('admin_notices', function () {
                echo '<div class="notice notice-error is-dismissible"><p>' . sprintf(__('<strong>Advanced Dynamic Pricing for WooCommerce</strong> requires WooCommerce version %s or later.',
                        'advanced-dynamic-pricing-for-woocommerce'), WC_ADP_MIN_WC_VERSION) . '</p></div>';
            });
            $state = false;
        }

        return $state;
    }

    /**
     * @param Context $context
     *
     * @return LoadStrategy
     */
    protected function selectLoadStrategy($context)
    {
        if ($context->is($context::CUSTOMIZER)) {
            $strategy = Factory::get("External_LoadStrategies_CustomizePreview", $context);
            /** @var $strategy CustomizePreview */
        } elseif ($context->is($context::WP_CRON)) {
            $strategy = Factory::get("External_LoadStrategies_WpCron", $context);
            /** @var $strategy WpCron */
        } elseif ($context->is($context::REST_API)) {
            $strategy = Factory::get("External_LoadStrategies_RestApi", $context);
            /** @var $strategy RestApi */
        } elseif ($context->is($context::AJAX)) {
            $strategy = Factory::get("External_LoadStrategies_AdminAjax", $context);
            /** @var $strategy AdminAjax */
        } elseif ($context->is($context::ADMIN)) {
            $strategy = Factory::get("External_LoadStrategies_AdminCommon", $context);
            /** @var $strategy AdminCommon */
        } elseif ($context->is($context::PHPUNIT)) {
            $strategy = Factory::get("External_LoadStrategies_PhpUnit", $context);
            /** @var $strategy PhpUnit */
        } else {
            $strategy = Factory::get("External_LoadStrategies_ClientCommon", $context);
            /** @var $strategy ClientCommon */
        }

        return $strategy;
    }
}
