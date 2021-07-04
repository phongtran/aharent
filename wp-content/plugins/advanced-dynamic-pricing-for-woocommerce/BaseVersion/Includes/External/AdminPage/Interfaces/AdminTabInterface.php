<?php

namespace ADP\BaseVersion\Includes\External\AdminPage\Interfaces;

use ADP\BaseVersion\Includes\Context;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface AdminTabInterface
{
    /**
     * AdminTabInterface constructor.
     *
     * @param Context $context
     */
    public function __construct($context);

    public function handleSubmitAction();

    public function registerAjax();

    public function enqueueScripts();

    /**
     * @return array
     */
    public function getViewVariables();

    /**
     * Display priority in the header
     *
     * @return int
     */
    public static function getHeaderDisplayPriority();

    /**
     * @return string
     */
    public static function getRelativeViewPath();

    /**
     * Unique tab key
     *
     * @return string
     */
    public static function getKey();

    /**
     * Localized title
     *
     * @return string
     */
    public static function getTitle();
}
