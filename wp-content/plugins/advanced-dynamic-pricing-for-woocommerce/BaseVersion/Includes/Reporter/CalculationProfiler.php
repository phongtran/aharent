<?php

namespace ADP\BaseVersion\Includes\Reporter;

use ADP\BaseVersion\Includes\Cart\CartProcessor;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\Product\Processor;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class CalculationProfiler
{
    const INITIAL_CART = 'initial_cart';
    const PROCESSED_CART = 'processed_cart';
    const PROCESSED_PRODUCTS = 'processed_products';
    const RULES_TIMING = 'rules_timing';
    const OPTIONS = 'options';
    const ACTIVE_HOOKS = 'active_hooks';

    /**
     * @var CartProcessor
     */
    protected $cartProcessor;

    /**
     * @var Processor
     */
    protected $productProcessor;

    /**
     * @var string
     */
    private $import_key;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var ReportsStorage
     */
    protected $storage;

    /**
     * @param Context $context
     * @param CartProcessor $cartProcessor
     * @param Processor $productProcessor
     */
    public function __construct($context, $cartProcessor, $productProcessor)
    {
        $this->context          = $context;
        $this->cartProcessor    = $cartProcessor;
        $this->productProcessor = $productProcessor;

        // should wait, because impossible to create import key earlier
        add_action('wp_loaded', function () {
            $this->import_key = $this->createImportKey();
            $this->storage    = new ReportsStorage($this->import_key);
        }, 1);
    }

    public function getImportKey()
    {
        return $this->import_key;
    }

    public function installActionCollectReport()
    {
        add_action('shutdown', array($this, 'collectAndStoreReport'), PHP_INT_MAX); // do not use shutdown hook
    }

    public function collectAndStoreReport()
    {
        $context     = $this->context;
        $activeRules = CacheHelper::loadActiveRules($context);

        $active_rules_as_dict = array();
        foreach ($activeRules->getRules() as $rule) {
            $active_rules_as_dict[$rule->getId()] = self::getRuleAsDict($rule, $context);
        }

        if ($context->is($context::AJAX)) {
            $prev_processed_products_report = $this->storage->getReport('processed_products');
            foreach ($prev_processed_products_report as $id => $report) {
                $this->productProcessor->calculateProduct($id);
            }
        }

        $reports = array(
            'processed_cart'     => (new Collectors\WcCart($this->cartProcessor))->collect(),
            'processed_products' => (new Collectors\Products($this->productProcessor))->collect(),
            'options'            => (new Collectors\Options($this->context))->collect(),
            'additions'          => (new Collectors\PluginsAndThemes())->collect(),
            'active_hooks'       => (new Collectors\ActiveHooks())->collect(),

            'rules' => $active_rules_as_dict,
        );

        foreach ($reports as $report_key => $report) {
            $this->storage->storeReport($report_key, $report);
        }
    }

    /**
     * @param Rule $rule
     * @param Context $context
     *
     * @return array
     */
    private static function getRuleAsDict($rule, $context)
    {
        $slug                  = 'wdp_settings';
        $tab                   = 'rules';
        $exporter              = Factory::get("Admin_Exporter", $context);
        $data                  = $exporter->convertRule($rule);
        $data['id']            = $rule->getId();
        $data['edit_page_url'] = admin_url("admin.php?page={$slug}&tab={$tab}&rule_id={$rule->getId()}");

        return $data;
    }

    private function createImportKey()
    {
        if ( ! did_action('wp_loaded')) {
            _doing_it_wrong(__FUNCTION__,
                sprintf(__('%1$s should not be called before the %2$s action.', 'woocommerce'), 'create_import_key',
                    'wp_loaded'), WC_ADP_VERSION);

            return null;
        }

        global $wp;

        return substr(md5($wp->request . '|' . (string)get_current_user_id()), 0, 8);
    }
}
