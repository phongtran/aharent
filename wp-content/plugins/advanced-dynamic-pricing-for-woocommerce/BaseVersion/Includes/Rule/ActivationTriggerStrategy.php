<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ActivationTriggerStrategy
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
    public function canBeAppliedUsingCouponCode($cart)
    {
        if ($this->rule->getActivationCouponCode() === null) {
            return true;
        }

        return in_array($this->rule->getActivationCouponCode(), $cart->getRuleTriggerCoupons(), true);
    }
}
