<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Cart\Structures\Cart;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface RuleProcessor
{
    /**
     * @param Context $context
     * @param Rule $rule
     */
    public function __construct($context, $rule);

    /**
     * @return Rule
     */
    public function getRule();

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function applyToCart($cart);

    /**
     * @return int
     */
    public function getStatus();

    /**
     * @return float
     */
    public function getLastExecTime();
}
