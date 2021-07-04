<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface RuleCondition
{
    public function __construct();

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function check($cart);

    /** @return array|null */
    public function getInvolvedCartItems();

    /**
     * @param Cart $cart
     *
     * @return bool
     */
    public function match($cart);

    /**
     * @return bool
     */
    public function hasProductDependency();

    /**
     * @return array
     */
    public function getProductDependency();

    /**
     * Compatibility with currency plugins
     *
     * @param float $rate
     */
    public function multiplyAmounts($rate);

    /**
     * @param string $languageCode
     *
     */
    public function translate($languageCode);

    /**
     * @return string
     */
    public static function getType();

    /**
     * @return string Localized label
     */
    public static function getLabel();

    /**
     * @return string
     */
    public static function getTemplatePath();

    /**
     * @return string
     */
    public static function getGroup();

    /**
     * @return bool
     */
    public function isValid();
}
