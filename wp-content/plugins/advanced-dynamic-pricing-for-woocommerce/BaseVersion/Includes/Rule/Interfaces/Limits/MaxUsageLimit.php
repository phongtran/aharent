<?php

namespace ADP\BaseVersion\Includes\Rule\Interfaces\Limits;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

interface MaxUsageLimit
{
    const MAX_USAGE_KEY = 'max_usage';

    /**
     * @param string|int $maxUsage
     */
    public function setMaxUsage($maxUsage);

    /**
     * @return int
     */
    public function getMaxUsage();
}
