<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PhpUnit implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
    }

    public function start()
    {
        // do nothing!
    }
}
