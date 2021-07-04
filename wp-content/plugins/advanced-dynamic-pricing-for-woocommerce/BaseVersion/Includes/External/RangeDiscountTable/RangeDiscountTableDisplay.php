<?php

namespace ADP\BaseVersion\Includes\External\RangeDiscountTable;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\Factory;

class RangeDiscountTableDisplay
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Context
     */
    protected $customizer;

    /**
     * @var RangeDiscountTable
     */
    protected $rangeDiscountTable;

    /**
     * @param Context $context
     * @param Customizer $customizer
     */
    public function __construct($context, $customizer)
    {
        $this->context            = $context;
        $this->customizer         = $customizer;
        $this->rangeDiscountTable = Factory::get("External_RangeDiscountTable_RangeDiscountTable", $context,
            $customizer);
    }

    public function installRenderHooks()
    {
        add_action('wp_loaded', function () {
            $themeOptions = $this->customizer->getThemeOptions();
            if ($this->context->getOption('show_matched_bulk_table')) {
                $actions = array($themeOptions[RangeDiscountTable::CONTEXT_PRODUCT_PAGE]['table']['product_bulk_table_action']);

                foreach (apply_filters('wdp_product_bulk_table_action', $actions) as $action) {
                    add_action($action, array($this, 'echoProductTableContent'), 50, 2);
                }
            }

            if ($this->context->getOption('show_category_bulk_table')) {
                $actions = array($themeOptions[RangeDiscountTable::CONTEXT_CATEGORY_PAGE]['table']['category_bulk_table_action']);

                foreach (apply_filters('wdp_category_bulk_table_action', $actions) as $action) {
                    add_action($action, array($this, 'echoCategoryTableContent'), 50, 2);
                }
            }
        });
    }

    public function echoProductTableContent()
    {
        echo $this->rangeDiscountTable->getProductTableContent();
    }

    public function echoCategoryTableContent()
    {
        echo $this->rangeDiscountTable->getCategoryTableContent();
    }

}
