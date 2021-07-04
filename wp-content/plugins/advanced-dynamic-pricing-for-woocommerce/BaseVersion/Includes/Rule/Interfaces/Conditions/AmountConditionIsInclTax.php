<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface AmountConditionIsInclTax
{
    const COMPARISON_IS_INCL_TAX_VALUE_KEY = 'is_incl_tax';

    /**
     * @param bool $inclTax
     */
    public function setInclTax($inclTax);

    /**
     * @return bool
     */
    public function isInclTax();
}
