<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Tabs;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Help implements AdminTabInterface
{
    /**
     * @var string
     */
    protected $title;

    /**
     * @var Context
     */
    protected $context;

    public function __construct($context)
    {
        $this->context = $context;
        $this->title   = self::getTitle();
    }

    public function handleSubmitAction()
    {
        // do nothing
    }

    public function getViewVariables()
    {
        return array();
    }

    public static function getRelativeViewPath()
    {
        return 'admin_page/tabs/help.php';
    }

    public static function getHeaderDisplayPriority()
    {
        return 80;
    }

    public static function getKey()
    {
        return 'help';
    }

    public static function getTitle()
    {
        return __('Help', 'advanced-dynamic-pricing-for-woocommerce');
    }

    public function enqueueScripts()
    {
    }

    public function registerAjax()
    {

    }
}
