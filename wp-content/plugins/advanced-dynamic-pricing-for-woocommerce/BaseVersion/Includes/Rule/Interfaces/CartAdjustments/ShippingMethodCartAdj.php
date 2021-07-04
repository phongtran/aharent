<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface ShippingMethodCartAdj
{
    const SHIPPING_CARTADJ_METHOD = 'shipping_cartadj_method';

    /**
     * @param string $shippingCartAdjMethod
     */
    public function setShippingCartAdjMethod($shippingCartAdjMethod);

    public function getShippingCartAdjMethod();
}
