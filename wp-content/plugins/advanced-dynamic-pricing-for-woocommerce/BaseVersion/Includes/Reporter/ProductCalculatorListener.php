<?php

namespace ADP\BaseVersion\Includes\Reporter;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Reporter\Interfaces\Listener;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleProcessor;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductCalculatorListener implements Listener
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var array
     */
    protected $totals;

    /**
     * @var array
     */
    protected $currentProductTotals;

    public function __construct($context)
    {
        $this->context              = $context;
        $this->totals               = array();
        $this->currentProductTotals = array();
    }

    /**
     * @param WC_Product $product
     */
    public function startCartProcessProduct($product)
    {
        $prodId                     = $product->get_id();
        $this->currentProductTotals = array();
        if ( ! isset($this->totals[$prodId])) {
            $this->totals[$prodId] = array();
        }
    }

    public function calcProcessStarted()
    {

    }

    /**
     * @param RuleProcessor $proc
     */
    public function ruleCalculated($proc)
    {
        if ( ! isset($this->currentProductTotals['rules'])) {
            $this->currentProductTotals['rules'] = array();
        }

        $this->currentProductTotals['rules'][] = array(
            'id'        => $proc->getRule()->getId(),
            'status'    => $proc->getStatus(),
            'exec_time' => $proc->getLastExecTime(),
        );
    }

    /**
     * @param bool $result
     */
    public function processResult($result)
    {

    }

    /**
     * @param WC_Product $product
     */
    public function finishCartProcessProduct($product)
    {

    }

    /**
     * @param ProcessedProductSimple $product
     */
    public function processedProduct($product)
    {
        $prodId                                = $product->getProduct()->get_id();
        $this->currentProductTotals['results'] = array(
            'parent_id'                     => $product->getProduct()->get_parent_id(),
            'name'                          => $product->getProduct()->get_name(),
            'page_url'                      => get_edit_post_link($prodId),
            'original_price'                => $product->getOriginalPrice(),
            'calculated_price'              => $product->getCalculatedPrice(),
            'price'                         => $product->getPrice(),
            'qty'                           => $product->getQty(),
            'are_rule_applied'              => $product->areRulesApplied(),
            'is_price_changed'              => $product->isPriceChanged(),
            'is_affected_by_range_discount' => $product->isAffectedByRangeDiscount(),
        );
        $this->currentProductTotals['rules']   = $product->getHistory();
        $this->totals[$prodId][]               = $this->currentProductTotals;
        $this->currentProductTotals            = array();
    }

    public function getTotals()
    {
        return $this->totals;
    }

}
