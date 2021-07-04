<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\CartTotals;
use ADP\BaseVersion\Includes\Cart\FreeCartItemChoicesSuitability;
use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSet;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\FreeCartItem;
use ADP\BaseVersion\Includes\Cart\Structures\FreeCartItemChoices;
use ADP\BaseVersion\Includes\Enums\BaseEnum;
use ADP\BaseVersion\Includes\Enums\GiftChoiceMethodEnum;
use ADP\BaseVersion\Includes\Enums\GiftChoiceTypeEnum;
use ADP\BaseVersion\Includes\Enums\GiftModeEnum;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\Gift;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\BaseVersion\Includes\Rule\ProductStock\ProductStockController;
use ADP\Factory;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}


class GiftStrategy
{
    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var ProductStockController
     */
    protected $ruleUsedStock;

    /**
     * @var FreeCartItemChoicesSuitability
     */
    protected $freeItemChoicesSuitability;

    /**
     * @param Rule $rule
     * @param ProductStockController $ruleUsedStock
     */
    public function __construct($rule, $ruleUsedStock)
    {
        $this->rule          = $rule;
        $this->ruleUsedStock = $ruleUsedStock;

        /** @var FreeCartItemChoicesSuitability $freeCartItemChoicesSuitability */
        $this->freeItemChoicesSuitability = Factory::get("Cart_FreeCartItemChoicesSuitability");
    }

    public function canGift()
    {
        return method_exists($this->rule, 'getGifts') && boolval($this->rule->getGifts());
    }

    public function canItemGifts()
    {
        return method_exists($this->rule, 'getItemGiftsCollection') && boolval(
                $this->rule->getItemGiftsCollection()->asArray()
            );
    }

    /**
     * TODO implement!
     * Not requires without frontend implementation
     *
     * @param Cart $cart
     */
    public function addGifts(&$cart)
    {
        $rule  = $this->rule;
        $gifts = $rule->getGifts();
    }

    /**
     * @param Cart $cart
     * @param CartItemsCollection $collection
     */
    public function addCartItemGifts(&$cart, $collection)
    {
        /** @var SingleItemRule $rule */
        $rule = $this->rule;

        $totalQty = floatval(0);

        /**
         * @var array $itemIndexes
         * Cheap solution to fetch product with which we add free product.
         * Needed for gifting products from collection.
         *
         * ItemIndexes is a list with items and indexes. Index in nutshell is number of attempt at which we begin to use item.
         * For example:
         *  We have 'single item' rule with collection 3 apple and 2 bananas and 1 orange.
         *  So, itemIndexes will be
         *      array(
         *          array( 'index' => 1, 'item' => apple ),
         *          array( 'index' => 4 (1 + qty of apple), 'item' => banana ),
         *          array( 'index' => 6 (1 + qty of apple + qty of bananas ), 'item' => orange ),
         *      )
         *  When we try to iterate on attempts, we get an item
         *  Attempt count | item
         *  1   | apple
         *  2   | apple
         *  3   | apple
         *  4   | banana
         *  5   | banana
         *  6   | orange
         */
        $itemIndexes = array();
        foreach ($collection->get_items() as $item) {
            $itemIndexes[] = array(
                'index' => $totalQty + 1,
                'item'  => $item,
            );
            $totalQty      += $item->getQty();
        }

        $attemptCount = 0;
        if ($rule->getItemGiftStrategy() === $rule::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY) {
            $attemptCount = min($totalQty, $rule->getItemGiftLimit());
        } elseif ($rule->getItemGiftStrategy() === $rule::BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY) {
            if ($rule->getItemGiftSubtotalDivider()) {
                $tmpCart = clone $cart;
                foreach ($collection->get_items() as $item) {
                    $tmpCart->addToCart($item);
                }
                $itemsSubtotals = (new CartTotals($tmpCart))->getSubtotal();
                $attemptCount   = min($totalQty, intval($itemsSubtotals / $rule->getItemGiftSubtotalDivider()));
            }
        }

        /** @var array<int, array<int,FreeCartItem>> $freeCartItemsByHash */
        $freeCartItemsByHash = array();

        $index = 0;
        while ($index < $attemptCount) {
            $index++;
            $item = null;
            foreach ($itemIndexes as $key => $data) {
                if ($data['index'] <= $index) {
                    $item = $data['item'];
                }
            }

            if ( ! $item) {
                continue;
            }

            $giftIndex = 0;
            /** @var CartItem $item */
            foreach ($rule->getItemGiftsCollection()->asArray() as $gift) {
                if ($gift->getQty() <= 0) {
                    continue;
                }

                $freeCartItemChoices = $this->convertGiftToFreeCartItemChoices($gift, array($item));
                $hash                = $freeCartItemChoices->generateHash($rule, $giftIndex, $gift);

                if ($gift->isAllowToSelect()) {
                    continue;
                } else {
                    $this->processFreeItem($freeCartItemsByHash, $hash, $cart, $freeCartItemChoices);
                }

                $giftIndex++;
            }
        }

        $customer = $cart->getContext()->getCustomer();

        foreach ($freeCartItemsByHash as $hash => $freeCartItems) {
            $removedFreeItems = $customer->getRemovedFreeItems($hash);

            foreach ($freeCartItems as $freeCartItem) {
                $deletedQty = $removedFreeItems->get($freeCartItem->hash());

                if ($freeCartItem->getQty() <= $deletedQty) {
                    $deletedQty = $freeCartItem->getQty();
                    $freeCartItem->setQty(0);
                } else {
                    $freeCartItem->setQty($freeCartItem->getQty() - $deletedQty);
                }

                $removedFreeItems->set($freeCartItem->hash(), $deletedQty);
                $cart->addToCart($freeCartItem);
            }
        }
    }

    /**
     * @param Gift $gift
     * @param array<int,CartItem> $items
     *
     * @return FreeCartItemChoices
     */
    protected function convertGiftToFreeCartItemChoices($gift, $items)
    {
        $newGiftChoices = array();
        foreach ($gift->getChoices() as $giftChoice) {
            if ($giftChoice->getType()->getValue() === GiftChoiceTypeEnum::CLONE_ADJUSTED) {
                $newGiftChoice = clone $giftChoice;
                $newGiftChoice->setType(new GiftChoiceTypeEnum(GiftChoiceTypeEnum::PRODUCT));

                $values = array();
                foreach ($items as $item) {
                    $values[] = $item->getWcItem()->getProduct()->get_id();
                }
                $newGiftChoice->setValues($values);

                $newGiftChoice->setMethod(new GiftChoiceMethodEnum(GiftChoiceMethodEnum::IN_LIST));
                $newGiftChoices = array($newGiftChoice);
                break;
            }

            if ( $gift->getMode()->equals(GiftModeEnum::GIFTABLE_PRODUCTS_IN_RANDOM()) && $giftChoice->getType()->equals(GiftChoiceTypeEnum::PRODUCT()) ) {
                $values = $giftChoice->getValues();
                shuffle($values);
                $giftChoice->setValues($values);
            }

            $newGiftChoices[] = $giftChoice;
        }

        $freeCartItemChoices = new FreeCartItemChoices();
        $freeCartItemChoices->setChoices($newGiftChoices);
        $freeCartItemChoices->setRequiredQty($gift->getQty());
        $freeCartItemChoices->setRequired($gift->getMode()->equals(GiftModeEnum::REQUIRE_TO_CHOOSE()) || $gift->getMode()->equals(GiftModeEnum::REQUIRE_TO_CHOOSE_FROM_PRODUCT_CAT()));

        return $freeCartItemChoices;
    }

    /**
     * @param Cart $cart
     * @param CartSetCollection $collection
     */
    public function addCartSetGifts(&$cart, $collection)
    {
        /** @var PackageRule $rule */
        $rule = $this->rule;

        $totalQty = floatval(0);

        $setIndexes = array();
        foreach ($collection->getSets() as $set) {
            $setIndexes[] = array(
                'index' => $totalQty + 1,
                'set'   => $set,
            );
            $totalQty     += $set->getQty();
        }

        $attemptCount = 0;
        if ($rule->getItemGiftStrategy() === $rule::BASED_ON_LIMIT_ITEM_GIFT_STRATEGY) {
            $attemptCount = min($totalQty, $rule->getItemGiftLimit());
        } elseif ($rule->getItemGiftStrategy() === $rule::BASED_ON_SUBTOTAL_ITEM_GIFT_STRATEGY) {
            if ($rule->getItemGiftSubtotalDivider()) {
                $tmpCart = clone $cart;
                foreach ($collection->getSets() as $set) {
                    foreach ($set->getItems() as $item) {
                        $tmpCart->addToCart($item);
                    }
                }
                $itemsSubtotals = (new CartTotals($tmpCart))->getSubtotal();
                $attemptCount   = min($totalQty, intval($itemsSubtotals / $rule->getItemGiftSubtotalDivider()));
            }
        }

        /** @var FreeCartItem[][] $freeCartItemsByHash */
        $freeCartItemsByHash = array();

        $index = 0;
        while ($index < $attemptCount) {
            $index++;
            $set = null;
            foreach ($setIndexes as $key => $data) {
                if ($data['index'] <= $index) {
                    $set = $data['set'];
                }
            }

            if ( ! $set) {
                continue;
            }

            $giftIndex = 0;
            /** @var CartSet $set */
            foreach ($rule->getItemGiftsCollection()->asArray() as $gift) {
                if ($gift->getQty() <= 0) {
                    continue;
                }

                $freeCartItemChoices = $this->convertGiftToFreeCartItemChoices($gift, $set->getItems());
                $hash                = $freeCartItemChoices->generateHash($rule, $giftIndex, $gift);

                if ($gift->isAllowToSelect()) {
                    continue;
                } else {
                    $this->processFreeItem($freeCartItemsByHash, $hash, $cart, $freeCartItemChoices);
                }

                $giftIndex++;
            }
        }

        $customer = $cart->getContext()->getCustomer();

        foreach ($freeCartItemsByHash as $hash => $freeCartItems) {
            $removedFreeItems = $customer->getRemovedFreeItems($hash);

            foreach ($freeCartItems as $freeCartItem) {
                $deletedQty = $removedFreeItems->get($freeCartItem->hash());

                if ($freeCartItem->getQty() <= $deletedQty) {
                    $deletedQty = $freeCartItem->getQty();
                    $freeCartItem->setQty(0);
                } else {
                    $freeCartItem->setQty($freeCartItem->getQty() - $deletedQty);
                }

                $removedFreeItems->set($freeCartItem->hash(), $deletedQty);
                $cart->addToCart($freeCartItem);
            }
        }
    }

    /**
     * @param Cart $cart
     * @param int $productId
     * @param float $qty
     * @param string $associatedGiftHash
     * @param array $variation
     * @param array $cartItemData
     *
     * @return FreeCartItem|false
     */
    protected function prepareFreeCartItem(
        $cart,
        $productId,
        $qty,
        $associatedGiftHash,
        $variation = array(),
        $cartItemData = array()
    ) {
        if ( ! ($cart instanceof Cart && is_numeric($productId) && is_numeric($qty))) {
            return false;
        }

        if ( ! $this->canItemGifts()) {
            return false;
        }

        /** @var SingleItemRule|PackageRule $rule */
        $rule = $this->rule;

        $productId = intval($productId);
        $product   = CacheHelper::getWcProduct($productId);
        $qty       = floatval($qty);

        if ($qty < floatval(0)) {
            return false;
        }

        if ($qty === floatval(0)) {
            return false;
        }

        $isReplace   = $rule->isReplaceItemGifts();
        $replaceCode = $rule->getReplaceItemGiftsCode();

        try {
            $freeItem = new FreeCartItem($product, $qty, $this->rule->getId(), $associatedGiftHash);
        } catch (Exception $e) {
            return false;
        }

        if (count($variation) > 0) {
            $freeItem->setVariation($variation);
        }

        $freeItem->setCartItemData($cartItemData);

        if ($isReplace && $replaceCode) {
            $freeItem->setReplaceWithCoupon($isReplace);
            $freeItem->setReplaceCouponCode($replaceCode);
        }

        return $freeItem;
    }

    /**
     * @param array<int, array<int,FreeCartItem>> $freeCartItemsByHash
     * @param string $hash
     * @param Cart $cart
     * @param FreeCartItemChoices $freeCartItemChoices
     * @param float $giftedCount
     */
    protected function processFreeItem(
        &$freeCartItemsByHash,
        $hash,
        $cart,
        $freeCartItemChoices,
        $giftedCount = 0.0
    ) {
        $readyList = $this->freeItemChoicesSuitability->getProductsSuitableToGift($freeCartItemChoices,
            $this->ruleUsedStock, $giftedCount);

        if ( ! isset($freeCartItemsByHash[$hash])) {
            $freeCartItemsByHash[$hash] = array();
        }

        /** @var FreeCartItem[] $freeCartItems */
        $freeCartItems = $freeCartItemsByHash[$hash];

        foreach ($readyList as $value) {
            list($productId, $qty) = $value;

            if ( ! ($freeCartItem = $this->prepareFreeCartItem($cart, $productId, $qty, $hash))) {
                continue;
            }
            $calculatedGiftHash = $freeCartItem->hash();

            if (isset($freeCartItems[$calculatedGiftHash])) {
                $freeCartItems[$calculatedGiftHash]->setQty($freeCartItems[$calculatedGiftHash]->getQty() + $freeCartItem->getQty());
            } else {
                $freeCartItems[$calculatedGiftHash] = clone $freeCartItem;
            }

            $this->ruleUsedStock->add($freeCartItem->getProduct()->get_id(), $freeCartItem->getQty(),
                $freeCartItem->getProduct()->get_parent_id(), $freeCartItem->getVariation(),
                $freeCartItem->getCartItemData());
        }

        $freeCartItemsByHash[$hash] = $freeCartItems;
    }
}
