<?php

namespace ADP\Settings\Varieties\Option;

use ADP\Settings\Exceptions\OptionValueFilterFailed;

use ADP\Settings\Varieties\Option\Abstracts\Option;

class ShortTextOption extends Option
{
    /**
     * @param mixed $value
     *
     * @return string
     * @throws OptionValueFilterFailed
     */
    protected function sanitize($value)
    {
        $value = filter_var($value, FILTER_SANITIZE_STRING);

        if ($value === false) {
            throw new OptionValueFilterFailed();
        }

        return (string)$value;
    }
}
