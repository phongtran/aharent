<?php /* Template Name: Sale Products Template */ ?>

<?php get_header(); ?>

<?php

    global $woocommerce_loop, $woocommerce;
    $featured_categories = array( 'da-ngoai', 'laptop', 'dsrl-camera-camcorders', 'xe-may', 'o-to' );

	$args = array(
		'post_type' => 'product',
		'tax_query'	=> array(
			array(
				'taxonomy' 	=> 'product_cat',
				'field' 	=> 'slug',
				'terms' 	=> $featured_categories,
			)
		),
        'meta_query'    => array(
            array(
                'key'   =>  'date_sale_ends',
                'value' =>  date('Y-m-d'),
                'compare' => '>=',
                'type'  => 'DATE',
            )
        ),
		'meta_key' => 'total_sales',
		'orderby' => 'meta_value_num',
		'stock'       => 1,
		'showposts'   => 12,
	);

    ob_start();

    $products = new WP_Query( $args );
?>


<div class="container">

    <?php woocommerce_breadcrumb(); ?>
    
        <div class="shop-container">
            
            <?php
            
                    get_sidebar( 'shop' );
            ?>

            <?php 
                // Configure shop template
                // - Remove result count hook
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices' );

            ?>

          
                <div class="shop-product-listing">

                    <?php

                        if ( $products->have_posts() ) : ?>

                            <?php woocommerce_product_loop_start(); ?>

                                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                                    <?php woocommerce_get_template_part( 'content', 'product' ); ?>

                                <?php endwhile; // end of the loop. ?>

                            <?php woocommerce_product_loop_end(); ?>

                        <?php endif;

                        wp_reset_postdata();   
                    ?>
                </div>
        </div>
</div>


<?php get_footer(); ?>