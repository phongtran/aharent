<?php
/**
 * VW Ecommerce Store: Block Patterns
 *
 * @package VW Ecommerce Store
 * @since   1.0.0
 */

/**
 * Register Block Pattern Category.
 */
if ( function_exists( 'register_block_pattern_category' ) ) {

	register_block_pattern_category(
		'vw-ecommerce-store',
		array( 'label' => __( 'VW Ecommerce Store', 'vw-ecommerce-store' ) )
	);
}

/**
 * Register Block Patterns.
 */
if ( function_exists( 'register_block_pattern' ) ) {
	register_block_pattern(
		'vw-ecommerce-store/banner-section',
		array(
			'title'      => __( 'Banner Section', 'vw-ecommerce-store' ),
			'categories' => array( 'vw-ecommerce-store' ),
			'content'    => "<!-- wp:columns {\"align\":\"wide\",\"className\":\"m-0\"} -->\n<div class=\"wp-block-columns alignwide m-0\"><!-- wp:column {\"width\":\"66.66%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:66.66%\"><!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png\",\"id\":6922,\"dimRatio\":0,\"minHeight\":490,\"className\":\"banner-section\"} -->\n<div class=\"wp-block-cover banner-section\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/banner.png);min-height:490px\"><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"level\":1,\"className\":\"mb-2 ms-4\",\"style\":{\"color\":{\"text\":\"#222222\"}}} -->\n<h1 class=\"mb-2 ms-4 has-text-color\" style=\"color:#222222\">LOREM IPSUM IS SIMPLY DUMMY</h1>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"className\":\"mb-2 ms-4 mt-0\",\"style\":{\"color\":{\"text\":\"#3a505c\"}}} -->\n<p class=\"mb-2 ms-4 mt-0 has-text-color\" style=\"color:#3a505c\">Lorem Ipsum has been the industrys standard.</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"className\":\"mt-0 ms-4\"} -->\n<div class=\"wp-block-buttons mt-0 ms-4\"><!-- wp:button {\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link no-border-radius\">SHOP MORE</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div></div>\n<!-- /wp:cover --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"width\":\"33.33%\"} -->\n<div class=\"wp-block-column\" style=\"flex-basis:33.33%\"><!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/sale-banner1.png\",\"id\":6898,\"dimRatio\":10,\"minHeight\":205,\"className\":\"mb-4\"} -->\n<div class=\"wp-block-cover has-background-dim-10 has-background-dim mb-4\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/sale-banner1.png);min-height:205px\"><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"level\":3,\"className\":\"mb-2 ms-3\",\"style\":{\"color\":{\"text\":\"#222222\"}}} -->\n<h3 class=\"mb-2 ms-3 has-text-color\" style=\"color:#222222\">PRODUCT TITLE</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"className\":\"mb-1 ms-3 mt-0\",\"style\":{\"color\":{\"text\":\"#3a505c\"}}} -->\n<p class=\"mb-1 ms-3 mt-0 has-text-color\" style=\"color:#3a505c\">One select item only</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"className\":\"mt-0 ms-3\"} -->\n<div class=\"wp-block-buttons mt-0 ms-3\"><!-- wp:button {\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link no-border-radius\">SHOP NOW</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div></div>\n<!-- /wp:cover -->\n\n<!-- wp:cover {\"url\":\"" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/sale-banner2.png\",\"id\":6899,\"dimRatio\":10,\"minHeight\":200} -->\n<div class=\"wp-block-cover has-background-dim-10 has-background-dim\" style=\"background-image:url(" . esc_url(get_template_directory_uri()) . "/inc/block-patterns/images/sale-banner2.png);min-height:200px\"><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"level\":3,\"className\":\"mb-2 ms-3\",\"style\":{\"color\":{\"text\":\"#222222\"}}} -->\n<h3 class=\"mb-2 ms-3 has-text-color\" style=\"color:#222222\">PRODUCT TITLE</h3>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"className\":\"mb-1 ms-3 mt-0\",\"style\":{\"color\":{\"text\":\"#3a505c\"}}} -->\n<p class=\"mb-1 ms-3 mt-0 has-text-color\" style=\"color:#3a505c\">One select item only</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:buttons {\"className\":\"mt-0 ms-3\"} -->\n<div class=\"wp-block-buttons mt-0 ms-3\"><!-- wp:button {\"borderRadius\":0,\"className\":\"is-style-outline\"} -->\n<div class=\"wp-block-button is-style-outline\"><a class=\"wp-block-button__link no-border-radius\">SHOP NOW</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div></div>\n<!-- /wp:cover --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns -->",
		)
	);

	register_block_pattern(
		'vw-ecommerce-store/best-seller-section',
		array(
			'title'      => __( 'Best Seller Section', 'vw-ecommerce-store' ),
			'categories' => array( 'vw-ecommerce-store' ),
			'content'    => "<!-- wp:cover {\"overlayColor\":\"white\",\"align\":\"wide\",\"className\":\"m-0 mb-5\"} -->\n<div class=\"wp-block-cover alignwide has-white-background-color has-background-dim m-0 mb-5\"><div class=\"wp-block-cover__inner-container\"><!-- wp:heading {\"textAlign\":\"center\",\"style\":{\"color\":{\"text\":\"#222222\"}}} -->\n<h2 class=\"has-text-align-center has-text-color\" style=\"color:#222222\">OUR BEST SELLER</h2>\n<!-- /wp:heading -->\n\n<!-- wp:woocommerce/product-category {\"columns\":4,\"rows\":1,\"categories\":[32],\"contentVisibility\":{\"title\":true,\"price\":true,\"rating\":false,\"button\":true},\"align\":\"wide\",\"className\":\"m-0\"} /-->\n\n<!-- wp:paragraph -->\n<p></p>\n<!-- /wp:paragraph --></div></div>\n<!-- /wp:cover -->",
		)
	);
}