<?php
	if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

    class Flexible_Checkout_Fields_Settings {

	    /**
	     * Flexible_Checkout_Fields_Settings constructor.
	     *
	     * @param Flexible_Checkout_Fields_Plugin $plugin .
	     */
        public function __construct( $plugin ) {

            $this->plugin = $plugin;

            add_action( 'init', array($this, 'init_polylang') );
            add_action( 'admin_init', array($this, 'init_wpml') );
        }

        function init_polylang() {
        	if ( function_exists( 'pll_register_string' ) ) {
        		$settings = get_option('inspire_checkout_fields_settings', array() );
        		$checkout_field_type = $this->plugin->get_fields();
        		foreach ( $settings as $section ) {
        			if ( is_array( $section ) ) {
        				foreach ( $section as $field ) {
        					if ( isset( $field['label'] ) && $field['label'] != '' ) {
        						pll_register_string( $field['label'], $field['label'], __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        					}
        					if ( isset( $field['placeholder'] ) && $field['placeholder'] != '' ) {
        						pll_register_string( $field['placeholder'], $field['placeholder'], __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        					}
					        if ( isset( $field['default'] ) && $field['default'] != '' ) {
						        pll_register_string( $field['default'], $field['default'], __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
					        }
        					if ( isset( $field['type'] ) && isset( $checkout_field_type[$field['type']]['has_options'] ) && $checkout_field_type[$field['type']]['has_options'] ) {
        						$array_options = explode("\n", $field['option']);
        						if ( !empty( $array_options ) ){
        							foreach ( $array_options as $option ) {
        								$tmp = explode(':', $option, 2);
        								$option_label = trim( $tmp[1] );
        								pll_register_string( $option_label, $option_label, __('Flexible Checkout Fields', 'flexible-checkout-fields' ) );
        								unset($tmp);
        							}
        						}
        					}
        				}
        			}
        		}
        	}
        }

        function init_wpml() {
        	if ( function_exists( 'icl_register_string' ) ) {
        		$icl_language_code = defined('ICL_LANGUAGE_CODE') ? ICL_LANGUAGE_CODE : get_bloginfo('language');
        		$settings = get_option('inspire_checkout_fields_settings', array() );
        		$checkout_field_type = $this->plugin->get_fields();
        		foreach ( $settings as $section ) {
        			if ( is_array( $section ) ) {
        				foreach ( $section as $field ) {
        					if ( isset( $field['label'] ) && $field['label'] != '' ) {
        						icl_register_string( 'flexible-checkout-fields', $field['label'], $field['label'], false, $icl_language_code );
        					}
        					if ( isset( $field['placeholder'] ) && $field['placeholder'] != '' ) {
        						icl_register_string( 'flexible-checkout-fields', $field['placeholder'], $field['placeholder'], false, $icl_language_code );
        					}
					        if ( isset( $field['default'] ) && $field['default'] != '' ) {
						        icl_register_string( 'flexible-checkout-fields', $field['default'], $field['default'], false, $icl_language_code );
					        }
        					if ( isset( $field['type'] ) && isset( $checkout_field_type[$field['type']]['has_options'] ) && $checkout_field_type[$field['type']]['has_options'] && $field['option'] ) {
        						$array_options = explode("\n", $field['option']);
        						if ( !empty( $array_options ) ){
        							foreach ( $array_options as $option ) {
        								$tmp = explode(':', $option, 2);
        								$option_label = trim( $tmp[1] );
        								icl_register_string( 'flexible-checkout-fields', $option_label, $option_label, false, $icl_language_code );
        								unset($tmp);
        							}
        						}
        					}
        				}
        			}
        		}
        	}
        }
    }
