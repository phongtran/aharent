<?php

namespace ADP\BaseVersion\Includes\Reporter\Collectors;

use ADP\BaseVersion\Includes\Cart\CartProcessor;

class WcCart
{
    /**
     * @var CartProcessor
     */
    protected $processor;

    /**
     * @param $processor CartProcessor
     */
    public function __construct($processor)
    {
        $this->processor = $processor;
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this->processor->getListener()->getTotals();
    }
}
