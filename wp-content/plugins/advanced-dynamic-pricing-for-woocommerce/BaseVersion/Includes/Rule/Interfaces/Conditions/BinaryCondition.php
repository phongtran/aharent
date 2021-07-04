<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface BinaryCondition
{
    const COMPARISON_BIN_VALUE_KEY = 'comparison_bin_value';

    /**
     * @param string|bool $comparisonValue
     */
    public function setComparisonBinValue($comparisonValue);

    /**
     * @return bool|null
     */
    public function getComparisonBinValue();
}
