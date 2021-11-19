<?php /* Template Name: Sale Products Template */ ?>

<?php get_header(); ?>

<?php

    global $woocommerce_loop, $woocommerce, $paged;

    if ( !$paged )
        $paged = 1;

    $per_page = 60;

	$args = array(
		'post_type' => 'product',
        'posts_per_page' => $per_page,
        'paged'         => $paged,
        'meta_query'    => array(
            array(
                'key'   =>  'date_sale_ends',
                'value' =>  date('Y-m-d'),
                'compare' => '>=',
                'type'  => 'DATE',
            )
        ),
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
              
                        wc_get_template( 'loop/result-count.php', array( 
                            'total' => $products->found_posts,
                            'per_page'  => $per_page,
                            'current'   => $paged
                        
                        ));

                        if ( woocommerce_product_loop() ) : ?>

                            <?php do_action( 'woocommerce_before_shop_loop' ); ?>

                            <?php woocommerce_product_loop_start(); ?>

                                <?php while ( $products->have_posts() ) : $products->the_post(); ?>

                                    <?php woocommerce_get_template_part( 'content', 'product' ); ?>

                                <?php endwhile; // end of the loop. ?>

                            <?php woocommerce_product_loop_end(); ?>

                        <?php endif;

                        $total_pages = ceil( $products->found_posts / $per_page );

                            wc_get_template( 'loop/pagination.php', array( 
                                'total' => $total_pages,
                                'current'   => $paged

                            ));

                        wp_reset_postdata();   
                    ?>
                </div>
        </div>
</div>


<?php get_footer(); ?>