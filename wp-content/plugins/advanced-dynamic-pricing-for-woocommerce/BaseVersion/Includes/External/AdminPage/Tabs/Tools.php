<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Tabs;

use ADP\BaseVersion\Includes\Common\Helpers;
use ADP\BaseVersion\Includes\Admin\Importer;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;
use ADP\Factory;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Tools implements AdminTabInterface
{
    const IMPORT_TYPE_OPTIONS = 'options';
    const IMPORT_TYPE_RULES = 'rules';

    /**
     * @var string
     */
    protected $title;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $groups;

    /**
     * @var array
     */
    protected $import_data_types;

    public function __construct($context)
    {
        $this->context = $context;
        $this->title   = self::getTitle();

        $this->import_data_types = array(
            self::IMPORT_TYPE_OPTIONS => __('Options', 'advanced-dynamic-pricing-for-woocommerce'),
            self::IMPORT_TYPE_RULES   => __('Rules', 'advanced-dynamic-pricing-for-woocommerce'),
        );
    }

    public function handleSubmitAction()
    {
        if (isset($_POST['wdp-import']) && ! empty($_POST['wdp-import-data']) && ! empty($_POST['wdp-import-type'])) {
            $data             = json_decode(str_replace('\\', '', wp_unslash($_POST['wdp-import-data'])), true);
            $import_data_type = $_POST['wdp-import-type'];
            $this->actionGroups($data, $import_data_type);
            wp_redirect($_SERVER['HTTP_REFERER']);
        }
    }

    public function getViewVariables()
    {
        $this->prepareExportGroups();
        $groups            = $this->groups;
        $import_data_types = $this->import_data_types;

        return compact('groups', 'import_data_types');
    }

    public static function getRelativeViewPath()
    {
        return 'admin_page/tabs/tools.php';
    }

    public static function getHeaderDisplayPriority()
    {
        return 60;
    }

    public static function getKey()
    {
        return 'tools';
    }

    public static function getTitle()
    {
        return __('Tools', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public function enqueueScripts()
    {
//		$is_settings_page = isset( $_GET['page'] ) && $_GET['page'] == 'wdp_settings';
//		// Load backend assets conditionally
//		if ( ! $is_settings_page ) {
//			return;
//		}

        $baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
        wp_enqueue_script('wdp-tools', $baseVersionUrl . 'assets/js/tools.js', array(), WC_ADP_VERSION, true);
    }

    protected function actionGroups($data, $importDataType)
    {
        $this->actionOptionsGroup($data, $importDataType);
        $this->actionRulesGroup($data, $importDataType);
    }

    protected function actionOptionsGroup($data, $importDataType)
    {
        if ($importDataType !== self::IMPORT_TYPE_OPTIONS) {
            return;
        }

        $settings = $this->context->getSettings();

        foreach (array_keys($settings->getOptions()) as $key) {
            $option = $settings->tryGetOption($key);

            if ($option) {
                if (isset($data[$key])) {
                    $option->set($data[$key]);
                }
            }
        }

        $settings->save();
    }

    protected function prepareExportGroups()
    {
        $this->prepareOptionsGroup();
        $this->prepareExportGroup();
    }

    protected function prepareOptionsGroup()
    {
        $options = $this->context->getSettings()->getOptions();

        $options_group = array(
            'label' => __('Options', 'advanced-dynamic-pricing-for-woocommerce'),
            'data'  => $options,
        );

        $this->groups['options'] = array(
            'label' => __('Options', 'advanced-dynamic-pricing-for-woocommerce'),
            'items' => array('options' => $options_group),
        );
    }

    protected function actionRulesGroup($data, $importDataType)
    {
        if ($importDataType !== self::IMPORT_TYPE_RULES) {
            return;
        }

        Importer::importRules($data, $_POST['wdp-import-data-reset-rules']);
    }

    protected function prepareExportGroup()
    {
        $exportItems = array();

        $exporter = Factory::get("Admin_Exporter", $this->context);
        $rules    = $exporter->exportRules();

        foreach ($rules as &$rule) {
            unset($rule['id']);

            if ( ! empty($rule['filters'])) {
                foreach ($rule['filters'] as &$item) {
                    $item['value'] = isset($item['value']) ? $item['value'] : array();
                    $item['value'] = $this->convertElementsFromIdToName($item['value'], $item['type']);
                }
                unset($item);
            }

            if ( ! empty($rule['get_products']['value'])) {
                foreach ($rule['get_products']['value'] as &$item) {
                    $item['value'] = isset($item['value']) ? $item['value'] : array();
                    $item['value'] = $this->convertElementsFromIdToName($item['value'], $item['type']);
                }
                unset($item);
            }

            if ( ! empty($rule['conditions'])) {
                foreach ($rule['conditions'] as &$item) {
                    foreach ($item['options'] as &$optionItem) {
                        if (is_array($optionItem)) {
                            $converted = null;
                            try {
                                $converted = $this->convertElementsFromIdToName($optionItem, $item['type']);
                            } catch (Exception $e) {

                            }

                            if ($converted) {
                                $optionItem = $converted;
                            }
                        }
                    }
                }
                unset($item);
            }
        }
        unset($rule);

        $exportItems['all'] = array(
            'label' => __('All', 'advanced-dynamic-pricing-for-woocommerce'),
            'data'  => $rules,
        );

        foreach ($rules as $rule) {
            $exportItems[] = array(
                'label' => "{$rule['title']}",
                'data'  => array($rule),
            );
        }

        $this->groups['rules'] = array(
            'label' => __('Rules', 'advanced-dynamic-pricing-for-woocommerce'),
            'items' => $exportItems
        );
    }

    /**
     * @param array $items or empty string
     * @param string $type
     *
     * @return array|string
     */
    protected function convertElementsFromIdToName($items, $type)
    {
        if (empty($items)) {
            return $items;
        }
        foreach ($items as &$value) {
            if ('products' === $type) {
                $value = Helpers::getProductName($value);
            } elseif ('product_categories' === $type) {
                $value = Helpers::getCategoryTitle($value);
            } elseif ('product_tags' === $type) {
                $value = Helpers::getTagTitle($value);
            } elseif ('product_attributes' === $type) {
                $value = Helpers::getAttributeTitle($value);
            }
        }

        return $items;
    }

    public function registerAjax()
    {

    }
}
