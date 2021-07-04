<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Rule\Interfaces;
use ADP\Factory;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OptionsConverter
{
    public static function convertCondition($data)
    {
        if (empty($data['type'])) {
            throw new Exception('Missing condition type');
        }

        $conditionsLoader = Factory::get("Rule_ConditionsLoader");
        /** @var ConditionsLoader $conditionsLoader */

        $condition = $conditionsLoader->create($data['type']);

        if (isset($data['options'][0])) {
            if ($condition instanceof Interfaces\Conditions\CombinationCondition and $condition instanceof Interfaces\Conditions\ValueComparisonCondition) {
                $data['options'][$condition::COMBINE_TYPE_KEY]            = $data['options'][0];
                $data['options'][$condition::COMBINE_LIST_KEY]            = $data['options'][1];
                $data['options'][$condition::COMPARISON_VALUE_METHOD_KEY] = $data['options'][2];
                $data['options'][$condition::COMPARISON_VALUE_KEY]        = $data['options'][3];
                unset($data['options'][0]);
                unset($data['options'][1]);
                unset($data['options'][2]);
                unset($data['options'][3]);
            } elseif ($condition instanceof Interfaces\Conditions\ValueComparisonCondition and $condition instanceof Interfaces\Conditions\TimeRangeCondition) {
                $data['options'][$condition::TIME_RANGE_KEY]              = $data['options'][0];
                $data['options'][$condition::COMPARISON_VALUE_METHOD_KEY] = $data['options'][1];
                $data['options'][$condition::COMPARISON_VALUE_KEY]        = $data['options'][2];
                unset($data['options'][0]);
                unset($data['options'][1]);
                unset($data['options'][2]);
            } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition and $condition instanceof Interfaces\Conditions\ValueComparisonCondition and $condition instanceof Interfaces\Conditions\AmountConditionIsInclTax) {
                $data['options'][$condition::COMPARISON_LIST_METHOD_KEY]  = $data['options'][0];
                $data['options'][$condition::COMPARISON_LIST_KEY]         = $data['options'][1];
                $data['options'][$condition::COMPARISON_VALUE_METHOD_KEY] = $data['options'][2];
                $data['options'][$condition::COMPARISON_VALUE_KEY]        = $data['options'][3];
                unset($data['options'][0]);
                unset($data['options'][1]);
                unset($data['options'][2]);
                unset($data['options'][3]);

                if ( isset($data['options'][4]) ) {
                    $data['options'][$condition::COMPARISON_IS_INCL_TAX_VALUE_KEY] = $data['options'][4];
                    unset($data['options'][4]);
                } else {
                    $data['options'][$condition::COMPARISON_IS_INCL_TAX_VALUE_KEY] = false;
                }
            } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition and $condition instanceof Interfaces\Conditions\RangeValueCondition) {
                $data['options'][$condition::START_RANGE_KEY]            = $data['options'][0];
                $data['options'][$condition::COMPARISON_LIST_METHOD_KEY] = $data['options'][1];
                $data['options'][$condition::COMPARISON_LIST_KEY]        = $data['options'][2];
                $data['options'][$condition::END_RANGE_KEY]              = $data['options'][3];
                unset($data['options'][0]);
                unset($data['options'][1]);
                unset($data['options'][2]);
                unset($data['options'][3]);
            } elseif ($condition instanceof Interfaces\Conditions\ValueComparisonCondition) {
                $data['options'][$condition::COMPARISON_VALUE_METHOD_KEY] = $data['options'][0];
                $data['options'][$condition::COMPARISON_VALUE_KEY]        = $data['options'][1];
                unset($data['options'][0]);
                unset($data['options'][1]);
            } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition) {
                $data['options'][$condition::COMPARISON_LIST_METHOD_KEY] = $data['options'][0];
                $data['options'][$condition::COMPARISON_LIST_KEY]        = isset($data['options'][1]) ? $data['options'][1] : array();
                unset($data['options'][0]);
                unset($data['options'][1]);
            } elseif ($condition instanceof Interfaces\Conditions\DateTimeComparisonCondition) {
                $data['options'][$condition::COMPARISON_DATETIME_METHOD_KEY] = $data['options'][0];
                $data['options'][$condition::COMPARISON_DATETIME_KEY]        = $data['options'][1];
                unset($data['options'][0]);
                unset($data['options'][1]);
            } elseif ($condition instanceof Interfaces\Conditions\BinaryCondition) {
                $data['options'][$condition::COMPARISON_BIN_VALUE_KEY] = $data['options'][0];
                unset($data['options'][0]);
            }
        }

        return $data;
    }

    public static function convertConditionToArray($condition)
    {
        $result         = array();
        $result['type'] = $condition->getType();
        if ($condition instanceof Interfaces\Conditions\CombinationCondition and $condition instanceof Interfaces\Conditions\ValueComparisonCondition) {
            $result['options'] = array(
                0 => $condition->getCombineType(),
                1 => $condition->getCombineList(),
                2 => $condition->getValueComparisonMethod(),
                3 => $condition->getComparisonValue(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\ValueComparisonCondition and $condition instanceof Interfaces\Conditions\TimeRangeCondition) {
            $result['options'] = array(
                0 => $condition->getTimeRange(),
                1 => $condition->getValueComparisonMethod(),
                2 => $condition->getComparisonValue(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition and $condition instanceof Interfaces\Conditions\ValueComparisonCondition) {
            $result['options'] = array(
                0 => $condition->getListComparisonMethod(),
                1 => $condition->getComparisonList(),
                2 => $condition->getValueComparisonMethod(),
                3 => $condition->getComparisonValue(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition and $condition instanceof Interfaces\Conditions\RangeValueCondition) {
            $result['options'] = array(
                0 => $condition->getStartRange(),
                1 => $condition->getListComparisonMethod(),
                2 => $condition->getComparisonList(),
                3 => $condition->getEndRange(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\ValueComparisonCondition) {
            $result['options'] = array(
                0 => $condition->getValueComparisonMethod(),
                1 => $condition->getComparisonValue(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\ListComparisonCondition) {
            $result['options'] = array(
                0 => $condition->getListComparisonMethod(),
                1 => $condition->getComparisonList(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\DateTimeComparisonCondition) {
            $result['options'] = array(
                0 => $condition->getDateTimeComparisonMethod(),
                1 => $condition->getComparisonDateTime(),
            );
        } elseif ($condition instanceof Interfaces\Conditions\BinaryCondition) {
            $result['options'] = array(
                0 => $condition->getComparisonBinValue(),
            );
        }

        return $result;
    }

    public static function convertCartAdj($data)
    {
        if (empty($data['type'])) {
            throw new Exception('Missing cart adjustment type');
        }

        $cartAdjLoader = Factory::get("Rule_CartAdjustmentsLoader");
        /** @var CartAdjustmentsLoader $cartAdjLoader */
        $adj = $cartAdjLoader->create($data['type']);

        if ($adj instanceof Interfaces\CartAdjustments\CouponCartAdj) {
            if (isset($data['options'][0])) {
                $data['options'][$adj::COUPON_VALUE_KEY] = $data['options'][0];
            }

            if (isset($data['options'][1])) {
                $data['options'][$adj::COUPON_CODE_KEY] = $data['options'][1];
            }

            if (isset($data['options'][2])) {
                $data['options'][$adj::COUPON_MAX_DISCOUNT] = $data['options'][2];
                unset($data['options'][2]);
            }
            unset($data['options'][0]);
            unset($data['options'][1]);
        } elseif ($adj instanceof Interfaces\CartAdjustments\FeeCartAdj) {
            if (isset($data['options'][0])) {
                $data['options'][$adj::FEE_VALUE_KEY] = $data['options'][0];
            }

            if (isset($data['options'][1])) {
                $data['options'][$adj::FEE_NAME_KEY] = $data['options'][1];
            }

            if (isset($data['options'][2])) {
                $data['options'][$adj::FEE_TAX_CLASS_KEY] = $data['options'][2];
            }

            unset($data['options'][0]);
            unset($data['options'][1]);
            unset($data['options'][2]);
        } elseif ($adj instanceof Interfaces\CartAdjustments\ShippingCartAdj && $adj instanceof Interfaces\CartAdjustments\ShippingMethodCartAdj) {
            if (isset($data['options'][0])) {
                $data['options'][$adj::SHIPPING_CARTADJ_METHOD] = $data['options'][0];
            }
            if (isset($data['options'][1])) {
                $data['options'][$adj::SHIPPING_CARTADJ_VALUE] = $data['options'][1];
            }
            unset($data['options'][0]);
            unset($data['options'][1]);
        } elseif ($adj instanceof Interfaces\CartAdjustments\ShippingCartAdj) {
            if (isset($data['options'][0])) {
                $data['options'][$adj::SHIPPING_CARTADJ_VALUE] = $data['options'][0];
            }
            unset($data['options'][0]);
        } elseif ($adj instanceof Interfaces\CartAdjustments\ShippingMethodCartAdj) {
            if (isset($data['options'][0])) {
                $data['options'][$adj::SHIPPING_CARTADJ_METHOD] = $data['options'][0];
            }
            unset($data['options'][0]);
        }

        return $data;
    }

    public static function convertCartAdjToArray($adj)
    {
        $result         = array();
        $result['type'] = $adj->getType();

        if ($adj instanceof Interfaces\CartAdjustments\CouponCartAdj) {
            $result['options'] = array(
                0 => $adj->getCouponValue(),
                1 => $adj->getCouponCode(),
            );
        } elseif ($adj instanceof Interfaces\CartAdjustments\FeeCartAdj) {
            $result['options'] = array(
                0 => $adj->getFeeValue(),
                1 => $adj->getFeeName(),
                2 => $adj->getFeeTaxClass(),
            );
        } elseif ($adj instanceof Interfaces\CartAdjustments\ShippingCartAdj) {
            $result['options'] = array(
                0 => $adj->getShippingCartAdjValue(),
            );
        }

        return $result;
    }

    public static function convertLimit($data)
    {
        if (empty($data['type'])) {
            throw new Exception('Missing cart adjustment type');
        }

        $limitsLoader = Factory::get("Rule_LimitsLoader");
        /** @var LimitsLoader $limitsLoader */
        $limit = $limitsLoader->create($data['type']);

        if ($limit instanceof Interfaces\Limits\MaxUsageLimit) {
            $data['options'] = array(
                $limit::MAX_USAGE_KEY => $data['options'],
            );
        }

        return $data;
    }

    public static function convertLimitToArray($limit)
    {
        $result         = array();
        $result['type'] = $limit->getType();

        if ($limit instanceof Interfaces\Limits\MaxUsageLimit) {
            $result['options'] = $limit->getMaxUsage();
        }

        return $result;
    }
}
