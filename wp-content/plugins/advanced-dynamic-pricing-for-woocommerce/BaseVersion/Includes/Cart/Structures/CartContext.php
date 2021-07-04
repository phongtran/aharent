<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\WC\WcCustomerSessionFacade;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartContext
{
    /**
     * @var CartCustomer
     */
    private $customer;

    /**
     * @var array
     */
    private $environment;

    /**
     * @var Context
     */
    private $context;

    /**
     * @var WcCustomerSessionFacade
     */
    protected $sessionFacade;

    /**
     * @param CartCustomer $customer
     * @param Context $context
     */
    public function __construct(CartCustomer $customer, Context $context)
    {
        $this->customer = $customer;
        $this->context  = $context;

        /** @var WcCustomerSessionFacade $wcSessionFacade */
        $this->sessionFacade = Factory::get("External_WC_WcCustomerSessionFacade", null);

        $this->environment = array(
            'timestamp'           => current_time('timestamp'),
            'prices_includes_tax' => $context->getIsPricesIncludeTax(),
            'tab_enabled'         => $context->getIsTaxEnabled(),
            'tax_display_shop'    => $context->getTaxDisplayShopMode(),
        );
    }

    /**
     * @param string $format
     *
     * @return string
     */
    public function datetime($format)
    {
        return date($format, $this->environment['timestamp']);
    }

    /**
     * @return Context
     */
    public function getGlobalContext(): Context
    {
        return $this->context;
    }

    /**
     * @return CartCustomer
     */
    public function getCustomer(): CartCustomer
    {
        return $this->customer;
    }

    /**
     * @return int
     */
    public function time()
    {
        return $this->environment['timestamp'];
    }

    public function getPriceMode()
    {
        return $this->getOption('discount_for_onsale');
    }

    public function isCombineMultipleDiscounts()
    {
        return $this->getOption('combine_discounts');
    }

    public function isCombineMultipleFees()
    {
        return $this->getOption('combine_fees');
    }

    public function getCustomerId()
    {
        return $this->customer->getId();
    }

    public function getCountOfRuleUsages($ruleId)
    {
        return Database::getCountOfRuleUsages($ruleId);
    }

    public function getCountOfRuleUsagesPerCustomer($ruleId, $customerId)
    {
        return Database::getCountOfRuleUsagesPerCustomer($ruleId, $customerId);
    }

    public function isTaxEnabled()
    {
        return isset($this->environment['tab_enabled']) ? $this->environment['tab_enabled'] : false;
    }

    public function isPricesIncludesTax()
    {
        return isset($this->environment['prices_includes_tax']) ? $this->environment['prices_includes_tax'] : false;
    }

    public function getTaxDisplayShop()
    {
        return isset($this->environment['tax_display_shop']) ? $this->environment['tax_display_shop'] : '';
    }

    public function getOption($key, $default = false)
    {
        return $this->context->getOption($key);
    }

    /**
     * @param WcCustomerSessionFacade $sessionFacade
     */
    public function withSession(WcCustomerSessionFacade $sessionFacade)
    {
        if ($sessionFacade instanceof WcCustomerSessionFacade) {
            $this->sessionFacade = $sessionFacade;
        }
    }

    /**
     * @return WcCustomerSessionFacade
     */
    public function getSession(): WcCustomerSessionFacade
    {
        return $this->sessionFacade;
    }
}
