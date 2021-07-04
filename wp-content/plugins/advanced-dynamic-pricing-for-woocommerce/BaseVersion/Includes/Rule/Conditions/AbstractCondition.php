<?php

namespace ADP\BaseVersion\Includes\Rule\Conditions;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleCondition;
use ADP\BaseVersion\Includes\Traits\Comparison;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class AbstractCondition implements RuleCondition
{
    use Comparison;

    protected $amountIndexes = array();
    protected $hasProductDependency = false;

    public function __construct()
    {

    }

    /**
     * @param float $rate
     */
    public function multiplyAmounts($rate)
    {
    }

    /**
     * @return bool
     */
    public function check($cart)
    {
        return false;
    }

    public function getInvolvedCartItems()
    {
        return null;
    }

    /**
     * @return bool
     */
    public function match($cart)
    {
        return $this->check($cart);
    }

    public function hasProductDependency()
    {
        return $this->hasProductDependency;
    }

    public function getProductDependency()
    {
        return array();
    }

    public function translate($languageCode)
    {
        return;
    }
}
