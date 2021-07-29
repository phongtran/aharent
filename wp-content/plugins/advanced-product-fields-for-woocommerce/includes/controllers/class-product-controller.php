<?php

namespace SW_WAPF\Includes\Controllers {

    use SW_WAPF\Includes\Classes\Enumerable;
    use SW_WAPF\Includes\Classes\Field_Groups;
    use SW_WAPF\Includes\Classes\Fields;
	use SW_WAPF\Includes\Classes\Helper;
	use SW_WAPF\Includes\Classes\Html;
    use SW_WAPF\Includes\Models\Field;

    if (!defined('ABSPATH')) {
        die;
    }

    class Product_Controller
    {

        public function __construct()
        {
            add_action('woocommerce_before_add_to_cart_button',             [$this, 'display_field_groups']);

            add_filter('woocommerce_add_to_cart_validation',                [$this, 'validate_cart_data'], 10, 4);

            add_action('woocommerce_before_calculate_totals',               [$this,'adjust_cart_item_pricing']);

            add_filter( 'woocommerce_add_cart_item_data',                   [$this, 'add_fields_to_cart_item'], 10, 3 );

            add_filter('woocommerce_get_item_data',                         [$this,'display_fields_on_cart_and_checkout'],10, 2);

            add_action( 'woocommerce_checkout_create_order_line_item',      [$this,'create_order_line_item'], 20, 4);

            add_filter('woocommerce_product_add_to_cart_text',              [$this, 'change_add_to_cart_text'], 10, 2);

            add_filter('woocommerce_product_supports',                      [$this, 'check_product_support'], 10, 3);

            add_filter('woocommerce_product_add_to_cart_url',               [$this, 'set_add_to_cart_url'], 10, 2);

        }

        public function set_add_to_cart_url($url, $product) {

            if($product->get_type() === 'external')
                return $url;

            if(Field_Groups::product_has_field_group($product))
                return $product->get_permalink();

            return $url;
        }

        public function check_product_support($support, $feature, $product)
        {
            if($feature === 'ajax_add_to_cart' && Field_Groups::product_has_field_group($product) )
                $support = false;

            return $support;
        }

        public function change_add_to_cart_text($text, $product) {

            if(!$product->is_in_stock())
                return $text;

            if (in_array($product->get_type(), ['grouped', 'external']))
                return $text;

            if( Field_Groups::product_has_field_group($product) )
	            return esc_html(get_option('wapf_add_to_cart_text', __('Select options','sw-wapf')));

            return $text;

        }

	    public function validate_cart_data($passed, $product_id, $qty, $variation_id = null) {

		    $field_groups = Field_Groups::get_field_groups_of_product($product_id);
		    if(empty($field_groups))
			    return $passed;

		    if(!isset($_REQUEST['wapf_field_groups'])) {
			    wc_add_notice(esc_html(__('Error adding product to cart.','sw-wapf')), 'error');
			    return false;
		    }

		    $field_group_ids = explode(',', sanitize_text_field($_REQUEST['wapf_field_groups']));
		    foreach($field_group_ids as $id) {
			    if(!Enumerable::from($field_groups)->any(function($x)use($id){ return $id == $x->id; })) {
				    wc_add_notice(esc_html(__('Error adding product to cart.','sw-wapf')), 'error');
				    return false;
			    }
		    }

		    foreach($field_groups as $group) {
			    foreach($group->fields as $field) {

				    if(!Fields::should_field_be_filled_out($group,$field))
					    continue;

				    $value = Fields::get_raw_field_value_from_request($field, 0, true);

				    if(empty($value)) {
					    wc_add_notice(__('The field "' . esc_html($field->label) . '" is required.', 'advanced-product-fields-for-woocommerce'), 'error');
					    return false;
				    }

			    }
		    }

		    return $passed;

	    }

        public function create_order_line_item($item, $cart_item_key, $values, $order) {

            if (empty($values['wapf']))
                return;

            foreach ($values['wapf'] as $field) {

                if(!empty($field['value']))
                    $item->add_meta_data( $field['label'], $field['value']);
            }

        }

        public function display_field_groups() {

	            global $product;
	            if(!$product)
	                return;

            if(in_array($product->get_type(),['grouped','external']))
                return;

            $field_groups = Field_Groups::get_valid_field_groups('product');

            $product_field_group = get_post_meta($product->get_id(),'_wapf_fieldgroup', true);

            if($product_field_group)
                array_unshift($field_groups, Field_Groups::process_data($product_field_group));

            if(empty($field_groups))
                return;

            $group_ids = [];

            echo '<div class="wapf-wrapper">';
            foreach ($field_groups as $field_group) {

                $variation_rules = [];

                if($product->is_type('variable')) {
                    $valids = Field_Groups::get_valid_rule_groups($field_group);

                    $variation_rules = [];

                    foreach ($valids as $rule_group) {
                        $filtered_rules = Enumerable::from($rule_group->rules)->where(function($rule) {
                            return $rule->subject === 'product_variation';
                        })->select(function($rule){
                            return [
                                'condition' => $rule->condition,
                                'values'     => Enumerable::from((array)$rule->value)->select(function($value) {
                                    return intval($value['id']);
                                })->toArray()
                            ];
                        })->toArray();

                        if(!empty($filtered_rules))
                            $variation_rules[] = $filtered_rules;
                    }

                }

                $group_ids[] = $field_group->id;

                $data = [
                    'has_variation_logic'   => !empty($variation_rules),
                    'variation_rules'       => $variation_rules
                ];

                echo Html::field_group($product, $field_group, $data);

            }

            echo '<input type="hidden" value="'.implode(',',$group_ids).'" name="wapf_field_groups"/>';
            echo '</div>';
            Html::product_totals($product);

        }

        public function add_fields_to_cart_item($cart_item_data, $product_id, $variation_id) {

            if(empty($_REQUEST['wapf']) || isset($cart_item_data['wapf']) || !isset($_REQUEST['wapf_field_groups']))
                return $cart_item_data;

            if(!is_array($_REQUEST['wapf']))
                return $cart_item_data;

            $field_groups = Field_Groups::get_by_ids(explode(',', sanitize_text_field($_REQUEST['wapf_field_groups'])));

            $fields = Enumerable::from($field_groups)->merge(function($x){return $x->fields; })->toArray();

            $quantity = empty($_REQUEST['quantity']) ? 1 : wc_stock_amount($_REQUEST['quantity']);

            $wapf_data = [];

	        $product = wc_get_product(empty($variation_id) ? $product_id : $variation_id);

            foreach($_REQUEST['wapf'] as $raw_field_id => $field_value) {
				if($field_value === '')
					continue;

                $field_id = str_replace('field_','',$raw_field_id);

                $field = Enumerable::from($fields)->firstOrDefault(function ($x) use ($field_id) {
                    return $x->id === $field_id;
                });

                if(!$field)
                    continue;

                $wapf_data[] = self::to_cart_fields($field, 0, $quantity, $product);

            }
            $wapf_data['clones'] = [];

            if(!empty($wapf_data))
                $cart_item_data['wapf'] = $wapf_data;

            return $cart_item_data;

        }

        public function adjust_cart_item_pricing($cart_obj) {

            if (is_admin() && ! defined('DOING_AJAX'))
                return;

            foreach( $cart_obj->get_cart() as $key=>$item ) {

                if(empty($item['wapf']))
                    continue;

                $quantity = empty($item['quantity']) ? 1 : wc_stock_amount($item['quantity']);
                $product = wc_get_product($item['variation_id'] ? $item['variation_id'] : $item['product_id']);
                $base = Helper::get_product_base_price($product);
                $options_total = 0;

                foreach ($item['wapf'] as $field) {
                    if(!empty($field['price'])) {
                        foreach ($field['price'] as $price) {

                            if($price['value']  === 0)
                                continue;

                            $options_total = $options_total + Fields::do_pricing($price['value'],$quantity);

                        }
                    }

                }

                if($options_total > 0)
                    $item['data']->set_price($base + $options_total);

            }

        }

        public function display_fields_on_cart_and_checkout($item_data, $cart_item) {

            if(empty($cart_item['wapf']) || !is_array($cart_item['wapf']) )
                return $item_data;

            if (!is_array($item_data))
                $item_data = [];

            if((is_cart() && get_option('wapf_settings_show_in_cart','yes') === 'yes') || (is_checkout() && get_option('wapf_settings_show_in_checkout','yes') === 'yes') ) {

                foreach($cart_item['wapf'] as $field) {
                    if(empty($field['value_cart']))
                        continue;

                    $item_data[] = [
                        'key'   => $field['label'], 
                        'value' => $field['value_cart']
                    ];

                }
            }
            return $item_data;

        }

        #region Private Helpers

        private function to_cart_fields(Field $field, $clone_idx = 0, $quantity = 1, $product) {

            $raw_value = Fields::get_raw_field_value_from_request($field, $clone_idx);

            $price_addition = []; 

            if( $field->pricing_enabled() ) {
                $price_addition = Fields::pricing_value($field, $raw_value);
            }

            return [
                'id'                => $field->id,
                'value'             => Fields::value_to_string($field, $raw_value, $price_addition > 0, $product),
                'value_cart'        => Fields::value_to_string($field, $raw_value, $price_addition > 0, $product,'cart'),
                'price'             => $price_addition,
                'qty'               => $quantity,
                'qty_based'         => false,
                'label'             => esc_html($field->label)
            ];
        }

        #endregion

    }
}