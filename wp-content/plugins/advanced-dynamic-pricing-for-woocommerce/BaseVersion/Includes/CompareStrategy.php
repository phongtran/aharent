<?php

namespace ADP\BaseVersion\Includes;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CompareStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     * You can't just compare floating point numbers!
     * Only with a certain accuracy.
     *
     * @param string|int|float $a
     * @param string|int|float $b
     *
     * @return bool
     */
    public function floatsAreEqual($a, $b)
    {
        $a = number_format(floatval($a), $this->context->getPriceDecimals());
        $b = number_format(floatval($b), $this->context->getPriceDecimals());

        return $a === $b;
    }
}
