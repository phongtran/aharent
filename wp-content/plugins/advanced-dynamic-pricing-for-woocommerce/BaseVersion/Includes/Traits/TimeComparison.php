<?php

namespace ADP\BaseVersion\Includes\Traits;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

trait TimeComparison
{
    /**
     * @param int $time
     * @param int $comparisonTime
     * @param string $comparisonMethod
     *
     * @return bool
     */
    public function checkTime($time, $comparisonTime, $comparisonMethod)
    {
        $result = false;

        if ($comparisonMethod === ComparisonMethods::LATER) {
            $result = $time > $comparisonTime;
        } elseif ($comparisonMethod === ComparisonMethods::EARLIER) {
            $result = $time < $comparisonTime;
        } elseif ($comparisonMethod === ComparisonMethods::FROM) {
            $result = $time >= $comparisonTime;
        } elseif ($comparisonMethod === ComparisonMethods::TO) {
            $result = $time <= $comparisonTime;
        } elseif ($comparisonMethod === ComparisonMethods::SPECIFIC_DATE) {
            $result = $time == $comparisonTime;
        }

        return $result;
    }
}
