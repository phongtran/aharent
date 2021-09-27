<?php
/**
 * Simple product add to cart
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/add-to-cart/simple.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.4.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

if ( ! $product->is_purchasable() ) {
	return;
}


if ( $product->is_in_stock() ) : ?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<div class="add-to-cart">

	<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
		<?php do_action( 'woocommerce_before_add_to_cart_button' ); ?>
		<?php
		do_action( 'woocommerce_before_add_to_cart_quantity' );
		?>
	
			
			<div class="form-row">
				<div class="form-label">
					<label>Số lượng: </label>
				</div>

				<div class="form-input quantity-input-increment">
					<input id="quantity-input" class="number-spinner" name="quantity" type="number" value="1" min="1" max="10" step="1" />
				</div>
			</div>
		

		<?php do_action( 'woocommerce_after_add_to_cart_quantity' ); ?>	
		
		
		<div class="form-row">
			<div class="form-label">
				<label>Thời gian thuê:</label>
				<div class="validate"><span>*Vui lòng chọn ngày</span></div>
			</div>

			<div class="form-input time-period">

					<div class="duration">
						<input id="duration" class="number-spinner" name="duration" type="number" value="1" min="1" max="10" step="1" />
					</div>

					<span class="time-delimiter">
						<span class="time-unit">
							<?php
								$time_unit = __( 'day', 'woocommerce' );
								$time_block = $product->get_meta( 'time_unit' );
								if ( !empty( $time_block) )
									$time_unit = __( $time_block, 'woocommerce' );

								echo $time_unit;
							?>
						</span>, từ <?php echo __( 'day', 'woocommerce' ); ?>
					</span>
				
					<input type="text" class="date" id="date-from" name="_date_from"  placeholder="Ngày" autocomplete="off" />
					<!-- <input type="text" id="date-to" name="_date_to" placeholder="Đến ngày" autocomplete="off" />	 -->


				
			</div>
		</div>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="aha-button single_add_to_cart_button button alt">CHỌN THUÊ</button>

		<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>
	</form>
</div>

	<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>

<?php else: ?>

<!-- Out of stock -->
<div class="stock-status">
	<?php echo wc_get_stock_html( $product ); // WPCS: XSS ok. ?>
</div>


<?php endif ?>