<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustments;
use ADP\BaseVersion\Includes\Rule\Interfaces\CartAdjustment;
use ADP\Factory;
use ADP\ProVersion\Includes\Rule\CartAdjustments\DiscountPercentage;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CartAdjustmentsLoader
{
    const KEY = 'cart_adjustments';

    const LIST_TYPE_KEY = 'type';
    const LIST_LABEL_KEY = 'label';
    const LIST_TEMPLATE_PATH_KEY = 'path';

    const GROUP_DISCOUNT = 'discount';
    const GROUP_FEE = 'fee';
    const GROUP_SHIPPING = 'shipping';

    /**
     * @var array
     */
    protected $groups = array();

    /**
     * @var string[]
     */
    protected $items = array();

    public function __construct()
    {
        $this->initGroups();

        foreach (Factory::getClassNames('Rule_CartAdjustments') as $className) {
            /**
             * @var $className CartAdjustment
             */
            $this->items[$className::getType()] = $className;
        }
    }

    protected function initGroups()
    {
        $this->groups[self::GROUP_DISCOUNT] = __('Discount', 'advanced-dynamic-pricing-for-woocommerce');
        $this->groups[self::GROUP_FEE]      = __('Fee', 'advanced-dynamic-pricing-for-woocommerce');
        $this->groups[self::GROUP_SHIPPING] = __('Shipping', 'advanced-dynamic-pricing-for-woocommerce');
    }

    /**
     * @param array $data
     *
     * @return CartAdjustment
     * @throws Exception
     */
    public function build($data)
    {
        if (empty($data['type'])) {
            throw new Exception('Missing cart adjustment type');
        }

        $adj = $this->create($data['type']);

        if ($adj instanceof CartAdjustments\CouponCartAdj) {
            $adj->setCouponValue($data['options'][$adj::COUPON_VALUE_KEY]);
            $adj->setCouponCode($data['options'][$adj::COUPON_CODE_KEY]);
        }
        if ($adj instanceof CartAdjustments\FeeCartAdj) {
            $adj->setFeeValue($data['options'][$adj::FEE_VALUE_KEY]);
            $adj->setFeeName($data['options'][$adj::FEE_NAME_KEY]);
            $adj->setFeeTaxClass($data['options'][$adj::FEE_TAX_CLASS_KEY]);
        }
        if ($adj instanceof CartAdjustments\ShippingCartAdj) {
            $adj->setShippingCartAdjValue($data['options'][$adj::SHIPPING_CARTADJ_VALUE]);
        }
        if ($adj instanceof CartAdjustments\ShippingMethodCartAdj) {
            $adj->setShippingCartAdjMethod($data['options'][$adj::SHIPPING_CARTADJ_METHOD]);
        }
        if ($adj instanceof DiscountPercentage) {
            if (isset($data['options'][$adj::COUPON_MAX_DISCOUNT])) {
                $adj->setCouponMaxDiscount($data['options'][$adj::COUPON_MAX_DISCOUNT]);
            }
        }

        if ($adj->isValid()) {
            return $adj;
        } else {
            throw new Exception('Wrong cart adjustment');
        }
    }

    /**
     * @param string $type
     *
     * @return CartAdjustment
     * @throws Exception
     */
    public function create($type)
    {
        if (isset($this->items[$type])) {
            $className = $this->items[$type];

            return new $className($type);
        } else {
            throw new Exception('Wrong cart adjustment');
        }
    }

    public function getAsList()
    {
        $list = array();

        foreach ($this->items as $type => $className) {
            /**
             * @var $className CartAdjustment
             */

            $list[$className::getGroup()][] = array(
                self::LIST_TYPE_KEY          => $className::getType(),
                self::LIST_LABEL_KEY         => $className::getLabel(),
                self::LIST_TEMPLATE_PATH_KEY => $className::getTemplatePath(),
            );
        }

        return $list;
    }

    /**
     * @return array
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * @param string $key
     *
     * @return string|null
     */
    public function getGroupLabel($key)
    {
        return isset($this->groups[$key]) ? $this->groups[$key] : null;
    }

    public function getItems()
    {
        return $this->items;
    }
}
