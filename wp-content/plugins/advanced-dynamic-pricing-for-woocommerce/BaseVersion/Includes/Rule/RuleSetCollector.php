<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartContext;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\CartSetCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartItemsCollection;
use ADP\BaseVersion\Includes\Cart\Structures\CartSet;
use ADP\BaseVersion\Includes\Rule\Structures\PackageItem;
use ADP\BaseVersion\Includes\Rule\Structures\PackageRule;
use ADP\BaseVersion\Includes\Rule\Structures\Range;
use ADP\Factory;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RuleSetCollector
{
    /**
     * @var PackageRule
     */
    protected $rule;

    /**
     * @var CartItemsCollection
     */
    protected $mutableItemsCollection;

    protected $checkExecutionTimeCallback;

    protected $packages;

    /**
     * @param PackageRule $rule
     */
    public function __construct($rule)
    {
        $this->rule                   = $rule;
        $this->mutableItemsCollection = new CartItemsCollection($rule->getId());
        $this->packages               = array();
    }

    public function registerCheckExecutionTimeFunction($callable, $context)
    {
        $this->checkExecutionTimeCallback = array(
            'callable' => $callable,
            'context'  => $context,
        );
    }

    private function checkExecutionTime()
    {
        if ( ! isset($this->checkExecutionTimeCallback['callable']) && $this->checkExecutionTimeCallback['context']) {
            return;
        }

        $callable = $this->checkExecutionTimeCallback['callable'];
        $context  = $this->checkExecutionTimeCallback['context'];

        call_user_func($callable, $context);
    }

    /**
     * @param array<int, CartItem> $mutableItems
     */
    public function addItems($mutableItems)
    {
        foreach ($mutableItems as $index => $cartItem) {
            $this->mutableItemsCollection->add($cartItem);
        }
    }

    /**
     * @param $cart Cart
     *
     * @throws Exception
     */
    public function applyFilters($cart)
    {
        $packages = array();

        // hashes with highest priority
        $type_products_hashes = array();

        foreach ($this->rule->getPackages() as $package) {
            $packages[] = $this->preparePackage($cart, $package, $type_products_hashes);
        }

        if (count($packages) === count($this->rule->getPackages())) {
            $this->packages = $packages;
        }

        foreach ($this->packages as &$filter) {
            $is_product_filter = $filter['is_product_filter'];
            unset($filter['is_product_filter']);

            /** Do not reorder 'exact products' filter hashes */
            if ($is_product_filter) {
                continue;
            }

            foreach (array_reverse($type_products_hashes) as $hash) {
                foreach ($filter['valid_hashes'] as $index => $valid_hash) {
                    if ($hash === $valid_hash) {
                        unset($filter['valid_hashes'][$index]);
                        $filter['valid_hashes'][] = $hash;
                        $filter['valid_hashes']   = array_values($filter['valid_hashes']);
                        break;
                    }
                }
            }
        }
    }

    /**
     * @param Cart $cart
     * @param PackageItem $package
     * @param array $typeProductsHashes
     *
     * @return array
     */
    protected function preparePackage($cart, $package, &$typeProductsHashes)
    {
        $filters = $package->getFilters();
//		$excludes = $package->getExcludes();

        /**
         * @var $productFiltering ProductFiltering
         * @var $productExcluding ProductFiltering
         */
        $productFiltering = Factory::get("Rule_ProductFiltering", $cart->getContext()->getGlobalContext());
        $productExcluding = Factory::get("Rule_ProductFiltering", $cart->getContext()->getGlobalContext());

        $productExcludingEnabled = $cart->getContext()->getOption('allow_to_exclude_products');
        $limitation              = $package->getLimitation();


        $valid_hashes = array();

        foreach ($this->mutableItemsCollection->get_items() as $cartItem) {
            /**
             * @var $cartItem CartItem
             */
            $wcCartItemFacade = $cartItem->getWcItem();
            $product          = $wcCartItemFacade->getProduct();

//				if ( $productExcludingEnabled ) {
//					$isExclude = false;
//
//					foreach ( $excludes as $exclude ) {
//						$productExcluding->prepare( $exclude->getType(), $exclude->getValue(), $exclude->getMethod() );
//
//						if ( $productExcluding->check_product_suitability( $product, $wcCartItemFacade->getData() ) ) {
//							$isExclude = true;
//							break;
//						}
//					}
//
//					if ( $isExclude ) {
//						continue;
//					}
//				}

            /**
             * Item must match all filters
             */
            $match = true;
            foreach ($filters as $filter) {
                $productFiltering->prepare($filter->getType(), $filter->getValue(), $filter->getMethod());

                if ($productExcludingEnabled) {
                    $productExcluding->prepare($filter::TYPE_PRODUCT, $filter->getExcludeProductIds(),
                        $filter::METHOD_IN_LIST);

                    if ($productExcluding->checkProductSuitability($product, $wcCartItemFacade->getData())) {
                        $match = false;
                        break;
                    }

                    if ($filter->isExcludeWcOnSale() && $product->is_on_sale('')) {
                        $match = false;
                        break;
                    }

                    if ($filter->isExcludeAlreadyAffected() && $cartItem->areRuleApplied()) {
                        $match = false;
                        break;
                    }
                }

                if ( ! $productFiltering->checkProductSuitability($product, $wcCartItemFacade->getData())) {
                    $match = false;
                    break;
                }
            }

            if ($match) {
                $valid_hashes[] = $cartItem->getHash();
                if ($productFiltering->isType('products')) {
                    $typeProductsHashes[] = $cartItem->getHash();
                }
            }
        }

        return array(
            'valid_hashes'      => $valid_hashes,
            'is_product_filter' => $productFiltering->isType('products'),
            'package'           => $package,
            'limitation'        => $limitation,
        );
    }

    /**
     * @param $cart Cart
     * @param $mode string
     *
     * @return CartSetCollection|false
     * @throws Exception
     */
    public function collectSets(&$cart, $mode = 'legacy')
    {
        if ('legacy' === $mode) {
            $collection = $this->collectSetsLegacy($cart);
        } else {
            $collection = false;
        }

        return $collection;
    }

    /**
     * @param string $limitation
     * @param array<int,string> $validHashes
     * @param Range $range
     *
     * @return array|null
     */
    private function handleUniqueLimitations($limitation, $validHashes, $range)
    {
        $packageSetItems   = array();
        $validItemsGrouped = array();

        foreach ($validHashes as $index => $valid_cart_item_hash) {
            $cartItem = $this->mutableItemsCollection->getNotEmptyItemWithReferenceByHash($valid_cart_item_hash);
            if ( ! $cartItem) {
                continue;
            }

            $product = $cartItem->getWcItem()->getProduct();

            if ($limitation === PackageItem::LIMITATION_UNIQUE_PRODUCT) {
                $productId = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

                if ( ! isset($validItemsGrouped[$productId])) {
                    $validItemsGrouped[$productId] = $cartItem;
                }
            } elseif ($limitation === PackageItem::LIMITATION_UNIQUE_VARIATION) {
                if ( ! isset($validItemsGrouped[$product->get_id()])) {
                    $validItemsGrouped[$product->get_id()] = $cartItem;
                }
            } elseif ($limitation === PackageItem::LIMITATION_UNIQUE_HASH) {
                $validItemsGrouped[] = $cartItem;
            }
        }

        if ($range->isLess(count($validItemsGrouped))) {
            return null;
        } elseif ($range->isIn(count($validItemsGrouped))) {
            // do nothing
        } elseif ($range->isGreater(count($validItemsGrouped))) {
            if ( ! is_infinite($range->getTo())) {
                $validItemsGrouped = array_slice($validItemsGrouped, 0, $range->getTo());
            }
        }

        foreach ($validItemsGrouped as $validItem) {
            $requireQty = 1;

            $setItem = clone $validItem;
            $setItem->setQty($setItem->getQty() - ($validItem->getQty() - $requireQty));
            $validItem->setQty($validItem->getQty() - $requireQty);

            $packageSetItems[] = $setItem;
        }

        return $packageSetItems;
    }

    /**
     * @param Cart $cart
     *
     * @return CartSetCollection|false
     * @throws Exception
     */
    public function collectSetsLegacy(&$cart)
    {
        $collection = new CartSetCollection();
        $applied    = true;

        while ($applied && $collection->getTotalSetsQty() !== $this->rule->getPackagesCountLimit()) {
            $set_items = array();

            foreach ($this->packages as $filter_key => &$filter) {
                $validHashes = ! empty($filter['valid_hashes']) ? $filter['valid_hashes'] : array();
                $limitation  = ! empty($filter['limitation']) ? $filter['limitation'] : PackageItem::LIMITATION_NONE;
                $package     = $filter['package'];
                /** @var $package PackageItem */
                $range = new Range($package->getQty(), $package->getQtyEnd(), $validHashes);

                if (in_array($limitation, array(
                    PackageItem::LIMITATION_UNIQUE_PRODUCT,
                    PackageItem::LIMITATION_UNIQUE_VARIATION,
                    PackageItem::LIMITATION_UNIQUE_HASH,
                ))) {
                    if ($packageSetItems = $this->handleUniqueLimitations($limitation, $validHashes, $range)) {
                        $applied     = $applied && count($packageSetItems);
                        $set_items[] = $packageSetItems;
                    } else {
                        $applied = false;
                    }

                    continue;
                }

                $valid_hashes_grouped = array();
                if ($limitation === PackageItem::LIMITATION_SAME_VARIATION) {
                    foreach ($validHashes as $index => $valid_cart_item_hash) {
                        $cartItem = $this->mutableItemsCollection->getNotEmptyItemWithReferenceByHash($valid_cart_item_hash);

                        if ( ! $cartItem) {
                            continue;
                        }
                        $product = $cartItem->getWcItem()->getProduct();

                        if ( ! isset($valid_hashes_grouped[$product->get_id()])) {
                            $valid_hashes_grouped[$product->get_id()] = array();
                        }

                        $valid_hashes_grouped[$product->get_id()][] = $valid_cart_item_hash;
                    }
                } elseif ($limitation === PackageItem::LIMITATION_SAME_PRODUCT) {
                    foreach ($validHashes as $index => $valid_cart_item_hash) {
                        $cartItem = $this->mutableItemsCollection->getNotEmptyItemWithReferenceByHash($valid_cart_item_hash);

                        if ( ! $cartItem) {
                            continue;
                        }
                        $product    = $cartItem->getWcItem()->getProduct();
                        $product_id = $product->get_parent_id() ? $product->get_parent_id() : $product->get_id();

                        if ( ! isset($valid_hashes_grouped[$product_id])) {
                            $valid_hashes_grouped[$product_id] = array();
                        }

                        $valid_hashes_grouped[$product_id][] = $valid_cart_item_hash;
                    }
                } elseif ($limitation === PackageItem::LIMITATION_SAME_HASH) {
                    foreach ($validHashes as $index => $valid_cart_item_hash) {
                        $valid_hashes_grouped[] = array($valid_cart_item_hash);
                    }
                } else {
                    $valid_hashes_grouped[] = $validHashes;
                }

                $filter_applied = false;

                foreach ($valid_hashes_grouped as $validHashes) {
                    $filter_applied = false;

                    $filter_set_items = array();

                    foreach ($validHashes as $index => $valid_cart_item_hash) {
                        $cart_item = $this->mutableItemsCollection->getNotEmptyItemWithReferenceByHash($valid_cart_item_hash);

                        if (is_null($cart_item)) {
                            unset($validHashes[$index]);
                            continue;
                        }

                        $collected_qty = 0;
                        foreach ($filter_set_items as $filter_set_item) {
                            /**
                             * @var $filter_set_item CartItem
                             */
                            $collected_qty += $filter_set_item->getQty();
                        }

                        $collected_qty += $cart_item->getQty();

                        if ( ! $range->isValid()) {
                            continue;
                        }

                        if ($range->isLess($collected_qty)) {
                            $set_cart_item = clone $cart_item;
                            $cart_item->setQty(0);
                            $filter_set_items[] = $set_cart_item;
                        } elseif ($range->isIn($collected_qty)) {
                            $set_cart_item = clone $cart_item;
                            $cart_item->setQty(0);
                            $filter_set_items[] = $set_cart_item;
                            $filter_applied     = true;
                            break;
                        } elseif ($range->isGreater($collected_qty)) {
                            $mode_value_to = $range->getTo();
                            if (is_infinite($mode_value_to)) {
                                continue;
                            }

                            $require_qty = $mode_value_to + $cart_item->getQty() - $collected_qty;

                            $set_cart_item = clone $cart_item;
                            $set_cart_item->setQty($set_cart_item->getQty() - ($cart_item->getQty() - $require_qty));
                            $cart_item->setQty($cart_item->getQty() - $require_qty);

                            $filter_set_items[] = $set_cart_item;
                            $filter_applied     = true;
                            break;
                        }
                    }

                    if ($filter_set_items) {
                        if ($filter_applied) {
                            $set_items[] = $filter_set_items;
                        } else {
                            /**
                             * For range filters, try to put remaining items in set
                             *
                             * If range 'to' equals infinity or 'to' not equal 'from'
                             */
                            if ($range->getQty() === false || $range->getQty()) {
                                $collected_qty = 0;
                                foreach ($filter_set_items as $filter_set_item) {
                                    /**
                                     * @var $filter_set_item CartItem
                                     */
                                    $collected_qty += $filter_set_item->getQty();
                                }

                                if ($range->isIn($collected_qty)) {
                                    $set_items[]      = $filter_set_items;
                                    $filter_set_items = array();
                                    $filter_applied   = true;
                                }
                            }

                            foreach ($filter_set_items as $item) {
                                /**
                                 * @var $item CartItem
                                 */
                                $this->mutableItemsCollection->add($item);
                            }
                        }

                        $filter_set_items = array();
                    }

                    if ($filter_applied) {
                        break;
                    }
                }

                $applied = $applied && $filter_applied;
            }

            if ($set_items && $applied) {
                $collection->add(new CartSet($this->rule->getId(), $set_items));
                $set_items = array();
            }

            $this->checkExecutionTime();
        }

        if ( ! empty($set_items)) {
            foreach ($set_items as $tmp_filter_set_items) {
                foreach ($tmp_filter_set_items as $item) {
                    $cart->addToCart($item);
                }
            }
        }

        if ( ! empty($filter_set_items)) {
            foreach ($filter_set_items as $item) {
                $cart->addToCart($item);
            }
        }

        foreach ($this->mutableItemsCollection->get_items() as $item) {
            /**
             * @var $item CartItem
             */
            $cart->addToCart($item);
        }

        return $collection;
    }

}
