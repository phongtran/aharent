<?php

namespace ADP\BaseVersion\Includes\External\PriceFormatters;

use ADP\BaseVersion\Includes\Context;

class Formatter
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var string
     */
    protected $template;

    /**
     * @var string[]
     */
    protected $availableReplacements;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context               = $context;
        $this->template              = "";
        $this->availableReplacements = array();
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        if ( ! is_string($template)) {
            return;
        }

        $this->template              = $template;
        $this->availableReplacements = array();
        if (preg_match_all("/{{([^ {}]+)}}/", $template, $matches) !== false) {
            if (isset($matches[1]) && is_array($matches[1])) {
                $this->availableReplacements = $matches[1];
            }
        }
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->template;
    }

    /**
     * @return array<int,string>
     */
    public function getAvailableReplacements()
    {
        return $this->availableReplacements;
    }

    /**
     * @param array<int,string> $replacements
     *
     * @return string
     */
    public function applyReplacements($replacements)
    {
        if ( ! is_array($replacements)) {
            return "";
        }

        $newReplacements = array();
        foreach ($this->availableReplacements as $key) {
            if ( ! isset($replacements[$key])) {
                $replacements[$key] = "";
            }

            $newReplacements["{{" . $key . "}}"] = $replacements[$key];
        }

        return str_replace(array_keys($newReplacements), array_values($newReplacements), $this->template);
    }

}
