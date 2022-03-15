<?php

/**
 * Add security deposit tab for product
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

global $product;

$vendor_login = $product->get_meta( 'vendor' );
$vendor = get_user_by( 'login', $vendor_login );

$vendor_receive_return_terms = get_user_meta ( $vendor->ID, 'vendor_receive_return_terms', true );

if ( !empty($vendor_receive_return_terms) )
	add_filter( 'woocommerce_product_tabs', 'receive_return_tab' );


function receive_return_tab_content()
{
	wc_get_template_part( 'single-product/tabs/receive', 'return' );	
}

?>