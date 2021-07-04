<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartContext;
use ADP\BaseVersion\Includes\Cart\Structures\Fee;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use WC_Cart;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartFeeProcessor
{
    /**
     * @var Fee[]
     */
    protected $fees;

    /**
     * @var CartContext
     */
    protected $cartContext;

    /**
     * @param Cart $cart
     */
    public function refreshFees($cart)
    {
        $this->fees = array();

        foreach ($cart->getFees() as $fee) {
            $this->fees[] = clone $fee;
        }

        $this->cartContext = $cart->getContext();
    }

    /**
     * @param WC_Cart $wcCart
     */
    public function sanitize($wcCart)
    {
        $this->fees = array();
    }

    /**
     * @param WC_Cart $wcCart
     */
    public function calculateFees($wcCart)
    {
        if (empty($this->fees) || empty($this->cartContext)) {
            return;
        }

        $context = $this->cartContext;

        $cartTotal = $wcCart->get_cart_contents_total();
        if ($context->isPricesIncludesTax()) {
            $cartTotal += $wcCart->get_cart_contents_tax();
        }

        $mergedFees = array();

        foreach ($this->fees as &$fee) {
            if ($fee->isType($fee::TYPE_FIXED_VALUE)) {
                $amount = $fee->getValue();
            } elseif ($fee->isType($fee::TYPE_PERCENTAGE)) {
                $amount = $cartTotal * $fee->getValue() / 100;
            } elseif ($fee->isType($fee::TYPE_ITEM_OVERPRICE)) {
                $amount = $fee->getValue();
            } else {
                continue;
            }

            $fee->setAmount($amount);

            if ($context->isCombineMultipleFees()) {
                $fee->setName($context->getOption('default_fee_name'));
            }

            $exists = false;
            foreach ($mergedFees as &$mergedFee) {
                $name     = $mergedFee['name'];
                $taxClass = $mergedFee['taxClass'];

                if ($name === $fee->getName() && $taxClass === $fee->getTaxClass()) {
                    $mergedFee['amount'] += $fee->getAmount();
                    $exists              = true;
                    break;
                }
            }

            if ( ! $exists) {
                $mergedFees[] = array(
                    'name'     => $fee->getName(),
                    'amount'   => $fee->getAmount(),
                    'taxable'  => $fee->isTaxAble(),
                    'taxClass' => $fee->getTaxClass(),
                );
            }
        }
        unset($fee);
        unset($mergedFee);

        foreach ($mergedFees as $mergedFee) {
            $wcCart->add_fee($mergedFee['name'], $mergedFee['amount'], $mergedFee['taxable'], $mergedFee['taxClass']);
        }

    }

    public function setFilterToCalculateFees()
    {
        add_filter('woocommerce_cart_calculate_fees', array($this, 'calculateFees'), 10, 3);
    }

    public function unsetFilterToCalculateFees()
    {
        remove_filter('woocommerce_cart_calculate_fees', array($this, 'calculateFees'), 10);
    }

    /**
     * @param WC_Cart $wcCart
     */
    public function updateTotals($wcCart)
    {
        $globalContext = $this->cartContext->getGlobalContext();
        $totalsWrapper = new WcTotalsFacade($globalContext, $wcCart);
        $totalsWrapper->insertFeesData($this->fees);
    }
}
