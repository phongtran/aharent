<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Tabs;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Statistics implements AdminTabInterface
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
        return 'admin_page/tabs/statistics.php';
    }

    public static function getHeaderDisplayPriority()
    {
        return 140;
    }

    public static function getKey()
    {
        return 'statistics';
    }

    public static function getTitle()
    {
        return __('Statistics', 'advanced-dynamic-pricing-for-woocommerce') . "&nbsp;&#x1f512;";
    }

    public function enqueueScripts()
    {
    }

    public function registerAjax()
    {

    }
}
