<?php



function mytheme_add_woocommerce_support() {
    add_theme_support( 'woocommerce' );
}
add_action( 'after_setup_theme', 'mytheme_add_woocommerce_support' );

// add_theme_support( 'wc-product-gallery-zoom' );
// add_theme_support( 'wc-product-gallery-lightbox' );
// add_theme_support( 'wc-product-gallery-slider' );


// Product loop column

if ( !function_exists('loop_columns') ) {
	function loop_columns() {
		return 5; // 5 products per row
	}
}
add_filter('loop_shop_columns', 'loop_columns', 999);

// Load theme stylesheets
function load_stylesheets()
{
    wp_register_style ( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/css/bootstrap.min.css', '', '5.0.2', 'all' );
    wp_enqueue_style( 'bootstrap' );

    wp_register_style( 'app', get_template_directory_uri() . '/assets/css/app.css', array('bootstrap'), '1', 'all' );
    wp_enqueue_style( 'app' );

	wp_register_style( 'jquery-zoom-image-carousel-style', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/style.css', '', '1', 'all' );
    wp_enqueue_style( 'jquery-zoom-image-carousel-style' );

	wp_register_style( 'datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.min.css', '', '1', 'all' );
    wp_enqueue_style( 'datetimepicker' );

}
add_action( 'wp_enqueue_scripts', 'load_stylesheets' );


// Load theme javascripts
function load_scripts()
{
	wp_register_script( 'jquery-zoom-image-carousel-zoom', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/scripts/zoom-image.js', array('jquery-core'), '1', 'all' );
	wp_enqueue_script( 'jquery-zoom-image-carousel-zoom' );

	wp_register_script( 'jquery-zoom-image-carousel-main', get_template_directory_uri() . '/assets/vendors/jquery-zoom-image-carousel/scripts/main.js', array('jquery-zoom-image-carousel-zoom'), '1', 'all' );
	wp_enqueue_script( 'jquery-zoom-image-carousel-main' );

	wp_register_script( 'bootstrap-input-spinner', get_template_directory_uri() . '/assets/vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js', array('jquery'), '1', 'all' );
	wp_enqueue_script( 'bootstrap-input-spinner' );

	wp_register_script( 'datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.full.min.js', array('jquery'), '1', 'all' );
	wp_enqueue_script( 'datetimepicker' );

	wp_register_script( 'custom-script', get_template_directory_uri() . '/assets/js/custom.js', array('jquery', 'datetimepicker'), '1', 'all' );
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

	$date_from = new DateTime( $cart_item_data['date-from'] );
	$date_to = new DateTime( $cart_item_data['date-to'] );
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


function customize_checkout_billing_kyc( $fields )
{
	unset($fields['billing']['billing_company']);
    unset($fields['billing']['billing_state']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);

	$fields['billing']['billing_national_id'] = array(
        'label'     => __('National ID Number', 'woocommerce'),
        'required'  => true
    );


	unset($fields['shipping']['shipping_company']);
    unset($fields['shipping']['shipping_state']);
	unset($fields['shipping']['shipping_postcode']);
	unset($fields['shipping']['shipping_country']);

    return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'customize_checkout_billing_kyc' );


?>