<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\External\WC\WcCustomerConverter;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartTotals
{
    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var WcCustomerConverter
     */
    protected $wcCustomerConverter;

    /**
     * @param Cart $cart
     */
    public function __construct($cart)
    {
        $this->cart                = $cart;
        $this->wcCustomerConverter = Factory::get("External_WC_WcCustomerConverter",
            $cart->getContext()->getGlobalContext());
    }

    /**
     * @param bool $inclTax
     *
     * @return float
     */
    protected function calculateItemsSubtotals($inclTax = true)
    {
        /** @see \WC_Cart_Totals::calculate_item_subtotals */

        $cart                            = $this->cart;
        $cartContext                     = $cart->getContext();
        $context                         = $cartContext->getGlobalContext();
        $adjust_non_base_location_prices = apply_filters('woocommerce_adjust_non_base_location_prices', true);
        $is_customer_vat_exempt          = $cart->getContext()->getCustomer()->isVatExempt();
        $calculate_tax                   = $context->getIsTaxEnabled() && ! $is_customer_vat_exempt;

        $itemsSubtotals = floatval(0);
        foreach ($cart->getItems() as $item) {
            $product          = $item->getWcItem()->getProduct();
            $priceIncludesTax = $context->getIsPricesIncludeTax();
            $taxable          = $context->getIsTaxEnabled() && 'taxable' === $product->get_tax_status();

            if ($item->isPriceChanged()) {
                $price = $item->getTotalPrice();
            } else {
                $price = $product->is_on_sale('edit') ? (float)$product->get_sale_price('edit') : $item->getPrice();
                $price *= $item->getQty();
            }

            $wcCustomer = $this->wcCustomerConverter->convertToWcCustomer($cartContext->getCustomer());

            if ($context->getIsTaxEnabled()) {
                $tax_rates = \WC_Tax::get_rates($product->get_tax_class(), $wcCustomer);
            } else {
                $tax_rates = array();
            }

            if ($priceIncludesTax) {
                if ($is_customer_vat_exempt) {

                    /** @see \WC_Cart_Totals::remove_item_base_taxes */
                    if ($priceIncludesTax && $taxable) {
                        if (apply_filters('woocommerce_adjust_non_base_location_prices', true)) {
                            $base_tax_rates = \WC_Tax::get_base_tax_rates($product->get_tax_class('unfiltered'));
                        } else {
                            $base_tax_rates = $tax_rates;
                        }

                        // Work out a new base price without the shop's base tax.
                        $taxes = \WC_Tax::calc_tax($price, $base_tax_rates, true);

                        // Now we have a new item price (excluding TAX).
                        $price            = round($price - array_sum($taxes));
                        $priceIncludesTax = false;
                    }

                } elseif ($adjust_non_base_location_prices) {

                    /** @see \WC_Cart_Totals::adjust_non_base_location_price */
                    if ($priceIncludesTax && $taxable) {
                        $base_tax_rates = \WC_Tax::get_base_tax_rates($product->get_tax_class('unfiltered'));

                        if ($tax_rates !== $base_tax_rates) {
                            // Work out a new base price without the shop's base tax.
                            $taxes     = \WC_Tax::calc_tax($price, $base_tax_rates, true);
                            $new_taxes = \WC_Tax::calc_tax($price - array_sum($taxes), $tax_rates, false);

                            // Now we have a new item price.
                            $price = $price - array_sum($taxes) + array_sum($new_taxes);
                        }
                    }

                }
            }

            $subtotal     = $price;
            $subtotal_tax = floatval(0);

            if ($calculate_tax && $taxable) {
                $subtotal_taxes = \WC_Tax::calc_tax($subtotal, $tax_rates, $priceIncludesTax);
                $subtotal_tax   = array_sum(array_map(array($this, 'roundLineTax'), $subtotal_taxes));

                if ($priceIncludesTax) {
                    // Use unrounded taxes so we can re-calculate from the orders screen accurately later.
                    $subtotal = $subtotal - array_sum($subtotal_taxes);
                }
            }

            $itemsSubtotals += $subtotal;

            if ($inclTax) {
                $itemsSubtotals += $subtotal_tax;
            }
        }

        return $itemsSubtotals;
    }

    protected static function roundLineTax($value, $in_cents = true)
    {
        if ( ! self::roundAtSubtotal()) {
            $value = wc_round_tax_total($value, $in_cents ? 0 : null);
        }

        return $value;
    }

    protected static function roundAtSubtotal()
    {
        return 'yes' === get_option('woocommerce_tax_round_at_subtotal');
    }

    /**
     * @param bool $inclTax
     *
     * @return float
     */
    public function getSubtotal($inclTax = false)
    {
        return $this->calculateItemsSubtotals($inclTax);
    }
}
