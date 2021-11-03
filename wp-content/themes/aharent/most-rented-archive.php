<?php /* Template Name: Most Rented Template */ ?>

<?php get_header(); ?>

<?php

    global $wp_query;
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
                           the_content();
                    ?>
                </div>
        </div>
</div>


<?php get_footer(); ?>