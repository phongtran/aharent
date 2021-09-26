<?php

/**
 * Add pricing policy tab for product
 */
function pricing_policy_tab( $tabs )
{
	
	// Adds the new tab
	
	$tabs['pricing_policy_tab'] = array(
		'title' 	=> __( 'Giá thuê chi tiết', 'woocommerce' ),
		'priority' 	=> 1,
		'callback' 	=> 'pricing_policy_tab_content'
	);

	return $tabs;

}

global $product;


$pricing_policy = $product->post->post_excerpt;

if ( !empty($pricing_policy) )
	add_filter( 'woocommerce_product_tabs', 'pricing_policy_tab' );


function pricing_policy_tab_content()
{
	wc_get_template_part( 'single-product/tabs/pricing', 'policy' );	
}

?>