<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Conditions;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface TimeRangeCondition
{
    const TIME_RANGE_KEY = 'time_range';

    /**
     * @param string|null $timeRange
     */
    public function setTimeRange($timeRange);

    /**
     * @return string|null
     */
    public function getTimeRange();
}
