<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface RangeValueCondition
{
    const START_RANGE_KEY = 'start_range';
    const END_RANGE_KEY = 'end_range';

    /**
     * @param int|null $startRange
     */
    public function setStartRange($startRange);

    /**
     * @return int|null
     */
    public function getStartRange();

    /**
     * @param int|null $endRange
     */
    public function setEndRange($endRange);

    /**
     * @return int|null
     */
    public function getEndRange();
}
