<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface ListComparisonCondition
{
    const COMPARISON_LIST_KEY = 'comparison_list';
    const COMPARISON_LIST_METHOD_KEY = 'comparison_list_method';

    /**
     * @param array|string $comparisonList
     */
    public function setComparisonList($comparisonList);

    /**
     * @return array|null
     */
    public function getComparisonList();

    /**
     * @param string|null $comparisonMethod
     */
    public function setListComparisonMethod($comparisonMethod);

    /**
     * @return string|null
     */
    public function getListComparisonMethod();
}
