<?php

/**
 * Fired during plugin deactivation
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 */

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class Woocommerce_Product_Gallery_Deactivator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function deactivate() {

		$domain = get_option('siteurl');

		$api_params = array( 
			'version'     => TP_WOOCOMMERCE_PRODUCT_GALLERY_VERSION,
			'pname'       => TPWPG_PLUGIN_NAME,
			'activate'    => 0,
			'deactivate'  => 1,
			'domain'      => $domain,
		);

		$response = wp_remote_get( add_query_arg( $api_params, TPWPG_PLUGIN_API.'/process-free-plugin.php' ), array( 
			'timeout' => 20, 
			'sslverify' => false
		));
	}

}
