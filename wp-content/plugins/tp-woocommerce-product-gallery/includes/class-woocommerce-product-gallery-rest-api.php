<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class TP_Woocommerce_Product_Gallery_Rest_Api {

	public function __construct() {
		//echo 'TP_Woocommerce_Product_Gallery_Rest_Api was called';
	}

	public static function get_api_key() {
		return get_option('tpwpg_lkey');
	}

	public static function get_api_state() {
		return get_option('tpwpg_lkey_state');
	}

	public static function get_api_expiresAt() {
		return get_option('tpwpg_lkey_expiresAt');
	}

	public static function set_api_key($license_key) {
		update_option('tpwpg_lkey', $license_key);
	}

	public static function set_api_state($state = 0) {
		update_option('tpwpg_lkey_state', $state);
	}

	public static function set_api_expiresAt($expiresAt) {
		update_option('tpwpg_lkey_expiresAt', $expiresAt);
	}

	public static function delete_api_expiresAt() {
		delete_option('tpwpg_lkey_expiresAt');
	}

	public static function check_license_key_expires() {
		$today = date('Y-m-d');
		if ( self::get_api_expiresAt() >= $today ) {
			return true;
		}
		return false;
	}

	public static function license_key_expires_message() {
		//return '<div class="license_key_expires_message">The license key for '.TPWPG_PLUGIN_NAME.' seems invalid. Please enter a valid license key. You can get it from our website</div>';
		if(TP_Woocommerce_Product_Gallery_Rest_Api::get_api_key()){
			return '<div class="tpwpg_full tpwpg_clear"><div class="license_key_expires_message">Your license key is expired. please renew the license key to get the latest updates.</div></div>';
		}
	}

	public static function check_api_state() {
		if ( self::get_api_state() ) {
			return true;
		}
		return false;
	}

}

//remove_action( 'woocommerce_before_single_product_summary', array( 'Woocommerce_Product_Gallery_Public', 'build_gallery_images' ), 20 );