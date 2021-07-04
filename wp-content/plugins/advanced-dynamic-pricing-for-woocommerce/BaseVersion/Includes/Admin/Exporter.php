<?php

namespace ADP\BaseVersion\Includes\Admin;

use ADP\BaseVersion\Includes\Rule\Enums\ProductAdjustmentSplitDiscount;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\Cart\RulesCollection;
use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Rule\OptionsConverter;
use ADP\BaseVersion\Includes\Rule\Structures\Discount;
use ADP\BaseVersion\Includes\Rule\Structures\NoItemRule;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentSplit;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule\ProductsAdjustmentTotal;
use ADP\BaseVersion\Includes\Rule\Structures\SetDiscount;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule\ProductsAdjustment;
use ADP\BaseVersion\Includes\Structures\GiftChoice;
use ADP\Factory;

class Exporter
{
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function exportRules()
    {
        $rules       = Database::getRules(array('exclusive' => 0));
        $ruleStorage = Factory::get("External_RuleStorage", $this->context);
        $rulesCol    = $ruleStorage->buildRules($rules);
        /**
         * @var RulesCollection $rulesCol
         */

        $rules = array();

        foreach ($rulesCol->getRules() as $ruleObject) {
            $rule = $this->convertRule($ruleObject);

            $rules[] = $rule;
        }

        return $rules;
    }

    /**
     * @param Rule $ruleObject
     *
     * @return array
     */
    public function convertRule($ruleObject)
    {
        $rule = array();
        if ($ruleObject instanceof SingleItemRule) {
            $rule[KeyKeeperDB::TYPE] = 'single_item';

            $filters = array();
            foreach ($ruleObject->getFilters() as $filter) {
                $filters[] = array(
                    'qty'             => 1,
                    'type'            => $filter->getType(),
                    'method'          => $filter->getMethod(),
                    'value'           => $filter->getValue(),
                    'product_exclude' => array(
                        'on_wc_sale'       => $filter->isExcludeWcOnSale() ? "1" : "",
                        'already_affected' => $filter->isExcludeAlreadyAffected() ? "1" : "",
                        'backorder'        => $filter->isExcludeBackorder() ? "1" : "",
                        'values'           => $filter->getExcludeProductIds() ? $filter->getExcludeProductIds() : array(),
                    ),
                );
            }
            $rule[KeyKeeperDB::FILTERS] = $filters;
        } elseif ($ruleObject instanceof PackageRule) {
            $rule[KeyKeeperDB::TYPE] = 'package';

            $filters = array();
            foreach ($ruleObject->getPackages() as $package) {
                foreach ($package->getFilters() as $filter) {
                    $filters[] = array(
                        'qty'             => $package->getQty(),
                        'qty_end'         => $package->getQtyEnd(),
                        'type'            => $filter->getType(),
                        'method'          => $filter->getMethod(),
                        'value'           => $filter->getValue(),
                        'product_exclude' => array(
                            'on_wc_sale'       => $filter->isExcludeWcOnSale() ? "1" : "",
                            'already_affected' => $filter->isExcludeAlreadyAffected() ? "1" : "",
                            'backorder'        => $filter->isExcludeBackorder() ? "1" : "",
                            'values'           => $filter->getExcludeProductIds() ? $filter->getExcludeProductIds() : array(),
                        ),
                    );
                }
            }
            $rule[KeyKeeperDB::FILTERS] = $filters;
        } else {
            $rule[KeyKeeperDB::TYPE] = 'no_item';

            $rule[KeyKeeperDB::FILTERS] = array();
        }

        $rule[KeyKeeperDB::TYPE]     = 'package';
        $rule[KeyKeeperDB::TITLE]    = $ruleObject->getTitle();
        $rule[KeyKeeperDB::PRIORITY] = $ruleObject->getPriority() ? $ruleObject->getPriority() : "";
        $rule[KeyKeeperDB::ENABLED]  = $ruleObject->getEnabled() ? "on" : "off";

        if ( ! ($ruleObject instanceof NoItemRule)) {
            $rule[KeyKeeperDB::SORT_BLOCKS_PRIOR] = $ruleObject->getSortableBlocksPriority();
        }
        //options?

        $additional                            = array();
        $additional['conditions_relationship'] = $ruleObject->getConditionsRelationship() ? $ruleObject->getConditionsRelationship() : "";
        if ( ! ($ruleObject instanceof NoItemRule)) {
            if ($ruleObject->hasProductAdjustment()) {
                $additional['is_replaced']  = $ruleObject->getProductAdjustmentHandler()->isReplaceWithCartAdjustment();
                $additional['replace_name'] = $ruleObject->getProductAdjustmentHandler()->getReplaceCartAdjustmentCode();
            } elseif ($ruleObject->getProductRangeAdjustmentHandler()) {
                $additional['is_replaced']  = $ruleObject->getProductRangeAdjustmentHandler()->isReplaceWithCartAdjustment();
                $additional['replace_name'] = $ruleObject->getProductRangeAdjustmentHandler()->getReplaceCartAdjustmentCode();
            } else {
                $additional['replace_name'] = "";
            }
            $additional['is_replace_free_products_with_discount'] = $ruleObject->isReplaceItemGifts();
            $additional['free_products_replace_name']             = $ruleObject->getReplaceItemGiftsCode();
            $additional['sortable_apply_mode']                    = $ruleObject->getSortableApplyMode();
        } else {
            $additional['replace_name']               = "";
            $additional['free_products_replace_name'] = "";
            $additional['sortable_apply_mode']        = "consistently";
        }
        $rule[KeyKeeperDB::ADDITIONAL] = $additional;

        $filters = array();


        $conditions = array();
        foreach ($ruleObject->getConditions() as $condition) {
            $conditions[] = OptionsConverter::convertConditionToArray($condition);
        }
        $rule[KeyKeeperDB::CONDITIONS] = $conditions;

        $cart_adjs = array();
        foreach ($ruleObject->getCartAdjustments() as $adj) {
            $cart_adjs[] = OptionsConverter::convertCartAdjToArray($adj);
        }
        $rule[KeyKeeperDB::CART_ADJS] = $cart_adjs;

        $limits = array();
        foreach ($ruleObject->getLimits() as $limit) {
            $limits[] = OptionsConverter::convertLimitToArray($limit);
        }
        $rule[KeyKeeperDB::LIMITS] = $limits;

        if ( ! ($ruleObject instanceof NoItemRule)) {
            if ($ruleObject->hasProductAdjustment()) {
                $product_adj = array();
                $adj_handler = $ruleObject->getProductAdjustmentHandler();
                if ($adj_handler instanceof ProductsAdjustment or
                    $adj_handler instanceof ProductsAdjustmentTotal) {
                    $product_adj['type']  = "total";
                    $product_adj['total'] = array(
                        'type'  => $this->getDiscountType($adj_handler->getDiscount()),
                        'value' => $adj_handler->getDiscount()->getValue(),
                    );
                    if ($adj_handler->isMaxAvailableAmountExists()) {
                        $product_adj['max_discount_sum'] = $adj_handler->getMaxAvailableAmount();
                    }

                    if ( $adj_handler instanceof ProductsAdjustmentTotal ) {
                        $product_adj['split_discount_by'] = $adj_handler->getSplitDiscount()->getValue();
                    } else {
                        $product_adj['split_discount_by'] = ProductAdjustmentSplitDiscount::ITEM_COST();
                    }
                    $rule[KeyKeeperDB::PROD_ADJS] = $product_adj;
                } elseif ($adj_handler instanceof ProductsAdjustmentSplit) {
                    $product_adj['type'] = "split";
                    foreach ($adj_handler->getDiscounts() as $discount) {
                        $product_adj['split'][] = array(
                            'type'  => $this->getDiscountType($discount),
                            'value' => $discount->getValue(),
                        );
                    }
                    if ($adj_handler->isMaxAvailableAmountExists()) {
                        $product_adj['max_discount_sum'] = $adj_handler->getMaxAvailableAmount();
                    }

                    $product_adj['split_discount_by'] = $adj_handler->getSplitDiscount()->getValue();

                    $rule[KeyKeeperDB::PROD_ADJS] = $product_adj;
                }
            }

            if ($adj_handler = $ruleObject->getProductRangeAdjustmentHandler()) {
                $product_adj['type']          = $adj_handler->getType();
                $product_adj['qty_based']     = $adj_handler->getGroupBy();
                $ranges                       = $adj_handler->getRanges();
                $product_adj['discount_type'] = $this->getDiscountType($ranges[0]->getData());
                foreach ($ranges as $range) {
                    $product_adj['ranges'][] = array(
                        'from'  => $range->getFrom(),
                        'to'    => $range->getTo() !== INF ? $range->getTo() : "",
                        'value' => $range->getData()->getValue(),
                    );
                }
                $product_adj['table_message'] = $adj_handler->getPromotionalMessage() ? $adj_handler->getPromotionalMessage() : "";

                $rule[KeyKeeperDB::BULK_ADJS] = $product_adj;
            }

            $role_discounts = array();
            if ($ruleObject->isDontApplyBulkIfRolesMatched()) {
                $role_discounts['dont_apply_bulk_if_roles_matched'] = "1";
            }
            if (null !== $ruleObject->getRoleDiscounts()) {
                foreach ($ruleObject->getRoleDiscounts() as $role_discount) {
                    $role_discounts['rows'][] = array(
                        'discount_type'  => $this->getDiscountType($role_discount->getDiscount()),
                        'discount_value' => $role_discount->getDiscount()->getValue(),
                        'roles'          => $role_discount->getRoles()
                    );
                }
            }

            $rule[KeyKeeperDB::ROLE_DISCOUNTS] = $role_discounts;

            $free_products = $this->addFreeProductsRepeatAndSubtotal($ruleObject);

            foreach ($ruleObject->getItemGiftsCollection()->asArray() as $gift) {
                $giftChoices = $gift->getChoices();
                /** @var GiftChoice $giftChoice */
                $giftChoice = reset($giftChoices);

                $free_products['value'][] = array(
                    'qty'       => $gift->getQty(),
                    'gift_mode' => $gift->getMode()->getValue(),
                    'type'      => $giftChoice->getType()->getValue(),
                    'value'     => $giftChoice->getValues(),
                );
            }
            $rule[KeyKeeperDB::FREE_PRODUCTS] = $free_products;
        }

        $options = array();
        if ( ! ($ruleObject instanceof NoItemRule)) {
            $options['apply_to'] = $ruleObject->getApplyFirstTo();
            if ($ruleObject instanceof SingleItemRule) {
                $options['repeat'] = $ruleObject->getItemsCountLimit();
            } elseif ($ruleObject instanceof PackageRule) {
                $options['repeat'] = $ruleObject->getPackagesCountLimit();
            }
        } else {
            $options = array(
                'repeat'   => "-1",
                'apply_to' => "expensive",
            );
        }
        $rule[KeyKeeperDB::OPTIONS] = $options;

        return $rule;
    }

    protected function addFreeProductsRepeatAndSubtotal($ruleObject)
    {
        $free_products = array();
        if ($ruleObject->getItemGiftStrategy() === $ruleObject::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY) {
            $free_products['repeat']          = $ruleObject->getItemGiftLimit() === INF ? "-1" : $ruleObject->getItemGiftLimit();
            $free_products['repeat_subtotal'] = "";
        } elseif ($ruleObject->getItemGiftStrategy() === $ruleObject::BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY) {
            $free_products['repeat']          = "based_on_subtotal";
            $free_products['repeat_subtotal'] = $ruleObject->getItemGiftSubtotalDivider();
        }

        return $free_products;
    }

    /**
     * @param SetDiscount|Discount $discount
     *
     * @return string
     * @return string
     */
    public function getDiscountType($discount)
    {
        $discountType = $discount->getType();
        $setPrefix    = "";
        if ($discount instanceof SetDiscount) {
            $setPrefix = "set_";
        }

        if ($discountType === Discount::TYPE_FIXED_VALUE) {
            return $setPrefix . "price__fixed";
        } elseif ($discountType === Discount::TYPE_PERCENTAGE) {
            return "discount__percentage";
        } elseif ($discountType === Discount::TYPE_AMOUNT) {
            return $setPrefix . "discount__amount";
        }

        return null;
    }
}
