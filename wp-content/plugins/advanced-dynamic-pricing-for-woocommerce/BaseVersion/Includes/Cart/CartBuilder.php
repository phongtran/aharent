<?php

namespace ADP\BaseVersion\Includes\Cart;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\CartContext;
use ADP\BaseVersion\Includes\Cart\Structures\CartCustomer;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Cmp\SomewhereWarmBundlesCmp;
use ADP\BaseVersion\Includes\External\Cmp\SomewhereWarmCompositesCmp;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\External\WC\WcCouponFacade;
use ADP\BaseVersion\Includes\External\WC\WcCustomerConverter;
use ADP\BaseVersion\Includes\External\WC\WcCustomerSessionFacade;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use ADP\Factory;
use WC_Cart;
use WC_Coupon;
use WC_Customer;
use WC_Session;

class CartBuilder
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var SomewhereWarmBundlesCmp
     */
    protected $bundlesCmp;

    /**
     * @var SomewhereWarmCompositesCmp
     */
    protected $compositeCmp;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context      = $context;
        $this->bundlesCmp   = new SomewhereWarmBundlesCmp($context);
        $this->compositeCmp = new SomewhereWarmCompositesCmp($context);
    }

    /**
     * @param WC_Customer|null $wcCustomer
     * @param \WC_Session_Handler|null $wcSession
     *
     * @return Cart
     */
    public function create($wcCustomer, $wcSession)
    {
        $context = $this->context;
        /** @var WcCustomerConverter $converter */
        $converter = Factory::get("External_WC_WcCustomerConverter", $context);
        $customer  = $converter->convertFromWcCustomer($wcCustomer, $wcSession);
        $userMeta = get_user_meta($customer->getId());
        $customer->setMetaData($userMeta ? $userMeta : array());

        $cartContext = new CartContext($customer, $context);
        /** @var WcCustomerSessionFacade $wcSessionFacade */
        $wcSessionFacade = Factory::get("External_WC_WcCustomerSessionFacade", $wcSession);
        $cartContext->withSession($wcSessionFacade);

        /** @var Cart $cart */
        $cart = Factory::get('Cart_Structures_Cart', $cartContext);

        return $cart;
    }

    /**
     *
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    public function populateCart($cart, $wcCart)
    {
        $pos = 0;

        foreach ($wcCart->cart_contents as $cartKey => $wcCartItem) {
            $wrapper = new WcCartItemFacade($this->context, $wcCartItem, $cartKey);

            if ($wrapper->isClone()) {
                continue;
            }

            $item = $wrapper->createItem();
            if ($item) {
                $item->setPos($pos);

                if ($this->bundlesCmp->isBundled($wrapper)) {
                    $item->addAttr($item::ATTR_IMMUTABLE);
                }

                if ($this->compositeCmp->isCompositeItem($wrapper)) {
                    if ($this->compositeCmp->isAllowToProcessPricedIndividuallyItems()) {
                        if ($this->compositeCmp->isCompositeItemNotPricedIndividually($wrapper, $wcCart)) {
                            $item->addAttr($item::ATTR_IMMUTABLE);
                        }
                    } else {
                        $item->addAttr($item::ATTR_IMMUTABLE);
                    }
                }

                $cart->addToCart($item);
            }

            $pos++;
        }

        /** Save applied coupons. It needs for detect free (gifts) products during current calculation and notify about them. */
        $this->addOriginCoupons($cart, $wcCart);
    }

    /**
     * @param Cart $cart
     * @param WC_Cart $wcCart
     */
    public function addOriginCoupons($cart, $wcCart)
    {
        if ( ! ($wcCart instanceof WC_Cart)) {
            return;
        }

        $adpCoupons = (new WcTotalsFacade($this->context, $wcCart))->getAdpCoupons();

        foreach ($wcCart->get_coupons() as $coupon) {
            /** @var $coupon WC_Coupon */
            $code = $coupon->get_code('edit');

            if ($coupon->is_valid()) {
                if ($coupon->get_discount_type('edit') === WcCouponFacade::TYPE_ADP_RULE_TRIGGER) {
                    $cart->addRuleTriggerCoupon($code);
                } elseif ( ! $coupon->get_meta('adp', true) && ! in_array($code, $adpCoupons)) {
                    $cart->addOriginCoupon($coupon->get_code('edit'));
                }
            }
        }
    }
}
