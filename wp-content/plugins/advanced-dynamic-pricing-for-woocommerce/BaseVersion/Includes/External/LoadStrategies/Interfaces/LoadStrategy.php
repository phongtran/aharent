<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces;

use ADP\BaseVersion\Includes\Context;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface LoadStrategy
{
    /**
     * @param Context $context
     */
    public function __construct($context);

    public function start();
}
