<?php

namespace ADP\BaseVersion\Includes\Traits;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface ComparisonMethods
{
    const LT = '<';
    const LTE = '<=';
    const MTE = '>=';
    const MT = '>';
    const EQ = '=';
    const NEQ = '!=';
    const IN_LIST = 'in_list';
    const NOT_IN_LIST = 'not_in_list';
    const AT_LEAST_ONE_ANY = 'at_least_one_any';
    const AT_LEAST_ONE = 'at_least_one';
    const ALL = 'all';
    const ONLY = 'only';
    const NONE = 'none';
    const NONE_AT_ALL = 'none_at_all';
    const IN_RANGE = 'in_range';
    const NOT_IN_RANGE = 'not_in_range';
    const LATER = 'later';
    const EARLIER = 'earlier';
    const FROM = 'from';
    const TO = 'to';
    const SPECIFIC_DATE = 'specific_date';
}

trait Comparison
{
    /**
     * @param mixed $value
     * @param array $comparisonList
     * @param string $comparisonMethod
     *
     * @return bool
     */
    public function compareValueWithList(
        $value,
        $comparisonList,
        $comparisonMethod = ComparisonMethods::IN_LIST
    ) {
        $result = false;

        if (ComparisonMethods::IN_LIST === $comparisonMethod) {
            $result = in_array($value, $comparisonList);
        } elseif (ComparisonMethods::NOT_IN_LIST === $comparisonMethod) {
            $result = ! in_array($value, $comparisonList);
        }

        return $result;
    }

    /**
     * @param array $list
     * @param array $comparisonList
     * @param string $comparisonMethod
     *
     * @return bool
     */
    public function compareLists(
        $list,
        $comparisonList,
        $comparisonMethod = ComparisonMethods::IN_LIST
    ) {
        $result = false;

//		if ( ComparisonMethods::IN_LIST === $comparison_method ) {
//			$result = count( array_intersect( $list, $comparison_list ) ) == count( $comparison_list );
//		} elseif ( ComparisonMethods::NOT_IN_LIST === $comparison_method ) {
//			$result = count( array_intersect( $list, $comparison_list ) ) == 0;
        if (ComparisonMethods::AT_LEAST_ONE_ANY === $comparisonMethod) {
            $result = ! empty($list);
        } elseif (ComparisonMethods::AT_LEAST_ONE === $comparisonMethod or ComparisonMethods::IN_LIST === $comparisonMethod) {
            $result = count(array_intersect($comparisonList, $list)) > 0;
        } elseif (ComparisonMethods::ALL === $comparisonMethod) {
            $result = count(array_intersect($comparisonList, $list)) == count($comparisonList);
        } elseif (ComparisonMethods::ONLY === $comparisonMethod) {
            $result = array_diff($comparisonList, $list) === array_diff($list,
                    $comparisonList) && count($comparisonList) === count($list);
        } elseif (ComparisonMethods::NONE === $comparisonMethod or ComparisonMethods::NOT_IN_LIST === $comparisonMethod) {
            $result = count(array_intersect($list, $comparisonList)) === 0;
        } elseif (ComparisonMethods::NONE_AT_ALL === $comparisonMethod) {
            $result = empty($list);
        }

        return $result;
    }

    /**
     * @param mixed $value
     * @param mixed $comparisonValue
     * @param string $comparisonMethod
     *
     * @return bool
     */
    public function compareValues($value, $comparisonValue, $comparisonMethod = ComparisonMethods::LT)
    {
        if ($comparisonMethod === ComparisonMethods::IN_RANGE) {
            $start = isset($comparisonValue[0]) ? (float)$comparisonValue[0] : null;
            $finish = isset($comparisonValue[1]) ? (float)$comparisonValue[1] : null;

            return $this->valueInRange($value, $start, $finish);
        }

        $result = false;

        if (ComparisonMethods::LT === $comparisonMethod) {
            $result = $value < $comparisonValue;
        } elseif (ComparisonMethods::LTE === $comparisonMethod) {
            $result = $value <= $comparisonValue;
        } elseif (ComparisonMethods::MTE === $comparisonMethod) {
            $result = $value >= $comparisonValue;
        } elseif (ComparisonMethods::MT === $comparisonMethod) {
            $result = $value > $comparisonValue;
        } elseif (ComparisonMethods::EQ === $comparisonMethod) {
            $result = $value === $comparisonValue;
        } elseif (ComparisonMethods::NEQ === $comparisonMethod) {
            $result = $value !== $comparisonValue;
        }

        return $result;
    }

    /**
     * @param int $value
     * @param int $start
     * @param int $finish
     *
     * @return bool
     */
    public function valueInRange($value, $start, $finish)
    {
        return $start && $finish && $start <= $value && $finish >= $value;
    }

    /**
     * @param int $value Time in unix format
     * @param int $comparison_value Time in unix format
     * @param string $comparisonMethod
     *
     * @return bool
     */
    public function compareTimeUnixFormat(
        $value,
        $comparison_value,
        $comparisonMethod = ComparisonMethods::LATER
    ) {
        $result = false;

        if ($comparisonMethod === ComparisonMethods::LATER) {
            $result = $value > $comparison_value;
        } elseif ($comparisonMethod === ComparisonMethods::EARLIER) {
            $result = $value < $comparison_value;
        } elseif ($comparisonMethod === ComparisonMethods::FROM) {
            $result = $value >= $comparison_value;
        } elseif ($comparisonMethod === ComparisonMethods::TO) {
            $result = $value <= $comparison_value;
        } elseif ($comparisonMethod === ComparisonMethods::SPECIFIC_DATE) {
            $result = $value == $comparison_value;
        }

        return $result;
    }
}
