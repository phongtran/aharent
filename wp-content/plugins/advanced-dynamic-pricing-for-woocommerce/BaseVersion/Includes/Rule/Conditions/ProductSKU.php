<?php

namespace ADP\BaseVersion\Includes\Rule\Conditions;

use ADP\BaseVersion\Includes\Rule\ConditionsLoader;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductSKU extends AbstractConditionCartItems
{
    protected $filterType = 'product_sku';

    public static function getType()
    {
        return 'product_sku';
    }

    public static function getLabel()
    {
        return __('Product SKU (qty)', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public static function getTemplatePath()
    {
        return WC_ADP_PLUGIN_VIEWS_PATH . 'conditions/products/product-sku.php';
    }

    public static function getGroup()
    {
        return ConditionsLoader::GROUP_CART_ITEMS;
    }
}
