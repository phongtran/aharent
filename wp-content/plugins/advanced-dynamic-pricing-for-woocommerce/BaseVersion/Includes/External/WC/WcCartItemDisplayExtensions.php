<?php


namespace ADP\BaseVersion\Includes\External\WC;


use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Cmp\WcSubscriptionsCmp;

class WcCartItemDisplayExtensions
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @var WcSubscriptionsCmp
     */
    protected $subscriptionCmp;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;

        $this->priceFunctions  = new PriceFunctions($context);
        $this->subscriptionCmp = new WcSubscriptionsCmp($context);
    }

    public function register()
    {
        add_filter('woocommerce_cart_item_price', array($this, 'wcCartItemPrice'), 10, 3);
        add_filter('woocommerce_cart_item_subtotal', array($this, 'wcCartItemSubtotal'), 10, 3);
    }

    /**
     * @param string $price formatted price after wc_price()
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    public function wcCartItemPrice($price, $cartItem, $cartItemKey)
    {
        if ($this->context->getOption('show_striked_prices')) {
            $price = $this->wcMainCartItemPrice($price, $cartItem, $cartItemKey);
        }

        return $price;
    }

    /**
     * @param string $price formatted price after wc_price()
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    public function wcCartItemSubtotal($price, $cartItem, $cartItemKey)
    {
        if ($this->context->getOption('show_striked_prices')) {
            $price = $this->wcMainCartItemSubtotal($price, $cartItem, $cartItemKey);
        }

        return $price;
    }

    /**
     * @param string $price formatted price after wc_price()
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    protected function wcMainCartItemPrice($price, $cartItem, $cartItemKey)
    {
        if ($this->subscriptionCmp->isSetFreeTrial($cartItem)) {
            return $price;
        }

        $context = $this->context;
        $facade  = new WcCartItemFacade($context, $cartItem, $cartItemKey);

        $subsCmp = new WcSubscriptionsCmp($context);

        $newPriceHtml = $price;

        if ('incl' === $context->getTaxDisplayCartMode()) {
            $oldPrice = $facade->getOriginalPriceWithoutTax() + $facade->getOriginalPriceTax();
            $newPrice = ($facade->getSubtotal() + $facade->getExactSubtotalTax()) / $facade->getQty();
        } else {
            $oldPrice = $facade->getOriginalPriceWithoutTax();
            $newPrice = $facade->getSubtotal() / $facade->getQty();
        }

        $newPrice = apply_filters('wdp_cart_item_new_price', $newPrice, $cartItem, $cartItemKey);
        $oldPrice = apply_filters('wdp_cart_item_initial_price', $oldPrice, $cartItem, $cartItemKey);

        if (is_numeric($newPrice) && is_numeric($oldPrice)) {
            $oldPriceRounded = round($oldPrice, $this->context->priceSettings->getDecimals());
            $newPriceRounded = round($newPrice, $this->context->priceSettings->getDecimals());

            if ($newPriceRounded < $oldPriceRounded) {
                $priceHtml = $this->priceFunctions->formatSalePrice($oldPrice, $newPrice);

                if ($subsCmp->isSubscriptionProduct($facade->getProduct())) {
                    $priceHtml = $subsCmp->maybeAddSubsTail($facade->getProduct(), $priceHtml);
                }
            } elseif ($newPriceRounded === $oldPriceRounded) {
                $priceHtml = $this->priceFunctions->format($oldPrice);

                if ($subsCmp->isSubscriptionProduct($facade->getProduct())) {
                    $priceHtml = $subsCmp->maybeAddSubsTail($facade->getProduct(), $priceHtml);
                }
            } else {
                $priceHtml = $newPriceHtml;
            }
        } else {
            $priceHtml = $newPriceHtml;
        }

        return $priceHtml;
    }

    /**
     * @param string $price formatted price after wc_price()
     * @param array $cartItem
     * @param string $cartItemKey
     *
     * @return string
     */
    protected function wcMainCartItemSubtotal($price, $cartItem, $cartItemKey)
    {
        if ($this->subscriptionCmp->isSetFreeTrial($cartItem)) {
            return $price;
        }

        $context = $this->context;
        $facade  = new WcCartItemFacade($context, $cartItem, $cartItemKey);

        $subsCmp = new WcSubscriptionsCmp($context);

        $newPriceHtml = $price;

        $displayPricesIncludingTax = 'incl' === $context->getTaxDisplayCartMode();

        if ($displayPricesIncludingTax) {
            $oldPrice = $facade->getOriginalPriceWithoutTax() + $facade->getOriginalPriceTax();
            $newPrice = ($facade->getSubtotal() + $facade->getExactSubtotalTax()) / $facade->getQty();
        } else {
            $oldPrice = $facade->getOriginalPriceWithoutTax();
            $newPrice = $facade->getSubtotal() / $facade->getQty();
        }

        $newPrice *= $facade->getQty();
        $oldPrice *= $facade->getQty();

        $newPrice = apply_filters('wdp_cart_item_subtotal', $newPrice, $cartItem, $cartItemKey);
        $oldPrice = apply_filters('wdp_cart_item_initial_subtotal', $oldPrice, $cartItem, $cartItemKey);

        if (is_numeric($newPrice) && is_numeric($oldPrice)) {
            $oldPriceRounded = round($oldPrice, $this->context->priceSettings->getDecimals());
            $newPriceRounded = round($newPrice, $this->context->priceSettings->getDecimals());

            if ($newPriceRounded < $oldPriceRounded) {
                $priceHtml = $this->priceFunctions->formatSalePrice($oldPrice, $newPrice);

                if ($displayPricesIncludingTax) {
                    if ( ! $context->getIsPricesIncludeTax() && $facade->getExactSubtotalTax() > 0) {
                        $priceHtml .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
                    }
                } else {
                    if ($context->getIsPricesIncludeTax() && $facade->getExactSubtotalTax() > 0) {
                        $priceHtml .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
                    }
                }

                if ($subsCmp->isSubscriptionProduct($facade->getProduct())) {
                    $priceHtml = $subsCmp->maybeAddSubsTail($facade->getProduct(), $priceHtml);
                }
            } elseif ($newPriceRounded === $oldPriceRounded) {
                $priceHtml = $this->priceFunctions->format($oldPrice);

                if ($subsCmp->isSubscriptionProduct($facade->getProduct())) {
                    $priceHtml = $subsCmp->maybeAddSubsTail($facade->getProduct(), $priceHtml);
                }
            } else {
                $priceHtml = $newPriceHtml;
            }
        } else {
            $priceHtml = $newPriceHtml;
        }

        return $priceHtml;
    }
}
