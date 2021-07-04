<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface RuleLimit
{

    public function __construct();

    /**
     * @param Rule $rule
     * @param Cart $cart
     *
     * @return bool
     */
    public function check($rule, $cart);

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
