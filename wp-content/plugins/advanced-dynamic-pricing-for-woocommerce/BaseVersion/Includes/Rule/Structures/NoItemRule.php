<?php

namespace ADP\BaseVersion\Includes\Rule\Structures;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Rule\Processors\NoItemRuleProcessor;
use ADP\BaseVersion\Includes\Rule\Structures\Abstracts\BaseRule;
use Exception;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class NoItemRule extends BaseRule implements Rule
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param Context $context
     *
     * @return NoItemRuleProcessor
     * @throws Exception
     */
    public function buildProcessor($context)
    {
        return new NoItemRuleProcessor($context, $this);
    }
}
