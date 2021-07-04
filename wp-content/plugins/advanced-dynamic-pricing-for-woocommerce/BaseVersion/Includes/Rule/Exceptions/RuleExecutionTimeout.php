<?php

namespace ADP\BaseVersion\Includes\Rule\Exceptions;

use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RuleExecutionTimeout extends Exception
{
    public function errorMessage()
    {
        return __('Rule execution timeout', 'advanced-dynamic-pricing-for-woocommerce');
    }

}
