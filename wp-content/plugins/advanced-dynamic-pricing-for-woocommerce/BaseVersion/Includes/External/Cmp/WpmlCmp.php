<?php

namespace ADP\BaseVersion\Includes\External\Cmp;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Interfaces\Rule;
use ADP\BaseVersion\Includes\Translators\RuleTranslator;

class WpmlCmp
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var \woocommerce_wpml|null
     */
    protected $wcWpml;

    /**
     * @var \SitePress|null
     */
    protected $sitepress;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
        $this->loadRequirements();
    }

    public function modifyContext() {
        add_filter("adp_replace_variation_data_store", "__return_false");
    }

    public function isActiveWcWpml()
    {
        return ! is_null($this->wcWpml) && ($this->wcWpml instanceof \woocommerce_wpml);
    }

    public function isActiveSitepress()
    {
        return ! is_null($this->sitepress) && ($this->sitepress instanceof \SitePress);
    }

    public function loadRequirements()
    {
        if ( ! did_action('plugins_loaded')) {
            _doing_it_wrong(__FUNCTION__, sprintf(__('%1$s should not be called earlier the %2$s action.',
                'advanced-dynamic-pricing-for-woocommerce'), 'load_requirements', 'plugins_loaded'), WC_ADP_VERSION);
        }

        $this->sitepress = isset($GLOBALS['sitepress']) ? $GLOBALS['sitepress'] : null;
        $this->wcWpml    = isset($GLOBALS['woocommerce_wpml']) ? $GLOBALS['woocommerce_wpml'] : null;
    }

    public function shouldTranslate()
    {
        return boolval(apply_filters('adp_should_translate_wpml', true));
    }

    /**
     * @param Rule $rule
     *
     * @return Rule
     */
    public function translateRule($rule): Rule
    {
        return RuleTranslator::translate($rule, $this->sitepress->get_current_language());
    }

    /**
     * @param Rule $rule
     *
     * @return Rule
     */
    public function changeRuleCurrency($rule): Rule
    {
        if (isset($this->wcWpml->multi_currency)) {
            $currency = $this->wcWpml->multi_currency->get_client_currency();
            $rate     = $this->wcWpml->multi_currency->exchange_rate_services->get_currency_rate($currency);
            if ($rate) {
                $rule = RuleTranslator::setCurrency($rule, $rate);
            }
        }

        return $rule;
    }
}
