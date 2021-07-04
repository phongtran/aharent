<?php

namespace ADP\BaseVersion\Includes\Rule\CartAdjustments;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

abstract class AbstractCartAdjustment
{
    protected $amountIndexes;

    /**
     * @param float $rate
     */
    public function multiplyAmounts($rate)
    {
        foreach ($this->amountIndexes as $index) {
            /**
             * @var string $index
             */
            if (isset($this->$index)) {
                $amount       = (float)$this->$index;
                $this->$index = $amount * $rate;
            }
        }
    }
}
