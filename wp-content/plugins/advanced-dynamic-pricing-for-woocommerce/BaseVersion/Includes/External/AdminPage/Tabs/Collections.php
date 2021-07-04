<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Tabs;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\AdminPage\Interfaces\AdminTabInterface;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Collections implements AdminTabInterface
{
    /**
     * @var string
     */
    protected $title;

    public function __construct($context)
    {
        $this->title = self::getTitle();
    }

    public function handleSubmitAction()
    {
        // do nothing
    }

    public static function getRelativeViewPath()
    {
        return 'admin_page/tabs/collections.php';
    }

    public static function getHeaderDisplayPriority()
    {
        return 120;
    }

    public static function getKey()
    {
        return 'product_collections';
    }

    public static function getTitle()
    {
        return __('Product Collections', 'advanced-dynamic-pricing-for-woocommerce') . "&nbsp;&#x1f512;";
    }

    public function getViewVariables()
    {
        return array();
    }

    public function enqueueScripts()
    {
    }

    public function registerAjax()
    {

    }
}
