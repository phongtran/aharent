<?php

namespace ADP\BaseVersion\Includes\External\Shortcodes;


use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\Customizer\Customizer;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTable;
use ADP\Factory;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class ProductRangeDiscountTableShortcode
{
    const NAME = 'adp_product_bulk_rules_table';

    /**
     * @var Context
     */
    protected $context;

    /**
     * @var Customizer
     */
    protected $customizer;

    /**
     * @param Context $context
     * @param Customizer $customizer
     */
    public function __construct($context, $customizer)
    {
        $this->context    = $context;
        $this->customizer = $customizer;
    }

    /**
     * @param Context $context
     * @param Customizer $customizer
     */
    public static function register($context, $customizer)
    {
        $shortcode = new self($context, $customizer);
        add_shortcode(self::NAME, array($shortcode, 'getContent'));
    }

    public function getContent($args)
    {
        /** @var RangeDiscountTable $table */
        $table = Factory::get("External_RangeDiscountTable_RangeDiscountTable", $this->context, $this->customizer);

        if ( ! empty($args['layout']) && in_array($args['layout'],
                array($table::LAYOUT_VERBOSE, $table::LAYOUT_SIMPLE))) {
            $layout = $args['layout'];
        } else {
            $layout = $table::LAYOUT_VERBOSE;
        }

        $forcePercentage = isset($args['force_percentage']) ? wc_string_to_bool($args['force_percentage']) : false;

        $table->setContextOptions(array(
            $table::CONTEXT_PRODUCT_PAGE => array(
                'table' => array(
                    'table_layout'                   => $layout,
                    'simply_layout_force_percentage' => $forcePercentage,
                ),
            ),
        ));

        $productId = ! empty($args['id']) ? intval($args['id']) : null;

        return $table->getProductTableContent($productId);
    }
}
