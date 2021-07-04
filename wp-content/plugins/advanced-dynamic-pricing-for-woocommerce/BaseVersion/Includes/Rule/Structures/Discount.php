<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

use ADP\BaseVersion\Includes\Context;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Discount
{
    const TYPE_FREE = 'free';
    const TYPE_PERCENTAGE = 'percentage';
    const TYPE_AMOUNT = 'fixed_amount';
    const TYPE_FIXED_VALUE = 'fixed_value';

    const AVAILABLE_TYPES = array(
        self::TYPE_PERCENTAGE,
        self::TYPE_AMOUNT,
        self::TYPE_FIXED_VALUE,
    );

    /**
     * @var string
     */
    protected $type;

    /**
     * @var float
     */
    protected $value;

    /**
     * @var string
     */
    protected $currencyCode;

    /**
     * Discount constructor.
     *
     * @param Context $context
     * @param string $type
     * @param float|string $value
     */
    public function __construct($context, $type, $value)
    {
        if ( ! in_array($type, self::AVAILABLE_TYPES)) {
            $context->handleError(new Exception(sprintf("Discount type '%s' not supported", $type)));
        }

        $this->type         = $type;
        $this->value        = floatval($value);
        $this->currencyCode = $context->getCurrencyCode();
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    /**
     * @param float $value
     */
    public function setValue($value)
    {
        $this->value = floatval($value);
    }

    /**
     * @param string $currencyCode
     */
    public function setCurrencyCode($currencyCode)
    {
        $this->currencyCode = $currencyCode;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return float
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @return string
     */
    public function getCurrencyCode()
    {
        return $this->currencyCode;
    }
}
