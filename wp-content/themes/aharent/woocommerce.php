<?php /* Template Name: Example Template */ ?>

<?php get_header(); ?>


<div class="container">

    <?php woocommerce_breadcrumb(); ?>
    
    <?php if ( is_archive() ): ?>
        <div class="shop-container">
            <?php get_sidebar( 'shop' ); ?>

            <?php 
                // Configure shop template
                // - Remove result count hook
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices' );

            ?>

            <div class="shop-product-listing">
                <?php woocommerce_content(); ?>
            </div>
        </div>

    <?php else: ?>

        <?php woocommerce_content(); ?>

    <?php endif; ?>
</div>


<?php get_footer(); ?>