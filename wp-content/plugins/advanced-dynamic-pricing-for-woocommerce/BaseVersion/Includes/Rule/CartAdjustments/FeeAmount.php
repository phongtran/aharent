<?php

namespace ADP\BaseVersion\Includes\Rule\CartAdjustments;

use ADP\BaseVersion\Includes\Cart\Structures\Cart;
use ADP\BaseVersion\Includes\Cart\Structures\Fee;
use ADP\BaseVersion\Includes\Rule\CartAdjustmentsLoader;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments\FeeCartAdj;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class FeeAmount extends AbstractCartAdjustment implements FeeCartAdj, CartAdjustment
{
    /**
     * @var float
     */
    protected $feeValue;

    /**
     * @var string
     */
    protected $feeName;

    /**
     * @var string
     */
    protected $feeTaxClass;

    public static function getType()
    {
        return 'fee__amount';
    }

    public static function getLabel()
    {
        return __('Fixed fee, once', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public static function getTemplatePath()
    {
        return WC_ADP_PLUGIN_VIEWS_PATH . 'cart_adjustments/fee.php';
    }

    public static function getGroup()
    {
        return CartAdjustmentsLoader::GROUP_FEE;
    }

    public function __construct()
    {
        $this->amountIndexes = array('feeValue');
    }

    /**
     * @param float $feeValue
     */
    public function setFeeValue($feeValue)
    {
        $this->feeValue = $feeValue;
    }

    /**
     * @param string $feeName
     */
    public function setFeeName($feeName)
    {
        $this->feeName = $feeName;
    }

    /**
     * @param string $taxClass
     */
    public function setFeeTaxClass($taxClass)
    {
        $this->feeTaxClass = $taxClass;
    }

    public function getFeeValue()
    {
        return $this->feeValue;
    }

    public function getFeeName()
    {
        return $this->feeName;
    }

    public function getFeeTaxClass()
    {
        return $this->feeTaxClass;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return isset($this->feeValue) or isset($this->feeName) or isset($this->feeTaxClass);
    }

    public function applyToCart($rule, $cart)
    {
        $context = $cart->getContext()->getGlobalContext();
        $cart->addFee(new Fee($context, Fee::TYPE_FIXED_VALUE, $this->feeName, $this->feeValue, $this->feeTaxClass,
            $rule->getId()));
    }
}
