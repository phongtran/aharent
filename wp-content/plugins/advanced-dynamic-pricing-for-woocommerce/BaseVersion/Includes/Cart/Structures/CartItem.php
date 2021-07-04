<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\Rule\Structures\ItemDiscount;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartItem
{
    const FLAG_IGNORE = 0;
    const FLAG_DISCOUNT_ORIGINAL = 1;

    /**
     * @var float
     */
    protected $qty;

    /**
     * @var float
     */
    protected $originalPrice;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var string
     */
    protected $calculatedHash;

    /**
     * @var string
     */
    protected $calculatedMergeHash;

    /**
     * @var int
     */
    protected $pos;

    /**
     * @var array
     */
    protected $attributes;
    const ATTR_IMMUTABLE = 'immutable';
    const ATTR_READONLY_PRICE = 'readonly_price';
    const ATTR_TEMP = 'temporary';

    /**
     * @var array
     */
    protected $history;

    /**
     * @var array
     */
    protected $discounts;

    /**
     * @var ItemDiscount[]
     */
    protected $objDiscounts;

    /**
     * @var WcCartItemFacade
     */
    protected $wcItem;

    /**
     * @var float
     */
    public $trdPartyPriceAdj;

    protected $marks;

    /**
     * @var float|null
     */
    protected $minDiscountRangePrice;

    /**
     * @param WcCartItemFacade $wcCartItemFacade
     * @param float|string $originalPrice
     * @param float $qty
     * @param int $pos
     */
    public function __construct(WcCartItemFacade $wcCartItemFacade, $originalPrice, $qty, $pos = -1)
    {
        $this->wcItem        = $wcCartItemFacade;
        $this->originalPrice = floatval($originalPrice);
        $this->price         = $this->originalPrice;
        $this->qty           = floatval($qty);
        $this->pos           = is_numeric($qty) ? (integer)$pos : -1;

        $this->history      = array();
        $this->discounts    = array();
        $this->objDiscounts = array();
        $this->attributes   = array();
        $this->marks        = array();
        $this->recalculateHash();
        $this->recalculateMergeHash();
    }

    public function __clone()
    {
        $this->recalculateHash();
        $this->recalculateMergeHash();
        $this->wcItem = clone $this->wcItem;

        $newObjDiscounts = array();
        foreach ($this->objDiscounts as $discount) {
            $newObjDiscounts[] = clone $discount;
        }
        $this->objDiscounts = $newObjDiscounts;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->qty;
    }

    /**
     * @param float $qty
     */
    public function setQty($qty)
    {
        $this->qty = floatval($qty);
    }

    /**
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * @param ItemDiscount $discount
     */
    public function setPriceNew(ItemDiscount $discount)
    {
        if ($this->hasAttr($this::ATTR_READONLY_PRICE) || $this->hasAttr($this::ATTR_IMMUTABLE)) {
            return;
        }

        if ( ! ($discount instanceof ItemDiscount)) {
            return;
        }

        $this->objDiscounts[] = $discount;
        $this->recalculateHash();
    }

    /**
     * @return array<int, ItemDiscount>
     */
    public function getObjDiscounts()
    {
        return $this->objDiscounts;
    }

    /**
     * @param integer $ruleId
     * @param float $price
     * @param array<int, string> $flags
     */
    public function setPrice($ruleId, $price, $flags = array())
    {
        if ($this->hasAttr($this::ATTR_READONLY_PRICE) || $this->hasAttr($this::ATTR_IMMUTABLE)) {
            return;
        }

        $flags = array_unique($flags);

        if (in_array(self::FLAG_DISCOUNT_ORIGINAL, $flags)) {
            $adjustment = $this->originalPrice - $price;

            foreach ($this->discounts as $ruleIdDiscount => $discount) {
                $price -= array_sum($discount);
            }
        } else {
            $adjustment = $this->price - $price;
        }

        if ( ! in_array(self::FLAG_IGNORE, $flags)) {
            if ( ! isset($this->discounts[$ruleId])) {
                $this->discounts[$ruleId] = array();
            }
            $this->discounts[$ruleId][] = $adjustment;
        }

        if ( ! isset($this->history[$ruleId])) {
            $this->history[$ruleId] = array();
        }
        $this->history[$ruleId][] = $adjustment;

        $this->price = $price;
        $this->recalculateHash();
    }

    /**
     * @return float
     */
    public function getOriginalPrice()
    {
        return $this->originalPrice;
    }

    /**
     * @return WcCartItemFacade
     */
    public function getWcItem(): WcCartItemFacade
    {
        return $this->wcItem;
    }

    /**
     * @param string $attribute
     *
     * @return bool
     */
    public function hasAttr($attribute)
    {
        return in_array($attribute, $this->attributes);
    }

    public function addAttr(...$attributes)
    {
        $allowedAttrs = array(
            self::ATTR_IMMUTABLE,
            self::ATTR_READONLY_PRICE,
            self::ATTR_TEMP,
        );

        foreach ($attributes as $attribute) {
            if (in_array($attribute, $allowedAttrs)) {
                $this->attributes[] = $attribute;
            }
        }
        $this->recalculateHash();
    }

    public function removeAttr(...$attributes)
    {
        foreach ($attributes as $attr) {
            $pos = array_search($attr, $this->attributes);

            if ($pos !== false) {
                unset($this->attributes[$pos]);
            }
        }

        $this->attributes = array_values($this->attributes);
        $this->recalculateHash();
        $this->recalculateMergeHash();
    }

    public function getAttrs()
    {
        return $this->attributes;
    }

    private function recalculateHash()
    {
        $data = array(
            'initial_price' => $this->originalPrice,
//			'qty'           => $this->qty,
            'attrs'         => $this->attributes,
            'history'       => $this->history,
            'pos'           => $this->pos,
        );

        $this->calculatedHash = md5(json_encode($data));
    }

    private function recalculateMergeHash()
    {
        $data = array(
            'initial_price' => $this->originalPrice,
            'attrs'         => $this->attributes,
            'wc_item_hash'  => $this->wcItem->getKey(),
            'pos'           => $this->pos,
        );

        $this->calculatedMergeHash = md5(json_encode($data));
    }

    /**
     * @param int $pos
     */
    public function setPos($pos)
    {
        $this->pos = $pos;
        $this->recalculateHash();
        $this->recalculateMergeHash();
    }

    /**
     * @return int
     */
    public function getPos()
    {
        return $this->pos;
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @return array<int, array<int, int>>
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->calculatedHash;
    }

    public function getTotalPrice()
    {
        return $this->getPrice() * $this->qty;
    }

    /**
     * @param string $mark
     *
     * @return bool
     */
    public function hasMark($mark)
    {
        return in_array($mark, $this->marks);
    }

    /**
     * @param array $marks
     */
    public function addMark(...$marks)
    {
        $this->marks = $marks;
        $this->recalculateHash();
        $this->recalculateMergeHash();
    }

    /**
     * @param array $marks
     */
    public function removeMark(...$marks)
    {
        foreach ($marks as $mark) {
            $pos = array_search($mark, $this->marks);

            if ($pos !== false) {
                unset($this->marks[$pos]);
            }
        }

        $this->marks = array_values($this->marks);
        $this->recalculateHash();
        $this->recalculateMergeHash();
    }

    /**
     * @return array<int, string>
     */
    public function getMarks()
    {
        return $this->marks;
    }

    public function areRuleApplied()
    {
        foreach ($this->history as $rule_id => $amounts) {
            if (floatval(array_sum($amounts)) !== floatval(0)) {
                return true;
            }
        }

        return false;
    }

    public function isPriceChanged()
    {
        foreach ($this->discounts as $rule_id => $amounts) {
            if (floatval(array_sum($amounts)) !== floatval(0)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param float $minDiscountRangePrice
     */
    public function setMinDiscountRangePrice($minDiscountRangePrice)
    {
        if (is_numeric($minDiscountRangePrice)) {
            $this->minDiscountRangePrice = floatval($minDiscountRangePrice);
        }
    }

    /**
     * @return float|null
     */
    public function getMinDiscountRangePrice()
    {
        return $this->minDiscountRangePrice;
    }

    /**
     * @return string
     */
    public function getMergeHash()
    {
        return $this->calculatedMergeHash;
    }

    /**
     * @return bool
     */
    public function isHistoryEqualsDiscounts() {
        return $this->discounts === $this->history;
    }
}
