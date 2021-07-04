<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Translators\RuleTranslator;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PriceBasedOnCountryCmp
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

    /**
     * @return bool
     */
    public function isActive()
    {
        return defined("WCPBC_PLUGIN_FILE");
    }

    /**
     * @param Rule $rule
     *
     * @return Rule
     */
    public function changeRuleCurrency($rule): Rule
    {
        if ( ! function_exists("wcpbc_get_zone_by_country")) {
            return $rule;
        }

        if ( ! ($zone = wcpbc_get_zone_by_country())) {
            return $rule;
        }

        if ($rate = $zone->get_real_exchange_rate()) {
            $rule = RuleTranslator::setCurrency($rule, $rate);
        }

        return $rule;
    }
}
