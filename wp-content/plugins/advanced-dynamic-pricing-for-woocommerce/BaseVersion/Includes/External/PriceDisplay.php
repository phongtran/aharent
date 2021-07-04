<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\External\Cmp\WcSubscriptionsCmp;
use ADP\BaseVersion\Includes\External\PriceFormatters\DefaultFormatter;
use ADP\BaseVersion\Includes\External\PriceFormatters\DiscountRangeFormatter;
use ADP\BaseVersion\Includes\External\WC\PriceFunctions;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use ADP\BaseVersion\Includes\External\WC\WcTotalsFacade;
use ADP\BaseVersion\Includes\Product\ProcessedProductSimple;
use ADP\BaseVersion\Includes\Product\Processor;
use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\Rule\Processors\SingleItemRuleProcessor;
use ADP\BaseVersion\Includes\Rule\Structures\SingleItemRule;
use ADP\HighLander\HighLander;
use ADP\HighLander\Queries\ClassMethodFilterQuery;
use WC_Cart;
use WC_Product;
use WC_Product_Variation;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class PriceDisplay
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Processor
     */
    protected $processor;

    /**
     * @var PriceFunctions
     */
    protected $priceFunctions;

    /**
     * @var DiscountRangeFormatter
     */
    protected $discountRangeFormatter;

    /**
     * @var DefaultFormatter
     */
    protected $defaultFormatter;

    /**
     * @param Context $context
     * @param Processor $processor
     */
    public function __construct($context, $processor)
    {
        $this->context = $context;
        $this->with($processor);
    }

    /**
     * @param Processor $processor
     */
    public function with($processor)
    {
        if ($processor instanceof Processor) {
            $this->processor = $processor;
        }
    }

    public function initHooks()
    {
        $context  = $this->context;
        $priority = PHP_INT_MAX - 1;

        add_filter('woocommerce_get_price_html', array($this, 'hookPriceHtml'), $priority, 2);

        /**
         * Should wait until 'wp' action!
         * Because existence of onSale hooks depends to where we processing now
         * e.g. the option 'do_not_modify_price_at_product_page' is active, so we should prevent price html changes, but
         * if onSale hooks have been installed, product becomes 'is on sale' and we will see corrupted price html
         */
        if ($context->is($context::REST_API)) {
            add_action('parse_request', array($this, 'installOnSaleHooks'), 1);
        } elseif ($context->is($context::WP_CRON)) {
            $this->installOnSaleHooks();
        } elseif ($context->is($context::AJAX)) {
            $this->installOnSaleHooks();
        } else {
            /**  */
            add_action('wp', array($this, 'installOnSaleHooks'));
        }

        if ($context->getOption('show_cross_out_subtotal_in_cart_totals')) {
            add_filter('woocommerce_cart_subtotal', array($this, 'hookCartSubtotal'), 10, 3);
        }

        if ($context->getOption('use_first_range_as_min_qty')) {
            // for simple
            add_filter('woocommerce_quantity_input_args', array($this, 'hookItemPageQtyArgs'), 10, 2);
            // for variable
            add_filter('woocommerce_available_variation', array($this, 'hookWcAvailableVariation'), 10, 3);
        }

        $this->priceFunctions         = new PriceFunctions($context);
        $this->discountRangeFormatter = new DiscountRangeFormatter($context);
        $this->defaultFormatter       = new DefaultFormatter($context);
    }

    /**
     * @return array
     */
    protected static function getHookList()
    {
        return array(
            'woocommerce_get_price_html'            => array(
                array(__CLASS__, 'hookPriceHtml'),
            ),
            'woocommerce_product_is_on_sale'        => array(
                array(__CLASS__, 'hookIsOnSale'),
            ),
            'woocommerce_product_get_sale_price'    => array(
                array(__CLASS__, 'hookGetSalePrice'),
            ),
            'woocommerce_product_get_regular_price' => array(
                array(__CLASS__, 'hookGetRegularPrice'),
            ),
        );
    }

    /**
     * @param callable $callback
     * @param array $args
     *
     * @return mixed
     */
    public static function processWithout($callback, ...$args)
    {
        if ( ! is_callable($callback) && ! isset($callback[0], $callback[1])) {
            return null;
        }

        $list = static::getHookList();

        $highLander = new HighLander();
        $queries    = array();
        foreach ($list as $tag => $hooks) {
            $query = new ClassMethodFilterQuery();
            $query->setList($hooks)->setAction($query::ACTION_REMOVE)->useTag($tag);
            $queries[] = $query;
        }
        $highLander->setQueries($queries);

        $highLander->execute();
        $result = call_user_func_array($callback, $args);
        $highLander->restore();

        return $result;
    }

    public static function removeHooks()
    {
        $list = static::getHookList();

        $highLander = new HighLander();
        $queries    = array();
        foreach ($list as $tag => $hooks) {
            $query = new ClassMethodFilterQuery();
            $query->setList($hooks)->setAction($query::ACTION_REMOVE)->useTag($tag);
            $queries[] = $query;
        }
        $highLander->setQueries($queries);

        $highLander->execute();
    }

    public function installOnSaleHooks()
    {
        $context  = $this->context;
        $priority = PHP_INT_MAX - 1;

        /**
         * do NOT install hooks if 'do_not_modify_price_at_product_page' is enabled!
         * Without it, with activated options 'do_not_modify_price_at_product_page' and 'show_onsale_badge', it will
         * affect on default price html. In \WC_Product::get_price_html() product is ON SALE, so we will see the same price
         * as striked and clear.
         *
         * @see \WC_Product::get_price_html()
         */
        if ($context->getOption('show_onsale_badge') && $this->priceHtmlIsModifyNeeded()) {
            add_filter('woocommerce_product_is_on_sale', array($this, 'hookIsOnSale'), $priority, 2);
            add_filter('woocommerce_product_get_sale_price', array($this, 'hookGetSalePrice'), $priority, 2);
            add_filter('woocommerce_product_get_regular_price', array($this, 'hookGetRegularPrice'), $priority, 2);
        }
    }

    /**
     * @param string $priceHtml
     * @param WC_Product $product
     *
     * @return string
     */
    public function hookPriceHtml($priceHtml, $product)
    {
        if ( ! ($product instanceof WC_Product)) {
            return $priceHtml;
        }

        $context = $this->context;

        if ( ! apply_filters("adp_get_price_html_is_mod_needed", true, $product, $context)) {
            return $priceHtml;
        }

        if ( ! $this->priceHtmlIsModifyNeeded()) {
            return $priceHtml;
        }

        $qty = floatval(1);
        // only if bulk rule must override QTY input
        if ($this->context->getOption('use_first_range_as_min_qty')) {
            $args = $this->hookItemPageQtyArgs(array(), $product);
            if (isset($args['input_value'])) {
                $qty = (float)$args['input_value'];
            }
        }

        $processedProduct = $this->processor->calculateProduct($product, $qty);

        if (is_null($processedProduct)) {
            return $priceHtml;
        }

        if ($this->discountRangeFormatter->isNeeded($processedProduct) && $this->discountRangeFormatter->setFormatterTemplate()) {
            return $this->discountRangeFormatter->getHtml($processedProduct);
        }

        if ($processedProduct->areRulesApplied()) {
            $priceHtml = $processedProduct->getPriceHtml(self::priceHtmlIsAllowToStrikethroughPrice($context));
        }

        return $this->defaultFormatter->isNeeded($processedProduct) ? $this->defaultFormatter->getHTml($priceHtml,
            $processedProduct) : $priceHtml;
    }

    /**
     * @param bool $onSale
     * @param WC_Product $product
     *
     * @return bool
     */
    public function hookIsOnSale($onSale, $product)
    {
        if ( ! apply_filters("adp_get_price_html_is_mod_needed", true, $product, $this->context)) {
            return $onSale;
        }

        if ($onSale) {
            return $onSale;
        }

        $processedProduct = $this->processor->calculateProduct($product);
        if (is_null($processedProduct)) {
            return $onSale;
        }

        return $processedProduct->isDiscounted();
    }

    /**
     * @param string $value
     * @param WC_Product $product
     *
     * @return string|float
     */
    public function hookGetSalePrice($value, $product)
    {
        if ( ! apply_filters("adp_get_price_html_is_mod_needed", true, $product, $this->context)) {
            return $value;
        }

        $processed = $this->processor->calculateProduct($product);
        if (is_null($processed)) {
            return $value;
        }

        if ($processed->areRulesApplied()) {
            if ($processed instanceof ProcessedProductSimple) {
                $value = $processed->getCalculatedPrice();
            }
        }

        return $value;
    }

    /**
     * @param string $value
     * @param WC_Product $product
     *
     * @return string|float
     */
    public function hookGetRegularPrice($value, $product)
    {
        if ( ! apply_filters("adp_get_price_html_is_mod_needed", true, $product, $this->context)) {
            return $value;
        }

        $processed = $this->processor->calculateProduct($product);
        if (is_null($processed)) {
            return $value;
        }

        if ($processed->areRulesApplied()) {
            if ($processed instanceof ProcessedProductSimple) {
                $value = $processed->getOriginalPrice();
            }
        }

        return $value;
    }

    /**
     * @return bool
     */
    public function priceHtmlIsModifyNeeded()
    {
        $context = $this->context;

        return $context->is($context::WC_PRODUCT_PAGE) ? ! $context->getOption('do_not_modify_price_at_product_page',
            false) : true;
    }

    /**
     * @param Context $context
     *
     * @return bool
     */
    public static function priceHtmlIsAllowToStrikethroughPrice($context)
    {
        return true;
    }

    /**
     * @param string $cartSubtotalHtml
     * @param bool $compound
     * @param WC_Cart $wcCart
     *
     * @return string
     */
    public function hookCartSubtotal($cartSubtotalHtml, $compound, $wcCart)
    {
        $context = $this->context;

        // if ( ! $context->is( $context::WC_CART_PAGE ) ) {
        // 	return $cartSubtotalHtml;
        // }

        if ($compound) {
            return $cartSubtotalHtml;
        }

        $facade        = new WcTotalsFacade($context, $wcCart);
        $initialTotals = $facade->getInitialTotals();

        if ( ! empty($initialTotals['subtotal']) || ! empty($initialTotals['subtotal_tax'])) {
            $initialCartSubtotal    = $initialTotals['subtotal'];
            $initialCartSubtotalTax = $initialTotals['subtotal_tax'];
        } else {
            return $cartSubtotalHtml;
        }

        $suffix = '';
        if ($wcCart->display_prices_including_tax()) {
            $initialCartSubtotal += $initialCartSubtotalTax;
            $cartSubtotal        = $wcCart->get_subtotal() + $wcCart->get_subtotal_tax();

            if ($wcCart->get_subtotal_tax() > 0 && ! wc_prices_include_tax()) {
                $suffix = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            }
        } else {
            $cartSubtotal = $wcCart->get_subtotal();

            if ($wcCart->get_subtotal_tax() > 0 && wc_prices_include_tax()) {
                $suffix = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
        }

        $initialCartSubtotal = apply_filters('wdp_initial_cart_subtotal', $initialCartSubtotal, $wcCart);
        $cartSubtotal        = apply_filters('wdp_cart_subtotal', $cartSubtotal, $wcCart);

        if ($cartSubtotal < $initialCartSubtotal) {
            $cartSubtotalHtml = $this->priceFunctions->formatSalePrice($initialCartSubtotal, $cartSubtotal) . $suffix;
        }

        return $cartSubtotalHtml;
    }

    /**
     * @param $args
     * @param $product
     *
     * @return array
     * @throws \Exception
     */
    public function hookItemPageQtyArgs($args, $product)
    {
        $context = $this->context;
        if ( ! $this->context->is($context::WC_PRODUCT_PAGE)) {
            return $args;
        }

        /** @var SingleItemRuleProcessor[] $ruleProcessors */
        $ruleProcessors = array();
        foreach (CacheHelper::loadActiveRules($context)->getRules() as $rule) {
            if ($rule instanceof SingleItemRule && $rule->getProductRangeAdjustmentHandler()) { // only for 'SingleItem' rule
                $ruleProcessors[] = $rule->buildProcessor($context);
            }
        }

        foreach ($ruleProcessors as $ruleProcessor) {
            if ($ruleProcessor->isProductMatched($this->processor->getCart(), $product, true)) {
                $matchedRuleProcessor = $ruleProcessor;

                $handler = $matchedRuleProcessor->getRule()->getProductRangeAdjustmentHandler();
                $ranges  = $handler->getRanges();

                $range = reset($ranges);
                if ($range) {
                    $args['input_value'] = $range->getFrom(); // Start from this value (default = 1)
                    $args['min_value']   = $range->getFrom(); // Min quantity (default = 0)
                }
            }
        }

        return $args;
    }

    /**
     * @param array $args
     * @param WC_Product $product
     * @param WC_Product_Variation $variation
     *
     * @return array
     * @throws \Exception
     */
    public function hookWcAvailableVariation($args, $product, $variation)
    {
        $newArgsParent = $this->hookItemPageQtyArgs(array(), $product);
        $newArgs       = $this->hookItemPageQtyArgs(array(), $variation);

        if (isset($newArgsParent['min_value'])) {
            $args['min_qty'] = $newArgsParent['min_value'];
        } elseif (isset($newArgs['min_value'])) {
            $args['min_qty'] = $newArgs['min_value'];
        }

        return $args;
    }
}
