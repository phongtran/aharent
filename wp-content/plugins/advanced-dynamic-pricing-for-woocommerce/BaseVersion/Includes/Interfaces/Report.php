<?php

namespace ADP\BaseVersion\Includes\Interfaces;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface Report
{
    /**
     * @return string
     */
    public function getTitle();

    /**
     * @return string
     */
    public function getSubtitle();

    /**
     * @return string
     */
    public function getType();

    /**
     * @param array $params
     *
     * @return array
     */
    public function getData($params);
}
