<?php

namespace ADP\BaseVersion\Includes\Reporter\Collectors;

use WP_Hook;

class ActiveHooks
{
    public function collect()
    {
        return $this->hooksDispatch();
    }

    private function hooksDispatch()
    {
        global $wp_filter;
        $filters = array();
        foreach ($wp_filter as $hook_name => $hook_obj) {
            /**
             * @var WP_Hook $hook_obj
             */
            if (preg_match('#^woocommerce_#', $hook_name)) {
                $filters[$hook_name] = array();

                foreach ($hook_obj->callbacks as $priority => $callbacks) {
                    $filters[$hook_name][$priority] = array();

                    foreach ($callbacks as $idx => $callback_details) {
                        $classname  = $this->fetchClassNameFromCallback($callback_details['function']);
                        $methodname = $this->fetchMethodNameFromCallback($callback_details['function']);

                        if (is_null($methodname) && is_null($classname)) {
                            continue;
                        }

                        $filters[$hook_name][$priority][] = ! is_null($classname) ? $classname . '::' . $methodname : $methodname;
                    }
                }
            }
        }

        return $filters;
    }

    /**
     * @param array|object $callback
     *
     * @return string|null
     */
    private function fetchClassNameFromCallback($callback)
    {
        $classname = null;
        if (is_array($callback)) {
            if (isset($callback[0])) {
                if (is_string($callback[0])) {
                    $classname = $callback[0];
                } elseif (is_object($callback[0])) {
                    $classname = get_class($callback[0]);
                }
            }
        }

        return $classname;
    }

    /**
     * @param array|string $callback
     *
     * @return string|null
     */
    private function fetchMethodNameFromCallback($callback)
    {
        $methodName = null;
        if (is_array($callback)) {
            if (isset($callback[1])) {
                $methodName .= $callback[1];
            }
        } elseif (is_string($callback)) {
            $methodName = $callback;
        }

        return $methodName;
    }

}
