<?php

namespace ADP\BaseVersion\Includes;

use Exception;
use WC_Cart;
use WC_Cart_Fees;
use WC_Cart_Session;

class StandaloneCart extends WC_Cart
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        $this->session          = new WC_Cart_Session($this);
        $this->fees_api         = new WC_Cart_Fees($this);
        $this->tax_display_cart = $this->isTaxDisplayedNew();

        add_action('woocommerce_add_to_cart', array($this, 'calculate_totals'), 20, 0);
        add_action('woocommerce_applied_coupon', array($this, 'calculate_totals'), 20, 0);
        add_action('woocommerce_cart_item_removed', array($this, 'calculate_totals'), 20, 0);
        add_action('woocommerce_cart_item_restored', array($this, 'calculate_totals'), 20, 0);
        add_action('woocommerce_check_cart_items', array($this, 'check_cart_items'), 1);
        add_action('woocommerce_check_cart_items', array($this, 'check_cart_coupons'), 1);
        add_action('woocommerce_after_checkout_validation', array($this, 'check_customer_coupons'), 1);
    }

    /**
     * Returns 'incl' if tax should be included in cart, otherwise returns 'excl'.
     *
     * @return string
     */
    private function isTaxDisplayedNew()
    {
        if ($this->get_customer() && $this->get_customer()->get_is_vat_exempt()) {
            return 'excl';
        }

        return get_option('woocommerce_tax_display_cart');
    }
}
