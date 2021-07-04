<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

/**
 * WooCommerce All Products For Subscriptions
 */
class WcsAttCmp
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var null|\WCS_ATT
     */
    protected $wcsAtt;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
        $this->loadRequirements();
    }

    public function loadRequirements()
    {
        if ( ! did_action('plugins_loaded')) {
            _doing_it_wrong(__FUNCTION__, sprintf(__('%1$s should not be called earlier the %2$s action.',
                'advanced-dynamic-pricing-for-woocommerce'), 'loadRequirements', 'plugins_loaded'), WC_ADP_VERSION);
        }

        $this->wcsAtt = class_exists("\WCS_ATT") ? \WCS_ATT::instance() : null;
    }

    /**
     * @return bool
     */
    public function isActive()
    {
        return ! is_null($this->wcsAtt) && ($this->wcsAtt instanceof \WCS_ATT);
    }
}
