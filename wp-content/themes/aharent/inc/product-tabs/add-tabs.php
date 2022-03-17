<?php

/**
 * Add security deposit tab for product
 */
function security_deposit_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['security_deposit_tab'] = array(
		'title' 	=> __( 'Security deposit', 'aharent' ),
		'priority' 	=> 50,
		'callback' 	=> 'security_deposit_tab_content'
	);

	return $tabs;
}

/**
 * Add receive return tab for product
 */
function receive_return_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['receive_return_tab'] = array(
		'title' 	=> __( 'Receive/Return', 'aharent' ),
		'priority' 	=> 50,
		'callback' 	=> 'receive_return_tab_content'
	);

	return $tabs;

}


/**
 * Add receive return tab for product
 */
function delivery_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['delivery_tab'] = array(
		'title' 	=> __( 'Delivery policy', 'aharent' ),
		'priority' 	=> 50,
		'callback' 	=> 'delivery_tab_content'
	);

	return $tabs;

}

/**
 * Add receive return tab for product
 */
function product_specification_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['specification_tab'] = array(
		'title' 	=> __( 'Specification', 'aharent' ),
		'priority' 	=> 50,
		'callback' 	=> 'product_specification_tab_content'
	);

	return $tabs;

}

global $product;

$vendor_login = $product->get_meta( 'vendor' );
$vendor = get_user_by( 'login', $vendor_login );

$product_specification = $product->get_meta('specification');
if ( !empty($product_specification) )
	add_filter('woocommerce_product_tabs', 'product_specification_tab' );

$product_rental_terms = $product->get_meta( 'rental_terms' );
$vendor_rental_terms = get_user_meta ( $vendor->ID, 'vendor_rental_terms', true );
if ( !empty( $product_rental_terms) || !empty($vendor_rental_terms ))
	add_filter( 'woocommerce_product_tabs', 'security_deposit_tab' );


$product_receive_return_terms = $product->get_meta( 'receive_return_terms' );
$vendor_receive_return_terms = get_user_meta ( $vendor->ID, 'receive_return_terms', true );

if ( !empty($product_receive_return_terms) || !empty($vendor_receive_return_terms) )
	add_filter( 'woocommerce_product_tabs', 'receive_return_tab' );

$delivery_terms = get_user_meta ( $vendor->ID, 'delivery_terms', true );
if ( !empty($delivery_terms) )
	add_filter( 'woocommerce_product_tabs', 'delivery_tab' );


function security_deposit_tab_content()
{
	wc_get_template_part( 'single-product/tabs/security', 'deposit' );	
}

function receive_return_tab_content()
{
	wc_get_template_part( 'single-product/tabs/receive', 'return' );	
}

function delivery_tab_content()
{
	wc_get_template_part( 'single-product/tabs/vendor', 'delivery' );	
}

function product_specification_tab_content()
{
	wc_get_template_part( 'single-product/tabs/product', 'specification' );	
}

?>