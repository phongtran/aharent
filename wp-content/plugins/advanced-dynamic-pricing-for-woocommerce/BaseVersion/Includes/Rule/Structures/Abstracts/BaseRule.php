<?php

namespace ADP\BaseVersion\Includes\Rule\Structures\Abstracts;

use ADP\BaseVersion\Includes\Currency;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleCondition;
use ADP\BaseVersion\Includes\Rule\Interfaces\RuleLimit;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\BaseVersion\Includes\Rule\Structures\Gift;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class BaseRule
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $priority;

    /**
     * @var bool
     */
    protected $enabled;

    /**
     * @var array<int,RuleCondition>
     */
    protected $conditions;

    /**
     * @var array<int,RuleLimit>
     */
    protected $limits = array();

    /**
     * @var array<int, CartAdjustment>
     */
    protected $cartAdjustments = array();

    // additional
    protected $conditionsRelationship;

    /**
     * @var Gift[]
     */
    protected $gifts;

    /**
     * @var int
     */
    protected $giftLimit;

    /**
     * @var Currency|null
     */
    protected $currency;

    /**
     * @var string
     */
    protected $activationCouponCode;

    /**
     * @var string
     */
    protected $dbHash;

    public function __construct()
    {
        $this->id                   = 0;
        $this->enabled              = false;
        $this->cartAdjustments      = array();
        $this->conditions           = array();
        $this->limits               = array();
        $this->gifts                = array();
        $this->giftLimit            = INF;
        $this->currency             = null;
        $this->activationCouponCode = null;
    }

    public function __clone()
    {
        $this->conditions = array_map(function ($item) {
            return clone $item;
        }, $this->conditions);

        $this->limits = array_map(function ($item) {
            return clone $item;
        }, $this->limits);

        $this->cartAdjustments = array_map(function ($item) {
            return clone $item;
        }, $this->cartAdjustments);

        $this->gifts = array_map(function ($item) {
            return clone $item;
        }, $this->gifts);
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function activate()
    {
        $this->enabled = true;
    }

    public function deactivate()
    {
        $this->enabled = false;
    }

    public function setEnabled($enabled)
    {
        $this->enabled = $enabled === "on";
    }

    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param RuleCondition $condition
     */
    public function addCondition($condition)
    {
        if ($condition instanceof RuleCondition) {
            $this->conditions[] = $condition;
        }
    }

    /**
     * @param array<int,RuleCondition> $conditions
     */
    public function setConditions($conditions)
    {
        $this->conditions = array();

        foreach ($conditions as $condition) {
            $this->addCondition($condition);
        }
    }

    /**
     * @return array<int,RuleCondition>
     */
    public function getConditions()
    {
        return $this->conditions;
    }

    /**
     * @param CartAdjustment $cartAdjustment
     */
    public function addCartAdjustment($cartAdjustment)
    {
        if ($cartAdjustment instanceof CartAdjustment) {
            $this->cartAdjustments[] = $cartAdjustment;
        }
    }

    /**
     * @param array<int,CartAdjustment> $cartAdjustments
     */
    public function setCartAdjustments($cartAdjustments)
    {
        $this->cartAdjustments = array();

        foreach ($cartAdjustments as $cartAdjustment) {
            $this->addCartAdjustment($cartAdjustment);
        }
    }

    /**
     * @return array<int,CartAdjustment>
     */
    public function getCartAdjustments()
    {
        return $this->cartAdjustments;
    }

    /**
     * @param RuleLimit $limit
     */
    public function addLimit($limit)
    {
        if ($limit instanceof RuleLimit) {
            $this->limits[] = $limit;
        }
    }

    /**
     * @param array<int,RuleLimit> $limits
     */
    public function setLimits($limits)
    {
        $this->limits = array();

        foreach ($limits as $limit) {
            $this->addLimit($limit);
        }
    }

    /**
     * @return array<int,RuleLimit>
     */
    public function getLimits()
    {
        return $this->limits;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setConditionsRelationship($rel)
    {
        $this->conditionsRelationship = $rel;
    }

    public function getConditionsRelationship()
    {
        return $this->conditionsRelationship;
    }

    /**
     * @param int|string $priority
     */
    public function setPriority($priority)
    {
        $this->priority = (int)$priority;
    }

    /**
     * @return int
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param array<int,Gift> $gifts
     */
    public function setGifts($gifts)
    {
        $filteredGifts = array();
        foreach ($gifts as $gift) {
            if ($gift instanceof Gift) {
                $filteredGifts[] = $gift;
            }
        }
        $this->gifts = $filteredGifts;
    }

    /**
     * @return array<int,Gift>
     */
    public function getGifts()
    {
        return $this->gifts;
    }

    /**
     * @return int
     */
    public function getGiftLimit()
    {
        return $this->giftLimit;
    }

    /**
     * @param int $giftLimit
     */
    public function setGiftLimit($giftLimit)
    {
        $this->giftLimit = $giftLimit;
    }

    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = addslashes($title);
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency($currency)
    {
        if ($currency instanceof Currency) {
            $this->currency = $currency;
        }
    }

    /**
     * @return Currency|null
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param string $code
     */
    public function setActivationCouponCode($code)
    {
        if (is_string($code) && ($code = strval($code))) {
            $this->activationCouponCode = wc_format_coupon_code($code);
        }
    }

    /**
     * @return string|null
     */
    public function getActivationCouponCode()
    {
        return $this->activationCouponCode;
    }

    /**
     * @param string $hash
     */
    public function setHash($hash)
    {
        if (is_string($hash)) {
            $this->dbHash = strval($hash);
        }
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->dbHash !== null ? $this->dbHash : "";
    }
}
