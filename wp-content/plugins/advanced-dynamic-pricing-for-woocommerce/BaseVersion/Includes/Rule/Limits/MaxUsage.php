<?php

namespace ADP\BaseVersion\Includes\Rule\Limits;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleLimit;
use ADP\BaseVersion\Includes\Rule\LimitsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\Limits\MaxUsageLimit;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class MaxUsage implements RuleLimit, MaxUsageLimit
{
    protected $maxUsage;

    public function __construct()
    {
    }

    /**
     * @param Rule $rule
     * @param Cart $cart
     *
     * @return bool
     */
    public function check($rule, $cart)
    {
        $comparison_value = (int)$this->maxUsage;

        $value = $cart->getContext()->getCountOfRuleUsages($rule->getId());

        return $value < $comparison_value;
    }

    public static function getType()
    {
        return 'max_usage';
    }

    public static function getLabel()
    {
        return __('Max usage', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public static function getTemplatePath()
    {
        return WC_ADP_PLUGIN_VIEWS_PATH . 'limits/max-usage.php';
    }

    public static function getGroup()
    {
        return LimitsLoader::GROUP_USAGE_RESTRICT;
    }

    /**
     * @param string|int $maxUsage
     */
    public function setMaxUsage($maxUsage)
    {
        $this->maxUsage = intval($maxUsage);
    }

    public function getMaxUsage()
    {
        return $this->maxUsage;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return isset($this->maxUsage);
    }
}
