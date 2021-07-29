<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class Woocommerce_Product_Gallery_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'tp-woocommerce-product-gallery',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
