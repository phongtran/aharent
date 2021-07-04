<?php

namespace ADP\BaseVersion\Includes\External\WC;

use ADP\BaseVersion\Includes\Cart\Structures\CartCustomer;
use ADP\BaseVersion\Includes\Context;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WcCustomerConverter
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * @param \WC_Customer|null $wcCustomer
     * @param \WC_Session_Handler|null $wcSession
     *
     * @return CartCustomer
     */
    public function convertFromWcCustomer($wcCustomer, $wcSession = null)
    {
        $context = $this->context;
        /** @var CartCustomer $customer */
        $customer = Factory::get("Cart_Structures_CartCustomer");

        if ( ! is_null($wcCustomer)) {
            $customer->setId($wcCustomer->get_id());
            $customer->setBillingAddress($wcCustomer->get_billing(''));
            $customer->setShippingAddress($wcCustomer->get_shipping(''));
            $customer->setIsVatExempt($wcCustomer->get_is_vat_exempt());
        }

        /** @var WcCustomerSessionFacade $wcSessionFacade */
        $wcSessionFacade = Factory::get("External_WC_WcCustomerSessionFacade", $wcSession);
        if ($wcSessionFacade->isValid()) {
            if ($context->is($context::WC_CHECKOUT_PAGE)) {
                $customer->setSelectedPaymentMethod($wcSessionFacade->getChosenPaymentMethod());
            }
            if ($context->is($context::WC_CHECKOUT_PAGE) || $context->is($context::WC_CART_PAGE) || ! $context->isCatalog()) {
                $customer->setSelectedShippingMethods($wcSessionFacade->getChosenShippingMethods());
            }

            $customer->setRemovedFreeItemsList($wcSessionFacade->getRemovedFreeItemsList());
        }

        $wpUser = new \WP_User($customer->getId());
        $customer->setRoles($wpUser->roles);

        return $customer;
    }

    /**
     * @param CartCustomer $customer
     *
     * @return \WC_Customer
     */
    public function convertToWcCustomer(CartCustomer $customer): \WC_Customer
    {
        $wcCustomer = new \WC_Customer();

        $wcCustomer->set_id($customer->getId());

        $wcCustomer->set_billing_country($customer->getBillingCountry());
        $wcCustomer->set_billing_state($customer->getBillingState());
        $wcCustomer->set_billing_postcode($customer->getBillingPostCode());
        $wcCustomer->set_billing_city($customer->getBillingCity());

        $wcCustomer->set_shipping_country($customer->getShippingCountry());
        $wcCustomer->set_shipping_state($customer->getShippingState());
        $wcCustomer->set_shipping_postcode($customer->getShippingPostCode());
        $wcCustomer->set_shipping_city($customer->getShippingCity());

        $wcCustomer->set_is_vat_exempt($customer->isVatExempt());

        return $wcCustomer;
    }

}
