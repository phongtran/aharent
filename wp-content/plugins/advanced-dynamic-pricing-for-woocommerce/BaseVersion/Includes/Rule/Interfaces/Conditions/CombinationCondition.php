<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface CombinationCondition
{
    const COMBINE_TYPE_KEY = 'combine_type';
    const COMBINE_LIST_KEY = 'combine_list';

    /**
     * @param string|null $combineType
     */
    public function setCombineType($combineType);

    /**
     * @return string|null
     */
    public function getCombineType();

    /**
     * @param array|null $combineList
     */
    public function setCombineList($combineList);

    /**
     * @return array|null
     */
    public function getCombineList();
}
