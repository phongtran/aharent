<?php /* Template Name: Woocommerce Template */ ?>

<?php get_header(); ?>


<div class="container">

    <?php woocommerce_breadcrumb(); ?>

    <?php
        global $post, $wp_query;
    ?>
    
    <?php if ( is_archive() ): ?>
        <div class="shop-container">
            
            <?php
                if ( !is_search() || ( is_search() &&  $wp_query->found_posts > 0 ))
                    get_sidebar( 'shop' );
            ?>

            <?php 
                // Configure shop template
                // - Remove result count hook
                remove_action( 'woocommerce_before_shop_loop', 'woocommerce_output_all_notices' );

            ?>

            <?php if ( is_search() && 0 == $wp_query->found_posts ) : woocommerce_content();  ?>
            <?php else : ?>
                <div class="shop-product-listing">
                    
                    <?php
                        
                        if ( 'san-pham-thue-nhieu' == $post->post_name)
                            the_content();
                        else
                            woocommerce_content();

                    ?>
                </div>
            <?php endif ?>
        </div>

    <?php else: ?>

        <?php woocommerce_content(); ?>

    <?php endif; ?>
</div>


<?php get_footer(); ?>