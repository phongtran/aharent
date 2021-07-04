<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class CustomizePreview implements LoadStrategy
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
        /** @var $strategy ClientCommon */
        $clientCommonStrategy = Factory::get("External_LoadStrategies_ClientCommon", $this->context);
        $clientCommonStrategy->start();

        /** @var $strategy AdminAjax */
        $ajaxStrategy = Factory::get("External_LoadStrategies_AdminAjax", $this->context);
        $ajaxStrategy->start();
    }
}
