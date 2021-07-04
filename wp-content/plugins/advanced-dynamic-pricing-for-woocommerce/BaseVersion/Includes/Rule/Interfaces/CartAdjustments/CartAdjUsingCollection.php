<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface CartAdjUsingCollection
{
    /**
     * @param Rule $rule
     * @param Cart $cart
     * @param CartItemsCollection $itemsCollection
     */
    public function applyToCartWithItems($rule, $cart, $itemsCollection);

    /**
     * @param Rule $rule
     * @param Cart $cart
     * @param CartSetCollection $setCollection
     */
    public function applyToCartWithSets($rule, $cart, $setCollection);
}
