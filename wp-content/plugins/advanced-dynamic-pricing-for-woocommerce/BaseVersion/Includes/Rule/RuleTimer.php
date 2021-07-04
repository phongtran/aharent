<?php

namespace ADP\BaseVersion\Includes\Rule;

use ADP\BaseVersion\Includes\Admin\Settings;
use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Exceptions\RuleExecutionTimeout;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class RuleTimer
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Rule
     */
    protected $rule;

    /**
     * @var float
     */
    protected $execRuleStart;

    /**
     * @var float
     */
    protected $lastExecTime;

    /**
     * @param Context $context
     * @param Rule $rule
     */
    public function __construct($context, $rule)
    {
        $this->context       = $context;
        $this->rule          = $rule;
        $this->execRuleStart = null;
        $this->lastExecTime  = null;
    }

    public function start()
    {
        $this->execRuleStart = microtime(true);
        $this->lastExecTime  = null;
    }

    /**
     * @return float
     */
    public function finish()
    {
        if ( ! isset($this->execRuleStart)) {
            return floatval(0);
        }

        $this->lastExecTime  = microtime(true) - $this->execRuleStart;
        $this->execRuleStart = null;

        return $this->lastExecTime;
    }

    /**
     * @throws RuleExecutionTimeout
     */
    public function checkExecutionTime()
    {
        $rule_max_exec_time = (float)$this->context->getOption('rule_max_exec_time');

        if (empty($rule_max_exec_time)) {
            return;
        }

        if ((microtime(true) - $this->execRuleStart) > $rule_max_exec_time) {
            throw new RuleExecutionTimeout();
        }
    }

    /**
     * @return float
     */
    public function getLastExecTime()
    {
        return $this->lastExecTime;
    }

    public function handleOutOfTime()
    {
        $value = get_option(Settings::$disabled_rules_option_name, array());

        $value[] = array(
            'id'           => $this->rule->getId(),
//			'is_exclusive' => $this->rule->is_exclusive(),
            'is_exclusive' => false,
        );

        update_option(Settings::$disabled_rules_option_name, $value);

        Database::markAsDisabledByPlugin($this->rule->getId());
    }
}
