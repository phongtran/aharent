<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartItem;
use ADP\BaseVersion\Includes\Cart\Structures\Coupon;
use ADP\BaseVersion\Includes\Cart\Structures\FreeCartItem;
use ADP\BaseVersion\Includes\Cart\Structures\TaxExemptProcessor;
use ADP\BaseVersion\Includes\CompareStrategy;
use ADP\BaseVersion\Includes\Currency;
use ADP\BaseVersion\Includes\CurrencyController;
use ADP\BaseVersion\Includes\External\Cmp\GiftCardsSomewhereWarmCmp;
use ADP\BaseVersion\Includes\External\Cmp\PDFProductVouchersCmp;
use ADP\BaseVersion\Includes\External\Cmp\PhoneOrdersCmp;
use ADP\BaseVersion\Includes\External\Cmp\SomewhereWarmBundlesCmp;
use ADP\BaseVersion\Includes\External\Cmp\SomewhereWarmCompositesCmp;
use ADP\BaseVersion\Includes\External\Cmp\WcDepositsCmp;
use ADP\BaseVersion\Includes\External\Cmp\WcsAttCmp;
use ADP\BaseVersion\Includes\External\Cmp\WcSubscriptionsCmp;
use ADP\BaseVersion\Includes\External\Cmp\WoocsCmp;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcNoFilterWorker;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use ADP\BaseVersion\Includes\OverrideCentsStrategy;
use ADP\BaseVersion\Includes\Reporter\CartCalculatorListener;
use ADP\BaseVersion\Includes\Rule\Structures\ItemDiscount;
use ADP\Factory;
use ADP\BaseVersion\Includes\Enums\ShippingMethodEnum;
use ReflectionClass;
use ReflectionException;
use WC_Cart;
use WC_Cart_Totals;
use WC_Product;
use WC_Product_Variation;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartProcessor
{
    /**
     * @var WC_Cart
     */
    protected $wcCart;

    /**
     * @var Cart
     */
    protected $cart;

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var WcNoFilterWorker
     */
    protected $wcNoFilterWorker;

    /**
     * @var CartCalculator
     */
    protected $calc;

    /**
     * @var CartCouponsProcessor
     */
    protected $cartCouponsProcessor;

    /**
     * @var CartFeeProcessor
     */
    protected $cartFeeProcessor;

    /**
     * @var CartShippingProcessor
     */
    protected $shippingProcessor;

    /**
     * @var TaxExemptProcessor
     */
    protected $taxExemptProcessor;

    /**
     * @var WcTotalsFacade
     */
    protected $cartTotalsWrapper;

    /**
     * @var CartBuilder
     */
    protected $cartBuilder;

    /**
     * @var CartCalculatorListener
     */
    protected $listener;

    /**
     * @var PhoneOrdersCmp
     */
    protected $poCmp;

    /**
     * @var OverrideCentsStrategy
     */
    protected $overrideCentsStrategy;

    /**
     * @var CompareStrategy
     */
    protected $compareStrategy;

    /**
     * @var WcSubscriptionsCmp
     */
    protected $wcSubsCmp;

    /**
     * @var WcsAttCmp
     */
    protected $wcsAttCmp;

    /**
     * @var PDFProductVouchersCmp
     */
    protected $vouchers;

    /**
     * @var SomewhereWarmBundlesCmp
     */
    protected $bundlesCmp;

    /**
     * @var SomewhereWarmCompositesCmp
     */
    protected $compositesCmp;

    /**
     * @var wcDepositsCmp
     */
    protected $wcDepositsCmp;

    /**
     * CartProcessor constructor.
     *
     * @param Context $context
     * @param WC_Cart|null $wcCart
     * @param CartCalculator|null $calc
     */
    public function __construct($context, $wcCart, $calc = null)
    {
        $this->context          = $context;
        $this->wcCart           = $wcCart;
        $this->wcNoFilterWorker = new WcNoFilterWorker();

        $this->listener = new CartCalculatorListener($context);

        if ($calc instanceof CartCalculator) {
            $this->calc = $calc;
        } else {
            $this->calc = Factory::callStaticMethod("Cart_CartCalculator", 'make', $context, $this->listener);
            /** @see CartCalculator::make() */
        }

        $this->cartCouponsProcessor  = Factory::get("Cart_CartCouponsProcessor", $context);
        $this->cartFeeProcessor      = new CartFeeProcessor();
        $this->shippingProcessor     = Factory::get("Cart_CartShippingProcessor", $context);
        $this->taxExemptProcessor    = new TaxExemptProcessor($context);
        $this->cartTotalsWrapper     = new WcTotalsFacade($this->context, $wcCart);
        $this->cartBuilder           = new CartBuilder($this->context);
        $this->poCmp                 = new PhoneOrdersCmp($context);
        $this->overrideCentsStrategy = new OverrideCentsStrategy($context);
        $this->compareStrategy       = new CompareStrategy($context);
        $this->wcSubsCmp             = new WcSubscriptionsCmp($context);
        $this->wcsAttCmp             = new WcsAttCmp($context);
        $this->vouchers              = new PDFProductVouchersCmp($context);
        $this->bundlesCmp            = new SomewhereWarmBundlesCmp($context);
        $this->compositesCmp         = new SomewhereWarmCompositesCmp($context);
        $this->wcDepositsCmp         = new WcDepositsCmp($context);
        $giftCart                    = new GiftCardsSomewhereWarmCmp($context);
        if ($giftCart->isActive()) {
            $giftCart->applyCompatibility();
        }
        if ($this->bundlesCmp->isActive()) {
            $this->bundlesCmp->addFilters();
        }
    }

    public function installActionFirstProcess()
    {
        $this->cartCouponsProcessor->setFilterToInstallCouponsData();
        $this->cartCouponsProcessor->setFiltersToSupportPercentLimitCoupon();
        $this->cartCouponsProcessor->setFiltersToSupportExactItemApplicationOfReplacementCoupon();
        $this->cartFeeProcessor->setFilterToCalculateFees();
        $this->shippingProcessor->setFilterToEditPackageRates();
        $this->shippingProcessor->setFilterToEditShippingMethodLabel();
        $this->shippingProcessor->setFilterForShippingChosenMethod();

        add_filter('woocommerce_update_cart_validation', array($this, 'filterCheckCartItemExistenceBeforeUpdate'), 10,
            4);
    }

    /**
     * The main process function.
     * WC_Cart -> Cart -> Cart processing -> New Cart -> modifying global WC_Cart
     *
     * @param bool $first
     *
     * @return Cart
     */
    public function process($first = false)
    {
        $wcCart           = $this->wcCart;
        $wcNoFilterWorker = $this->wcNoFilterWorker;

        $this->syncCartItemHashes($wcCart);

        $this->listener->processStarted($wcCart);
        $this->taxExemptProcessor->maybeRevertTaxExempt(WC()->customer, WC()->session);
        $cart = $this->cartBuilder->create(WC()->customer, WC()->session);
        $this->listener->cartCreated($cart);

        if ( ! $wcCart || $wcCart->is_empty()) {
            return $cart;
        }

        $chosenShippingMethods    = WC()->session->get("chosen_shipping_methods");
        $chosenOwnShippingMethods = array();

        if (is_array($chosenShippingMethods)) {
            foreach ($chosenShippingMethods as $index => $chosenShippingMethod) {
                if (strpos($chosenShippingMethod, ShippingMethodEnum::TYPE_ADP_FREE_SHIPPING) !== false) {
                    $chosenOwnShippingMethods[$index] = $chosenShippingMethod;
                }
            }
        }

        // add previously added free items to internal Cart and remove them from WC_Cart
        $this->processFreeItems($cart, $wcCart);
        $this->eliminateClones($wcCart);

        $this->poCmp->sanitizeWcCart($wcCart);

        // fill internal Cart from cloned WC_Cart
        // do not use global WC_Cart because we change prices to get correct initial subtotals
        $clonedWcCart     = clone $wcCart;
        $currencySwitcher = $this->context->currencyController;

        if ($currencySwitcher->isCurrencyChanged()) {
            foreach ($clonedWcCart->cart_contents as $cartKey => $wcCartItem) {
                $facade  = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);
                $product = $facade->getProduct();

                $product->set_price($currencySwitcher->getCurrentCurrencyProductPrice($product));
                $salePrice = $currencySwitcher->getCurrentCurrencyProductSalePrice($product);
                if ($salePrice !== null) {
                    $product->set_sale_price($salePrice);
                }
                $product->set_regular_price($currencySwitcher->getCurrentCurrencyProductRegularPrice($product));

                $price_mode = $this->context->getOption('discount_for_onsale');

                if ($product->is_on_sale('edit')) {
                    if ('sale_price' === $price_mode || 'discount_sale' === $price_mode) {
                        $price = $product->get_sale_price('edit');
                    } else {
                        $price = $product->get_regular_price('edit');
                    }
                } else {
                    $price = $product->get_price('edit');
                }

                $product->set_price($price);

                $facade->setCurrency($currencySwitcher->getCurrentCurrency());
                $clonedWcCart->cart_contents[$cartKey] = $facade->getData();
            }
        } else {
            foreach ($clonedWcCart->cart_contents as $cartKey => $wcCartItem) {
                $facade               = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);
                $product              = $facade->getProduct();
                $prodPropsWithFilters = $this->context->getOption('initial_price_context') === 'view';

                if ($first) {
                    $facade->setInitialCustomPrice(null);
                    if ($prodPropsWithFilters && ! $this->compareStrategy->floatsAreEqual($product->get_price('edit'),
                            $product->get_price('view'))) {
                        $facade->setInitialCustomPrice(floatval($product->get_price('view')));
                    } elseif ( ! isset($product->get_changes()['price'])) {
                        self::setProductPriceDependsOnPriceMode($product);
                    } else {
                        $facade->setInitialCustomPrice($product->get_price('edit'));
                    }
                } else {
                    if ($prodPropsWithFilters && ! $this->compareStrategy->floatsAreEqual($product->get_price('edit'),
                            $product->get_price('view'))) {
                        self::setProductPriceDependsOnPriceMode($product);
                        $facade->setInitialCustomPrice(floatval($product->get_price('view')));
                    } elseif ($this->poCmp->isCartItemCostUpdateManually($facade)) {
                        $product->set_price($this->poCmp->getCartItemCustomPrice($facade));
                        $product->set_regular_price($this->poCmp->getCartItemCustomPrice($facade));
                        $facade->addAttribute($facade::ATTRIBUTE_IMMUTABLE);
                    } elseif ($facade->getInitialCustomPrice() !== null) {
                        $product->set_price($facade->getInitialCustomPrice());
                    } /**
                     * Catch 3rd party price changes
                     * e.g. during action 'before calculate totals'
                     */ elseif ($facade->getNewPrice() !== null && ! $this->compareStrategy->floatsAreEqual($facade->getNewPrice(),
                            $product->get_price('edit'))) {
                        $facade->setInitialCustomPrice($product->get_price('edit'));
                        $product->set_price($product->get_price('edit'));
                    } else {
                        self::setProductPriceDependsOnPriceMode($product);
                    }

                }

                $clonedWcCart->cart_contents[$cartKey] = $facade->getData();
            }
        }

        $flags = array();
        if ($this->wcSubsCmp->isActive() && $this->wcsAttCmp->isActive()) {
            $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
        }

        if ($this->bundlesCmp->isActive()) {
            $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
        }

        if ($this->context->isDisableShippingCalculationDuringProcess()) {
            $flags[] = $wcNoFilterWorker::FLAG_DISALLOW_SHIPPING_CALCULATION;
        }

        $flags = apply_filters("adp_calculate_totals_flags_for_cloned_cart_before_process", $flags, $wcNoFilterWorker, $first, $clonedWcCart, $this);
        $wcNoFilterWorker->calculateTotals($clonedWcCart, ...$flags);
        $this->cartBuilder->populateCart($cart, $clonedWcCart);
        $this->listener->cartCompleted($cart);
        // fill internal Cart from cloned WC_Cart ended

        // Delete all 'pricing' data from the cart
        $this->sanitizeWcCart($wcCart);
        $this->cartCouponsProcessor->sanitize($wcCart);
        $this->cartFeeProcessor->sanitize($wcCart);
        $this->shippingProcessor->sanitize($wcCart);

        /**
         * Add flag 'FLAG_ALLOW_PRICE_HOOKS'
         * because some plugins set price using 'get_price' hooks instead of modify WC_Product property.
         */
        $flags = array($wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS);
        if ($this->context->isDisableShippingCalculationDuringProcess()) {
            $flags[] = $wcNoFilterWorker::FLAG_DISALLOW_SHIPPING_CALCULATION;
        }
        $wcNoFilterWorker->calculateTotals($wcCart, ...$flags);
        // Delete all 'pricing' data from the cart ended

        $result = $this->calc->processCart($cart);

        if ($result) {
            $context = $this->context;
            if (
                $context->getOption('external_coupons_behavior') === 'disable_if_any_rule_applied'
                || $context->getOption('external_coupons_behavior') === 'disable_if_any_of_cart_items_updated'
            ) {
                $this->replaceWcNotice(
                    array(
                        'text' => __('Coupon code applied successfully.', 'woocommerce'),
                        'type' => 'success',
                    ),
                    array(
                        'text' => __('Sorry, coupons are disabled for these products.',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'type' => 'error',
                    )
                );
            }

            // todo replace $this
            do_action('wdp_before_apply_to_wc_cart', $this, $wcCart, $cart);

            //TODO Put to down items that are not filtered?

            // process free items
            $freeProducts = apply_filters('wdp_internal_free_products_before_apply', $cart->getFreeItems(), $this);
            /** @var $freeProducts FreeCartItem[] */

            $freeProductsMapping = array();
            foreach ($freeProducts as $index => $freeItem) {
                $product = $freeItem->getProduct();

                $product_id = $product->get_id();
                if ($product instanceof WC_Product_Variation) {
                    /** @var WC_Product_Variation $product */
                    $variationId = $product_id;
                    $product_id  = $product->get_parent_id();
                    $variation   = $freeItem->getVariation();
                } else {
                    $variationId = 0;
                    $variation   = array();
                }

                $cartItemData = $freeItem->getCartItemData();

                if ($cartItemKey = $wcNoFilterWorker->addToCart($clonedWcCart, $product_id, $freeItem->qty,
                    $variationId, $variation, $cartItemData)) {

                    if ( ! isset($freeProductsMapping[$cartItemKey])) {
                        $freeProductsMapping[$cartItemKey] = array();
                    }

                    $freeProductsMapping[$cartItemKey][] = $freeItem;

                    if ($currencySwitcher->isCurrencyChanged()) {
                        $facade = new WcCartItemFacade($this->context, $clonedWcCart->cart_contents[$cartItemKey],
                            $cartItemKey);

                        $product = $facade->getProduct();
                        $product->set_price($currencySwitcher->getCurrentCurrencyProductPrice($product));
                        $salePrice = $currencySwitcher->getCurrentCurrencyProductSalePrice($product);
                        if ($salePrice !== null) {
                            $product->set_sale_price($salePrice);
                        }
                        $product->set_regular_price($currencySwitcher->getCurrentCurrencyProductRegularPrice($product));

                        $price_mode = $this->context->getOption('discount_for_onsale');

                        if ($product->is_on_sale('edit')) {
                            if ('sale_price' === $price_mode || 'discount_sale' === $price_mode) {
                                $price = $product->get_sale_price('edit');
                            } else {
                                $price = $product->get_regular_price('edit');
                            }
                        } else {
                            $price = $product->get_price('edit');
                        }

                        $product->set_price($price);

                        $facade->setCurrency($currencySwitcher->getCurrentCurrency());
                        $clonedWcCart->cart_contents[$cartItemKey] = $facade->getData();
                    }
                }
            }

            $flags = array($wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS);
            if ($this->context->isDisableShippingCalculationDuringProcess()) {
                $flags[] = $wcNoFilterWorker::FLAG_DISALLOW_SHIPPING_CALCULATION;
            }

            // Here we have an initial cart with full-price free products
            // Save the totals of the initial cart to show the difference
            // Use the flag 'FLAG_ALLOW_PRICE_HOOKS' to get filtered product prices
            if ($currencySwitcher->isCurrencyChanged()) {
                $wcNoFilterWorker->calculateTotals($clonedWcCart);
            } else {
                $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
                $wcNoFilterWorker->calculateTotals($clonedWcCart, ...$flags);
            }
            $initialTotals = $clonedWcCart->get_totals();

            foreach ($freeProductsMapping as $loopCartItemKey => $freeItems) {
                foreach ($freeItems as $freeItem) {
                    /** @var FreeCartItem $freeItem */

                    $facade = new WcCartItemFacade($this->context, $clonedWcCart->cart_contents[$loopCartItemKey],
                        $loopCartItemKey);

                    $rules = array($freeItem->getRuleId() => array($freeItem->getInitialPrice()));

                    $cartItemQty = $facade->getQty();
                    $facade->setQty($freeItem->getQty());

                    $facade->setOriginalPrice($facade->getProduct()->get_price('edit'));

                    $facade->addAttribute($facade::ATTRIBUTE_FREE);

                    /**
                     * @var Coupon|null $coupon
                     *
                     * We must keep the reference, because the affected items are not yet known
                     */
                    $coupon = null;

                    if ($freeItem->isReplaceWithCoupon()) {
                        // no need to change the price, it is already full
                        $facade->setDiscounts(array());

                        if ($this->context->priceSettings->isIncludeTax()) {
                            $couponAmount = $facade->getSubtotal() + $facade->getExactSubtotalTax();
                        } else {
                            $couponAmount = $facade->getSubtotal();
                        }
                        $couponAmount = ($couponAmount / $cartItemQty) * $freeItem->getQty();

                        $coupon = new Coupon(
                            $this->context,
                            Coupon::TYPE_FREE_ITEM,
                            $freeItem->getReplaceCouponCode(),
                            $couponAmount / $freeItem->getQty(),
                            $freeItem->getRuleId(),
                            null
                        );

                        $cart->addCoupon($coupon);

                        $facade->setReplaceWithCoupon(true);
                        $facade->setReplaceCouponCode($freeItem->getReplaceCouponCode());
                    } elseif ($this->context->getOption('free_products_as_coupon',
                            false) && $this->context->getOption('free_products_coupon_name', false)) {
                        $facade->setDiscounts(array());

                        if ($this->context->priceSettings->isIncludeTax()) {
                            $couponAmount = $facade->getSubtotal() + $facade->getExactSubtotalTax();
                        } else {
                            $couponAmount = $facade->getSubtotal();
                        }
                        $couponAmount = ($couponAmount / $cartItemQty) * $freeItem->getQty();

                        $coupon = new Coupon(
                            $this->context,
                            Coupon::TYPE_FREE_ITEM,
                            $this->context->getOption('free_products_coupon_name'),
                            $couponAmount / $freeItem->getQty(),
                            $freeItem->getRuleId(),
                            null
                        );

                        $cart->addCoupon($coupon);

                        $facade->setReplaceWithCoupon(true);
                        $facade->setReplaceCouponCode($this->context->getOption('free_products_coupon_name'));
                    } else {
                        $facade->setNewPrice(0);
                        $facade->setDiscounts($rules);
                    }

                    $facade->setOriginalPriceWithoutTax($facade->getSubtotal() / $cartItemQty);
                    $facade->setOriginalPriceTax($facade->getExactSubtotalTax() / $cartItemQty);
                    $facade->setHistory($rules);
                    $facade->setAssociatedGiftHash($freeItem->getAssociatedGiftHash());
                    $facade->setFreeCartItemHash($freeItem->hash());
                    $facade->setSelectedFreeCartItem($freeItem->isSelected());

                    $cartItemKey = $wcNoFilterWorker->addToCart($wcCart, $facade->getProductId(), $facade->getQty(),
                        $facade->getVariationId(), $facade->getVariation(), $facade->getCartItemData());

                    $newFacade = new WcCartItemFacade($this->context, $wcCart->cart_contents[$cartItemKey],
                        $cartItemKey);
                    $newFacade->setNewPrice($facade->getProduct()->get_price('edit'));
                    $wcCart->cart_contents[$cartItemKey] = $newFacade->getData();

                    if (isset($coupon)) {
                        $coupon->setAffectedCartItem($newFacade);
                    }
                }
            }

            $flags = array();
            if ($this->context->isDisableShippingCalculationDuringProcess()) {
                $flags[] = $wcNoFilterWorker::FLAG_DISALLOW_SHIPPING_CALCULATION;
            }

            $wcNoFilterWorker->calculateTotals($wcCart, ...$flags);
            // process free items ended

            $this->addCommonItems($cart, $wcCart);

            // handle option 'external_coupons_behavior'
            $this->maybeRemoveOriginCoupons($cart, $wcCart);

            $this->applyTotals($cart, $wcCart);

            if (count($chosenOwnShippingMethods) > 0) {
                $chosenShippingMethods = WC()->session->get("chosen_shipping_methods");
                foreach ($chosenOwnShippingMethods as $index => $chosenOwnShippingMethod) {
                    $chosenShippingMethods[$index] = $chosenOwnShippingMethod;
                }
                WC()->session->set("chosen_shipping_methods", $chosenShippingMethods);
            }

            $this->taxExemptProcessor->installTaxExemptFromNewCart($cart, WC()->customer, WC()->session);

            $flags = array();

            if ($this->vouchers->isActive()) {
                $flags[] = $wcNoFilterWorker::FLAG_ALLOW_TOTALS_HOOKS;
            }

            if (
                $this->wcSubsCmp->isActive() && $this->wcsAttCmp->isActive()
            ) {
                $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
            }

            if ($this->bundlesCmp->isActive()) {
                $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
            }

            if ($this->compositesCmp->isActive()) {
                $flags[] = $wcNoFilterWorker::FLAG_ALLOW_PRICE_HOOKS;
            }

            $wcNoFilterWorker->calculateTotals($wcCart, ...$flags);
            $wcCart->set_session();

            $this->cartCouponsProcessor->updateTotals($wcCart);
            $this->cartFeeProcessor->updateTotals($wcCart);
            $this->shippingProcessor->updateTotals($wcCart);
            $this->cartTotalsWrapper->insertInitialTotals($initialTotals);

            if ($this->context->getOption('show_message_after_add_free_product')) {
                $this->notifyAboutAddedFreeItems($cart);
            }

            if ($this->wcDepositsCmp->isActive()) {
                $this->wcDepositsCmp->updateDepositsData($wcCart);
            }
            do_action('wdp_after_apply_to_wc_cart', $this, $cart, $wcCart);
            $this->poCmp->forceToSkipFreeCartItems($wcCart);
        }

        $this->listener->processFinished($wcCart);

        return $cart;
    }

    /**
     * Merge cloned items into the 'locomotive' item. Destroy them after.
     * If the 'locomotive' item has been removed, promote the first clone.
     *
     * @param WC_Cart $wcCart
     */
    protected function eliminateClones($wcCart)
    {
        foreach ($wcCart->cart_contents as $cartKey => $wcCartItem) {
            $wrapper = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);

            if ($wrapper->getOriginalKey()) {
                if (isset($wcCart->cart_contents[$wrapper->getOriginalKey()])) {
                    $originalWrapper = new WcCartItemFacade($this->context,
                        $wcCart->cart_contents[$wrapper->getOriginalKey()], $wrapper->getOriginalKey());
                    $originalWrapper->setQty($originalWrapper->getQty() + $wrapper->getQty());
                    $wcCart->cart_contents[$originalWrapper->getKey()] = $originalWrapper->getData();
                } else {
                    /** The 'locomotive' is not in cart. Promote the clone! */
                    $wrapper->setKey($wrapper->getOriginalKey());
                    $wrapper->setOriginalKey(null);
                    $wcCart->cart_contents[$wrapper->getKey()] = $wrapper->getData();
                }

                /** do not forget to remove clone */
                unset($wcCart->cart_contents[$cartKey]);
            }
        }
    }

    /**
     * @param $cart Cart
     * @param $wcCart WC_Cart
     */
    protected function processFreeItems($cart, $wcCart)
    {
        $pos = 0;
        foreach ($wcCart->cart_contents as $cartKey => $wcCartItem) {
            $wrapper = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);
            if ($wrapper->isFreeItem()) {
                $item = $wrapper->createItem();
                $item->setPos($pos);
                $cart->addToCart($item);
                unset($wcCart->cart_contents[$cartKey]);
            }

            $pos++;
        }
    }

    /**
     * @param WC_Cart $wcCart
     */
    public function sanitizeWcCart($wcCart)
    {
        foreach ($wcCart->cart_contents as $cartKey => $wcCartItem) {
            $wrapper = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);
            $wrapper->sanitize();
            $wcCart->cart_contents[$cartKey] = $wrapper->getData();
        }
    }

    /**
     * @param Cart $cart
     *
     * @return array<int, CartItem>
     */
    protected function getCommonItemsFromCart($cart) {
        // todo replace $this
        return apply_filters('wdp_internal_cart_items_before_apply', $cart->getItems(), $this);
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     *
     */
    protected function addCommonItems($cart, $wcCart)
    {
        $cartContext = $cart->getContext();

        $items = $this->getCommonItemsFromCart($cart);

        $processedItemKeys = array();

        foreach ($items as $item) {
            /** have to clone! because of split items are having the same WC_Product object */
            $facade = clone $item->getWcItem();

            $productPrice = $item->getOriginalPrice();
            foreach ($item->getDiscounts() as $ruleId => $amounts) {
                $productPrice -= array_sum($amounts);
            }
            if ($cartContext->getOption('is_calculate_based_on_wc_precision')) {
                $productPrice = round($productPrice, wc_get_price_decimals());
            }

            $facade->setOriginalPrice($facade->getProduct()->get_price('edit'));
            $productPrice = $this->overrideCentsStrategy->maybeOverrideCentsForItem($productPrice, $item);

            $facade->setNewPrice($productPrice);
            $facade->setHistory($item->getHistory());
            $facade->setDiscounts($item->getDiscounts());

            $facade->setOriginalPriceWithoutTax($facade->getSubtotal() / $facade->getQty());
            $facade->setOriginalPriceTax($facade->getExactSubtotalTax() / $facade->getQty());
            $facade->setQty($item->getQty());

            if (in_array($facade->getKey(), $processedItemKeys)) {
                $originalCartItemKey = $facade->getKey();
                $facade->setOriginalKey($originalCartItemKey);

                $cart_item_key = $wcCart->generate_cart_id($facade->getProductId(), $facade->getVariationId(),
                    $facade->getVariation(), $facade->getCartItemData());

                if (isset($wcCart->cart_contents[$cart_item_key])) {
                    $alreadyProcessedItemFacade = new WcCartItemFacade($this->context,
                        $wcCart->cart_contents[$cart_item_key], $cart_item_key);
                    $alreadyProcessedItemFacade->setQty($alreadyProcessedItemFacade->getQty() + $facade->getQty());
                    $wcCart->cart_contents[$cart_item_key] = $alreadyProcessedItemFacade->getData();
                    continue;
                }

                $facade->setKey($cart_item_key);
            }

            $wcCart->cart_contents[$facade->getKey()] = $facade->getData();
            $processedItemKeys[]                      = $facade->getKey();
        }
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    public function applyTotals($cart, $wcCart)
    {
        $this->purgeAppliedCoupons($wcCart);
        $this->addOriginCoupons($cart, $wcCart);
        $this->addRuleTriggerCoupons($cart, $wcCart);

        $this->cartCouponsProcessor->refreshCoupons($cart);
        $this->cartCouponsProcessor->applyCoupons($wcCart);

        $this->cartFeeProcessor->refreshFees($cart);

        if ( ! $this->context->isDisableShippingCalculationDuringProcess()) {
            $this->shippingProcessor->purgeCalculatedPackagesInSession();
        }
        $this->shippingProcessor->refresh($cart);
    }

    /**
     * @param WC_Cart $wcCart
     */
    protected function purgeAppliedCoupons($wcCart)
    {
        $wcCart->applied_coupons = array();
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    protected function addOriginCoupons(&$cart, &$wcCart)
    {
        $wcCart->applied_coupons = array_merge($wcCart->applied_coupons, $cart->getOriginCoupons());
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    protected function addRuleTriggerCoupons(&$cart, &$wcCart)
    {
        $wcCart->applied_coupons = array_merge($wcCart->applied_coupons, $cart->getRuleTriggerCoupons());
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    protected function maybeRemoveOriginCoupons($cart, $wcCart)
    {
        if ($this->context->getOption('external_coupons_behavior') === 'disable_if_any_rule_applied') {
            $cart->removeAllOriginCoupon();
        } elseif ($this->context->getOption('external_coupons_behavior') === 'disable_if_any_of_cart_items_updated') {
            $is_price_changed = false;

            foreach ($wcCart->cart_contents as $cartKey => $wcCartItem) {
                $wrapper = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);
                foreach ($wrapper->getDiscounts() as $ruleId => $amounts) {
                    if (array_sum($amounts) > 0) {
                        $is_price_changed = true;
                        break;
                    }
                }
            }

            // todo change to "wdp_external_coupons_behavior_if_items_updated" later
            $is_price_changed = (bool)apply_filters('wdp_is_disable_external_coupons_if_items_updated',
                $is_price_changed, $this, $wcCart);

            if ($is_price_changed) {
                $cart->removeAllOriginCoupon();
            } else {
                $this->replaceWcNotice(
                    array(
                        'text' => __('Sorry, coupons are disabled for these products.',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'type' => 'error',
                    ),
                    array(
                        'text' => __('Coupon code applied successfully.', 'woocommerce'),
                        'type' => 'success',
                    )
                );
            }
        }
    }

    /**
     * @param array $needleNotice
     * @param array $newNotice
     */
    protected function replaceWcNotice($needleNotice, $newNotice)
    {
        if ( ! is_array($needleNotice) || ! is_array($newNotice)) {
            return;
        }

        $needleNotice = array(
            'type' => isset($needleNotice['type']) ? $needleNotice['type'] : null,
            'text' => isset($needleNotice['text']) ? $needleNotice['text'] : "",
        );

        $newNotice = array(
            'type' => isset($newNotice['type']) ? $newNotice['type'] : null,
            'text' => isset($newNotice['text']) ? $newNotice['text'] : "",
        );


        $newNotices = array();
        foreach (wc_get_notices() as $type => $notices) {
            if ( ! isset($newNotices[$type])) {
                $newNotices[$type] = array();
            }

            foreach ($notices as $loopNotice) {
                if ( ! empty($loopNotice['notice'])
                     && $needleNotice['text'] === $loopNotice['notice']
                     && ( ! $needleNotice['type'] || $needleNotice['type'] === $type)
                ) {
                    if ($newNotice['type'] === null) {
                        $newNotice['type'] = $type;
                    }

                    if ( ! isset($newNotices[$newNotice['type']])) {
                        $newNotices[$newNotice['type']] = array();
                    }

                    $newNotices[$newNotice['type']][] = array(
                        'notice' => $newNotice['text'],
                        'data'   => array(),
                    );

                    continue;
                } else {
                    $newNotices[$type][] = $loopNotice;
                }
            }
        }
        wc_set_notices($newNotices);
    }

    /**
     * @param Cart $cart
     */
    public function notifyAboutAddedFreeItems($cart)
    {
        $freeItems = $cart->getFreeItems();
        foreach ($freeItems as $freeItem) {
            $freeItemTmp = clone $freeItem;
            $giftedQty   = $freeItemTmp->qty - $freeItem->getQtyAlreadyInWcCart();
            if ($giftedQty > 0) {
                $this->addNoticeAddedFreeProduct($freeItem->getProduct(), $giftedQty);
            } elseif ($freeItemTmp->qty > 0 && $giftedQty < 0) {
                $this->addNoticeRemovedFreeProduct($freeItem->getProduct(), -$giftedQty);
            }
        }
    }

    protected function addNoticeAddedFreeProduct($product, $qty)
    {
        $template  = $this->context->getOption('message_template_after_add_free_product');
        $arguments = array(
            '{{qty}}'          => $qty,
            '{{product_name}}' => $product->get_name(),
        );
        $message   = str_replace(array_keys($arguments), array_values($arguments), $template);
        $type      = 'success';
        $data      = array('adp' => true);

        wc_add_notice($message, $type, $data);
    }

    protected function addNoticeRemovedFreeProduct($product, $qty)
    {
        $template  = __("Removed {{qty}} free {{product_name}}",
            'advanced-dynamic-pricing-for-woocommerce'); // todo replace with option?
        $arguments = array(
            '{{qty}}'          => $qty,
            '{{product_name}}' => $product->get_name(),
        );
        $message   = str_replace(array_keys($arguments), array_values($arguments), $template);
        $type      = 'success';
        $data      = array('adp' => true);

        wc_add_notice($message, $type, $data);
    }

    /**
     * @return CartCalculatorListener
     */
    public function getListener()
    {
        return $this->listener;
    }

    /**
     * @return WcNoFilterWorker
     */
    public function getWcNoFilterWorker()
    {
        return $this->wcNoFilterWorker;
    }

    /**
     * You can delete the item during \WC_Cart::set_quantity() if qty is set to 0.
     * This action triggers \WC_Cart::calculate_totals() and calls our cart processor.
     * After $this->eliminateClones() the hashes of the items may change and wc-form-handler will throw the error.
     * e.g. you are removing the 'locomotive' item and the first clone becomes 'loco', so the hash of the clone item is replaced.
     *
     * To prevent this, we double check for existence.
     *
     * @param bool $passedValidation
     * @param string $cartItemKey
     * @param array $values
     * @param int|float $quantity
     *
     * @return bool
     */
    public function filterCheckCartItemExistenceBeforeUpdate(
        $passedValidation,
        $cartItemKey,
        $values,
        $quantity
    ) {
        if ( ! isset(WC()->cart->cart_contents[$cartItemKey])) {
            $passedValidation = false;
        }

        return $passedValidation;
    }

    /**
     * @param WC_Product $product
     */
    protected function setProductPriceDependsOnPriceMode($product)
    {
        $price_mode = $this->context->getOption('discount_for_onsale');

        try {
            $reflection = new ReflectionClass($product);
            $property   = $reflection->getProperty('changes');
            $property->setAccessible(true);
            $changes = $property->getValue($product);
            unset($changes['price']);
            $property->setValue($product, $changes);
        } catch (ReflectionException $exception) {
            $property = null;
        }

        if ($product->is_on_sale('edit')) {
            if ('sale_price' === $price_mode || 'discount_sale' === $price_mode) {
                $price = $product->get_sale_price('edit');
            } else {
                $price = $product->get_regular_price('edit');
            }
        } else {
            $price = $product->get_price('edit');
        }

        $product->set_price($price);
    }

    /**
     * In case if index of the $wcCart->cart_contents element is not equal value by index 'key' of element
     *
     * Scheme of $wcCart->cart_contents
     *
     * [
     *   ['example_hash'] =>
     *      [
     *          'key' => 'example_hash_in_the_element'
     *          ...
     *      ]
     * ]
     *
     * So, sometimes 'example_hash' does not equal 'example_hash_in_the_element', but it should!
     * This method solves the problem.
     *
     * @param WC_Cart|null $wcCart
     */
    protected function syncCartItemHashes($wcCart)
    {
        if ( ! $wcCart || $wcCart->is_empty()) {
            return;
        }

        foreach ($wcCart->cart_contents as $cartItemHash => $cartItem) {
            if (isset($this->wcCart->cart_contents[$cartItemHash][WcCartItemFacade::KEY_KEY])) {
                $this->wcCart->cart_contents[$cartItemHash][WcCartItemFacade::KEY_KEY] = $cartItemHash;
            }
        }
    }
}
