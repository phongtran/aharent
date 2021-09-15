<?php

define( 'THEME_DIR', trailingslashit( get_template_directory() ) );
define( 'THEME_URI', trailingslashit( esc_url( get_template_directory_uri() ) ) );

// Importer functions
require_once THEME_DIR . 'inc/importer/product-tabs-mapping.php';

// Add dokan vendor custom settings
require_once THEME_DIR . 'inc/dokan/vendor-custom-settings.php';

// single-product ajax
require_once THEME_DIR . 'inc/ajax/single-product.php';


function wpai_is_xml_preprocess_enabled( $is_enabled ) {
    return false;
}
add_filter( 'is_xml_preprocess_enabled', 'wpai_is_xml_preprocess_enabled', 10, 1 );

function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

// add_theme_support( 'wc-product-gallery-zoom' );
// add_theme_support( 'wc-product-gallery-lightbox' );
// add_theme_support( 'wc-product-gallery-slider' );


// Product loop column in shop page
if ( !function_exists('loop_columns') ) {
	function loop_columns() {
		return 5; // 5 products per row
	}
}
add_filter('loop_shop_columns', 'loop_columns', 999);


function lw_loop_shop_per_page( $products )
{
 	$products = 20; // number of products per page
 	return $products;
}
add_filter( 'loop_shop_per_page', 'lw_loop_shop_per_page', 30 );


function register_menus() { 
    register_nav_menus(
        array(
            'main-menu' => 'Main Menu',
			'socials-menu' => 'Social Menu',
            'footer-menu' => 'Footer Menu',
        )
    ); 
}
add_action( 'init', 'register_menus' );



/**
 * Remove product data tabs
 */
function woo_remove_product_tabs( $tabs )
{
    // woocommerce tabs
	unset( $tabs[ 'reviews' ] ); 			// Remove the reviews tab
    unset( $tabs[ 'additional_information' ] );  	// Remove the additional information tab
	
	// Dokan tabs
	unset( $tabs[ 'seller' ]);
	unset( $tabs[ 'more_seller_product' ]);

    return $tabs;
}
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );

// Remove tab title
add_filter( 'yikes_woocommerce_custom_repeatable_product_tabs_heading', '__return_false', 99 );


// Load theme stylesheets
function load_stylesheets()
{
    wp_register_style ( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/css/bootstrap.min.css', '', '5.0.2', 'all' );
    wp_enqueue_style( 'bootstrap' );

	wp_register_style ( 'bootstrap-utilities', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/css/bootstrap-utilities.min.css', '', '5.0.2', 'all' );
    wp_enqueue_style( 'bootstrap-utilities' );

    wp_register_style( 'app', get_template_directory_uri() . '/assets/css/app.css', array('bootstrap'), '1', 'all' );
    wp_enqueue_style( 'app' );

	wp_register_style( 'jquery-zoom-image-carousel-style', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/style.css', '', '1', 'all' );
    wp_enqueue_style( 'jquery-zoom-image-carousel-style' );


	wp_register_style( 'datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.min.css', '', '1', 'all' );
    wp_enqueue_style( 'datetimepicker' );


}
add_action( 'wp_enqueue_scripts', 'load_stylesheets' );


function aharent_widgets_init() {
    register_sidebar( array(
        'name'          => __( 'Shop Sidebar', 'aharent' ),
        'id'            => 'shop',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ) );

	register_sidebar( array(
        'name'          => __( 'Footer Social Links', 'aharent' ),
        'id'            => 'footer-social-links',
        'before_widget' => '<aside id="%1$s" class="widget %2$s">',
        'after_widget'  => '</aside>',
    ) );
}
add_action( 'widgets_init', 'aharent_widgets_init' );


// Load theme javascripts
function load_scripts()
{
	wp_enqueue_script( 'wp-util' );

	wp_register_script( 'tinymce-editor', 'https://cdn.tiny.cloud/1/no-api-key/tinymce/5/tinymce.min.js', array(), '1', 'all' );
	wp_enqueue_script( 'tinymce-editor' );

	wp_register_script( 'bootstrap-bundle-js-min', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/js/bootstrap.bundle.min.js', array(), '1', 'all' );
	wp_enqueue_script( 'bootstrap-bundle-js-min' );	

	wp_register_script( 'jquery-zoom-image-carousel-zoom', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/scripts/zoom-image.js', array('jquery-core'), '1', 'all' );
	wp_enqueue_script( 'jquery-zoom-image-carousel-zoom' );

	wp_register_script( 'jquery-zoom-image-carousel-main', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/scripts/main.js', array('jquery-zoom-image-carousel-zoom'), '1', 'all' );
	wp_enqueue_script( 'jquery-zoom-image-carousel-main' );

	wp_register_script( 'bootstrap-input-spinner', get_template_directory_uri() . '/assets/vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js', array('jquery'), '1', 'all' );
	wp_enqueue_script( 'bootstrap-input-spinner' );

	wp_register_script( 'datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.full.min.js', array('jquery'), '1', 'all' );
	wp_enqueue_script( 'datetimepicker' );

	wp_register_script( 'custom-script', get_template_directory_uri() . '/assets/js/custom.js', array( 'jquery', 'datetimepicker' ), '1', 'all' );
	wp_enqueue_script( 'custom-script' );
}
add_action ('wp_enqueue_scripts', 'load_scripts' );




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

	// Platform fee, collect from current vendor
	// woocommerce_wp_text_input( 
	// 	array( 
	// 		'id'          => '_platform_fee', 
	// 		'label'       => __( 'Platform fee', 'woocommerce' ),
	// 	)
	// );
	
	
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

	$vendor_id = get_post_field( 'post_author', $product_id );
	$dokan_admin_percentage = get_user_meta( $vendor_id, 'dokan_admin_percentage' );
	
	if (!empty($dokan_admin_percentage))
		$dokan_admin_percentage = $dokan_admin_percentage[0];
	
	$cart_item_data['deposit'] = $cart_item_data['rental_price'] * $dokan_admin_percentage / 100; 
	$cart_item_data['rental_price'] -= $cart_item_data['deposit'];

	if (isset($_POST['_date_from']))
		$cart_item_data['date-from'] = $_POST['_date_from'];

	if (isset($_POST['_date_to']))
	$cart_item_data['date-to'] = $_POST['_date_to'];

	$date_from = new DateTime( str_replace( '/', '-', $cart_item_data['date-from'] ) );
	$date_to = new DateTime( str_replace( '/', '-', $cart_item_data['date-to'] ) );
	$cart_item_data['number_of_days'] = $date_from->diff( $date_to )->format("%a");


	return $cart_item_data;
}
add_filter( 'woocommerce_add_cart_item_data', 'add_cart_item_data_with_optional_prices', 10, 3);





function set_cart_calculation( $cart )
{
	foreach ( $cart->get_cart() as $cart_item )
	{	
		$cart_item['data']->set_price( $cart_item['deposit'] * $cart_item['number_of_days'] );
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


function calculate_cart_total_rental_fee()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_rental_fee = 0;
	foreach ( $cart as $item => $values )
		$_total_rental_fee += $values['rental_price'] * $values['quantity'] * $values['number_of_days'];

	return $_total_rental_fee;
}

function calculate_cart_total_security_deposit()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_security_deposit = 0;
	// var_dump($cart); 
	foreach ( $cart as $item => $values )
		$_total_security_deposit += $values['security_deposit'] * $values['quantity'] * $values['number_of_days'];

	return $_total_security_deposit;
}

function calculate_cart_total_deposit()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_deposit = 0;
	foreach ( $cart as $item => $values )
		$_total_deposit += $values['deposit'] * $values['quantity'] * $values['number_of_days'];

	return $_total_deposit;
}

function add_the_date_validation( $passed )
{ 
	$quantity = $_POST['quantity'];
	$date_from = $_POST['_date_from'];
	$date_to = $_POST['_date_to'];

	if ( (!isset( $quantity ) || $quantity <= 0) ||
		( !isset( $date_from ) || ( "" == $date_from )) || 
		( !isset( $date_to ) || ( "" == $date_to )) )
	{
		wc_add_notice(  __( 'Vui lòng chọn thông tin trước khi đặt thuê.', 'woocommerce' ), 'error' );
		$passed = false;
	}
		
	return $passed;
}
add_filter( 'woocommerce_add_to_cart_validation', 'add_the_date_validation', 10, 5 );  


function customize_checkout_billing_kyc( $fields )
{
	$fields['billing']['billing_state']['default'] = 'Ho Chi Minh';
	$fields['billing']['billing_state']['value'] = 'Ho Chi Minh';

	//$fields['billing']['billing_state']['custom_attributes']['readonly'] = true;
	//$fields['billing']['billing_state']['custom_attributes']['disabled'] = true;
	unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);

	$fields['billing']['billing_national_id'] = array(
        'label'     => __('National ID Number', 'woocommerce'),
        'required'  => true,
		'priority'	=> 30,
		
    );

	$fields['shipping']['shipping_state']['default'] = 'Ho Chi Minh';
	unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_city']);
	unset($fields['shipping']['shipping_postcode']);
	unset($fields['shipping']['shipping_country']);

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'customize_checkout_billing_kyc' );


function get_new_price( $product_id, $date_from, $date_to, $quantity )
{
	require_once THEME_DIR . 'inc/product/price-handler.php';

	$product 		= wc_get_product ( $product_id );

	if ( $product->is_type( 'simple' ))
	{
		$price_handler = 'get_price_from_simple_product';
	}
	else
	{
		$price_handler = 'get_price_for_duration';

		$vendor_name 	= get_post_meta ( $product_id, '_vendor' );
		
		if ( isset( $vendor_name[0] ) && !empty( $vendor_name[0]) && function_exists( 'get_price_from_' . $vendor_name[0]) )
			$price_handler = 'get_price_from_' . $vendor_name[0];

	}

	if ( function_exists( $price_handler ) )
		return $price_handler( $product_id, $date_from, $date_to, $quantity );
	
	return 0;
}

function get_vendor_percentage( $vendor_id )
{
	$dokan_admin_percentage = get_user_meta( $vendor_id, 'dokan_admin_percentage' );
				
	if (!empty($dokan_admin_percentage))
		return $dokan_admin_percentage[0];

	return false;
}

function get_product_vendor ( $post )
{
	$_vendor_login = get_post_meta( $post->ID, '_vendor' );

	if ( isset( $_vendor_login ) && !empty( $_vendor_login[0] ))
		$user = get_user_by( 'login', $_vendor_login[0] );

	if ( isset( $user ) && !empty( $user ))
		return $user->id;

	return $post->post_author;
}


function aha_woocommerce_show_page_title() {
	return false;
}
add_filter( 'woocommerce_show_page_title', 'aha_woocommerce_show_page_title' );


function get_product_default_variation ( $product_id )
{
	$product				= wc_get_product( $product_id );
	$default_attributes		= $product->get_default_attributes();
	$variations 			= $product->get_available_variations();

	foreach ( $variations as $variation )
		return $variation['variation_id'];

	return false;
}


?>