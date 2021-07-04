<?php

namespace ADP\BaseVersion\Includes\External\LoadStrategies;

use ADP\BaseVersion\Includes\Admin\Settings;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\AdminPage;
use ADP\BaseVersion\Includes\External\LoadStrategies\Interfaces\LoadStrategy;
use ADP\BaseVersion\Includes\External\Updater\Updater;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class AdminCommon implements LoadStrategy
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    public function start()
    {
        Updater::update();

        /**
         * @var AdminPage $adminPage
         */
        $adminPage = Factory::get('External_AdminPage_AdminPage', $this->context);
        $adminPage->installRegisterPageHook();
        if ($this->context->isPluginAdminPage()) {
            $adminPage->initPage();
        }

        new Settings($this->context);

        /** @see Functions::install() */
        Factory::callStaticMethod("Functions", 'install', $this->context);
    }
}
