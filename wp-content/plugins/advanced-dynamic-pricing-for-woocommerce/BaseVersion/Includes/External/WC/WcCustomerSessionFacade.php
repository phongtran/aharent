<?php

namespace ADP\BaseVersion\Includes\External\WC;

use ADP\BaseVersion\Includes\Cart\Structures\CartCustomer;
use ADP\BaseVersion\Includes\Cart\Structures\CartCustomer\RemovedFreeItems;
use WC_Session_Handler;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WcCustomerSessionFacade
{
    const ADP_SESSION_KEY = 'adp';

    const WC_CHOSEN_PAYMENT_METHOD_KEY = 'chosen_payment_method';
    const WC_CHOSEN_SHIPPING_METHODS_KEY = 'chosen_shipping_methods';

    const ADP_KEY_REMOVED_FREE_ITEMS_LIST = 'removed_free_items_list';

    /**
     * @var WC_Session_Handler
     */
    protected $wcCustomerSession;

    /**
     * @var string
     */
    protected $chosenPaymentMethod;

    /**
     * @var array<int,string>
     */
    protected $chosenShippingMethods;

    /**
     * @var array<int,RemovedFreeItems>
     */
    protected $removedFreeItemsList;

    /**
     * @param WC_Session_Handler|null $wcCustomerSession
     */
    public function __construct($wcCustomerSession)
    {
        if ($wcCustomerSession instanceof WC_Session_Handler) {
            $this->wcCustomerSession = $wcCustomerSession;
        }

        $this->chosenPaymentMethod   = "";
        $this->chosenShippingMethods = array();
        $this->initAdpProps();

        $this->load($this->wcCustomerSession);
    }

    /**
     * @param CartCustomer $customer
     */
    public function fetchPropsFromCustomer($customer)
    {
        if ( ! $this->isValid()) {
            return;
        }

        $this->setRemovedFreeItemsList($customer->getRemovedFreeItemsList());
    }

    /**
     * @param WC_Session_Handler|null $wcCustomerSession
     */
    protected function load($wcCustomerSession)
    {
        if ( ! $wcCustomerSession instanceof WC_Session_Handler) {
            return;
        }

        $this->chosenPaymentMethod   = $wcCustomerSession->get(self::WC_CHOSEN_PAYMENT_METHOD_KEY, '');
        $this->chosenShippingMethods = $wcCustomerSession->get(self::WC_CHOSEN_SHIPPING_METHODS_KEY, array());
        $this->loadAdpProps($wcCustomerSession->get(self::ADP_SESSION_KEY, array()));
    }

    /**
     * @return string
     */
    public function getChosenPaymentMethod()
    {
        return $this->chosenPaymentMethod;
    }

    /**
     * @return array<int,string>
     */
    public function getChosenShippingMethods()
    {
        return $this->chosenShippingMethods;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->wcCustomerSession instanceof WC_Session_Handler;
    }

    /**
     * @return bool
     */
    public function pushAll()
    {
        if ( ! $this->isValid()) {
            return false;
        }

        $this->wcCustomerSession->set(self::WC_CHOSEN_PAYMENT_METHOD_KEY, $this->chosenPaymentMethod);
        $this->wcCustomerSession->set(self::WC_CHOSEN_SHIPPING_METHODS_KEY, $this->chosenShippingMethods);
        $this->wcCustomerSession->set(self::ADP_SESSION_KEY, $this->prepareAdpPropsToPush());

        return true;
    }

    /**
     * @return bool
     */
    public function push()
    {
        if ( ! $this->isValid()) {
            return false;
        }

        $this->wcCustomerSession->set(self::ADP_SESSION_KEY, $this->prepareAdpPropsToPush());

        return true;
    }

    /**
     * @return array<int,RemovedFreeItems>
     */
    public function getRemovedFreeItemsList()
    {
        return $this->removedFreeItemsList;
    }

    /**
     * @param array<int,RemovedFreeItems> $removedFreeItemsList
     */
    public function setRemovedFreeItemsList($removedFreeItemsList)
    {
        $this->removedFreeItemsList = $removedFreeItemsList;
    }

    /**
     * @param string $giftHash
     *
     * @return RemovedFreeItems|null
     */
    public function getRemovedFreeItems($giftHash)
    {
        $result = null;

        foreach ($this->removedFreeItemsList as $removedFreeItems) {
            if ($removedFreeItems->getGiftHash() === $giftHash) {
                $result = $removedFreeItems;
                break;
            }
        }

        return $result;
    }

    protected function initAdpProps()
    {
        $this->removedFreeItemsList = array();
    }

    /**
     * @param array $adpData
     */
    protected function loadAdpProps($adpData)
    {
        if (isset($adpData[self::ADP_KEY_REMOVED_FREE_ITEMS_LIST])) {
            $this->removedFreeItemsList = $adpData[self::ADP_KEY_REMOVED_FREE_ITEMS_LIST];
        }
    }

    /**
     * @return array
     */
    protected function prepareAdpPropsToPush()
    {
        return array(
            self::ADP_KEY_REMOVED_FREE_ITEMS_LIST => $this->removedFreeItemsList,
        );
    }
}
