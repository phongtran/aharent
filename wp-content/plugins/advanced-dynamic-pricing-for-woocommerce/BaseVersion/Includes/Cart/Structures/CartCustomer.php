<?php

namespace ADP\BaseVersion\Includes\Cart\Structures;

use ADP\BaseVersion\Includes\Cart\Structures\CartCustomer\RemovedFreeItems;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartCustomer
{
    /**
     * @var int|null
     */
    protected $id;

    /**
     * @var array
     */
    protected $billingAddress;

    /**
     * @var array
     */
    protected $shippingAddress;

    /**
     * @var string
     */
    protected $selectedPaymentMethod;

    /**
     * @var string[]
     */
    protected $selectedShippingMethods;

    /**
     * @var bool
     */
    protected $isVatExempt;

    /**
     * @var array
     */
    protected $roles;

    /**
     * @var RemovedFreeItems[]
     */
    protected $removedFreeItemsList;

    /**
     * @var array
     */
    protected $meta;

    /**
     * @param int $id
     */
    public function __construct($id = null)
    {
        $this->id = null;
        $this->setId($id);

        $this->billingAddress          = array();
        $this->shippingAddress         = array();
        $this->selectedPaymentMethod   = null;
        $this->selectedShippingMethods = array();
        $this->isVatExempt             = false;
        $this->removedFreeItemsList    = array();
        $this->meta                    = array();
    }

    /**
     * @param array $metaArray
     */
    public function setMetaData($metaArray)
    {
        if (is_array($metaArray)) {
            $this->meta = array_filter($metaArray, 'is_array');
        }
    }

    /**
     * @return array
     */
    public function getMeta()
    {
        return $this->meta;
    }

    /**
     * @param $key string
     *
     * @return mixed|null
     */
    public function getMetaValue($key)
    {
        return isset($this->meta[$key]) ? $this->meta[$key] : null;
    }

    /**
     * @param int|null $id
     */
    public function setId($id)
    {
        if (is_numeric($id) && intval($id) >= 0) {
            $this->id = intval($id);
        }
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function isGuest()
    {
        return $this->id === null || $this->id === 0;
    }

    /**
     * @param array $billingAddress
     */
    public function setBillingAddress($billingAddress)
    {
        $this->billingAddress = (array)$billingAddress;
    }

    /**
     * @return array
     */
    public function getBillingAddress()
    {
        return $this->billingAddress;
    }

    /**
     * @param array $shippingAddress
     */
    public function setShippingAddress($shippingAddress)
    {
        $this->shippingAddress = (array)$shippingAddress;
    }

    /**
     * @return array
     */
    public function getShippingAddress()
    {
        return $this->shippingAddress;
    }

    /**
     * @param array<int, string> $selectedShippingMethods
     */
    public function setSelectedShippingMethods($selectedShippingMethods)
    {
        $this->selectedShippingMethods = $selectedShippingMethods;
    }

    /**
     * @return array<int, string>
     */
    public function getSelectedShippingMethods()
    {
        return $this->selectedShippingMethods;
    }

    /**
     * @param string|null $selectedPaymentMethod
     */
    public function setSelectedPaymentMethod($selectedPaymentMethod)
    {
        $this->selectedPaymentMethod = $selectedPaymentMethod;
    }

    /**
     * @return string|null
     */
    public function getSelectedPaymentMethod()
    {
        return $this->selectedPaymentMethod;
    }

    /**
     * @param array<int, string> $roles
     */
    public function setRoles($roles)
    {
        $this->roles = (array)$roles;
    }

    /**
     * All non registered users have a dummy 'wdp_guest' role
     *
     * @return array<int, string>
     */
    public function getRoles()
    {
        return ! empty($this->roles) ? $this->roles : array('wdp_guest');
    }

    /**
     * @param bool $isVatExempt
     */
    public function setIsVatExempt($isVatExempt)
    {
        $this->isVatExempt = boolval($isVatExempt);
    }

    /**
     * @return bool
     */
    public function isVatExempt()
    {
        return $this->isVatExempt;
    }

    public function getShippingCountry()
    {
        return isset($this->shippingAddress['country']) ? $this->shippingAddress['country'] : "";
    }

    public function getShippingState()
    {
        return isset($this->shippingAddress['state']) ? $this->shippingAddress['state'] : "";
    }

    public function getShippingPostCode()
    {
        return isset($this->shippingAddress['postcode']) ? $this->shippingAddress['postcode'] : "";
    }

    public function getShippingCity()
    {
        return isset($this->shippingAddress['city']) ? $this->shippingAddress['city'] : "";
    }

    public function getBillingCountry()
    {
        return isset($this->billingAddress['country']) ? $this->billingAddress['country'] : "";
    }

    public function getBillingState()
    {
        return isset($this->billingAddress['state']) ? $this->billingAddress['state'] : "";
    }

    public function getBillingPostCode()
    {
        return isset($this->billingAddress['postcode']) ? $this->billingAddress['postcode'] : "";
    }

    public function getBillingCity()
    {
        return isset($this->billingAddress['city']) ? $this->billingAddress['city'] : "";
    }

    /**
     * @return array<int, RemovedFreeItems>
     */
    public function getRemovedFreeItemsList()
    {
        return $this->removedFreeItemsList;
    }

    /**
     * @param array<int, RemovedFreeItems> $removedFreeItemsList
     */
    public function setRemovedFreeItemsList($removedFreeItemsList)
    {
        if ( ! is_array($removedFreeItemsList)) {
            return;
        }

        $this->removedFreeItemsList = array();
        foreach ($removedFreeItemsList as $item) {
            if ($item instanceof RemovedFreeItems) {
                $this->removedFreeItemsList[] = $item;
            }
        }
    }

    /**
     * @param string $giftHash
     *
     * @return RemovedFreeItems
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

        if ($result === null) {
            $result                       = new RemovedFreeItems($giftHash);
            $this->removedFreeItemsList[] = $result;
        }

        return $result;
    }
}
