<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://www.tplugins.com/
 * @since             1.0.0
 * @package           Woocommerce_Product_Gallery
 *
 * @wordpress-plugin
 * Plugin Name:       TP WooCommerce Product Gallery
 * Plugin URI:        https://www.tplugins.com/
 * Description:       Increase your sales by change woocommerce default product gallery to beautiful gallery with a lot of new features.
 * Version:           1.0.6
 * Author:            TP Plugins
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       tp-woocommerce-product-gallery
 * Domain Path:       /languages
 * WC requires at least: 3.0
 * WC tested up to: 5.1.0
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Check if WooCommerce is active
 **/
if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	//die( 'Hey, WooCommerce is required' );
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define('TP_WOOCOMMERCE_PRODUCT_GALLERY_VERSION', '1.0.6');
define('TPWPG_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('TPWPG_PLUGIN_HOME', 'https://www.tplugins.com/');
define('TPWPG_PLUGIN_NAME', 'TP Woocommerce Product Gallery');
define('TPWPG_PLUGIN_API', 'https://www.tplugins.com/tp-services');
define('TPWPG_PLUGIN_PRO_SLUG', 'tp-woocommerce-product-gallery-pro');

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-woocommerce-product-gallery-activator.php
 */
function activate_woocommerce_product_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-gallery-activator.php';
	Woocommerce_Product_Gallery_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-woocommerce-product-gallery-deactivator.php
 */
function deactivate_woocommerce_product_gallery() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-gallery-deactivator.php';
	Woocommerce_Product_Gallery_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_woocommerce_product_gallery' );
register_deactivation_hook( __FILE__, 'deactivate_woocommerce_product_gallery' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-woocommerce-product-gallery.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_woocommerce_product_gallery() {

	$plugin = new Woocommerce_Product_Gallery();
	$plugin->run();

}
run_woocommerce_product_gallery();