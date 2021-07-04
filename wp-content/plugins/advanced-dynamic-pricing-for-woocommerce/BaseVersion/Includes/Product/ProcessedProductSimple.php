<?php

namespace ADP\BaseVersion\Includes\Product;

use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\CompareStrategy;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\PriceFunctions;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\OverrideCentsStrategy;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProcessedProductSimple
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CompareStrategy
     */
    protected $compareStrategy;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @var WC_Product
     */
    protected $product;

    /**
     * @var float
     */
    protected $qty;

    /**
     * @var float
     */
    protected $qtyAlreadyInCart;

    /**
     * @var CartItem[]
     */
    protected $cartItems;

    /**
     * @var OverrideCentsStrategy
     */
    protected $overrideCentsStrategy;

    /**
     * @param Context $context
     * @param WC_Product $product
     * @param array<int, CartItem> $cartItems
     */
    public function __construct($context, $product, $cartItems)
    {
        $this->context         = $context;
        $this->compareStrategy = new CompareStrategy($context);
        $this->product         = $product;
        $this->cartItems       = $cartItems;

        $qty = floatval(0);
        foreach ($cartItems as $cartItem) {
            $qty += $cartItem->getQty();
        }
        $this->qty = $qty;

        $this->qtyAlreadyInCart = floatval(0);

        $this->priceFunctions = new PriceFunctions($context);

        $this->overrideCentsStrategy = new OverrideCentsStrategy($context);
    }

    /**
     * @param int|null $pos
     *
     * @return float|null
     */
    public function getOriginalPrice($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        return isset($item) ? $item->getOriginalPrice() : null;
    }

    /**
     * @param int|null $pos
     *
     * @return float|null
     */
    public function getCalculatedPrice($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        return $this->overrideCentsStrategy->maybeOverrideCentsForItem($item->getPrice(), $item);
    }

    /**
     * @param int|null $pos
     *
     * @return float|null
     */
    public function getPrice($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        $totalAdjustments = array_sum(array_map(function ($amounts) {
            return array_sum($amounts);
        }, $item->getDiscounts()));

        return ! $this->compareStrategy->floatsAreEqual($totalAdjustments,
            0) ? $this->overrideCentsStrategy->maybeOverrideCentsForItem($item->getPrice(), $item) : $item->getOriginalPrice();
    }

    /**
     * @param int|null $pos
     *
     * @return bool
     */
    public function areRulesApplied($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return false;
        }

        $totalAdjustments = array_sum(array_map(function ($amounts) {
            return array_sum($amounts);
        }, $item->getHistory()));

        return ! $this->compareStrategy->floatsAreEqual($totalAdjustments, 0);
    }

    /**
     * @param int|null $pos
     *
     * @return array<int, array<int,int>>
     */
    public function getHistory($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return array();
        }

        return $item->getHistory();
    }

    /**
     * @param int|null $pos
     *
     * @return array<int, array<int, int>>
     */
    public function getDiscounts($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return array();
        }

        return $item->getDiscounts();
    }

    /**
     * @param int|null $pos
     *
     * @return bool
     */
    public function isPriceChanged($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return false;
        }

        $totalAdjustments = array_sum(array_map(function ($amounts) {
            return array_sum($amounts);
        }, $item->getDiscounts()));

        return ! $this->compareStrategy->floatsAreEqual($totalAdjustments, 0);
    }

    /**
     * @param int|null $pos
     *
     * @return bool
     */
    public function isDiscounted($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return false;
        }

        $totalAdjustments = array_sum(array_map(function ($amounts) {
            return array_sum($amounts);
        }, $item->getDiscounts()));

        return $totalAdjustments > 0;
    }

    /**
     * @param int|null $pos
     *
     * @return bool
     */
    public function isAffectedByRangeDiscount($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return false;
        }

        $affected  = false;
        $discounts = $item->getObjDiscounts();
        foreach ($discounts as $discount) {
            if ($discount->isType($discount::SOURCE_SINGLE_ITEM_RANGE) || $discount->isType($discount::SOURCE_PACKAGE_RANGE)) {
                $affected = true;
                break;
            }
        }

        return $affected;
    }

    /**
     * @param int|null $pos
     *
     * @return int|null
     */
    public function getPos($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        return $item->getPos();
    }

    /**
     * @param int|null $pos
     *
     * @return WcCartItemFacade|null
     */
    public function getWcCartItem($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        return $item->getWcItem();
    }

    /**
     * @param int|null $pos
     *
     * @return float|null
     */
    public function getMinDiscountRangePrice($pos = null)
    {
        $item = $this->getItemByPos($pos);

        if ( ! isset($item)) {
            return null;
        }

        return $item->getMinDiscountRangePrice();
    }

    /**
     * @param int|null $pos
     *
     * @return CartItem|null
     */
    protected function getItemByPos($pos = null)
    {
        $pos  = is_numeric($pos) ? intval($pos) : null;
        $item = null;

        if (is_null($pos)) {
            $item = reset($this->cartItems);
        } else {
            $counter = floatval(0);
            foreach ($this->cartItems as $cartItem) {
                if ($counter < $pos && $pos <= ($counter + $cartItem->getQty())) {
                    $item = $cartItem;
                    break;
                }

                $counter += $cartItem->getQty();
            }
        }

        return $item;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @return WC_Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * @param bool $strikethrough
     *
     * @return string
     */
    public function getPriceHtml($strikethrough = true)
    {
        return $this->getHtml(1, $strikethrough);
    }

    /**
     * @param bool $strikethrough
     *
     * @return string
     */
    public function getSubtotalHtml($strikethrough = true)
    {
        return $this->getHtml($this->getQty(), $strikethrough);
    }

    /**
     * @param float $qty
     * @param bool $strikethrough
     *
     * @return string
     */
    protected function getHtml($qty = 1.0, $strikethrough = true)
    {
        $priceFunc = $this->priceFunctions;

        $calcPrice = $priceFunc->getPriceToDisplay($this->getProduct(),
            array('price' => $this->getCalculatedPrice(), 'qty' => $qty));

        if ($strikethrough) {
            $origPrice = $priceFunc->getPriceToDisplay($this->getProduct(),
                array('price' => $this->getOriginalPrice(), 'qty' => $qty));

            if ($calcPrice < $origPrice) {
                $priceHtml = $priceFunc->formatSalePrice($origPrice,
                        $calcPrice) . $this->getProduct()->get_price_suffix($calcPrice);
            } else {
                $priceHtml = $priceFunc->format($calcPrice) . $this->getProduct()->get_price_suffix($calcPrice);
            }
        } else {
            $priceHtml = $priceFunc->format($calcPrice) . $this->getProduct()->get_price_suffix($calcPrice);
        }

        return $priceHtml;
    }

    /**
     * @return float
     */
    public function getQtyAlreadyInCart()
    {
        return $this->qtyAlreadyInCart;
    }

    /**
     * @param float $qtyAlreadyInCart
     */
    public function setQtyAlreadyInCart($qtyAlreadyInCart)
    {
        $this->qtyAlreadyInCart = $qtyAlreadyInCart;
    }
}
