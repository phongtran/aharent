<?php

namespace ADP\BaseVersion\Includes\Rule\Conditions;

use ADP\BaseVersion\Includes\Rule\ConditionsLoader;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductTags extends AbstractConditionCartItems
{
    protected $filterType = 'product_tags';

    public static function getType()
    {
        return 'product_tags';
    }

    public static function getLabel()
    {
        return __('Product tags (qty)', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public static function getTemplatePath()
    {
        return WC_ADP_PLUGIN_VIEWS_PATH . 'conditions/products/product-tags.php';
    }

    public static function getGroup()
    {
        return ConditionsLoader::GROUP_CART_ITEMS;
    }
}
