<?php
/**
 * Astra functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package Astra
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Define Constants
 */
define( 'ASTRA_THEME_VERSION', '3.6.1' );
define( 'ASTRA_THEME_SETTINGS', 'astra-settings' );
define( 'ASTRA_THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'ASTRA_THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );


/**
 * Minimum Version requirement of the Astra Pro addon.
 * This constant will be used to display the notice asking user to update the Astra addon to the version defined below.
 */
define( 'ASTRA_EXT_MIN_VER', '3.5.0' );

/**
 * Setup helper functions of Astra.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-theme-options.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-theme-strings.php';
require_once ASTRA_THEME_DIR . 'inc/core/common-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-icons.php';

/**
 * Update theme
 */
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-update.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/astra-update-functions.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-theme-background-updater.php';
require_once ASTRA_THEME_DIR . 'inc/theme-update/class-astra-pb-compatibility.php';


/**
 * Fonts Files
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-font-families.php';
if ( is_admin() ) {
	require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts-data.php';
}

require_once ASTRA_THEME_DIR . 'inc/lib/webfont/class-astra-webfont-loader.php';
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-fonts.php';

require_once ASTRA_THEME_DIR . 'inc/dynamic-css/custom-menu-old-header.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/container-layouts.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/astra-icons.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-walker-page.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-enqueue-scripts.php';
require_once ASTRA_THEME_DIR . 'inc/core/class-gutenberg-editor-css.php';
require_once ASTRA_THEME_DIR . 'inc/dynamic-css/inline-on-mobile.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-dynamic-css.php';

/**
 * Custom template tags for this theme.
 */
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-attr.php';
require_once ASTRA_THEME_DIR . 'inc/template-tags.php';

require_once ASTRA_THEME_DIR . 'inc/widgets.php';
require_once ASTRA_THEME_DIR . 'inc/core/theme-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/admin-functions.php';
require_once ASTRA_THEME_DIR . 'inc/core/sidebar-manager.php';

/**
 * Markup Functions
 */
require_once ASTRA_THEME_DIR . 'inc/markup-extras.php';
require_once ASTRA_THEME_DIR . 'inc/extras.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog-config.php';
require_once ASTRA_THEME_DIR . 'inc/blog/blog.php';
require_once ASTRA_THEME_DIR . 'inc/blog/single-blog.php';

/**
 * Markup Files
 */
require_once ASTRA_THEME_DIR . 'inc/template-parts.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-loop.php';
require_once ASTRA_THEME_DIR . 'inc/class-astra-mobile-header.php';

/**
 * Functions and definitions.
 */
require_once ASTRA_THEME_DIR . 'inc/class-astra-after-setup-theme.php';

// Required files.
require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-helper.php';

require_once ASTRA_THEME_DIR . 'inc/schema/class-astra-schema.php';

if ( is_admin() ) {

	/**
	 * Admin Menu Settings
	 */
	require_once ASTRA_THEME_DIR . 'inc/core/class-astra-admin-settings.php';
	require_once ASTRA_THEME_DIR . 'inc/lib/notices/class-astra-notices.php';

	/**
	 * Metabox additions.
	 */
	require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-boxes.php';
}

require_once ASTRA_THEME_DIR . 'inc/metabox/class-astra-meta-box-operations.php';

/**
 * Customizer additions.
 */
require_once ASTRA_THEME_DIR . 'inc/customizer/class-astra-customizer.php';

/**
 * Astra Modules.
 */
require_once ASTRA_THEME_DIR . 'inc/modules/related-posts/class-astra-related-posts.php';

/**
 * Compatibility
 */
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-jetpack.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/woocommerce/class-astra-woocommerce.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/edd/class-astra-edd.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/lifterlms/class-astra-lifterlms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/learndash/class-astra-learndash.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bb-ultimate-addon.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-contact-form-7.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-visual-composer.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-site-origin.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-gravity-forms.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-bne-flyout.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-ubermeu.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-divi-builder.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-amp.php';
require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-yoast-seo.php';
require_once ASTRA_THEME_DIR . 'inc/addons/transparent-header/class-astra-ext-transparent-header.php';
require_once ASTRA_THEME_DIR . 'inc/addons/breadcrumbs/class-astra-breadcrumbs.php';
require_once ASTRA_THEME_DIR . 'inc/addons/heading-colors/class-astra-heading-colors.php';
require_once ASTRA_THEME_DIR . 'inc/builder/class-astra-builder-loader.php';

// Elementor Compatibility requires PHP 5.4 for namespaces.
if ( version_compare( PHP_VERSION, '5.4', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-elementor-pro.php';
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-web-stories.php';
}

// Beaver Themer compatibility requires PHP 5.3 for anonymus functions.
if ( version_compare( PHP_VERSION, '5.3', '>=' ) ) {
	require_once ASTRA_THEME_DIR . 'inc/compatibility/class-astra-beaver-themer.php';
}

require_once ASTRA_THEME_DIR . 'inc/core/markup/class-astra-markup.php';

/**
 * Load deprecated functions
 */
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-filters.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-hooks.php';
require_once ASTRA_THEME_DIR . 'inc/core/deprecated/deprecated-functions.php';


/**
 * Display the custom text field for product data in woocommerce
 * @since 1.0.0
 */
function create_product_custom_field()
{

	// Rental time unit	
	// woocommerce_wp_select( 
	// 	array( 
	// 		'id'      => '_rental_time_unit', 
	// 		'label'   => __( 'Rental time unit', 'woocommerce' ), 
	// 		'options' => array(
	// 			'hour'   => __( 'Hour', 'woocommerce' ),
	// 			'day'   => __( 'Day', 'woocommerce' ),
	// 			'month' => __( 'Month', 'woocommerce' )
	// 			)
	// 		)
	// 	);


	// Security deposit checkbox
	woocommerce_wp_checkbox( 
		array( 
			'id'            => '_security_deposit', 
			'wrapper_class' => 'show_if_simple', 
			'label'         => __('Security deposit', 'woocommerce' ),
			)
		);

	// Security deposit amount
	woocommerce_wp_text_input( 
		array( 
			'id'          => '_security_deposit_amount', 
			'label'       => __( 'Security deposit amount', 'woocommerce' ),
		)
	);

	// Security deposit notes
	woocommerce_wp_textarea_input( 
		array( 
			'id'          => '_security_deposit_notes', 
			'label'       => __( 'Security deposit notes', 'woocommerce' ),
		)
	);

	// Security deposit amount
	woocommerce_wp_text_input( 
		array( 
			'id'          => '_platform_fee', 
			'label'       => __( 'Platform fee', 'woocommerce' ),
		)
	);
	
	
}
add_action( 'woocommerce_product_options_general_product_data', 'create_product_custom_field' );

function cfwc_save_custom_field( $post_id ) {
	$product = wc_get_product( $post_id );
	
	// $security_deposit_checkbox = isset( $_POST['_security_deposit'] ) ? $_POST['_security_deposit'] : '' ;

	// $rental_period_unit = isset( $_POST['_rental_period_unit'])
	
	$security_deposit_amount = isset( $_POST['_security_deposit_amount'] ) ? $_POST['_security_deposit_amount'] : '';
	$product->update_meta_data( '_security_deposit_amount', sanitize_text_field( $security_deposit_amount ) );
	
	$platform_fee = isset ( $_POST['_platform_fee'] ) ? $_POST['_platform_fee'] : '' ;
	$product->update_meta_data( '_platform_fee', sanitize_text_field( $platform_fee));

	$product->save();
}
add_action( 'woocommerce_process_product_meta', 'cfwc_save_custom_field' );


// Re-calculating cart values
function add_cart_item_data_with_optional_prices( $cart_item_data, $product_id, $variation_id )
{
	$product = get_product( $product_id );
	$cart_item_data['security_deposit'] = $product->get_meta( '_security_deposit_amount' );
	$cart_item_data['rental_price'] = $product->get_price();
	$cart_item_data['deposit'] = $product->get_meta( '_platform_fee' ); 

	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data_with_optional_prices', 10, 3);





function set_cart_calculation( $cart )
{
	foreach ( $cart->get_cart() as $cart_item )
	{	
		$cart_item['data']->set_price( $cart_item['deposit'] );
	}
		
	
}
add_action( 'woocommerce_before_calculate_totals', 'set_cart_calculation', 10, 1);


function save_order_custom_values_of_items( $item, $cart_item_key, $values, $order )
{
	$item->add_meta_data( '_rental_price', $values['rental_price'] );
}
add_action( 'woocommerce_checkout_create_order_line_item', 'save_order_custom_values_of_items', 10, 4 );



function woocommerce_admin_order_item_headers()
{
    // set the column name
    $column_name = 'Rental price';

    // display the column name
    echo '<th>' . $column_name . '</th>';
}
add_action('woocommerce_admin_order_item_headers', 'woocommerce_admin_order_item_headers');

// Add custom column values here


function woocommerce_admin_order_item_values($_product, $item, $item_id = null)
{
    // get the post meta value from the associated product
	$rental_price = wc_get_order_item_meta( $item_id, '_rental_price');
    
	// display the value
    echo '<td>' . wc_price( $rental_price ) . '</td>';
}
add_action('woocommerce_admin_order_item_values', 'woocommerce_admin_order_item_values', 10, 3);


function custom_woocommerce_hidden_order_itemmeta( $arr ) {
    $arr[] = '_rental_price';
    return $arr;
}
add_filter('woocommerce_hidden_order_itemmeta', 'custom_woocommerce_hidden_order_itemmeta', 10, 1);