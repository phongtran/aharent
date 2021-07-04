<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface ShippingCartAdj
{
    const SHIPPING_CARTADJ_VALUE = 'shipping_cartadj_value';

    /**
     * @param float|string $shippingCartAdjValue
     */
    public function setShippingCartAdjValue($shippingCartAdjValue);

    /**
     * @return float
     */
    public function getShippingCartAdjValue();
}
