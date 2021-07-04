<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\Cart\CartBuilder;
use ADP\BaseVersion\Includes\Cart\CartCalculator;
use ADP\BaseVersion\Includes\Cart\RulesCollection;
use ADP\BaseVersion\Includes\External\CacheHelper;
use ADP\BaseVersion\Includes\External\Engine;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\ProcessedVariableProduct;
use ADP\BaseVersion\Includes\Product\Processor;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\Factory;
use Exception;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Functions
{
    /**
     * @var self
     */
    protected static $instance = null;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Engine
     */
    protected $globalEngine;

    /**
     * @var Engine
     */
    protected $productProcessor;

    /**
     * @var CartBuilder
     */
    protected $cartBuilder;

    /**
     * @param Context $context
     * @param Engine $engine
     */
    public function __construct($context, $engine = null)
    {
        $this->context          = $context;
        $this->globalEngine     = $engine;
        $this->productProcessor = new Processor($this->context);
        $this->cartBuilder      = new CartBuilder($context);
    }

    /**
     * @param Context $context
     * @param Engine $engine
     */
    public static function install($context, $engine = null)
    {
        if (static::$instance === null) {
            static::$instance = new static($context, $engine);
        }
    }

    /**
     * @return bool
     */
    protected function isGlobalEngineExisting()
    {
        return isset($this->globalEngine);
    }

    /**
     * @return self
     */
    public static function getInstance()
    {
        return static::$instance;
    }

    /**
     * @return array<int,float>
     */
    public function getGiftedCartProducts()
    {
        $products = array();

        foreach (WC()->cart->cart_contents as $ket => $wcCartItem) {
            $facade = new WcCartItemFacade($this->context, $wcCartItem, $ket);

            if ($facade->isFreeItem()) {
                $prodId = $facade->getVariationId() ? $facade->getVariationId() : $facade->getProductId();
                $qty    = $facade->getQty();

                if ( ! isset($products[$prodId])) {
                    $products[$prodId] = floatval(0);
                }

                $products[$prodId] += $qty;
            }
        }

        return $products;
    }

    /**
     * @param int|WC_Product $theProd
     * @param float $qty
     * @param bool $useEmptyCart
     *
     * @return array<int,Rule>
     */
    public function getActiveRulesForProduct($theProd, $qty = 1.0, $useEmptyCart = false)
    {
        if ($useEmptyCart || ! $this->isGlobalEngineExisting()) {
            $productProcessor = $this->productProcessor;
            $cart             = $this->cartBuilder->create(WC()->customer, WC()->session);
            $productProcessor->withCart($cart);
        } else {
            $productProcessor = $this->globalEngine->getProductProcessor();
        }

        if (is_numeric($theProd)) {
            $product = CacheHelper::getWcProduct($theProd);
        } elseif ($theProd instanceof WC_Product) {
            $product = clone $theProd;
        } else {
            return array();
        }

        $processedProduct = $productProcessor->calculateProduct($product, $qty);

        if (is_null($processedProduct)) {
            return array();
        }

        if ($processedProduct instanceof ProcessedVariableProduct) {
            return array();
        }

        $rules = array();

        /** @var ProcessedProductSimple $processedProduct */
        foreach ($processedProduct->getHistory() as $ruleId => $amounts) {
            $rules[] = $ruleId;
        }

        return CacheHelper::loadRules($rules, $this->context);
    }

    /**
     *
     * @param array $listOfProducts
     * array[]['product_id']
     * array[]['qty']
     * array[]['cart_item_data'] Optional
     * @param boolean $plain Type of returning array. With False returns grouped by rules
     *
     * @return array
     * @throws Exception
     *
     */
    public function getDiscountedProductsForCart($listOfProducts, $plain = false)
    {
        if ( ! did_action('wp_loaded')) {
            _doing_it_wrong(__FUNCTION__,
                sprintf(__('%1$s should not be called before the %2$s action.', 'woocommerce'),
                    'getDiscountedProductsForCart', 'wp_loaded'), WC_ADP_VERSION);

            return array();
        }
        $result = array();
        $cart   = $this->cartBuilder->create(WC()->customer, WC()->session);

        foreach ($listOfProducts as $data) {
            if ( ! isset($data['product_id'], $data['qty'])) {
                continue;
            }

            $prodId       = intval($data['product_id']);
            $qty          = floatval($data['qty']);
            $cartItemData = array();
            if (isset($data['cart_item_data']) && is_array($data['cart_item_data'])) {
                $cartItemData = $data['cart_item_data'];
            }

            if ( ! $product = CacheHelper::getWcProduct($prodId)) {
                continue;
            }

            $cartItem = WcCartItemFacade::createFromProduct($this->context, $product, $cartItemData);
            $cartItem->setQty($qty);
            $cart->addToCart($cartItem->createItem());
        }

        $activeRuleCollection = CacheHelper::loadActiveRules($this->context);

        foreach ($activeRuleCollection->getRules() as $rule) {
            if ( ! ($rule instanceof SingleItemRule)) {
                continue;
            }

            /** @var SingleItemRule $rule */
            try {
                $ruleProc = $rule->buildProcessor($this->context);
            } catch (Exception $e) {
                continue;
            }

            if ( ! $rule->getConditions() || ! $ruleProc->isRuleMatchedCart($cart)) {
                continue;
            }

            $filters = $rule->getFilters();

            if ( ! $filters) {
                continue;
            }

            $listOfProductIds = array();
            foreach ($filters as $filter) {
                if ($filter->getType() === $filter::TYPE_PRODUCT && $filter->getMethod() === $filter::METHOD_IN_LIST) {
                    $listOfProductIds = array_merge($listOfProductIds, $filter->getValue());
                } else {
                    $listOfProductIds = null;
                    break;
                }
            }

            if ( ! $listOfProductIds) {
                continue;
            }

            $items = array();
            foreach ($listOfProductIds as $index => $prodId) {
                if ($product = CacheHelper::getWcProduct($prodId)) {
                    $cartItem = WcCartItemFacade::createFromProduct($this->context, $product);
                    $item     = $cartItem->createItem();
                    $item->addAttr($item::ATTR_TEMP);
                    $items[] = $item;
                    $cart->addToCart($item);
                }
            }

            if ( ! $items) {
                continue;
            }

            if ($plain) {
                /** @var CartCalculator $calc $calc */
                /** @see CartCalculator::make() */
                $calc = Factory::callStaticMethod("Cart_CartCalculator", 'make', $this->context);
                $calc->processCart($cart);
            } else {
                $ruleCollection = new RulesCollection(array($rule));
                $calc           = Factory::get("Cart_CartCalculator", $this->context, $ruleCollection);
                $calc->processCart($cart);
            }

            $ruleResult = array();
            foreach ($cart->getItems() as $item) {
                if ( ! $item->hasAttr($item::ATTR_TEMP)) {
                    continue;
                }

                $ruleResult[$item->getWcItem()->getProduct()->get_id()] = array(
                    'original_price'   => $item->getOriginalPrice(),
                    'discounted_price' => $item->getPrice(),
                );
            }

            if ( ! $ruleResult) {
                continue;
            }

            if ($plain) {
                if ( ! $result) {
                    $result = $ruleResult;
                } else {
                    foreach ($result as &$resultItem) {
                        foreach ($ruleResult as $k => $ruleItem) {
                            if ($ruleItem['product_id'] == $resultItem['product_id']) {
                                if ($resultItem['discounted_price'] > $ruleItem['discounted_price']) {
                                    $resultItem['discounted_price'] = $ruleItem['discounted_price'];
                                }
                                if ($resultItem['original_price'] < $ruleItem['original_price']) {
                                    $resultItem['original_price'] = $ruleItem['original_price'];
                                }
                                unset($ruleResult[$k]);
                                $ruleResult = array_values($ruleResult);
                                break;
                            }
                        }
                    }

                    $result = array_merge($result, $ruleResult);
                }
            } else {
                $result[] = $ruleResult;
            }

        }


        return $result;
    }

    /**
     * @param int|WC_product $theProd
     * @param float $qty
     * @param bool $useEmptyCart
     *
     * @return float|array|null
     * float for simple product
     * array is (min, max) range for variable
     * null if product is incorrect
     */
    public function getDiscountedProductPrice($theProd, $qty, $useEmptyCart = true)
    {
        if ($useEmptyCart || ! $this->isGlobalEngineExisting()) {
            $productProcessor = $this->productProcessor;
            $cart             = $this->cartBuilder->create(WC()->customer, WC()->session);
            $productProcessor->withCart($cart);
        } else {
            $productProcessor = $this->globalEngine->getProductProcessor();
        }

        if (is_numeric($theProd)) {
            $product = CacheHelper::getWcProduct($theProd);
        } elseif ($theProd instanceof WC_Product) {
            $product = clone $theProd;
        } else {
            return null;
        }

        $processedProduct = $productProcessor->calculateProduct($product, $qty);

        if (is_null($processedProduct)) {
            return array();
        }

        if ($processedProduct instanceof ProcessedVariableProduct) {
            return array($processedProduct->getLowestPrice(), $processedProduct->getHighestPrice());
        } elseif ($processedProduct instanceof ProcessedProductSimple) {
            return $processedProduct->getPrice();
        } else {
            return null;
        }
    }

    public function processCartManually()
    {
        if ($this->isGlobalEngineExisting()) {
            $this->globalEngine->process(false);
        }
    }

    /**
     * @param int|WC_product $theProd
     * @param float $qty
     * @param bool $useEmptyCart
     *
     * @return ProcessedProductSimple|ProcessedVariableProduct|null
     */
    public function calculateProduct($theProd, $qty, $useEmptyCart = true)
    {
        if ($useEmptyCart || ! $this->isGlobalEngineExisting()) {
            $productProcessor = $this->productProcessor;
            $cart             = $this->cartBuilder->create(WC()->customer, WC()->session);
            $productProcessor->withCart($cart);
        } else {
            $productProcessor = $this->globalEngine->getProductProcessor();
        }

        if (is_numeric($theProd)) {
            $product = CacheHelper::getWcProduct($theProd);
        } elseif ($theProd instanceof WC_Product) {
            $product = clone $theProd;
        } else {
            return null;
        }

        return $productProcessor->calculateProduct($product, $qty);
    }
}
