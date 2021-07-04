<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface CouponInterface
{
    /**
     * @param string $code
     */
    public function setCode($code);

    /**
     * @return string
     */
    public function getCode();

    /**
     * @param float $value
     */
    public function setValue($value);

    /**
     * @return float
     */
    public function getValue();

    /**
     * @return int
     */
    public function getRuleId();

    /**
     * @param float $amount
     */
    public function setMaxDiscount($amount);

    /**
     * @return float
     */
    public function getMaxDiscount();

    /**
     * @param string $label
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @return bool
     */
    public function isMaxDiscountDefined();
}
