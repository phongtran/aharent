<?php

namespace ADP\BaseVersion\Includes\Translators;

use ADP\BaseVersion\Includes\Enums\GiftChoiceTypeEnum;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\Discount;
use ADP\BaseVersion\Includes\Rule\Structures\NoItemRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentSplit;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentTotal;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule\ProductsAdjustment;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RuleTranslator
{
    /**
     * @param SingleItemRule|PackageRule|NoItemRule $rule
     * @param float $rate
     *
     * @return NoItemRule|PackageRule|SingleItemRule
     */
    public static function setCurrency($rule, $rate)
    {
        //$this->currency = $currency;

        if ($rule->hasProductAdjustment()) {
            $productAdj = $rule->getProductAdjustmentHandler();
            if ($productAdj instanceof ProductsAdjustment or
                $productAdj instanceof ProductsAdjustmentTotal) {
                if ($productAdj->isMaxAvailableAmountExists()) {
                    $productAdj->setMaxAvailableAmount($productAdj->getMaxAvailableAmount() * $rate);
                }
                $discount = $productAdj->getDiscount();
                if ($discount->getType() !== Discount::TYPE_PERCENTAGE) {
                    $discount->setValue($discount->getValue() * $rate);
                }
                $productAdj->setDiscount($discount);
            } elseif ($productAdj instanceof ProductsAdjustmentSplit) {
                $discounts = $productAdj->getDiscounts();
                foreach ($discounts as &$discount) {
                    if ($discount->getType() !== Discount::TYPE_PERCENTAGE) {
                        $discount->setValue($discount->getValue() * $rate);
                    }
                }
                $productAdj->setDiscounts($discounts);
            }

            $rule->installProductAdjustmentHandler($productAdj);
        }

        if ($rule->hasProductRangeAdjustment()) {
            $productAdj = $rule->getProductRangeAdjustmentHandler();
            $ranges     = $productAdj->getRanges();
            foreach ($ranges as &$range) {
                $discount = $range->getData();
                if ($discount->getType() !== Discount::TYPE_PERCENTAGE) {
                    $discount->setValue($discount->getValue() * $rate);
                    $range->setData($discount);
                }
            }
            $productAdj->setRanges($ranges);

            $rule->installProductRangeAdjustmentHandler($productAdj);
        }

        $roleDiscounts = array();
        if ($rule->getRoleDiscounts() !== null) {
            foreach ($rule->getRoleDiscounts() as $roleDiscount) {
                $discount = $roleDiscount->getDiscount();
                if ($discount->getType() !== Discount::TYPE_PERCENTAGE) {
                    $discount->setValue($discount->getValue() * $rate);
                }
                $roleDiscount->setDiscount($discount);
                $roleDiscounts[] = $roleDiscount;
            }
            $rule->setRoleDiscounts($roleDiscounts);
        }

        if ($rule->getCartAdjustments()) {
            $cartAdjs = $rule->getCartAdjustments();
            foreach ($cartAdjs as $cartAdjustment) {
                $cartAdjustment->multiplyAmounts($rate);
            }
            $rule->setCartAdjustments($cartAdjs);
        }

        if ($rule->getConditions()) {
            $cart_conditions = $rule->getConditions();
            foreach ($cart_conditions as $cart_condition) {
                $cart_condition->multiplyAmounts($rate);
            }
            $rule->setConditions($cart_conditions);
        }

        if ($rule instanceof SingleItemRule || $rule instanceof PackageRule) {
            $rule->setItemGiftSubtotalDivider($rule->getItemGiftSubtotalDivider() * $rate);
        }

        return $rule;
    }

    /**
     * @param Rule $rule
     * @param string $languageCode
     *
     * @return Rule
     */
    public static function translate($rule, $languageCode)
    {
        $filterTranslator = new FilterTranslator();

        if ($rule instanceof SingleItemRule) {
            $filters = array();
            foreach ($rule->getFilters() as $filter) {
                $filter->setValue($filterTranslator->translateByType($filter->getType(), $filter->getValue(),
                    $languageCode));
                $filter->setExcludeProductIds($filterTranslator->translateProduct($filter->getExcludeProductIds(),
                    $languageCode));
                $filters[] = $filter;
            }
            $rule->setFilters($filters);
        } elseif ($rule instanceof PackageRule) {
            $packages = array();
            foreach ($rule->getPackages() as $package) {
                $filters = array();
                foreach ($package->getFilters() as $filter) {
                    $filter->setValue($filterTranslator->translateByType($filter->getType(), $filter->getValue(),
                        $languageCode));
                    $filter->setExcludeProductIds($filterTranslator->translateProduct($filter->getExcludeProductIds(),
                        $languageCode));
                    $filters[] = $filter;
                }
                $package->setFilters($filters);
                $packages[] = $package;
            }
            $rule->setPackages($packages);
        }

        if ($rule instanceof SingleItemRule || $rule instanceof PackageRule) {
            if ($rule->hasProductRangeAdjustment()) {
                $productAdj = $rule->getProductRangeAdjustmentHandler();
                $productAdj->setSelectedProductIds($filterTranslator->translateProduct($productAdj->getSelectedCategoryIds(),
                    $languageCode));
                $productAdj->setSelectedCategoryIds($filterTranslator->translateCategory($productAdj->getSelectedCategoryIds(),
                    $languageCode));
                $rule->installProductRangeAdjustmentHandler($productAdj);
            }

            foreach ( $rule->getItemGiftsCollection()->asArray() as $gift ) {
                foreach ($gift->getChoices() as $choice) {
                    if ( $choice->getType()->equals(GiftChoiceTypeEnum::PRODUCT())) {
                        $choice->setValues($filterTranslator->translateProduct($choice->getValues(), $languageCode));
                    }

                    if ( $choice->getType()->equals(GiftChoiceTypeEnum::CATEGORY())) {
                        $choice->setValues($filterTranslator->translateCategory($choice->getValues(), $languageCode));
                    }
                }
            }
        }

        $cart_conditions = array();
        foreach ($rule->getConditions() as $cartCondition) {
            $cartCondition->translate($languageCode);
            $cart_conditions[] = $cartCondition;
        }
        $rule->setConditions($cart_conditions);

        return $rule;
    }
}
