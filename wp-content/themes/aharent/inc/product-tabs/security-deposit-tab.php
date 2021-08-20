<?php

/**
 * Add security deposit tab for product
 */
function security_deposit_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['security_deposit_tab'] = array(
		'title' 	=> __( 'Security deposit', 'woocommerce' ),
		'priority' 	=> 50,
		'callback' 	=> 'security_deposit_tab_content'
	);

	return $tabs;

}

global $post;

$vendor_rental_terms = get_user_meta ( $post->post_author, 'vendor_rental_terms', true );

if ( !empty($vendor_rental_terms) )
	add_filter( 'woocommerce_product_tabs', 'security_deposit_tab' );


function security_deposit_tab_content()
{
	wc_get_template_part( 'single-product/tabs/security', 'deposit' );	
}

?>