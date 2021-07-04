<?php

namespace ADP\BaseVersion\Includes\External\WC;

use ADP\BaseVersion\Includes\Cart\OriginalPriceCalculation;
use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\FreeCartItem;
use ADP\BaseVersion\Includes\CompareStrategy;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Currency;
use ADP\Factory;
use Exception;
use ReflectionClass;
use WC_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WcCartItemFacade
{
    const KEY_ADP = 'adp';
    const ADP_PARENT_CART_ITEM_KEY = 'original_key';
    const ADP_ATTRIBUTES_KEY = 'attr';
    const ADP_ORIGINAL_KEY = 'orig';
    const ADP_HISTORY_KEY = 'history';
    const ADP_DISCOUNTS_KEY = 'discount';
    const ADP_NEW_PRICE_KEY = 'new_price';
    const ADP_REPLACE_WITH_COUPON = 'free_item_replace_with_coupon';
    const ADP_REPLACE_COUPON_NAME = 'free_item_replace_coupon_name';
    const ADP_CURRENCY = 'currency';
    const ADP_ASSOCIATED_GIFT_HASH = 'gift_hash';
    const ADP_FREE_CART_ITEM_HASH = 'free_cart_item_hash';
    const ADP_SELECTED_FREE_CART_ITEM = 'selected_free_cart_item';

    const KEY_KEY = 'key';
    const KEY_PRODUCT = 'data';
    const KEY_DATA_HASH = 'data_hash';
    const KEY_PRODUCT_ID = 'product_id';
    const KEY_VARIATION_ID = 'variation_id';
    const KEY_VARIATION = 'variation';
    const KEY_QTY = 'quantity';

    // totals
    const KEY_TAX_DATA = 'line_tax_data';
    const KEY_SUBTOTAL = 'line_subtotal';
    const KEY_SUBTOTAL_TAX = 'line_subtotal_tax';
    const KEY_TOTAL = 'line_total';
    const KEY_TAX = 'line_tax';

    const WC_CART_ITEM_DEFAULT_KEYS = array(
        self::KEY_KEY,
        self::KEY_PRODUCT_ID,
        self::KEY_VARIATION_ID,
        self::KEY_VARIATION,
        self::KEY_QTY,
        self::KEY_PRODUCT,
        self::KEY_DATA_HASH,
        self::KEY_TAX_DATA,
        self::KEY_SUBTOTAL,
        self::KEY_SUBTOTAL_TAX,
        self::KEY_TOTAL,
        self::KEY_TAX,
    );

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var CompareStrategy
     */
    protected $compareStrategy;

    /**
     * @var bool
     */
    protected $visible;

    /**
     * @var string
     */
    protected $key;

    /**
     * @var float
     */
    protected $qty;

    /**
     * @var int
     */
    protected $productId;

    /**
     * @var int
     */
    protected $variationId;

    /**
     * @var array
     */
    protected $variation;

    /**
     * @var WC_Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $dataHash;

    /**
     * @var array
     */
    protected $lineTaxData;

    /**
     * @var float
     */
    protected $lineSubtotal;

    /**
     * @var float
     */
    protected $lineSubtotalTax;

    /**
     * @var float
     */
    protected $lineTotal;

    /**
     * @var array
     */
    protected $lineTax;


    /**
     * Item key of WC cart from which this item was cloned.
     * In other words, cart key of the locomotive
     *
     * @var string|null
     */
    protected $parentItemKey;

    /**
     * AFTER ADDING NEW ATTRIBUTE, DO NOT FORGET TO ALLOW IT IN 'addAttribute' method!
     *
     * A set of attributes that define behavior in our internal cart
     * E.g. immutability or if it was marked as free
     * @see Cart
     *
     * @var string[]
     */
    protected $attributes;
    const ATTRIBUTE_IMMUTABLE = 'immutable';
    const ATTRIBUTE_READONLY_PRICE = 'readonly_price';
    const ATTRIBUTE_FREE = 'free';

    /**
     * @var array
     */
    protected $originalData;

    protected $history;
    protected $discounts;

    /**
     * @var array
     */
    protected $thirdPartyData;

    /**
     * @var float
     */
    protected $newPrice;

    /**
     * Free item attribute property!
     *
     * @var bool
     */
    protected $replaceWithCoupon;

    /**
     * Free item attribute property!
     *
     * @var string
     */
    protected $replaceCouponCode;

    /**
     * @var Currency|null
     */
    protected $currency;

    /**
     * @var string
     */
    protected $associatedGiftHash;

    /**
     * @var string
     */
    protected $freeCartItemHash;

    /**
     * @var bool
     */
    protected $selectedFreeCartItem;

    /**
     * @param Context $context
     * @param array $wcCartItem
     * @param string|null $key
     */
    public function __construct($context, $wcCartItem, $key = null)
    {
        $this->context         = $context;
        $this->compareStrategy = new CompareStrategy($context);

        $this->key         = isset($wcCartItem[self::KEY_KEY]) ? $wcCartItem[self::KEY_KEY] : $key;
        $this->productId   = $wcCartItem[self::KEY_PRODUCT_ID];
        $this->variationId = $wcCartItem[self::KEY_VARIATION_ID];
        $this->variation   = $wcCartItem[self::KEY_VARIATION];
        $this->qty         = $wcCartItem[self::KEY_QTY];

        /**
         * It important to clone product instead of get them by the reference!
         * Causes problem when WC calculates shipping.
         * They are unsets 'data' key and destroys the product in the cart
         *
         * @see \WC_Shipping::calculate_shipping_for_package
         */
        if (isset($wcCartItem[self::KEY_PRODUCT])) {
            $this->product = clone $wcCartItem[self::KEY_PRODUCT];
        }

        if (isset($wcCartItem[self::KEY_DATA_HASH])) {
            $this->dataHash = $wcCartItem[self::KEY_DATA_HASH];
        } else {
            $this->dataHash = null;
        }

        // totals
        $this->lineTaxData     = isset($wcCartItem[self::KEY_TAX_DATA]) ? $wcCartItem[self::KEY_TAX_DATA] : null;
        $this->lineSubtotal    = isset($wcCartItem[self::KEY_SUBTOTAL]) ? $wcCartItem[self::KEY_SUBTOTAL] : null;
        $this->lineSubtotalTax = isset($wcCartItem[self::KEY_SUBTOTAL_TAX]) ? $wcCartItem[self::KEY_SUBTOTAL_TAX] : null;
        $this->lineTotal       = isset($wcCartItem[self::KEY_TOTAL]) ? $wcCartItem[self::KEY_TOTAL] : null;
        $this->lineTax         = isset($wcCartItem[self::KEY_TAX]) ? $wcCartItem[self::KEY_TAX] : null;

        $this->thirdPartyData = array();
        foreach ($wcCartItem as $key => $value) {
            if ( ! in_array($key, self::WC_CART_ITEM_DEFAULT_KEYS) && $key !== self::KEY_ADP) {
                $this->thirdPartyData[$key] = $value;
            }
        }


        $this->setInitialCustomPrice(null);

        $adp = isset($wcCartItem[self::KEY_ADP]) ? $wcCartItem[self::KEY_ADP] : null;

        $this->parentItemKey = isset($adp[self::ADP_PARENT_CART_ITEM_KEY]) ? $adp[self::ADP_PARENT_CART_ITEM_KEY] : null;
        $this->attributes    = isset($adp[self::ADP_ATTRIBUTES_KEY]) ? $adp[self::ADP_ATTRIBUTES_KEY] : null;
        $this->originalData  = isset($adp[self::ADP_ORIGINAL_KEY]) ? $adp[self::ADP_ORIGINAL_KEY] : null;
        $this->history       = isset($adp[self::ADP_HISTORY_KEY]) ? $adp[self::ADP_HISTORY_KEY] : array();
        $this->discounts     = isset($adp[self::ADP_DISCOUNTS_KEY]) ? $adp[self::ADP_DISCOUNTS_KEY] : array();
        $this->newPrice      = isset($adp[self::ADP_NEW_PRICE_KEY]) ? $adp[self::ADP_NEW_PRICE_KEY] : null;

        $this->visible = boolval(apply_filters('woocommerce_widget_cart_item_visible', true, $this->getData(),
            $this->getKey()));

        $this->replaceWithCoupon = isset($adp[self::ADP_REPLACE_WITH_COUPON]) ? $adp[self::ADP_REPLACE_WITH_COUPON] : null;
        $this->replaceCouponCode = isset($adp[self::ADP_REPLACE_COUPON_NAME]) ? $adp[self::ADP_REPLACE_COUPON_NAME] : null;

        $this->currency = null;
        if (isset($adp[self::ADP_CURRENCY])) {
            $this->currency = $this->unpackCurrencyObject($adp[self::ADP_CURRENCY]);
        }

        $this->associatedGiftHash   = isset($adp[self::ADP_ASSOCIATED_GIFT_HASH]) ? $adp[self::ADP_ASSOCIATED_GIFT_HASH] : '';
        $this->freeCartItemHash     = isset($adp[self::ADP_FREE_CART_ITEM_HASH]) ? $adp[self::ADP_FREE_CART_ITEM_HASH] : '';
        $this->selectedFreeCartItem = isset($adp[self::ADP_SELECTED_FREE_CART_ITEM]) ? $adp[self::ADP_SELECTED_FREE_CART_ITEM] : false;
    }

    public function __clone()
    {
        $this->product = clone $this->product;
    }

    /**
     * @return FreeCartItem|CartItem|null
     */
    public function createItem()
    {
        if ($this->isFreeItem()) {
            return $this->createFreeItem();
        }

        return $this->createCommonItem();
    }

    /**
     * @return CartItem|null
     */
    protected function createCommonItem()
    {
        try {
            $origPriceCalc = new OriginalPriceCalculation($this->context);
        } catch (Exception $e) {
            return null;
        }

        Factory::callStaticMethod('External\PriceDisplay', 'processWithout', array($origPriceCalc, 'process'), $this);
        $initial_cost = $origPriceCalc->priceToAdjust;

        if ($this->isImmutable() && $this->getHistory()) {
            foreach ($this->getHistory() as $rule_id => $amounts) {
                $initial_cost += array_sum($amounts);
            }
        }

        $qty = floatval(apply_filters('wdp_get_product_qty', $this->qty, $this));

        $item                   = new CartItem($this, $initial_cost, $qty);
        $item->trdPartyPriceAdj = $origPriceCalc->trdPartyAdjustmentsAmount;

        if ($origPriceCalc->isReadOnlyPrice) {
            $item->addAttr($item::ATTR_READONLY_PRICE);
        }

        if ($this->isImmutable()) {
            foreach ($this->getHistory() as $rule_id => $amounts) {
                $item->setPrice($rule_id, $item->getPrice() - array_sum($amounts));
            }
            $item->addAttr($item::ATTR_IMMUTABLE);
        }

        if ( ! $this->isVisible()) {
            $item->addAttr($item::ATTR_IMMUTABLE);
        }

        return $item;
    }


    /**
     * @return FreeCartItem|null
     */
    protected function createFreeItem()
    {
        // todo replace keys
        $rule_id = array_keys($this->getHistory());
        $rule_id = reset($rule_id);

        $product = clone $this->product;

        try {
            $reflection = new ReflectionClass($product);
            $property   = $reflection->getProperty('changes');
            $property->setAccessible(true);
            $property->setValue($product, array());
        } catch (Exception $e) {

        }

        try {
            $item = new FreeCartItem($product, 0, $rule_id, $this->associatedGiftHash);
        } catch (Exception $e) {
            return null;
        }

        $item->setQtyAlreadyInWcCart($this->qty);

        $item->setVariation($this->variation);
        $item->setCartItemData($this->thirdPartyData);

        if ($this->getReplaceWithCoupon()) {
            $item->setReplaceWithCoupon(true);
            $item->setReplaceCouponCode($this->getReplaceCouponCode());
        }

        $item->setSelected($this->selectedFreeCartItem);

        return $item;
    }

    /**
     * @return bool
     */
    public function isAffected()
    {
        return ! empty($this->history);
    }

    public function sanitize()
    {
        $this->parentItemKey = null;
        $this->attributes    = null;
        $this->originalData  = null;
        $this->discounts     = array();

        if ($this->history && $this->compareStrategy->floatsAreEqual($this->newPrice,
                $this->product->get_price('edit'))) {
            try {
                $reflection = new ReflectionClass($this->product);
                $property   = $reflection->getProperty('changes');
                $property->setAccessible(true);
                $property->setValue($this->product, array());
            } catch (Exception $e) {

            }
        }

        $this->history              = array();
        $this->newPrice             = null;
        $this->replaceWithCoupon    = null;
        $this->replaceCouponCode    = null;
        $this->currency             = $this->context->currencyController->getCurrentCurrency();
        $this->associatedGiftHash   = '';
        $this->freeCartItemHash     = '';
        $this->selectedFreeCartItem = false;
    }

    public function getClearData()
    {
        return array(
            self::KEY_KEY          => $this->key,
            self::KEY_PRODUCT_ID   => $this->productId,
            self::KEY_VARIATION_ID => $this->variationId,
            self::KEY_VARIATION    => $this->variation,
            self::KEY_QTY          => $this->qty,
            self::KEY_PRODUCT      => $this->product,
            self::KEY_DATA_HASH    => $this->dataHash,
            self::KEY_TAX_DATA     => $this->lineTaxData,
            self::KEY_SUBTOTAL     => $this->lineSubtotal,
            self::KEY_SUBTOTAL_TAX => $this->lineSubtotalTax,
            self::KEY_TOTAL        => $this->lineTotal,
            self::KEY_TAX          => $this->lineTax,
        );
    }

    public function getData()
    {
        return array_merge($this->getClearData(), $this->getCartItemData());
    }

    /**
     * @return WC_Product
     */
    public function getProduct(): WC_Product
    {
        return $this->product;
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
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return int
     */
    public function getProductId()
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getVariationId()
    {
        return $this->variationId;
    }

    /**
     * @return array
     */
    public function getVariation()
    {
        return $this->variation;
    }

    /**
     * @return array
     */
    public function getThirdPartyData()
    {
        return $this->thirdPartyData;
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setThirdPartyData($key, $value)
    {
        $this->thirdPartyData[$key] = $value;
    }

    /**
     * @param string $key
     */
    public function deleteThirdPartyData($key)
    {
        unset($this->thirdPartyData[$key]);
    }

    public function getOurData()
    {
        return array(
            self::ADP_PARENT_CART_ITEM_KEY    => $this->parentItemKey,
            self::ADP_ATTRIBUTES_KEY          => $this->attributes,
            self::ADP_ORIGINAL_KEY            => $this->originalData,
            self::ADP_HISTORY_KEY             => $this->history,
            self::ADP_DISCOUNTS_KEY           => $this->discounts,
            self::ADP_NEW_PRICE_KEY           => $this->newPrice,
            self::ADP_REPLACE_WITH_COUPON     => $this->replaceWithCoupon,
            self::ADP_REPLACE_COUPON_NAME     => $this->replaceCouponCode,
            self::ADP_CURRENCY                => $this->packCurrencyObject($this->currency),
            self::ADP_ASSOCIATED_GIFT_HASH    => $this->associatedGiftHash,
            self::ADP_FREE_CART_ITEM_HASH     => $this->freeCartItemHash,
            self::ADP_SELECTED_FREE_CART_ITEM => $this->selectedFreeCartItem,
        );
    }

    /**
     * @param Currency|null $currency
     *
     * @return array
     */
    protected function packCurrencyObject($currency)
    {
        if ($currency === null) {
            return array();
        }

        return array(
            'code'   => $currency->getCode(),
            'symbol' => $currency->getSymbol(),
            'rate'   => $currency->getRate(),
        );
    }

    /**
     * @param array $data
     *
     * @return Currency|null
     * @throws Exception
     */
    protected function unpackCurrencyObject($data)
    {
        if ( ! isset($data['code'], $data['symbol'], $data['rate'])) {
            return null;
        }

        return new Currency($data['code'], $data['symbol'], $data['rate']);
    }

    /**
     * @return array
     */
    public function getCartItemData()
    {
        $cartItemData                = $this->thirdPartyData;
        $cartItemData[self::KEY_ADP] = $this->getOurData();

        return $cartItemData;
    }

    /**
     * @param null|string $key
     */
    public function setOriginalKey($key)
    {
        $this->parentItemKey = $key;
    }

    /**
     * @return string|null
     */
    public function getOriginalKey()
    {
        return $this->parentItemKey;
    }

    public function isClone()
    {
        return isset($this->parentItemKey);
    }

    /**
     * @return float
     */
    public function getSubtotal()
    {
        return $this->lineSubtotal;
    }

    /**
     * @return float
     */
    public function getSubtotalTax()
    {
        return $this->lineSubtotalTax;
    }

    /**
     * @return float
     */
    public function getExactSubtotalTax()
    {
        $lineSubtotalTaxData = isset($this->lineTaxData['subtotal']) ? $this->lineTaxData['subtotal'] : array();

        return array_sum($lineSubtotalTaxData);
    }

    /**
     * @return bool
     */
    public function isImmutable()
    {
        return ! empty($this->attributes) && in_array(self::ATTRIBUTE_IMMUTABLE, $this->attributes);
    }

    /**
     * @return bool
     */
    public function isFreeItem()
    {
        return ! empty($this->attributes) && in_array(self::ATTRIBUTE_FREE, $this->attributes);
    }

    /**
     * @return array
     */
    public function getHistory()
    {
        return $this->history;
    }

    /**
     * @param array $history
     */
    public function setHistory($history)
    {
        if (is_array($history)) {
            $this->history = $history;
        }
    }

    /**
     * @return array
     */
    public function getDiscounts()
    {
        return $this->discounts;
    }

    /**
     * @param array $discounts
     */
    public function setDiscounts($discounts)
    {
        if (is_array($discounts)) {
            $this->discounts = $discounts;
        }
    }

    /**
     * @param float|null $price
     */
    public function setInitialCustomPrice($price)
    {
        $this->originalData['initial_custom_price'] = is_null($price) ? $price : floatval($price);
    }

    /**
     * Value of $product->get_price('edit') on first time cart processing
     * Required to process 3rd party custom prices
     *
     * @return float|null
     */
    public function getInitialCustomPrice()
    {
        return isset($this->originalData['initial_custom_price']) ? $this->originalData['initial_custom_price'] : null;
    }

    /**
     * @param float $price
     */
    public function setOriginalPriceWithoutTax($price)
    {
        $this->originalData['original_price_without_tax'] = floatval($price);
    }

    /**
     * @return float|null
     */
    public function getOriginalPriceWithoutTax()
    {
        return isset($this->originalData['original_price_without_tax']) ? $this->originalData['original_price_without_tax'] : null;
    }

    /**
     * @param float $price
     */
    public function setOriginalPrice($price)
    {
        $this->originalData['original_price'] = floatval($price);
    }

    /**
     * @return float|null
     */
    public function getOriginalPrice()
    {
        return isset($this->originalData['original_price']) ? $this->originalData['original_price'] : null;
    }

    /**
     * @param float $priceTax
     */
    public function setOriginalPriceTax($priceTax)
    {
        $this->originalData['original_price_tax'] = floatval($priceTax);
    }

    /**
     * @return float|null
     */
    public function getOriginalPriceTax()
    {
        return isset($this->originalData['original_price_tax']) ? $this->originalData['original_price_tax'] : null;
    }

    /**
     * @param string $attr
     */
    public function addAttribute($attr)
    {
        $allowedAttributes = array(
            self::ATTRIBUTE_FREE,
            self::ATTRIBUTE_IMMUTABLE,
            self::ATTRIBUTE_READONLY_PRICE,
        );

        $attr = (string)$attr;

        if ($attr && in_array($attr, $allowedAttributes)) {
            if ( ! is_array($this->attributes)) {
                $this->attributes = array();
            }

            if ( ! in_array($attr, $this->attributes)) {
                $this->attributes[] = $attr;
            }
        }
    }

    /**
     * @param string $attr
     */
    public function removeAttribute($attr)
    {
        $attr = (string)$attr;

        if ( ! $attr || ! is_array($this->attributes)) {
            return;
        }

        $pos = array_search($attr, $this->attributes);

        if ($pos !== false) {
            unset($this->attributes[$pos]);
            $this->attributes = array_values($this->attributes);
        }
    }

    /**
     * @param float $newPrice
     */
    public function setNewPrice($newPrice)
    {
        $this->newPrice = floatval($newPrice);
        $this->product->set_price($newPrice);
    }

    /**
     * @return float|null
     */
    public function getNewPrice()
    {
        return $this->newPrice;
    }

    /**
     * @return bool
     */
    public function isVisible()
    {
        return $this->visible;
    }

    /**
     * @param bool $replaceWithCoupon
     */
    public function setReplaceWithCoupon($replaceWithCoupon)
    {
        $this->replaceWithCoupon = boolval($replaceWithCoupon);
    }

    /**
     * @return bool
     */
    public function getReplaceWithCoupon()
    {
        return $this->replaceWithCoupon === true; // $this->replaceWithCoupon can be null
    }

    /**
     * @param string $replaceCouponCode
     */
    public function setReplaceCouponCode($replaceCouponCode)
    {
        $this->replaceCouponCode = strval($replaceCouponCode);
    }

    /**
     * @return string
     */
    public function getReplaceCouponCode()
    {
        return $this->replaceCouponCode;
    }

    /**
     * @return Currency|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        if ($currency instanceof Currency) {
            $this->currency = $currency;
        }
    }

    /**
     * @param string $associatedGiftHash
     */
    public function setAssociatedGiftHash($associatedGiftHash)
    {
        $this->associatedGiftHash = strval($associatedGiftHash);
    }

    /**
     * @return string
     */
    public function getAssociatedGiftHash()
    {
        return $this->associatedGiftHash;
    }

    /**
     * @param string $freeCartItemHash
     */
    public function setFreeCartItemHash($freeCartItemHash)
    {
        if (is_string($freeCartItemHash)) {
            $this->freeCartItemHash = strval($freeCartItemHash);
        }
    }

    /**
     * @return string
     */
    public function getFreeCartItemHash()
    {
        return $this->freeCartItemHash;
    }

    /**
     * @param bool $selectedFreeCartItem
     */
    public function setSelectedFreeCartItem($selectedFreeCartItem)
    {
        $this->selectedFreeCartItem = $selectedFreeCartItem;
    }

    /**
     * @return bool
     */
    public function isSelectedFreeCartItem()
    {
        return $this->selectedFreeCartItem;
    }

    /**
     * @param Context $context
     * @param \WC_Product $product
     * @param array $cartItemData
     *
     * @return self
     */
    public static function createFromProduct(
        Context $context,
        \WC_Product $product,
        array $cartItemData = array()
    ) {

        // unset totals key from cart item data
        foreach (
            array(
                self::KEY_TAX_DATA,
                self::KEY_SUBTOTAL,
                self::KEY_SUBTOTAL_TAX,
                self::KEY_TOTAL,
                self::KEY_TAX
            ) as $key
        ) {
            unset($cartItemData[$key]);
        }

        if ($product->is_type('variation')) {
            $variationId = $product->get_id();
            $productId   = $product->get_parent_id();
            $variation   = $product->get_variation_attributes();
        } else {
            $productId   = $product->get_id();
            $variationId = 0;
            $variation   = array();
        }

        // do not passing product
        $fakeWcCartItem = array(
            self::KEY_KEY          => self::generateCartId($productId, $variationId, $variation, $cartItemData),
            self::KEY_PRODUCT_ID   => $productId,
            self::KEY_VARIATION_ID => $variationId,
            self::KEY_VARIATION    => $variation,
            self::KEY_QTY          => floatval(1),
            self::KEY_DATA_HASH    => self::wcGetCartItemDataHash($product),
        );

        // performance trick to prevent cloning
        $obj          = new self($context, $fakeWcCartItem);
        $obj->product = $product;

        return $obj;
    }

    /**
     * Generate a unique ID for the cart item being added.
     *
     * @param int $productId - id of the product the key is being generated for.
     * @param int $variationId of the product the key is being generated for.
     * @param array $variation data for the cart item.
     * @param array $cartItemData other cart item data passed which affects this items uniqueness in the cart.
     *
     * @return string cart item key
     * @see \WC_Cart::generate_cart_id() THE PLACE WHERE IT WAS COPIED FROM
     */
    public static function generateCartId(
        int $productId,
        int $variationId = 0,
        array $variation = array(),
        array $cartItemData = array()
    ) {
        $idParts = array($productId);

        if ($variationId && 0 !== $variationId) {
            $idParts[] = $variationId;
        }

        if (is_array($variation) && ! empty($variation)) {
            $variationKey = '';
            foreach ($variation as $key => $value) {
                $variationKey .= trim($key) . trim($value);
            }
            $idParts[] = $variationKey;
        }

        if (is_array($cartItemData) && ! empty($cartItemData)) {
            $cart_item_data_key = '';
            foreach ($cartItemData as $key => $value) {
                if (is_array($value) || is_object($value)) {
                    $value = http_build_query($value);
                }
                $cart_item_data_key .= trim($key) . trim($value);

            }
            $idParts[] = $cart_item_data_key;
        }

        return apply_filters('woocommerce_cart_id', md5(implode('_', $idParts)), $productId, $variationId,
            $variation, $cartItemData);
    }

    /**
     * @param WC_Product $product Product object.
     *
     * @return string
     *
     * @see wc_get_cart_item_data_hash()
     */
    public static function wcGetCartItemDataHash($product)
    {
        return md5(wp_json_encode(apply_filters('woocommerce_cart_item_data_to_validate', array(
            'type'       => $product->get_type(),
            'attributes' => 'variation' === $product->get_type() ? $product->get_variation_attributes() : '',
        ), $product)));
    }
}
