<?php

namespace ADP\BaseVersion\Includes\Reporter\Collectors;

use ADP\BaseVersion\Includes\Product\Processor;

class Products
{
    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @param $listener Processor
     */
    public function __construct($listener)
    {
        $this->processor = $listener;
    }

    /**
     * @return array
     */
    public function collect()
    {
        return $this->processor->getListener()->getTotals();
    }

}
