<?php

namespace ADP\BaseVersion\Includes\Interfaces;

use WC_Order;
use WP_User;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface User
{
    /**
     * @param WP_User|null $wp_user
     */
    public function __construct($wp_user = null);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return bool
     */
    public function isLoggedIn();

    /**
     * @return array
     */
    public function getRoles();

    /**
     * @param $time
     *
     * @return int
     */
    public function getOrderCountAfter($time);

    /**
     * @return string|null
     */
    public function getShippingCountry();

    /**
     * @param string $country
     */
    public function setShippingCountry($country);

    /**
     * @return string|null
     */
    public function getShippingState();

    /**
     * @param string $state
     */
    public function setShippingState($state);

    /**
     * @return string|null
     */
    public function getPaymentMethod();

    /**
     * @param string $method
     */
    public function setPaymentMethod($method);

    /**
     * @return string|null
     */
    public function getShippingMethods();

    /**
     * @param string $method
     */
    public function setShippingMethods($method);

    public function setIsVatExempt($taxExempt);

    public function getTaxExempt();

    /**
     * @return float
     */
    public function getAvgSpendAmount();

    /**
     * @param $time
     *
     * @return float
     */
    public function getTotalSpendAmount($time);

    /**
     * @return false|WC_Order
     */
    public function getLastPaidOrder();
}
