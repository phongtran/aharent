<?php

namespace ADP\BaseVersion\Includes\Reporter\Interfaces;

use ADP\BaseVersion\Includes\Rule\Interfaces\RuleProcessor;

interface Listener
{
    public function calcProcessStarted();

    /**
     * @param bool $result
     */
    public function processResult($result);

    /**
     * @param RuleProcessor $proc
     */
    public function ruleCalculated($proc);
}
