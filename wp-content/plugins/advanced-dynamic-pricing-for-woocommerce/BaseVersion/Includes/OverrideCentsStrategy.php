<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\Cart\Structures\CartItem;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class OverrideCentsStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @param float $price
     * @param CartItem $item
     *
     * @return float
     */
    public function maybeOverrideCentsForItem($price, $item)
    {
        $product = $item->getWcItem()->getProduct();

        if ($customPrice = apply_filters("wdp_custom_override_cents", false, $price, $this->context, $product,
            $item)) {
            return $customPrice;
        }

        if ( ! $this->context->getOption('is_override_cents')) {
            return $price;
        }

        $pricesEndsWith = $this->context->getOption('prices_ends_with');

        $priceFraction    = $price - intval($price);
        $newPriceFraction = $pricesEndsWith / 100;

        $roundNewPriceFraction = round($newPriceFraction);

        if (0 == intval($price) and 0 < $newPriceFraction) {
            $price = $newPriceFraction;

            return $price;
        }

        if ($roundNewPriceFraction) {

            if ($priceFraction <= $newPriceFraction - round(1 / 2, 2)) {
                $price = intval($price) - 1 + $newPriceFraction;
            } else {
                $price = intval($price) + $newPriceFraction;
            }

        } else {

            if ($priceFraction >= $newPriceFraction + round(1 / 2, 2)) {
                $price = intval($price) + 1 + $newPriceFraction;
            } else {
                $price = intval($price) + $newPriceFraction;
            }

        }

        return $price;
    }

    /**
     * @param float $price
     *
     * @return float
     */
    public function maybeOverrideCents($price)
    {
        if ( ! $this->context->getOption('is_override_cents')) {
            return $price;
        }

        $pricesEndsWith = $this->context->getOption('prices_ends_with');

        $priceFraction    = $price - intval($price);
        $newPriceFraction = $pricesEndsWith / 100;

        $roundNewPriceFraction = round($newPriceFraction);

        if (0 == intval($price) and 0 < $newPriceFraction) {
            $price = $newPriceFraction;

            return $price;
        }

        if ($roundNewPriceFraction) {

            if ($priceFraction <= $newPriceFraction - round(1 / 2, 2)) {
                $price = intval($price) - 1 + $newPriceFraction;
            } else {
                $price = intval($price) + $newPriceFraction;
            }

        } else {

            if ($priceFraction >= $newPriceFraction + round(1 / 2, 2)) {
                $price = intval($price) + 1 + $newPriceFraction;
            } else {
                $price = intval($price) + $newPriceFraction;
            }

        }

        return $price;
    }
}
