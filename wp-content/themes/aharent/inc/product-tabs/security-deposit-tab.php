<?php

/**
 * Add security deposit tab for product
 */
function security_deposit_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['security_deposit_tab'] = array(
		'title' 	=> __( 'Điều khoản đặt cọc', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'security_deposit_tab_content'
	);

	return $tabs;

}

global $product;

$vendor_login = $product->get_meta( 'vendor' );
$vendor = get_user_by( 'login', $vendor_login );

$vendor_rental_terms = get_user_meta ( $vendor->ID, 'vendor_rental_terms', true );

if ( !empty($vendor_rental_terms) )
	add_filter( 'woocommerce_product_tabs', 'security_deposit_tab' );


function security_deposit_tab_content()
{
	wc_get_template_part( 'single-product/tabs/security', 'deposit' );	
}

?>