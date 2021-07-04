<?php

namespace ADP\BaseVersion\Includes;

use ADP\BaseVersion\Includes\Common\Database;
use ADP\BaseVersion\Includes\External\WC\WcCartItemFacade;
use WC_Coupon;
use WC_Order;
use WC_Order_Item_Product;

if ( ! defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class Frontend
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

        add_action('wp_print_styles', array($this, 'loadFrontendAssets'));

        if (apply_filters('wdp_checkout_update_order_review_process_enabled', true)) {
            add_action('woocommerce_checkout_update_order_review',
                array($this, 'woocommerce_checkout_update_order_review'), 100);
        }

        add_filter('woocommerce_cart_id', array($this, 'woocommerceCartId'), 10, 5);
        add_filter('woocommerce_add_to_cart_sold_individually_found_in_cart',
            array($this, 'woocommerceAddToCartSoldIndividuallyFoundInCart'), 10, 5);

        add_filter('woocommerce_order_again_cart_item_data', function ($cart_item, $item, $order) {
            $load_as_immutable = apply_filters('wdp_order_again_cart_item_load_with_order_deals', false, $cart_item,
                $item, $order);

            if ($load_as_immutable) {
                $rules = $item->get_meta('_wdp_rules');
                if ( ! empty($rules)) {
                    $cart_item['wdp_rules']     = $rules;
                    $cart_item['wdp_immutable'] = true;
                }
            }

            return $cart_item;
        }, 10, 3);

        add_action('woocommerce_checkout_create_order_line_item_object',
            array($this, 'saveInitialPpriceToOrderItem'), 10, 4);


        if ($this->context->getOption('hide_coupon_word_in_totals')) {
            /**
             * Same hook is added by Settings::__construct().
             * In this case hook is fired during http/https requests.
             */
            add_filter('woocommerce_cart_totals_coupon_label', function ($html, $coupon) {
                /**
                 * @var WC_Coupon $coupon
                 */
                if ($coupon->get_virtual() && ($adp_meta = $coupon->get_meta('adp', true))) {
                    if ( ! empty($adp_meta['parts']) && count($adp_meta['parts']) < 2) {
                        $adp_coupon = array_pop($adp_meta['parts']);
                        $html       = $adp_coupon->getLabel();
                    } else {
                        $html = $coupon->get_code();
                    }
                }

                return $html;
            }, 5, 2);
        }

        add_filter('woocommerce_cart_totals_coupon_html', function ($coupon_html, $coupon, $discount_amount_html) {
            /**
             * @var WC_Coupon $coupon
             */
            if ($coupon->get_virtual() && $coupon->get_meta('adp', true)) {
                $coupon_html = preg_replace('#<a(.*?)class="woocommerce-remove-coupon"(.*?)</a>#', '', $coupon_html);
            }

            return $coupon_html;
        }, 10, 3);

        /** Additional css class for free item line */
        add_filter('woocommerce_cart_item_class', function ($str_classes, $cart_item, $cart_item_key) {
            $classes = explode(' ', $str_classes);
            if ( ! empty($cart_item['wdp_gifted'])) {
                $classes[] = 'wdp_free_product';
            }

            if ( ! empty($cart_item['wdp_rules']) && (float)$cart_item['data']->get_price() == 0) {
                $classes[] = 'wdp_zero_cost_product';
            }

            return implode(' ', $classes);
        }, 10, 3);
    }

    /**
     * @param WC_Order_Item_Product $item
     * @param string $cartItemKey
     * @param array $values
     * @param WC_Order $order
     *
     * @return WC_Order_Item_Product
     */
    public function saveInitialPpriceToOrderItem(
        $item,
        $cartItemKey,
        $values,
        $order
    ) {
        if ( ! empty($values['wdp_rules'])) {
            $item->add_meta_data('_wdp_rules', $values['wdp_rules']);
        }

        return $item;
    }

    /**
     * @param string $templateName
     * @param array $args
     * @param string $templatePath
     *
     * @return false|string
     */
    public static function wdpGetTemplate($templateName, $args = array(), $templatePath = '')
    {
        if ( ! empty($args) && is_array($args)) {
            extract($args);
        }

        $fullTemplatePath = trailingslashit(WC_ADP_PLUGIN_TEMPLATES_PATH);

        if ($templatePath) {
            $fullTemplatePath .= trailingslashit($templatePath);
        }

        $fullExternalTemplatePath = locate_template(array(
            'advanced-dynamic-pricing-for-woocommerce/' . trailingslashit($templatePath) . $templateName,
            'advanced-dynamic-pricing-for-woocommerce/' . $templateName,
        ));

        if ($fullExternalTemplatePath) {
            $fullTemplatePath = $fullExternalTemplatePath;
        } else {
            $fullTemplatePath .= $templateName;
        }

        ob_start();
        include $fullTemplatePath;
        $templateContent = ob_get_clean();

        return $templateContent;
    }

    public function loadFrontendAssets()
    {
        $context        = $this->context;
        $baseVersionUrl = WC_ADP_PLUGIN_URL . "/BaseVersion/";

        wp_enqueue_style('wdp_pricing-table', $baseVersionUrl . 'assets/css/pricing-table.css', array(),
            WC_ADP_VERSION);
        wp_enqueue_style('wdp_deals-table', $baseVersionUrl . 'assets/css/deals-table.css', array(), WC_ADP_VERSION);

        if ($context->is($context::WC_PRODUCT_PAGE) || $context->is($context::PRODUCT_LOOP)) {
            wp_enqueue_script('wdp_deals', $baseVersionUrl . 'assets/js/frontend.js', array(), WC_ADP_VERSION);
        }

        if (Database::isConditionTypeActive(array('customer_shipping_method'))) {
            wp_enqueue_script('wdp_update_cart', $baseVersionUrl . 'assets/js/update-cart.js', array('wc-cart'),
                WC_ADP_VERSION);
        }

        $scriptData = array(
            'ajaxurl'               => admin_url('admin-ajax.php'),
            'update_price_with_qty' => $context->getOption('update_price_with_qty') && ! $context->getOption('do_not_modify_price_at_product_page'),
            'js_init_trigger'       => apply_filters('wdp_bulk_table_js_init_trigger', ""),
        );

        wp_localize_script('wdp_deals', 'script_data', $scriptData);
    }

    private $lastVariation = array();
    private $lastVariationHash = array();

    /**
     * The only way to snatch $variation before woocommerce_add_to_cart_sold_individually_found_in_cart()
     *
     * @param string $hash
     * @param int $productId
     * @param int $variation_id
     * @param array $variation
     * @param array $cartItemData
     *
     * @return string
     */
    public function woocommerceCartId(
        $hash,
        $productId,
        $variation_id,
        $variation,
        $cartItemData
    ) {
        $this->lastVariation     = $variation;
        $this->lastVariationHash = $hash;

        return $hash;
    }

    /**
     * @param bool $found
     * @param int $productId
     * @param int $variationId
     * @param array $cartItemData
     * @param string $cartId
     *
     * @return bool|null
     */
    public function woocommerceAddToCartSoldIndividuallyFoundInCart(
        $found,
        $productId,
        $variationId,
        $cartItemData,
        $cartId
    ) {
        // already found in cart
        if ($found) {
            return true;
        }

        $variation = array();
        if ($this->lastVariationHash && $this->lastVariationHash === $cartId) {
            $variation = $this->lastVariation;
        }

        $wdp_keys           = array(
            'wdp_rules',
            'wdp_gifted',
            'wdp_original_price',
            WcCartItemFacade::KEY_ADP,
        );
        $cartItemData       = array_filter($cartItemData, function ($key) use ($wdp_keys) {
            return ! in_array($key, $wdp_keys);
        }, ARRAY_FILTER_USE_KEY);
        $no_pricing_cart_id = WC()->cart->generate_cart_id($productId, $variationId, $variation, $cartItemData);
        if ( ! $no_pricing_cart_id) {
            return $found;
        }

        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($no_pricing_cart_id === $this->calculateCartItemHashWithoutPricing($cart_item)) {
                return true;
            }
        }

        return $found;
    }

    private function calculateCartItemHashWithoutPricing($cartItemData)
    {
        $productId = isset($cartItemData['product_id']) ? $cartItemData['product_id'] : 0;

        if ( ! $productId) {
            return false;
        }

        $variationId = isset($cartItemData['variation_id']) ? $cartItemData['variation_id'] : 0;
        $variation   = isset($cartItemData['variation']) ? $cartItemData['variation'] : array();

        $wdpKeys = array(
            'wdp_rules',
            'wdp_gifted',
            'wdp_original_price',
        );

        $defaultKeys = array(
            'key',
            'product_id',
            'variation_id',
            'variation',
            'quantity',
            'data',
            'data_hash',
            'line_tax_data',
            'line_subtotal',
            'line_subtotal_tax',
            'line_total',
            'line_tax',
        );

        $cartItemData = array_filter($cartItemData, function ($key) use ($wdpKeys, $defaultKeys) {
            return ! in_array($key, array_merge($wdpKeys, $defaultKeys));
        }, ARRAY_FILTER_USE_KEY);

        return WC()->cart->generate_cart_id($productId, $variationId, $variation, $cartItemData);
    }
}
