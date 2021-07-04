<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class LimitsCheckStrategy
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @param Rule $rule
     */
    public function __construct($rule)
    {
        $this->rule = $rule;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function check($cart)
    {
        $limits = $this->rule->getLimits();

        if (count($limits) === 0) {
            return true;
        }

        foreach ($limits as $limit) {
            if ( ! $limit->check($this->rule, $cart)) {
                return false;
            }
        }

        return true;
    }
}
