<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartSet
{
    /**
     * @var string
     */
    private $hash;

    /**
     * @var CartItem[]
     */
    private $items;

    /**
     * @var integer
     */
    private $qty;

    /**
     * @var int
     */
    private $ruleId;

    /**
     * @var array<int,int>
     */
    private $itemPositions;

    /**
     * @var array
     */
    protected $marks;

    /**
     * @param int $ruleId int
     * @param array<int,CartItem> $cartItems
     * @param int $qty
     */
    public function __construct($ruleId, $cartItems, $qty = 1)
    {
        $this->ruleId = $ruleId;

        $plainItems = array();
        foreach (array_values($cartItems) as $index => $item) {
            if ($item instanceof CartItem) {
                $plainItems[] = array(
                    'pos'  => $index,
                    'item' => $item,
                );
            } elseif (is_array($item)) {
                foreach ($item as $subItem) {
                    if ($subItem instanceof CartItem) {
                        $plainItems[] = array(
                            'pos'  => $index,
                            'item' => $subItem,
                        );
                    }
                }
            }
        }

        usort($plainItems, function ($plainItemA, $plainItemB) {
            $item_a = $plainItemA['item'];
            $item_b = $plainItemB['item'];
            /**
             * @var $item_a CartItem
             * @var $item_b CartItem
             */

            $tmp_a = $item_a->hasAttr($item_a::ATTR_TEMP);
            $tmp_b = $item_b->hasAttr($item_a::ATTR_TEMP);

            if ( ! $tmp_a && $tmp_b) {
                return -1;
            }

            if ($tmp_a && ! $tmp_b) {
                return 1;
            }

            return 0;
        });

        $this->items         = array_column($plainItems, 'item');
        $this->itemPositions = array_column($plainItems, 'pos');

        $this->recalculateHash();
        $this->hash  = $this->getHash();
        $this->qty   = $qty;
        $this->marks = array();
    }

    private function sortItems()
    {
        usort($this->items, function ($itemA, $itemB) {
            /**
             * @var $itemA CartItem
             * @var $itemB CartItem
             */
            if ( ! $itemA->hasAttr($itemA::ATTR_TEMP) && $itemB->hasAttr($itemB::ATTR_TEMP)) {
                return -1;
            }

            if ($itemA->hasAttr($itemA::ATTR_TEMP) && ! $itemB->hasAttr($itemB::ATTR_TEMP)) {
                return 1;
            }

            return 0;
        });

    }

    public function __clone()
    {
        $new_items = array();
        foreach ($this->items as $item) {
            $new_items[] = clone $item;
        }

        $this->items = $new_items;
    }

    public function getTotalPrice()
    {
        return $this->getPrice() * $this->qty;
    }

    public function getPrice()
    {
        $price = 0.0;
        foreach ($this->items as $item) {
            $price += $item->getPrice() * $item->getQty();
        }

        return $price;
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->hash;
    }

    public function recalculateHash()
    {
        $hashes = array_map(function ($item) {
            /**
             * @var $item CartItem
             */
            return $item->getHash();
        }, $this->items);

        $this->hash = md5(json_encode($hashes));
    }

//	public function calc_no_price_hash() {
//		$hashes = array_map( function ( $item ) {
//			/**
//			 * @var $item CartItem
//			 */
//			return $item->calc_no_price_hash();
//		}, $this->items );
//
//		return md5( json_encode( $hashes ) );
//	}

    /**
     * @return array<int, CartItem>
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @return array<int, int>
     */
    public function getPositions()
    {
        $positions = array_unique(array_values($this->itemPositions));
        sort($positions);

        return $positions;
    }

    /**
     * @param string $hash
     * @param float $price
     * @param float|null $qty
     * @param int|null $position
     */
    public function setPriceForItem($hash, $price, $qty = null, $position = null)
    {
        if ($position) {
            $items = $this->getItemsByPositionWithReference($position);
        } else {
            $items = $this->items;
        }

        foreach ($items as &$item) {
            if ($item->getHash() === $hash) {
                if ($qty && $item->getQty() > $qty) {
                    $new_item = clone $item;
                    $new_item->setQty($qty);
                    $new_item->setPrice($this->ruleId, $price);
                    $this->items[] = $new_item;

                    $item->setQty($item->getQty() - $qty);
                } else {
                    $item->setPrice($this->ruleId, $price);
                }

                break;
            }
        }
        $this->recalculateHash();
    }

    /**
     * @param int $index
     * @param array<int,float> $prices
     */
    public function setPriceForItemsByPosition($index, $prices)
    {
        $items = $this->getItemsByPositionWithReference($index);

        if ( ! $items) {
            return;
        }

        $items  = array_values($items);
        $prices = array_values($prices);

        if (count($items) !== count($prices)) {
            return;
        }

        foreach ($items as $index => $item) {
            /**
             * @var $item CartItem
             */
            $item->setPrice($this->ruleId, $prices[$index]);
        }

        $this->recalculateHash();
    }

    public function incQty($qty)
    {
        $this->qty += $qty;
    }

    /**
     * @param int $index
     *
     * @return array<int, CartItem>
     */
    public function getItemsByPosition($index)
    {
        $items = array();
        foreach ($this->getItemsByPositionWithReference($index) as $item) {
            $items[] = $item;
        }

        return $items;
    }

//	public function set_first_discount_range_rule( $rule_id ) {
//		foreach ( $this->items as $item ) {
//			/**
//			 * @var $item CartItem
//			 */
//			$item->set_first_discount_range_rule( $rule_id );
//		}
//	}

    /**
     * @param int $index
     *
     * @return array<int, CartItem>
     */
    private function getItemsByPositionWithReference($index)
    {
        $items = array();
        foreach ($this->itemPositions as $internal_index => $position) {
            if ($position === $index) {
                $items[] = $this->items[$internal_index];
            }
        }

        return $items;
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
    }

    /**
     * @param float $qty
     */
    public function setQty($qty)
    {
        $this->qty = $qty;
    }

    /**
     * @return float
     */
    public function getQty()
    {
        return $this->qty;
    }
}
