<?php

/**
 * Fired during plugin activation
 *
 * @link       https://www.tplugins.com/
 * @since      1.0.0
 *
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 */

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Woocommerce_Product_Gallery
 * @subpackage Woocommerce_Product_Gallery/includes
 * @author     TP Plugins <tp.sites.info@gmail.com>
 */
class Woocommerce_Product_Gallery_Activator {

	/**
	 * Short Description. (use period)
	 *
	 * Long Description.
	 *
	 * @since    1.0.0
	 */
	public static function activate() {
		add_option( 'tpwpg_thumbnail', 1 );
		//add_option( 'tpwpg_slidesToShow', 4 );
		//add_option( 'tpwpg_imageSize', 'medium_large' );
		add_option( 'tpwpg_dots', 0 );
		add_option( 'tpwpg_accessibility', 1 );
		
		// add_option( 'tpwpg_autoplay', 0 );
		// add_option( 'tpwpg_autoplaySpeed', 3000 );
		add_option( 'tpwpg_arrows', 1 );
		add_option( 'tpwpg_speed', 300 );
		add_option( 'tpwpg_verticalSwiping', 0 );
		add_option( 'tpwpg_vertical', 0 );

		add_option( 'tpwpg_draggable', 1 );
		add_option( 'tpwpg_fade', 0 );
		add_option( 'tpwpg_focusOnSelect', 1 );
		add_option( 'tpwpg_adaptiveHeight', 1 );

		add_option( 'tpwpg_centerMode', 0 );
		add_option( 'tpwpg_centerPadding', '50px' );
		add_option( 'tpwpg_infinite', 1 );
		add_option( 'tpwpg_arrow_background', '#000000' );
		add_option( 'tpwpg_arrow_color', '#ffffff' );
		add_option( 'tpwpg_icons_background', '#000000' );
		add_option( 'tpwpg_icons_color', '#ffffff' );
		add_option( 'tpwpg_active_zoom', 1 );
		add_option( 'tpwpg_active_lightbox', 1 );
		add_option( 'tpwpg_lightbox_speed', 600 );
		add_option( 'tpwpg_lightbox_hideBarsDelay', 6000 );
		add_option( 'tpwpg_lightbox_mode', 'lg-slide' );
		add_option( 'tpwpg_lightbox_closable', 1 );
		add_option( 'tpwpg_lightbox_loop', 1 );
		add_option( 'tpwpg_lightbox_mousewheel', 1 );
		add_option( 'tpwpg_lightbox_product_name', 1 );
		add_option( 'tpwpg_lightbox_loadYoutubeThumbnail', 1 );

		

	}

}
