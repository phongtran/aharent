<?php

namespace ADP\BaseVersion\Includes\External;

use ADP\BaseVersion\Includes\Context;
use ADP\Factory;
use ADP\HighLander\HighLander;
use ADP\HighLander\Queries\ClassMethodFilterQuery;
use ADP\HighLander\Queries\TagFilterQuery;

class ExternalHooksSuppressor
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    public function registerHookSuppressor()
    {
        add_action("wp_loaded", array($this, 'removeExternalHooks'));
    }

    public function removeExternalHooks()
    {
        $allowedHooks = array(
            //Filters
            "woocommerce_get_price_html"            => array(
                array(Factory::getClassName('External_PriceDisplay'), "hookPriceHtml",),
            ),
            "woocommerce_product_is_on_sale"        => array(
                array(Factory::getClassName('External_PriceDisplay'), "hookIsOnSale",),
            ),
            "woocommerce_product_get_sale_price"    => array(
                array(Factory::getClassName('External_PriceDisplay'), "hookGetSalePrice",),
            ),
            "woocommerce_product_get_regular_price" => array(
                array(Factory::getClassName('External_PriceDisplay'), "hookGetRegularPrice",),
            ),
            "woocommerce_variable_price_html"       => array(),
            "woocommerce_variable_sale_price_html"  => array(),
            "woocommerce_cart_item_price"           => array(
                array(Factory::getClassName('External_PriceDisplay'), "wcCartItemPriceOrSubtotal",),
            ),
            "woocommerce_cart_item_subtotal"        => array(
                array(Factory::getClassName('External_PriceDisplay'), "wcCartItemPriceOrSubtotal",),
            ),
            //Actions
            "woocommerce_checkout_order_processed"  => array(
                array(Factory::getClassName('External_WcCartStatsCollector'), "checkout_order_processed",),
            ),
            "woocommerce_before_calculate_totals"   => array(), //nothing allowed!
        );

        $highLander = new HighLander();
        $queries    = array();

        $tagQuery = new TagFilterQuery();
        $tagQuery->setList(array_keys($allowedHooks))->setAction($tagQuery::ACTION_REMOVE_ALL_IN_TAG);
        $queries[] = $tagQuery;

        foreach ($allowedHooks as $tag => $hooks) {
            $query = new ClassMethodFilterQuery();
            $query->setList($hooks)->setAction($query::ACTION_SAVE)->useTag($tag);

            $queries[] = $query;
        }
        $highLander->setQueries($queries);

        $highLander->execute();
    }
}
