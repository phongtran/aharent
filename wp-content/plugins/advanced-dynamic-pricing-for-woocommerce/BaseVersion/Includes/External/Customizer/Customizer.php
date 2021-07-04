<?php

namespace ADP\BaseVersion\Includes\External\Customizer;

use ADP\BaseVersion\Includes\Context;
use ADP\BaseVersion\Includes\External\DiscountMessage;
use ADP\BaseVersion\Includes\External\RangeDiscountTable\RangeDiscountTable;
use WP_Customize_Manager;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Customizer
{
    const LAYOUT_VERBOSE = RangeDiscountTable::LAYOUT_VERBOSE;
    const LAYOUT_SIMPLE = RangeDiscountTable::LAYOUT_SIMPLE;
    const ANY = 'any';

    protected static $optionName = 'woocommerce_wdp_bulk_table';
    protected $options = array();

    /**
     * @var Context
     */
    protected $context;

    /**
     * Customizer constructor.
     *
     * @param Context $context
     */
    public function __construct($context)
    {
        $this->context = $context;
        $this->init();
    }

    public function runStyleCustomize()
    {
        add_action('wp_head', function () {
            $this->customizeCss();
        });
    }

    public function customizeRegister()
    {
        add_action('customize_register', array($this, 'add_sections'));
        add_action('customize_controls_enqueue_scripts', array($this, 'customizerControlsScripts'), 999);
        add_action('customize_preview_init', array($this, 'customizePreviewInit'));
    }

    protected function init()
    {
        $this->options['wdp_product_bulk_table'] = array(
            'key'      => RangeDiscountTable::CONTEXT_PRODUCT_PAGE,
            'title'    => __('Product bulk table (Advanced Dynamic Pricing)',
                'advanced-dynamic-pricing-for-woocommerce'),
            'priority' => 200,
            'options'  => $this->getProductTableOptions('wdp_product_bulk_table'),
        );

        $this->options['wdp_category_bulk_table'] = array(
            'key'      => RangeDiscountTable::CONTEXT_CATEGORY_PAGE,
            'title'    => __('Category bulk table (Advanced Dynamic Pricing)',
                'advanced-dynamic-pricing-for-woocommerce'),
            'priority' => 200,
            'options'  => $this->getCategoryTableOptions('wdp_category_bulk_table'),
        );

        $this->options['wdp_discount_message'] = array(
            'key'      => DiscountMessage::PANEL_KEY,
            'title'    => __('Discount message (Advanced Dynamic Pricing)', 'advanced-dynamic-pricing-for-woocommerce'),
            'priority' => 200,
            'options'  => $this->getDiscountMessageOptions('wdp_discount_message'),
        );
    }

    protected function initFontOptions($panelId, $section)
    {
        $mapSectionAndCssSelector = array(
            "{$panelId}-table_header"  => '.wdp_bulk_table_content .wdp_pricing_table_caption',
            "{$panelId}-table_columns" => '.wdp_bulk_table_content table thead td',
            "{$panelId}-table_body"    => '.wdp_bulk_table_content table tbody td',
            "{$panelId}-table_footer"  => '.wdp_bulk_table_content .wdp_pricing_table_footer',
        );

        if (empty($mapSectionAndCssSelector[$section])) {
            return false;
        }
        $selector = $mapSectionAndCssSelector[$section];

        $font_options = array(
            "{$panelId}-emphasis_bold"   => array(
                'label'             => __('Bold text', 'advanced-dynamic-pricing-for-woocommerce'),
                'default'           => false,
                'sanitize_callback' => 'wc_bool_to_string',
                'control_class'     => 'ADP\BaseVersion\Includes\External\Customizer\Controls\FontEmphasisBold',
                'priority'          => 10,

                'apply_type'       => 'css',
                'selector'         => $selector,
                'css_option_name'  => 'font-weight',
                'css_option_value' => 'bold',
                'layout'           => self::ANY,
            ),
            "{$panelId}-emphasis_italic" => array(
                'label'             => __('Italic text', 'advanced-dynamic-pricing-for-woocommerce'),
                'default'           => false,
                'sanitize_callback' => 'wc_bool_to_string',
                'control_class'     => 'ADP\BaseVersion\Includes\External\Customizer\Controls\FontEmphasisItalic',
                'priority'          => 20,

                'apply_type'       => 'css',
                'selector'         => $selector,
                'css_option_name'  => 'font-style',
                'css_option_value' => 'italic',
                'layout'           => self::ANY,
            ),
            "{$panelId}-text_align"      => array(
                'label'         => __('Text Align', 'advanced-dynamic-pricing-for-woocommerce'),
                'default'       => '',
                'control_class' => 'ADP\BaseVersion\Includes\External\Customizer\Controls\TextAlign',
                'priority'      => 30,

                'apply_type'      => 'css',
                'selector'        => $selector,
                'css_option_name' => 'text-align',
                'layout'          => self::ANY,
            ),
            "{$panelId}-text_color"      => array(
                'label'             => __('Text color', 'advanced-dynamic-pricing-for-woocommerce'),
                'default'           => '#6d6d6d',
                'sanitize_callback' => 'sanitize_hex_color',
                'control_class'     => '\WP_Customize_Color_Control',
                'priority'          => 10,

                'apply_type'      => 'css',
                'selector'        => $selector,
                'css_option_name' => 'color',
                'layout'          => self::ANY,
            ),
        );

        // bulk_table_header BOLD by default
        if ("{$panelId}-bulk_table_header" == $section) {
            $font_options["{$panelId}-emphasis_bold"]['default'] = true;
        }

        return $font_options;
    }

    protected function getProductTableOptions($panelId)
    {
        $type = 'product';

        $product_options = array(
            "{$panelId}-table"         => array(
                'title'    => __('Options', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 10,
                'options'  => array(
                    'table_layout'              => array(
                        'label'        => __('Product table layout', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => self::LAYOUT_VERBOSE,
                        'control_type' => 'select',
                        'choices'      => array(
                            self::LAYOUT_VERBOSE => __('Display ranges as rows',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            self::LAYOUT_SIMPLE  => __('Display ranges as headers',
                                'advanced-dynamic-pricing-for-woocommerce'),
                        ),
                        'priority'     => 5,

                        'apply_type' => 'filter',
//						'hook'       => "wdp_{$type}_bulk_table_action",
                        'layout'     => self::ANY,
                    ),
                    'product_bulk_table_action' => array(
                        'label'        => __('Product Bulk Table position', 'advanced-dynamic-pricing-for-woocommerce'),
                        'description'  => __('You can use shortcode [adp_product_bulk_rules_table] in product template.',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => 'woocommerce_after_add_to_cart_form',
                        'control_type' => 'select',
                        'choices'      => apply_filters('wdp_product_bulk_table_places', array(
                            'woocommerce_before_single_product_summary' => __('Above product summary',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_after_single_product_summary'  => __('Below product summary',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_before_single_product'         => __('Above product',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_after_single_product'          => __('Below product',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_before_add_to_cart_form'       => __('Above add to cart',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_after_add_to_cart_form'        => __('Below add to cart',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_product_meta_start'            => __('Above product meta',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_product_meta_end'              => __('Below product meta',
                                'advanced-dynamic-pricing-for-woocommerce'),
                        )),
                        'priority'     => 10,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_action",
                        'layout'     => self::ANY,
                    ),
                    'show_discounted_price'     => array(
                        'label'             => __('Show discounted price', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => true,
                        'priority'          => 20,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_show_discounted_price_in_{$type}_bulk_table",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'show_discount_column'      => array(
                        'label'             => __('Show fixed discount column',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => true,
                        'priority'          => 30,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_show_product_discount_in_{$type}_bulk_table",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'show_footer'               => array(
                        'label'             => __('Show footer', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => true,
                        'priority'          => 40,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_show_footer_in_{$type}_bulk_table",
                        'layout'     => self::ANY,
                    ),
                ),

            ),
            "{$panelId}-table_header"  => array(
                'title'    => __('Style header', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 20,
                'options'  => array(
                    'use_message_as_title' => array(
                        'label'             => __('Use bulk table message as table header',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => false,
                        'priority'          => 50,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_use_message_as_{$type}_bulk_table_header",
                        'layout'     => self::ANY,
                    ),
                    'bulk_title'           => array(
                        'label'    => __('Header bulk title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Bulk deal', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_header_for_bulk_title",
                        'layout'     => self::ANY,
                    ),
                    'tier_title'           => array(
                        'label'    => __('Header tier title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Tier deal', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_header_for_tier_title",
                        'layout'     => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_columns" => array(
                'title'    => __('Style columns', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 30,
                'options'  => array(
                    'qty_column_title'                           => array(
                        'label'    => __('Quantity column title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Quantity', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_qty_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title'                      => array(
                        'label'    => __('Discount column title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 60,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_discount_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title_for_rule_fixed_price' => array(
                        'label'    => __('Discount column title, for fixed price',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 65,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_rule_fixed_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title_for_fixed_price'      => array(
                        'label'    => __('Discounted price column title, for fixed price',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Fixed price', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 70,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_fixed_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discounted_price_title'                     => array(
                        'label'    => __('Discounted price column title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Discounted price', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 80,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_discounted_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'header_background_color'                    => array(
                        'label'             => __('Background color', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => '#efefef',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class'     => 'WP_Customize_Color_Control',
                        'priority'          => 90,

                        'apply_type'      => 'css',
                        'selector'        => '.wdp_bulk_table_content table thead td',
                        'css_option_name' => 'background-color',
                        'layout'          => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_body"    => array(
                'title'    => __('Style body', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 40,
                'options'  => array(
                    'body_background_color' => array(
                        'label'             => __('Background color', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class'     => 'WP_Customize_Color_Control',
                        'priority'          => 50,

                        'apply_type'      => 'css',
                        'selector'        => '.wdp_bulk_table_content table tbody td',
                        'css_option_name' => 'background-color',
                        'layout'          => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_footer"  => array(
                'title'    => __('Style footer', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 50,
                'options'  => array(),
            ),
        );


        foreach ($product_options as $section => &$section_data) {
            if ($font_options = $this->initFontOptions($panelId, $section)) {
                $section_data['options'] = array_merge($font_options, $section_data['options']);
            }
        }

        return $product_options;
    }

    protected function getCategoryTableOptions($panelId)
    {
        $type = 'category';

        $categoryOptions = array(
            "{$panelId}-table"         => array(
                'title'    => __('Options', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 10,
                'options'  => array(
                    'table_layout'               => array(
                        'label'        => __('Category table layout', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => self::LAYOUT_VERBOSE,
                        'control_type' => 'select',
                        'choices'      => array(
                            self::LAYOUT_VERBOSE => __('Display ranges as rows',
                                'advanced-dynamic-pricing-for-woocommerce'),
                        ),
                        'priority'     => 5,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_action",
                        'layout'     => self::ANY,
                    ),
                    'category_bulk_table_action' => array(
                        'label'        => __('Category Bulk Table position',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'description'  => __('You can use shortcode [adp_product_bulk_rules_table] in product template.',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => 'woocommerce_before_shop_loop',
                        'control_type' => 'select',
                        'choices'      => apply_filters('wdp_category_bulk_table_places', array(
                            'woocommerce_before_shop_loop' => __('At top of the page',
                                'advanced-dynamic-pricing-for-woocommerce'),
                            'woocommerce_after_shop_loop'  => __('At bottom of the page',
                                'advanced-dynamic-pricing-for-woocommerce'),
                        )),
                        'priority'     => 10,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_action",
                        'layout'     => self::ANY,
                    ),

                    'show_discount_column' => array(
                        'label'             => __('Show fixed discount column',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => true,
                        'priority'          => 30,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_show_product_discount_in_{$type}_bulk_table",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'show_footer'          => array(
                        'label'             => __('Show footer', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => true,
                        'priority'          => 40,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_show_footer_in_{$type}_bulk_table",
                        'layout'     => self::ANY,
                    ),
                ),

            ),
            "{$panelId}-table_header"  => array(
                'title'    => __('Style header', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 20,
                'options'  => array(
                    'use_message_as_title' => array(
                        'label'             => __('Use bulk table message as table header',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => false,
                        'priority'          => 50,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                        'hook'       => "wdp_use_message_as_{$type}_bulk_table_header",
                        'layout'     => self::ANY,
                    ),
                    'bulk_title'           => array(
                        'label'    => __('Header bulk title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Bulk deal', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_header_for_bulk_title",
                        'layout'     => self::ANY,
                    ),
                    'tier_title'           => array(
                        'label'    => __('Header tier title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Tier deal', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_header_for_tier_title",
                        'layout'     => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_columns" => array(
                'title'    => __('Style columns', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 30,
                'options'  => array(
                    'qty_column_title'                           => array(
                        'label'    => __('Quantity column title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Quantity', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 50,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_qty_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title'                      => array(
                        'label'    => __('Discount column title', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 60,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_discount_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title_for_rule_fixed_price' => array(
                        'label'    => __('Discount column title, for fixed price',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Discount', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 65,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_rule_fixed_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'discount_column_title_for_fixed_price'      => array(
                        'label'    => __('Discounted price column title, for fixed price',
                            'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __('Fixed price', 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 70,

                        'apply_type' => 'filter',
                        'hook'       => "wdp_{$type}_bulk_table_fixed_price_title",
                        'layout'     => self::LAYOUT_VERBOSE,
                    ),
                    'header_background_color'                    => array(
                        'label'             => __('Background color', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => '#efefef',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class'     => 'WP_Customize_Color_Control',
                        'priority'          => 90,

                        'apply_type'      => 'css',
                        'selector'        => '.wdp_bulk_table_content table thead td',
                        'css_option_name' => 'background-color',
                        'layout'          => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_body"    => array(
                'title'    => __('Style body', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 40,
                'options'  => array(
                    'body_background_color' => array(
                        'label'             => __('Background color', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => '#ffffff',
                        'sanitize_callback' => 'sanitize_hex_color',
                        'control_class'     => 'WP_Customize_Color_Control',
                        'priority'          => 50,

                        'apply_type'      => 'css',
                        'selector'        => '.wdp_bulk_table_content table tbody td',
                        'css_option_name' => 'background-color',
                        'layout'          => self::ANY,
                    ),
                ),
            ),
            "{$panelId}-table_footer"  => array(
                'title'    => __('Style footer', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 50,
                'options'  => array(),
            ),
        );

        foreach ($categoryOptions as $section => &$sectionData) {
            if ($fontOptions = $this->initFontOptions($panelId, $section)) {
                $sectionData['options'] = array_merge($fontOptions, $sectionData['options']);
            }
        }

        return $categoryOptions;
    }

    protected function getDiscountMessageOptions($panelId)
    {
        $sections = array(
            "{$panelId}-global"                                => array(
                'title'    => __('Global options', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 5,
                'options'  => array(
                    'amount_saved_label' => array(
                        'label'    => __('Amount saved label', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'  => __("Amount Saved", 'advanced-dynamic-pricing-for-woocommerce'),
                        'priority' => 5,

                        'apply_type' => 'filter',
                    ),
                )
            ),
            "{$panelId}-" . DiscountMessage::CONTEXT_CART      => array(
                'title'    => __('Cart', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 10,
                'options'  => array(
                    'enable'   => array(
                        'label'             => __('Enable', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => false,
                        'priority'          => 5,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                    ),
                    'position' => array(
                        'label'        => __('Position', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => "woocommerce_cart_totals_before_order_total",
                        'control_type' => 'select',
                        'choices'      => apply_filters("wdp_" . DiscountMessage::CONTEXT_CART . "_discount_message_places",
                            array(
                                'woocommerce_cart_totals_before_order_total' => __('Before order total',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                                'woocommerce_cart_totals_after_order_total'  => __('After order total',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                            )),
                        'priority'     => 10,

                        'apply_type' => 'filter',
                    ),
                )
            ),
            "{$panelId}-" . DiscountMessage::CONTEXT_MINI_CART => array(
                'title'    => __('Mini Cart', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 15,
                'options'  => array(
                    'enable'   => array(
                        'label'             => __('Enable', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => false,
                        'priority'          => 5,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                    ),
                    'position' => array(
                        'label'        => __('Position', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => "woocommerce_mini_cart_contents",
                        'control_type' => 'select',
                        'choices'      => apply_filters("wdp_" . DiscountMessage::CONTEXT_MINI_CART . "_discount_message_places",
                            array(
                                'woocommerce_before_mini_cart_contents' => __('Before mini cart contents',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                                'woocommerce_mini_cart_contents'        => __('After mini cart contents',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                            )),
                        'priority'     => 10,

                        'apply_type' => 'filter',
                    ),
                )
            ),
            "{$panelId}-" . DiscountMessage::CONTEXT_CHECKOUT  => array(
                'title'    => __('Checkout', 'advanced-dynamic-pricing-for-woocommerce'),
                'priority' => 20,
                'options'  => array(
                    'enable'   => array(
                        'label'             => __('Enable', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'           => false,
                        'priority'          => 5,
                        'control_type'      => 'checkbox',
                        'sanitize_callback' => 'wc_string_to_bool',

                        'apply_type' => 'filter',
                    ),
                    'position' => array(
                        'label'        => __('Position', 'advanced-dynamic-pricing-for-woocommerce'),
                        'default'      => "woocommerce_review_order_after_cart_contents",
                        'control_type' => 'select',
                        'choices'      => apply_filters("wdp_" . DiscountMessage::CONTEXT_CHECKOUT . "_discount_message_places",
                            array(
                                'woocommerce_review_order_before_cart_contents' => __('Before cart contents',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                                'woocommerce_review_order_after_cart_contents'  => __('After cart contents',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                                'woocommerce_review_order_after_order_total'    => __('After order total',
                                    'advanced-dynamic-pricing-for-woocommerce'),
                            )),
                        'priority'     => 10,

                        'apply_type' => 'filter',
                    ),
                )
            ),
        );

        return $sections;
    }

    public function customizeCss()
    {
        $css         = array();
        $attrOptions = get_theme_mod(self::$optionName);
        $context     = $this->context;
        $important   = ! $context->is($context::CUSTOMIZER) ? '! important' : "";

        $isProduct      = $context->is($context::WC_PRODUCT_PAGE);
        $productLoop    = $context->is($context::PRODUCT_LOOP);
        $isCategoryPage = $context->is($context::WC_CATEGORY_PAGE);

        $panelId = $isProduct || $productLoop ? 'wdp_product_bulk_table' : ($isCategoryPage ? 'wdp_category_bulk_table' : false);
        if (empty($panelId) || empty($this->options[$panelId])) {
            return;
        }
        $panelData = $this->options[$panelId];

        if (empty($panelData['options']) && ! is_array($panelData['options'])) {
            return;
        }
        foreach ($panelData['options'] as $sectionId => $sectionSettings) {
            foreach ($sectionSettings['options'] as $optionId => $optionData) {
                if (empty($optionData['apply_type'])) {
                    continue;
                }
                if ('css' == $optionData['apply_type'] && $optionData['selector']) {
                    $default = $optionData['default'];
                    if ( ! isset($attrOptions[$panelId][$sectionId][$optionId])) {
                        $optionValue = $default;
                    } else {
                        $optionValue = $attrOptions[$panelId][$sectionId][$optionId];
                    }
                    if ( ! empty($optionData['css_option_value'])) {
                        if ($optionValue) {
                            $css[] = sprintf("%s { %s: %s ! important}", $optionData['selector'],
                                $optionData['css_option_name'], $optionData['css_option_value']);
                        }
                    } else {
                        if ($optionValue) {
                            $css[] = sprintf("%s { %s: %s %s}", $optionData['selector'],
                                $optionData['css_option_name'], $optionValue, $important);
                        }
                    }
                }
            }
        }
        ?>
        <style type="text/css">
            <?php echo join(' ', $css); ?>
        </style>
        <?php

    }

    public function getThemeOptions()
    {
        if ( ! did_action('wp_loaded')) {
            _doing_it_wrong(__FUNCTION__,
                sprintf(__('%1$s should not be called before the %2$s action.', 'woocommerce'),
                    __NAMESPACE__ . '/Customizer::get_theme_options', 'wp_loaded'), '2.2.2');

            return array();
        }

        $result      = array();
        $attrOptions = get_theme_mod(self::$optionName);

        foreach ($this->options as $panelId => $panelData) {
            if (empty($panelData['options']) || empty($panelData['key'])) {
                continue;
            }

            $key = $panelData['key'];

            $sectionOptions = array();
            foreach ($panelData['options'] as $sectionId => $sectionSettings) {
                if ( ! isset($sectionSettings['options'])) {
                    continue;
                }

                $sectionKey = str_replace($panelId . '-', "", $sectionId);

                $options = array();
                foreach ($sectionSettings['options'] as $optionId => $optionData) {
                    if (empty($optionData['apply_type'])) {
                        continue;
                    }

                    // font options
                    $optionKey = str_replace($panelId . '-', "", $optionId);

                    $default = $optionData['default'];
                    if ( ! isset($attrOptions[$panelId][$sectionId][$optionId])) {
                        $attrOption = $default;
                    } else {
                        $attrOption = $attrOptions[$panelId][$sectionId][$optionId];
                    }

                    /**
                     * Do not apply saved value which not in choices
                     * e.g. delete add_action
                     */
                    $choices = isset($optionData['choices']) ? $optionData['choices'] : array();
                    if ($choices && empty($choices[$attrOption])) {
                        $attrOption = $default;
                    }

                    $options[$optionKey] = $attrOption;
                }

                $sectionOptions[$sectionKey] = $options;
            }

            $result[$key] = $sectionOptions;
        }

        return $result;
    }

    /**
     * @param WP_Customize_Manager $wpCustomize Theme Customizer object.
     */
    public function add_sections(WP_Customize_Manager $wpCustomize)
    {
        foreach ($this->options as $panel_id => $panel_data) {
            $panel_title   = ! empty($panel_data['title']) ? $panel_data['title'] : null;
            $panel_options = ! empty($panel_data['options']) ? $panel_data['options'] : null;

            if ( ! $panel_title || ! $panel_options) {
                continue;
            }

            $wpCustomize->add_panel($panel_id, array(
                'title'    => $panel_title,
                'priority' => ! empty($panel_data['priority']) ? $panel_data['priority'] : 200,
            ));

            foreach ($panel_options as $section_id => $section_settings) {
                $this->add_section($wpCustomize, $section_id, $section_settings, $panel_id);
            }
        }

    }


    /**
     * @param WP_Customize_Manager $wp_customize Theme Customizer object.
     * @param string $section_id Parent menu id
     * @param array $sectionSettings (See above)
     * @param string $panelId
     */
    protected function add_section(
        WP_Customize_Manager $wp_customize,
        string $section_id,
        array $sectionSettings,
        string $panelId
    ) {
        if ( ! empty($sectionSettings['options'])) {
            $wp_customize->add_section($section_id, array(
                'title'    => $sectionSettings['title'],
                'priority' => isset($sectionSettings['priority']) ? $sectionSettings['priority'] : 20,
                'panel'    => $panelId,
            ));

            uasort($sectionSettings['options'], function ($item1, $item2) {
                if ($item1['priority'] == $item2['priority']) {
                    return 0;
                }

                return $item1['priority'] < $item2['priority'] ? -1 : 1;
            });

            foreach ($sectionSettings['options'] as $option_id => $data) {
                $setting = sprintf('%s[%s][%s][%s]', self::$optionName, $panelId, $section_id, $option_id);
                $this->add_option($wp_customize, $setting, $section_id, $data);
            }
        }
    }

    /**
     * @param WP_Customize_Manager $wpCustomize Theme Customizer object.
     * @param string $setting Option id
     * @param string $sectionId Parent menu id
     * @param array $data Option data
     */
    protected function add_option(WP_Customize_Manager $wpCustomize, $setting, $sectionId, $data)
    {
        $priority    = ! empty($data['priority']) ? $data['priority'] : 20;
        $description = ! empty($data['description']) ? $data['description'] : "";

        $transport = 'refresh';
        if ($data['apply_type'] == 'css') {
            $transport = 'postMessage';
        }

        $wpCustomize->add_setting($setting, array(
            'default'    => $data['default'],
            'capability' => 'edit_theme_options',
            'transport'  => $transport,
            'priority'   => $priority,
        ));


        if ( ! empty($data['control_class']) && class_exists($data['control_class'])) {
            $class   = $data['control_class'];
            $control = new $class($wpCustomize, $setting, array(
                'label'       => $data['label'],
                'description' => $description,
                'section'     => $sectionId,
                'settings'    => $setting,
                'priority'    => $priority,
            ));
            $wpCustomize->add_control($control);
        } else {
            $wpCustomize->add_control($setting, array(
                'label'       => $data['label'],
                'description' => $description,
                'section'     => $sectionId,
                'settings'    => $setting,
                'type'        => isset($data['control_type']) ? $data['control_type'] : 'text',
                'choices'     => isset($data['choices']) ? $data['choices'] : array(),
            ));
        }
    }

    public function customizerControlsScripts()
    {
        $baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
        wp_enqueue_style('wc-plc-customizer-control-css', $baseVersionUrl . 'assets/css/customize-controls.css',
            array(), WC_ADP_VERSION);
        wp_enqueue_script('wc-plc-customizer-control-js', $baseVersionUrl . 'assets/js/customize-controls.js', array(),
            WC_ADP_VERSION);
    }

    public function customizePreviewInit()
    {
        $baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";
        wp_enqueue_script('wc-plc-customizer-preview-js', $baseVersionUrl . 'assets/js/wdp-customize-preview.js',
            array(), WC_ADP_VERSION, true);

        $cssControls = array();
        foreach ($this->options as $panelId => $panelData) {
            if (empty($panelData['options'])) {
                continue;
            }

            foreach ($panelData['options'] as $sectionId => $sectionSettings) {
                if (isset($sectionSettings['options'])) {
                    foreach ($sectionSettings['options'] as $option_id => $optionData) {
                        if (empty($optionData['apply_type'])) {
                            continue;
                        }

                        if ('css' == $optionData['apply_type']) {
                            $control_id     = sprintf('%s[%s][%s][%s]', self::$optionName, $panelId, $sectionId,
                                $option_id);
                            $selector       = $optionData['selector'];
                            $cssOptionName  = $optionData['css_option_name'];
                            $cssOptionValue = isset($optionData['css_option_value']) ? $optionData['css_option_value'] : null;

                            $cssControls[$control_id] = array(
                                'selector'         => $selector,
                                'css_option_name'  => $cssOptionName,
                                'css_option_value' => $cssOptionValue,
                            );
                        }
                    }
                }
            }
        }

        $localize = array(
            'css_controls' => $cssControls,
        );
        wp_localize_script('wc-plc-customizer-preview-js', 'wdp_customize_preview', $localize);
    }

}
