<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}
?>
<a class="product-link" href="<?php echo get_permalink( $product->id ); ?>">
	<li <?php wc_product_class( 'product-item', $product ); ?>>
	
		<div class="thumbnail">
			<?php $image = get_the_post_thumbnail_url( $product->id, 'medium' ); ?>
			<img src="<?php echo (!empty($image)) ? $image : '/wp-content/uploads/supplier_photos/default.png'; ?>" />
		</div>

		<div class="product-name">
			<h2><?php echo wp_trim_words( $product->name, 6 ); ?></h2>
		</div>

		<div class="price">
			
			<div class="rent-price">
				<?php $prices = get_product_prices( $product ); ?>
				<?php foreach ( $prices as $time_unit => $price ) : ?>
					<div class="time-unit-price">
						<?php echo wc_price( $price[ array_key_first( $price )]['price'] ) ?>/<?php echo __( $time_unit, 'woocommerce' ); ?>
					</div>
				<?php endforeach ?>
			</div>
			

			<div class="rent-count">
				<?php 
					// $total_sales = $product->get_meta( 'total_sales' );
					// if ( isset($total_sales) && $total_sales > 0 )
					// 	echo $total_sales . ' lượt thuê';
				?>
			</div>

			<?php
				$discount = get_discount( $product );
				if ( $discount ):
			?>
				<div class="discount-tag">
					<span>-<?php echo $discount['value']; echo ('percentage' == $discount['type'])? '%':'đ' ?></span>
				</div>

			<?php  endif ?>

		</div>

		
	
	
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10
		 */
		
		// do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		// do_action( 'woocommerce_before_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_product_title - 10
		 */
		// do_action( 'woocommerce_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item_title.
		 *
		 * @hooked woocommerce_template_loop_rating - 5
		 * @hooked woocommerce_template_loop_price - 10
		 */
		// do_action( 'woocommerce_after_shop_loop_item_title' );

		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5
		 * @hooked woocommerce_template_loop_add_to_cart - 10
		 */
		// do_action( 'woocommerce_after_shop_loop_item' );
		?>
		
	</li>
</a>
