<?php
/**
 * Empty cart page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-empty.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;

/*
 * @hooked wc_empty_cart_message - 10
 */
//do_action( 'woocommerce_cart_is_empty' );
?>

<div class="cart-empty">
	<img src="<?php echo get_template_directory_uri() . '/assets/img/order-complete.png' ?>" />
	<span><h2><?php esc_html_e( 'GIỎ HÀNG TRỐNG', 'woocommerce'); ?></h2></span>
</div>

<div class="popular-products">
        <div class="title-bar">
            <h3>SẢN PHẨM NỔI BẬT</h3>
        </div>

    
            <?php

            woocommerce_product_loop_start();

                $args = array(
                    'post_type'   => 'product',
                    'stock'       => 1,
                    'showposts'   => 15,
                    'orderby'     => 'date',
                    'order'       => 'DESC' ,
                );

                $loop = new WP_Query( $args );

                while ( $loop->have_posts() ) :
                    $loop->the_post();
                    
                    wc_get_template_part( 'content', 'product' );
                endwhile;

                wp_reset_query();

                woocommerce_product_loop_end();
            ?>

        <div class="button-more">
            <a href="/store">
                <button class="aha-button" type="button">Xem thêm</button>
            </a>
        </div>
        
    </div>
