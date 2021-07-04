<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface ValueComparisonCondition
{
    const COMPARISON_VALUE_KEY = 'comparison_value';
    const COMPARISON_VALUE_METHOD_KEY = 'comparison_value_method';

    /**
     * @param string|null $comparisonMethod
     */
    public function setValueComparisonMethod($comparisonMethod);

    /**
     * @return string|null
     */
    public function getValueComparisonMethod();

    /**
     * @param string|float|null $comparisonValue
     */
    public function setComparisonValue($comparisonValue);

    /**
     * @return string|float|null
     */
    public function getComparisonValue();
}
