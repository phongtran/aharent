<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RangeDiscount extends Range
{
    /**
     * @param string|float $from
     * @param string|float $to
     * @param Discount|SetDiscount $discount
     *
     * @throws Exception
     */
    public function __construct($from, $to, $discount)
    {
        if ( ! ($discount instanceof Discount)) {
            throw new Exception(sprintf("Incorrect type %s", var_export($discount, true)));
        }

        parent::__construct($from, $to, $discount);
    }

    /**
     * @return Discount|SetDiscount
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param Discount|SetDiscount $data
     */
    public function setData($data)
    {
        $this->data = $data;
    }
}
