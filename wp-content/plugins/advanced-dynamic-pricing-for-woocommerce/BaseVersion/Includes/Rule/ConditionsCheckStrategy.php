<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ConditionsCheckStrategy
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
        $conditions = $this->rule->getConditions();

        if (count($conditions) === 0) {
            return true;
        }

        $relationship = $this->rule->getConditionsRelationship();
        $result       = false;

        foreach ($conditions as $condition) {
            if ($condition->check($cart)) {
                // check_conditions always true if relationship not 'and' and at least one condition checked
                $result = true;
            } elseif ('and' == $relationship) {
                return false;
            }
        }

        return $result;
    }

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function match($cart)
    {
        $conditions = $this->rule->getConditions();

        if (count($conditions) === 0) {
            return true;
        }

        $relationship = $this->rule->getConditionsRelationship();
        $result       = false;

        foreach ($conditions as $condition) {
            if ($condition->match($cart)) {
                // check_conditions always true if relationship not 'and' and at least one condition checked
                $result = true;
            } elseif ('and' == $relationship) {
                return false;
            }
        }

        return $result;
    }
}
