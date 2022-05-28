<?php

define('THEME_DIR', trailingslashit(get_template_directory()));
define('THEME_URI', trailingslashit(esc_url(get_template_directory_uri())));

// Importer functions
require_once THEME_DIR . 'inc/importer/product-tabs-mapping.php';

// Add dokan vendor custom settings
require_once THEME_DIR . 'inc/dokan/vendor-custom-settings.php';

// single-product ajax
require_once THEME_DIR . 'inc/ajax/single-product.php';


function wpai_is_xml_preprocess_enabled($is_enabled)
{
	return false;
}
add_filter('is_xml_preprocess_enabled', 'wpai_is_xml_preprocess_enabled', 10, 1);

function mytheme_add_woocommerce_support()
{
	add_theme_support('woocommerce');
}
add_action('after_setup_theme', 'mytheme_add_woocommerce_support');


// Product loop column in shop page
if (!function_exists('loop_columns')) {
	function loop_columns()
	{
		return 5; // 5 products per row
	}
}
add_filter('loop_shop_columns', 'loop_columns', 999);


function lw_loop_shop_per_page($products)
{
	$products = 60; // number of products per page (shop page)
	return $products;
}
add_filter('loop_shop_per_page', 'lw_loop_shop_per_page', 100);


function jk_related_products_args($args)
{
	$args['posts_per_page'] = 10; // 4 related products
	$args['columns'] = 5; // arranged in 2 columns
	return $args;
}
add_filter('woocommerce_output_related_products_args', 'jk_related_products_args', 20);


function register_menus()
{
	register_nav_menus(
		array(
			'main-menu' => 'Main Menu',
			'socials-menu' => 'Social Menu',
			'footer-menu' => 'Footer Menu',
		)
	);
}
add_action('init', 'register_menus');



/**
 * Remove product data tabs
 */
function woo_remove_product_tabs($tabs)
{
	// woocommerce tabs
	unset($tabs['reviews']); 			// Remove the reviews tab
	unset($tabs['additional_information']);  	// Remove the additional information tab

	// Dokan tabs
	unset($tabs['seller']);
	unset($tabs['more_seller_product']);

	return $tabs;
}
add_filter('woocommerce_product_tabs', 'woo_remove_product_tabs', 98);

// Remove tab title
add_filter('yikes_woocommerce_custom_repeatable_product_tabs_heading', '__return_false', 99);


function aharent_widgets_init()
{
	register_sidebar(array(
		'name'          => __('Shop Sidebar', 'aharent'),
		'id'            => 'shop',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	));

	register_sidebar(array(
		'name'          => __('Footer Social Links', 'aharent'),
		'id'            => 'footer-social-links',
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
	));
}
add_action('widgets_init', 'aharent_widgets_init');


// Remove emoji script
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');


// add async and defer attributes to enqueued scripts
function attribute_script_loader_tag($tag, $handle, $src)
{

	if (is_admin()) {
		return $tag;
	}


	$normal_loading = array('jquery-core', 'wp-i18n', 'wp-hooks');
	$async_loading = array();


	if (in_array($handle, $async_loading))
		$tag = str_replace(' src', ' async src', $tag);
	elseif (in_array($handle, $normal_loading))
		$tag = str_replace(' src', ' src', $tag);
	else
		$tag = str_replace(' src', ' defer src', $tag);

	return $tag;
}
add_filter('script_loader_tag', 'attribute_script_loader_tag', 10, 3);



function basic_scripts()
{
	global $wp_scripts;

	// Replace local jquery with cdn
	wp_deregister_script('jquery-core');
	wp_register_script('jquery-core', "https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js", array(), null, false);


	// CSS Registration
	wp_register_style('bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/css/bootstrap.min.css', '', '5.0.2', false);
	wp_register_style('datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.min.css', '', '1', false);
	wp_register_style('app', get_template_directory_uri() . '/assets/css/app.min.css', array('bootstrap'), '1', 'all');
	wp_register_style('swipe-style', 'https://unpkg.com/swiper@7/swiper-bundle.min.css');
	wp_register_style('bootstrap-utilities', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/css/bootstrap-utilities.min.css', '', '5.0.2', true);
	wp_register_style('fontawesome-cdn', 'https://use.fontawesome.com/eeaff9ebde.css');

	// JS Registration
	wp_register_script('loading-bar', get_template_directory_uri() . '/assets/vendors/topbar-master/topbar.min.js', array(), null, true);
	wp_register_script('page-loading', get_template_directory_uri() . '/assets/js/loading.js', array('loading-bar'), null, true);
	wp_register_script('header', get_template_directory_uri() . '/assets/js/header.js', array(), null, true);
	wp_register_script('swipe-script', 'https://unpkg.com/swiper@7/swiper-bundle.min.js', array(), 1, true);
	wp_register_script('single-product-script', get_template_directory_uri() . '/assets/js/single-product.js', array(), 1, true);
	wp_register_script('bootstrap-bundle-js-min', get_template_directory_uri() . '/assets/vendors/bootstrap-5.0.2/dist/js/bootstrap.bundle.min.js', array(), '1', true);
	wp_register_script('bootstrap-input-spinner', get_template_directory_uri() . '/assets/vendors/bootstrap-input-spinner/src/bootstrap-input-spinner.js', array(), '1', true);
	wp_register_script('datetimepicker', get_template_directory_uri() . '/assets/vendors/datetimepicker/build/jquery.datetimepicker.full.min.js', array(), '1', true);
	wp_register_script('custom-script', get_template_directory_uri() . '/assets/js/custom.js', array(), '1', true);
	wp_register_script('home-script', get_template_directory_uri() . '/assets/js/home.js', array(), '1', true);

	// CSS queue
	wp_enqueue_style('bootstrap');
	wp_enqueue_style('app');

	// wp_dequeue_style( 'dashicons' );
	// wp_dequeue_style( 'wp-block-library' );
	// wp_dequeue_style( 'wc-block-vendors-style' );
	// wp_dequeue_style( 'wc-block-style' );

	// wp_dequeue_style( 'font-awesome' );


	// JS queue
	// wp_enqueue_script( 'loading-bar' );
	// wp_enqueue_script( 'page-loading' );
	wp_enqueue_script('header');

	wp_enqueue_script('bootstrap-bundle-js-min');

	// wp_deregister_script( 'wp-embed' );



	// wp_dequeue_script( 'woo-viet-provinces-script' );


	if (is_front_page()) {

		// turn off dokan
		// wp_dequeue_script( 'dokan-script' );
		// wp_deregister_script( 'dokan-i18n-jed' );
		// wp_deregister_script( 'dokan-tooltip' );
		// wp_deregister_script( 'dokan-form-validate' );
		// wp_deregister_script( 'speaking-url' );
		// wp_deregister_script( 'dokan-select2-js' );
		// wp_dequeue_script( 'dokan-util-helper' );
		// wp_dequeue_script( 'dokan-login-form-popup' );
		// wp_dequeue_script( 'dokan-popup' );
		// wp_dequeue_style( 'dokan-style' );
		// wp_dequeue_style( 'dokan-fontawesome' );

		// wp_deregister_script( 'underscore' );
		// wp_deregister_script( 'wp-embed' );
		// wp_dequeue_style( 'berocket_aapf_widget-style' );
		// wp_dequeue_style( 'wapf-frontend-css' );
		// wp_dequeue_style( 'woocommerce-layout' );
		// wp_dequeue_style( 'woocommerce-general' );
		// wp_dequeue_style( 'woo-viet-provinces-style' );
		// wp_enqueue_style( 'fontawesome-cdn' );
		// wp_dequeue_script( 'woocommerce' );

		// wp_dequeue_script( 'wc-cart-fragments' );
		// wp_dequeue_script( 'wc-single-product' );

		// wp_deregister_script( 'jquery-ui-slider' );
		// wp_deregister_script( 'jquery-ui-mouse' );
		// wp_deregister_script( 'jquery-ui-core' );

		wp_enqueue_script('home-script');
	} else if (is_single()) {
		// Load swipe for image slider
		// Swipe CSS


		wp_enqueue_style('swipe-style');
		wp_enqueue_style('datetimepicker');

		// wp_dequeue_style( 'dokan-select2-css' );

		// Swipe JS

		wp_enqueue_script('wp-util');
		wp_enqueue_script('bootstrap-input-spinner');
		wp_enqueue_script('datetimepicker');
		wp_enqueue_script('swipe-script');
		wp_enqueue_script('single-product-script');
		wp_enqueue_script('custom-script');

		// wp_deregister_script( 'jquery-ui-core' );


		// wp_dequeue_script( 'dokan-script' );
		// wp_deregister_script( 'dokan-i18n-jed' );
		// wp_deregister_script( 'dokan-tooltip' );
		// wp_deregister_script( 'dokan-form-validate' );
		// wp_deregister_script( 'speaking-url' );
		// wp_deregister_script( 'dokan-select2-js' );


	} else {
		// wp_deregister_script( 'jquery-blockui' );

		if (is_cart() || is_checkout()) {
			// Css

			wp_enqueue_style('datetimepicker');
			wp_dequeue_style('woo-viet-provinces-style');


			// JS
			wp_enqueue_script('bootstrap-bundle-js-min');
			wp_enqueue_script('wp-util');
			wp_enqueue_style('bootstrap-utilities');
			wp_enqueue_script('bootstrap-input-spinner');
			wp_enqueue_script('datetimepicker');
			wp_enqueue_script('custom-script');

			// wp_dequeue_script( 'wc-cart' );


		} elseif (is_archive()) {
		} elseif (is_front_page()) {
		} elseif (!dokan_is_seller_dashboard()) {
			// wp_dequeue_style( 'woocommerce-layout' );
			// wp_dequeue_style( 'woocommerce-general' );

		} elseif (dokan_is_seller_dashboard()) {
			wp_enqueue_style('datetimepicker');
			wp_enqueue_script('datetimepicker');
		}
	}
}
add_action('wp_enqueue_scripts', 'basic_scripts', 200);



function remove_jquery_migrate($scripts)
{
	if (!is_admin() && isset($scripts->registered['jquery'])) {
		$script = $scripts->registered['jquery'];
		if ($script->deps) {
			// Check whether the script has any dependencies
			$script->deps = array_diff($script->deps, array('jquery-migrate'));
		}
	}
}
add_action('wp_default_scripts', 'remove_jquery_migrate');


add_action('dokan_dashboard_wrap_start', 'load_dokan_style_and_script');
function load_dokan_style_and_script()
{
	// CSS


	// JS

	wp_enqueue_script('dokan-tinymce');

	wp_register_script('rich-editor', get_template_directory_uri() . '/assets/js/rich-editor.js', array('dokan-tinymce'), null, true);
	wp_enqueue_script('rich-editor');
}


function wc_set_up_breadcrumb($defaults)
{
	// Change the breadcrumb home text from 'Home' to 'Apartment'
	$defaults['home'] = 'Trang chủ';
	return $defaults;
}
add_filter('woocommerce_breadcrumb_defaults', 'wc_set_up_breadcrumb');



function cfwc_save_custom_field($post_id)
{
	// $product = wc_get_product( $post_id );

	// $security_deposit_checkbox = isset( $_POST['_security_deposit'] ) ? $_POST['_security_deposit'] : '' ;

	// $rental_period_unit = isset( $_POST['_rental_period_unit'])

	// $security_deposit_amount = isset( $_POST['_security_deposit_amount'] ) ? $_POST['_security_deposit_amount'] : '';
	// $product->update_meta_data( '_security_deposit_amount', sanitize_text_field( $security_deposit_amount ) );

	// $platform_fee = isset ( $_POST['_platform_fee'] ) ? $_POST['_platform_fee'] : '' ;
	// $product->update_meta_data( '_platform_fee', sanitize_text_field( $platform_fee));

	// $product->save();
}
add_action('woocommerce_process_product_meta', 'cfwc_save_custom_field');


// Re-calculating cart values
function add_cart_item_data_with_optional_prices($cart_item_data, $product_id, $variation_id)
{
	if (isset($_POST['_date_from']))
		$cart_item_data['date-from'] = $_POST['_date_from'];

	if (isset($_POST['time_unit']))
		$cart_item_data['time-unit'] = $_POST['time_unit'];

	if (isset($_POST['duration']))
		$cart_item_data['duration'] = $_POST['duration'];

	if (isset($_POST['delivery_option']))
		$cart_item_data['delivery-option'] = $_POST['delivery_option'];

	$product = wc_get_product($product_id);
	$security_deposit = $product->get_meta('security_deposit');
	if (!empty($security_deposit) && is_numeric($security_deposit))
		$cart_item_data['security-deposit'] = $security_deposit;
	else
		$cart_item_data['security-deposit'] = 0;

	$discount = get_discount($product);
	if ($discount)
		$cart_item_data['discount'] = $discount;

	if (isset($cart_item_data['time-unit']))
		$new_price = get_new_price($product_id, $cart_item_data['date-from'], $cart_item_data['duration'], $cart_item_data['time-unit']);
	else
		$new_price = get_new_price($product_id, $cart_item_data['date-from'], $cart_item_data['duration']);

	$cart_item_data['rental_price'] = $new_price['price'];
	$cart_item_data['deposit'] = $new_price['price'];

	WC()->customer->set_is_vat_exempt(true);

	return $cart_item_data;
}
add_filter('woocommerce_add_cart_item_data', 'add_cart_item_data_with_optional_prices', 10, 3);



function update_cart_meta($cart_updated)
{
	$cart_post = $_POST['cart'];

	foreach ($cart_post as $key => $cart_item_data) {
		WC()->cart->cart_contents[$key]['duration'] = $cart_item_data['duration'];
		WC()->cart->cart_contents[$key]['date-from'] = $cart_item_data['_date_from'];
		WC()->cart->cart_contents[$key]['delivery-option'] = $cart_item_data['delivery_option'];

		if (isset($cart_item_data['time_unit']))
			WC()->cart->cart_contents[$key]['time-unit'] = $cart_item_data['time_unit'];

		$session_item = WC()->cart->cart_contents[$key];

		if (isset($session_item['time-unit']))
			$new_price = get_new_price($session_item['product_id'], $session_item['date-from'], $session_item['duration'], $session_item['time-unit']);
		else
			$new_price = get_new_price($session_item['product_id'], $session_item['date-from'], $session_item['duration']);

		WC()->cart->cart_contents[$key]['deposit'] = $new_price['deposit'];
		WC()->cart->cart_contents[$key]['rental_price'] = $new_price['price'];
	}
}
add_action('woocommerce_update_cart_action_cart_updated', 'update_cart_meta', 10, 3);


function save_order_custom_values_of_items($item, $cart_item_key, $values, $order)
{
	$payment_method = $order->get_payment_method();
	$item->add_meta_data('deposit', $values['deposit']);
	$item->add_meta_data('_rental_price', $values['rental_price']);

	$duration = $item->get_meta_data('duration');
	if ($duration)
		$item->update_meta_data('duration', $values['duration']);
	else
		$item->add_meta_data('duration', $values['duration']);

	$item->add_meta_data('security-deposit', $values['security-deposit']);

	if ($values['discount'])
		$item->add_meta_data('discount', $values['discount']);

	if (!$values['time-unit'])
		$item->add_meta_data('time-unit', 'day');
	else
		$item->add_meta_data('time-unit', $values['time-unit']);

	$item->add_meta_data('date-from', $values['date-from']);
	$item->add_meta_data('delivery-option', $values['delivery-option']);
}
add_action('woocommerce_checkout_create_order_line_item', 'save_order_custom_values_of_items', 10, 4);




// Update the order meta with field value
add_action('woocommerce_checkout_create_order', 'custom_checkout_field_create_order', 10, 2);
function custom_checkout_field_create_order($order, $data)
{
	// Save national id number
	if (isset($_POST['billing_national_id']))
		$order->update_meta_data('billing_national_id', sanitize_text_field($_POST['billing_national_id']));

	// Save tax receipt options
	if (isset($_POST['order_vat']) && $_POST['order_vat']) {
		$order->update_meta_data('order_vat', $_POST['order_vat']);
		$order->update_meta_data('order_vat_company', sanitize_text_field($_POST['order_vat_company']));
		$order->update_meta_data('order_vat_code', sanitize_text_field($_POST['order_vat_code']));
		$order->update_meta_data('order_vat_address', sanitize_text_field($_POST['order_vat_address']));
	}
}


add_action('woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1);
function my_custom_checkout_field_display_admin_order_meta($order)
{
	// Display Nation ID Number
	if ($national_id = $order->get_meta('billing_national_id'))
		echo '<p><strong>' . __('National ID number', 'woocommerce') . ':</strong> ' . $national_id . '</p>';

	// display order VAT options
	if ($order_vat = $order->get_meta('order_vat')) {
		echo '<p><strong>' . __('Tax receipt', 'woocommerce') . ':</strong> ' . ' yes' . '</p>';
		echo '<p><strong>' . __('Company name', 'woocommerce') . ':</strong> ' . $order->get_meta('order_vat_company') . '</p>';
		echo '<p><strong>' . __('Tax code', 'woocommerce') . ':</strong> ' . $order->get_meta('order_vat_code') . '</p>';
		echo '<p><strong>' . __('Address', 'woocommerce') . ':</strong> ' . $order->get_meta('order_vat_address') . '</p>';
	} else {
		echo '<p><strong>' . __('Tax receipt', 'woocommerce') . ':</strong> ' . ' no' . '</p>';
	}
}



function woocommerce_admin_order_item_headers()
{
	// set the column name
	$column_name = array(
		'Trả sau',
		'Rental duration',
		'From date',
		'Delivery/Pick-up'
	);

	// display the column name
	foreach ($column_name as $column)
		echo '<th>' . $column . '</th>';
}
add_action('woocommerce_admin_order_item_headers', 'woocommerce_admin_order_item_headers');

// Add custom column values here


function woocommerce_admin_order_item_values($_product, $item, $item_id = null)
{
	// get the post meta value from the associated product
	$rental_price = wc_get_order_item_meta($item_id, '_rental_price');
	$deposit = wc_get_order_item_meta($item_id, 'deposit');
	$rental_duration = wc_get_order_item_meta($item_id, 'duration');
	$rental_date_from = wc_get_order_item_meta($item_id, 'date-from');
	$time_unit = wc_get_order_item_meta($item_id, 'time-unit');
	$delivery_option = wc_get_order_item_meta($item_id, 'delivery-option');

	// display the value
	if ($deposit > 0)
		echo '<td>' . wc_price($rental_price - $deposit) . '</td>';
	else
		echo '<td>none</td>';

	echo '<td>' . $rental_duration . ' ' . $time_unit . '</td>';
	echo '<td>' . $rental_date_from . '</td>';
	echo '<td>' . $delivery_option . '</td>';
}
add_action('woocommerce_admin_order_item_values', 'woocommerce_admin_order_item_values', 10, 3);


function custom_woocommerce_hidden_order_itemmeta($arr)
{
	$arr[] = '_rental_price';
	$arr[] = 'deposit';
	$arr[] = 'delivery-option';
	$arr[] = 'duration';
	$arr[] = 'time-unit';
	$arr[] = 'date-from';
	return $arr;
}
add_filter('woocommerce_hidden_order_itemmeta', 'custom_woocommerce_hidden_order_itemmeta', 10, 1);




function set_cart_calculation($cart)
{
	$payment_method = WC()->session->get('chosen_payment_method');
	foreach ($cart->get_cart() as $cart_item) {
		if (!$payment_method || 'cod' == $payment_method) {
			$amount = $cart_item['rental_price'];

			if ($cart_item['discount'])
				if ('percentage' == $cart_item['discount']['type'])
					$amount = $amount * (100 - $cart_item['discount']['value']) / 100;

			$cart_item['data']->set_price($amount + $cart_item['security-deposit']);
		} else {
			$amount = $cart_item['deposit'];

			if ($cart_item['discount'])
				if ('percentage' == $cart_item['discount']['type'])
					$amount = $amount * (100 - $cart_item['discount']['value']) / 100;

			$cart_item['data']->set_price($amount + $cart_item['security-deposit']);
		}
	}
}
add_action('woocommerce_before_calculate_totals', 'set_cart_calculation', 10, 1);


function calculate_cart_total_rental_fee()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_rental_fee = 0;
	foreach ($cart as $item => $values) {
		$amount = $values['rental_price'] * $values['quantity'];

		if ($values['discount'])
			if ('percentage' == $values['discount']['type'])
				$amount = $amount * (100 - $values['discount']['value']) / 100;

		$_total_rental_fee += $amount;
	}

	return $_total_rental_fee;
}

function calculate_cart_total_security_deposit()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_security_deposit = 0;
	// var_dump($cart); 
	foreach ($cart as $item => $values)
		$_total_security_deposit += $values['security_deposit'] * $values['quantity'];

	return $_total_security_deposit;
}

function calculate_cart_total_deposit()
{
	global $woocommerce;
	$cart = $woocommerce->cart->get_cart();

	$_total_deposit = 0;
	foreach ($cart as $item => $values) {
		$amount = $values['deposit'] * $values['quantity'];

		if ($values['discount'])
			if ('percentage' == $values['discount']['type'])
				$amount = $amount * (100 - $values['discount']['value']) / 100;

		$_total_deposit += $amount;
	}


	return $_total_deposit;
}

function add_the_date_validation($passed)
{
	$quantity = $_POST['quantity'];
	$date_from = $_POST['_date_from'];
	$duration = $_POST['duration'];
	// $date_to = $_POST['_date_to'];

	if ((!isset($quantity) || $quantity <= 0) ||
		(!isset($date_from) || ("" == $date_from)) ||
		(!isset($duration) || ($duration <= 0))
	) {
		wc_add_notice(__('Vui lòng chọn thông tin trước khi đặt thuê.', 'woocommerce'), 'error');
		$passed = false;
	}

	return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'add_the_date_validation', 10, 5);


function customize_checkout_billing_kyc($fields)
{
	unset($fields['billing']['billing_first_name']['class']);
	// unset($fields['billing']['billing_last_name']['class']);

	unset($fields['billing']['billing_last_name']);
	unset($fields['billing']['billing_company']);
	unset($fields['billing']['billing_city']);
	unset($fields['billing']['billing_postcode']);
	unset($fields['billing']['billing_country']);


	// $fields['billing']['billing_state'] = array(
	// 	'label'		=>  __( 'Province', 'woocommerce' ),
	// 	'type'		=>	'text',
	// 	'required'	=>	true,
	// 	'default'	=> 'Hồ Chí Minh',
	// 	'value'		=> 'Hồ Chí Minh',
	// 	'custom_attributes'	=> array( 'readonly' => 'readonly' ),
	// 	'priority'	=> 65,
	// 	'class'		=> array( 'form-row-wide', 'address-field' )
	// );

	return $fields;
}
add_filter('woocommerce_checkout_fields', 'customize_checkout_billing_kyc');


// Remove "optional" text in form fields
add_filter('woocommerce_form_field', 'elex_remove_checkout_optional_text', 10, 4);
function elex_remove_checkout_optional_text($field, $key, $args, $value)
{
	if (is_checkout() && !is_wc_endpoint_url()) {
		$optional = '&nbsp;<span class="optional">(' . esc_html__('optional', 'woocommerce') . ')</span>';
		$field = str_replace($optional, '', $field);
	}
	return $field;
}


function add_vat_tax_option()
{
}
add_action('woocommerce_after_checkout_shipping_form', 'add_vat_tax_option');


function get_new_price($product_id, $date_from, $duration, $time_unit = 'day')
{
	require_once THEME_DIR . 'inc/product/price-handler.php';

	$product 		= wc_get_product($product_id);

	if ($product->is_type('simple')) {
		$price_handler = 'get_price_from_simple_product';
	} else {
		$price_handler = 'get_price_for_variable_product';

		$vendor_name 	= get_post_meta($product_id, 'vendor');

		if (isset($vendor_name[0]) && !empty($vendor_name[0]) && function_exists('get_price_from_' . $vendor_name[0]))
			$price_handler = 'get_price_from_' . $vendor_name[0];
	}

	$date_from      = new DateTime(str_replace('/', '-', $date_from));

	if (function_exists($price_handler))
		return $price_handler($product_id, $date_from, $duration, $time_unit);

	return 0;
}


function get_product_prices($product)
{
	if ($product->is_type('simple')) {
		$time_unit = 'day';
		$time_block = $product->get_meta('time_unit');

		if (!empty($time_block))
			$time_unit = __($time_block, 'woocommerce');

		return array($time_unit => array(array(
			'price' => $product->price,
			'block-price' => false
		)));
	} else {
		$time_units = $product->get_attribute('time_unit');
		$variations = $product->get_available_variations();
		$prices = array();

		if (!$time_units) {
			foreach ($variations as $key => $variation) {
				$block_price = get_post_meta($variation['variation_id'], 'block_price');

				$price_block = array(
					'price'			=>	$variation['display_price'],
					'block_price'	=>	!empty($block_price)
				);


				if (isset($variation['attributes']['attribute_duration'])) {
					if (in_array($variation['attributes']['attribute_duration'], array('more', 'extra'))) {
						$more_label = get_post_meta($variation['variation_id'], 'more_label');
						$price_block['more_label'] = $more_label[0];
					}

					$prices['day'][$variation['attributes']['attribute_duration']] = $price_block;
				} elseif (isset($variation['attributes']['attribute_day']))
					$prices['day'][$variation['attributes']['attribute_day']] = $price_block;
				else
					$prices['day']['single'] = $price_block;
			}
		} else {
			foreach ($variations as $key => $variation) {
				$block_price = get_post_meta($variation['variation_id'], 'block_price');

				$price_block = array(
					'price'			=>	$variation['display_price'],
					'block_price'	=>	!empty($block_price) && $block_price[0]
				);

				

					if (
						!isset($prices[$variation['attributes']['attribute_time_unit']]) ||
						(isset($prices[$variation['attributes']['attribute_time_unit']]) &&
							$variation['display_price'] < $prices[$variation['attributes']['attribute_time_unit']])
					) {
						if (!empty($variation['attributes']['attribute_duration'])) {
							if (in_array($variation['attributes']['attribute_duration'], array('more', 'extra'))) {
								$more_label = get_post_meta($variation['variation_id'], 'more_label');
								$price_block['more_label'] = $more_label[0];
							}

							$prices[$variation['attributes']['attribute_time_unit']][$variation['attributes']['attribute_duration']] = $price_block;
						} else
							$prices[$variation['attributes']['attribute_time_unit']]['single'] = $price_block;
					}
				
			}
		}

		return $prices;
	}
}

function get_product_deposit_percentage($product)
{
	$vendor = get_product_vendor($product);
	$percentage = get_vendor_percentage($vendor);

	if (!$percentage)
		$percentage = get_vendor_percentage($product->post->post_author);

	return $percentage;
}

function get_vendor_percentage($vendor_id)
{
	$dokan_admin_percentage = get_user_meta($vendor_id, 'dokan_admin_percentage');

	if (!empty($dokan_admin_percentage))
		return $dokan_admin_percentage[0];

	return false;
}

function get_product_vendor($post)
{
	$_vendor_login = get_post_meta($post->ID, 'vendor');

	if (isset($_vendor_login) && !empty($_vendor_login[0]))
		$user = get_user_by('login', $_vendor_login[0]);

	if (isset($user) && !empty($user))
		return $user->ID;

	return $post->post_author;
}


function aha_woocommerce_show_page_title()
{
	return false;
}
add_filter('woocommerce_show_page_title', 'aha_woocommerce_show_page_title');


function get_product_default_variation($product_id)
{
	$product				= wc_get_product($product_id);
	// $default_attributes		= $product->get_default_attributes();
	$variations 			= $product->get_available_variations();

	foreach ($variations as $variation)
		return $variation['variation_id'];

	return false;
}



function aha_save_post_product($post_id, $post, $update)
{
	error_log('inside saving');

	if ($post->post_parent > 0)
		$post_id = $post->post_parent;

	$post = wc_get_product($post_id);

	$vendor_login = get_post_meta($post_id, 'vendor');
	$user = get_user_by('login', $vendor_login[0]);

	if (!empty($user)) {
		if ($user->ID != $post->post_author) {
			global $wpdb;
			$wpdb->get_results("UPDATE wp_posts SET post_author = $user->ID WHERE id = $post_id;");
		}
	}
}
add_action('save_post_product', 'aha_save_post_product', 25, 3);



function aha_after_post_meta($meta_id, $post_id, $meta_key, $meta_value)
{
	$post = wc_get_product($post_id);

	$vendor_login = get_post_meta($post_id, 'vendor');
	$user = get_user_by('login', $vendor_login[0]);

	if (!empty($user)) {
		if ($user->ID != $post->post_author) {
			global $wpdb;
			$wpdb->get_results("UPDATE wp_posts SET post_author = $user->ID WHERE id = $post_id;");
		}
	}
}
add_action('updated_post_meta', 'aha_after_post_meta', 10, 4);


function get_featured_products_query()
{
	$featured_categories = array('da-ngoai', 'laptop', 'dsrl-camera-camcorders', 'xe-may', 'o-to');

	return array(
		'post_type' => 'product',
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $featured_categories,
			)
		),
		// 'meta_key' => 'total_sales',
		// 'orderby' => 'meta_value_num',
		'orderby'	=> 'rand',
		// 'stock'       => 1,
		'showposts'   => 12,
	);
}

function get_most_rented_products_query()
{
	$featured_categories = array('da-ngoai', 'laptop', 'dsrl-camera-camcorders', 'xe-may', 'o-to');

	return array(
		'post_type' => 'product',
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $featured_categories,
			)
		),
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		// 'stock'       => 1,
		'showposts'   => 12,
	);
}

function get_car_products_query()
{
	$featured_categories = array('o-to');

	return array(
		'post_type' => 'product',
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $featured_categories,
			)
		),
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		// 'stock'       => 1,
		'showposts'   => 12,
	);
}

function get_recommended_products_query()
{
	$items = WC()->cart->get_cart();
	$categories = array();

	foreach ($items as $item) {
		$product_id = $item['product_id'];
		$terms = get_the_terms($product_id, 'product_cat');
		foreach ($terms as $term)
			array_push($categories, $term->slug);
	}

	return array(
		'post_type' => 'product',
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $categories,
			)
		),
		// 'meta_key' => 'total_sales',
		// 'orderby' => 'meta_value_num',
		'orderby'	=> 'rand',
		// 'stock'       => 1,
		'showposts'   => 18,
	);
}


function update_order_review($checkout)
{
	$get_values = array();
	parse_str($checkout, $get_values);

	if ($get_values['order_vat']) {
		// WC()->session->set( 'vat', true );
		WC()->customer->set_is_vat_exempt(true);
	} else {
		// WC()->session->set( 'vat', false );
		WC()->customer->set_is_vat_exempt(true);
	}
}
add_action('woocommerce_checkout_update_order_review', 'update_order_review');



function aha_validate_checkout_fields($fields, $errors)
{
	if ($_POST['order_vat']) {
		if (!$_POST['order_vat_company'] || empty($_POST['order_vat_company']))
			$errors->add('validation', 'company name required');
	}
}
add_action('woocommerce_after_checkout_validation', 'aha_validate_checkout_fields', 10, 2);


function get_vendor_profiles($vendor_login)
{
	$user = get_user_by('login', $vendor_login);
	$dokan_profiles = get_user_meta($user->ID, 'dokan_profile_settings', true);

	return $dokan_profiles;
}


function strip_style($string)
{
	//return $string;
	return preg_replace('/(<[^>]+) style=".*?"/i', '$1', $string);
}


function update_order_items_booking_time($order_id)
{
	$order = wc_get_order($order_id);
	$items = $order->get_items();

	foreach ($items as $item_key => $item) {
		$product = $item->get_product();

		if ($product->is_type('variation'))
			$product = new WC_Product_Variable($product->get_parent_id());

		$time_unit = $item->get_meta('time-unit');
		$duration_count = $item->get_meta('duration');
		$start = $item->get_meta('date-from');
		$start_date = DateTime::createFromFormat('d/m/Y', $start);

		$duration = '';
		if ('day' == $time_unit)
			$duration = 'P' . $duration_count . 'D';
		elseif ('week' == $time_unit)
			$duration = 'P' . $duration_count * 7 . 'D';
		elseif ('month' == $time_unit)
			$duration = 'P' . $duration_count . 'M';

		$end_date = clone $start_date;
		$end_date->add(new DateInterval($duration));

		$booking_time = $product->get_meta('booking_time');

		$booking = array(
			'start'	=>	$start_date->format('d/m/Y'),
			'end'	=>	$end_date->format('d/m/Y')
		);

		if ($booking_time)
			$booking_time[$order_id] = $booking;
		else
			$booking_time = array($order_id => $booking);


		update_post_meta($product->get_id(), 'booking_time', $booking_time);
	}
}


add_filter('wc_add_to_cart_message', 'wc_add_to_cart_message_filter', 10, 2);
function wc_add_to_cart_message_filter($message, $product_id = null)
{
	$titles[] = get_the_title($product_id);

	$titles = array_filter($titles);
	$added_text = sprintf(_n('%s has been added to your cart.', '%s have been added to your cart.', sizeof($titles), 'woocommerce'), wc_format_list_of_items($titles));

	$message = sprintf(
		'%s <div class="options"><a href="%s" class="button wc-forward">%s</a> <a href="#" class="close-message button wc-forward">%s</a></div>',
		esc_html($added_text),
		esc_url(wc_get_page_permalink('cart')),
		esc_html__('View cart', 'woocommerce'),
		__('Continue renting', 'aharent')
	);

	return $message;
}

add_filter('woocommerce_add_to_cart_redirect', 'aha_skip_cart_redirect_checkout');
function aha_skip_cart_redirect_checkout($url)
{
	if ($_POST['rent-now'] && 'rent-now' == $_POST['rent-now']) {
		return wc_get_checkout_url();
	}
}

// Set default payment
add_action('template_redirect', 'define_default_payment_gateway');
function define_default_payment_gateway()
{
	if (is_checkout() && !is_wc_endpoint_url()) {
		// HERE define the default payment gateway ID
		$default_payment_id = 'cod';

		WC()->session->set('chosen_payment_method', $default_payment_id);
	}
}

function random_total_sales()
{
	$featured_categories = array('da-ngoai', 'dien-tu', 'may-anh-quay-phim', 'xe-may', 'o-to');
	$query = array(
		'post_type' => 'product',
		'show_count'	=> 0,
		'hide_empty'	=> 0,
		'showposts'		=> 500,
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $featured_categories,
			)
		)
	);

	$products = get_posts($query);
	foreach ($products as $product) {
		update_post_meta($product->ID, 'total_sales', rand(5, 30));
	}
}

function product_query($q)
{
	// var_dump( $q );
}
add_action('woocommerce_product_query', 'product_query');


function is_store_discount($product)
{
	$vendor = get_product_vendor($product);
	$discount_percentage = get_user_meta($vendor, 'discount_percentage', true);

	if (!$discount_percentage)
		return false;

	$date_from = get_user_meta($vendor, 'discount_date_from', true);
	$discount_date_from = new DateTime(str_replace('/', '-', $date_from));

	$date_to = get_user_meta($vendor, 'discount_date_to', true);
	$discount_date_to = new DateTime(str_replace('/', '-', $date_to));

	$now = new DateTime();

	if ($now < $discount_date_from || $discount_date_to < $now)
		return false;

	return $discount_percentage;
}


function get_discount($product)
{
	$date_sale_starts = get_post_meta($product->get_id(), 'date_sale_starts', true);
	if ($date_sale_starts)
		$date_sale_starts = new DateTime($date_sale_starts);

	$date_sale_ends = get_post_meta($product->get_id(), 'date_sale_ends', true);
	if ($date_sale_ends)
		$date_sale_ends = new DateTime($date_sale_ends);

	$now = new DateTime();

	if ($now < $date_sale_starts || $date_sale_ends < $now)
		return false;

	$discount_type = get_post_meta($product->get_id(), 'sale_type', true);
	$discount_value = get_post_meta($product->get_id(), 'sale_price', true);

	return array('type' => $discount_type, 'value' => $discount_value);
}

/**
 * Turn off the email suggestion.
 *
 * @link  https://wpforms.com/developers/how-to-disable-the-email-suggestion-on-the-email-form-field/
 */

add_filter('wpforms_mailcheck_enabled', '__return_false');


add_shortcode('sale_products', 'sale_products');
function sale_products($atts)
{
	global $woocommerce_loop, $woocommerce;

	// extract( shortcode_atts( array(
	//     'per_page'      => '12',
	//     'columns'       => '4',
	//     'orderby'       => 'title',
	//     'order'         => 'asc'
	//     ), $atts ) );

	// Get products on sale
	//$product_ids_on_sale = woocommerce_get_product_ids_on_sale();

	// $meta_query = array();
	// $meta_query[] = $woocommerce->query->visibility_meta_query();
	// $meta_query[] = $woocommerce->query->stock_status_meta_query();

	$args = array(
		'posts_per_page' => 60,

		'post_status' 	=> 'publish',
		'post_type' 	=> 'product',
		'orderby' 		=> 'date',
		'order' 		=> 'ASC',
		// 'meta_query' 	=> $meta_query,
		// 'post__in'		=> $product_ids_on_sale
	);

	ob_start();

	$products = new WP_Query($args);

	// $woocommerce_loop['columns'] = $columns;

	if ($products->have_posts()) : ?>

			<?php woocommerce_product_loop_start(); ?>

				<?php while ($products->have_posts()) : $products->the_post(); ?>

					<?php // woocommerce_get_template_part( 'content', 'product' ); 
					?>

				<?php endwhile; // end of the loop. 
				?>

			<?php woocommerce_product_loop_end(); ?>

		<?php endif;

	wp_reset_postdata();

	return ob_get_clean();
}

add_filter('woocommerce_catalog_orderby', 'aha_remove_default_sorting_options');

function aha_remove_default_sorting_options($options)
{

	//unset( $options[ 'popularity' ] );
	//unset( $options[ 'menu_order' ] );
	//unset( $options[ 'rating' ] );
	//unset( $options[ 'date' ] );
	//unset( $options[ 'price' ] );
	//unset( $options[ 'price-desc' ] );

	return $options;
}

function filter_woocommerce_cart_totals_coupon_html($coupon_html, $coupon, $discount_amount_html)
{
	global $wp;
	$coupon_html = $discount_amount_html . '<a class="remove-coupon" href="' . home_url($_SERVER['REQUEST_URI']) . '/?remove_coupon=' .  $coupon->code . '"><img src="' . get_template_directory_uri()  . '/assets/img/remove.png" /></a>';

	return $coupon_html;
}
//add_filter( 'woocommerce_cart_totals_coupon_html', 'filter_woocommerce_cart_totals_coupon_html', 10, 3 );

		?>